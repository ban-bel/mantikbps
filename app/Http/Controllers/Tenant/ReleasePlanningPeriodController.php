<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\PublicationTeam;
use App\Models\PublicationTeamAssignment;
use App\Models\PublicationTeamAssignmentHistory;
use App\Models\ReleasePlanningPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReleasePlanningPeriodController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        $periods = ReleasePlanningPeriod::query()
            ->where('tenant_id', $tenantId)
            ->withCount('scheduleProposals')
            ->orderBy('target_year', 'desc')
            ->get();

        // Count publications per period target year
        foreach ($periods as $period) {
            $period->publications_count = Publication::where('tenant_id', $tenantId)
                ->where('tahun', $period->target_year)
                ->count();
        }

        $suggestedTargetYear = Carbon::now()->year + 1;

        return view('tenant.release_planning.index', compact('periods', 'suggestedTargetYear'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $validated = $request->validate([
            'target_year' => [
                'required',
                'integer',
                'min:2020',
                'max:2099',
                function ($attribute, $value, $fail) use ($tenantId) {
                    $exists = ReleasePlanningPeriod::where('tenant_id', $tenantId)
                        ->where('target_year', $value)
                        ->exists();
                    if ($exists) {
                        $fail("Periode perencanaan untuk tahun target {$value} sudah ada.");
                    }
                },
            ],
            'notes'      => 'nullable|string|max:500',
            'is_open'    => 'nullable|boolean',
        ], [
            'target_year.required' => 'Tahun target rilis wajib diisi.',
        ]);

        $isOpen = $request->boolean('is_open', true);

        ReleasePlanningPeriod::create([
            'tenant_id'   => $tenantId,
            'target_year' => $validated['target_year'],
            'notes'       => $validated['notes'] ?? null,
            'is_open'     => $isOpen,
            'opened_at'   => $isOpen ? now() : null,
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('tenant.release-planning.index')
            ->with('success', "Periode perencanaan untuk tahun {$validated['target_year']} berhasil dibuat.");
    }

    public function toggle(ReleasePlanningPeriod $period)
    {
        if ($period->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $newStatus = !$period->is_open;
        $period->is_open = $newStatus;

        if ($newStatus) {
            $period->opened_at = now();
            $period->closed_at = null;
        } else {
            $period->closed_at = now();
        }

        $period->save();

        $statusText = $newStatus ? 'dibuka' : 'ditutup';
        return redirect()
            ->route('tenant.release-planning.index')
            ->with('success', "Periode perencanaan tahun {$period->target_year} berhasil {$statusText}.");
    }

    /**
     * View roll-over interface.
     */
    public function rollOverView(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $sourceYear = (int)$request->input('source_year', Carbon::now()->year);
        $targetYear = (int)$request->input('target_year', $sourceYear + 1);

        // Fetch source publications
        $sourcePublications = Publication::query()
            ->where('tenant_id', $tenantId)
            ->where('tahun', $sourceYear)
            ->with(['team.assignments.user'])
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_publikasi', 'asc')
            ->get();

        // Check which publications have already been rolled over to target year
        $existingTargetPubs = Publication::query()
            ->where('tenant_id', $tenantId)
            ->where('tahun', $targetYear)
            ->whereNotNull('source_publication_id')
            ->pluck('source_publication_id')
            ->flip()
            ->toArray();

        // Also check if an active planning period exists for target year
        $targetPeriod = ReleasePlanningPeriod::query()
            ->where('tenant_id', $tenantId)
            ->where('target_year', $targetYear)
            ->first();

        return view('tenant.release_planning.roll_over', compact(
            'sourcePublications',
            'existingTargetPubs',
            'sourceYear',
            'targetYear',
            'targetPeriod'
        ));
    }

    /**
     * Execute roll-over for selected publications.
     */
    public function executeRollOver(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $validated = $request->validate([
            'source_year'         => 'required|integer',
            'target_year'         => 'required|integer|different:source_year',
            'publication_ids'     => 'required|array|min:1',
            'publication_ids.*'   => 'required|integer|exists:publications,id',
            'copy_team'           => 'nullable|boolean',
        ], [
            'publication_ids.required' => 'Pilih minimal satu publikasi untuk di-roll over.',
            'publication_ids.min'      => 'Pilih minimal satu publikasi untuk di-roll over.',
        ]);

        $copyTeam = $request->boolean('copy_team', true);
        $targetYear = (int)$validated['target_year'];
        $pubIds = $validated['publication_ids'];

        $sources = Publication::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('id', $pubIds)
            ->with(['team.assignments.user'])
            ->get();

        $clonedCount = 0;

        DB::transaction(function () use ($sources, $tenantId, $targetYear, $copyTeam, &$clonedCount) {
            foreach ($sources as $source) {
                // Ensure not already cloned to target year
                $alreadyExists = Publication::where('tenant_id', $tenantId)
                    ->where('tahun', $targetYear)
                    ->where('source_publication_id', $source->id)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $nextNomor = (Publication::where('tenant_id', $tenantId)->lockForUpdate()->max('nomor') ?? 0) + 1;

                $adjustedTitle = self::adjustPublicationTitle($source->nama_publikasi, (int)$source->tahun, $targetYear);

                $newPub = Publication::create([
                    'tenant_id'                => $tenantId,
                    'created_by'               => Auth::id(),
                    'source_publication_id'    => $source->id,
                    'nomor'                    => $nextNomor,
                    'nama_publikasi'           => $adjustedTitle,
                    'estimasi_nomor_publikasi' => $source->estimasi_nomor_publikasi,
                    'pembuat_publikasi'        => $source->pembuat_publikasi,
                    'kategori'                 => $source->kategori, // preserved ARC/Non-ARC
                    'jadwal_rilis'             => null, // cleared for planning
                    'jadwal_upload'            => null,
                    'jadwal_mulai_pemeriksaan' => null,
                    'jadwal_mulai_penyusunan'  => null,
                    'akurasi_publikasi'        => $source->akurasi_publikasi,
                    'tahun'                    => $targetYear,
                    'periode'                  => $source->periode,
                    'wilayah'                  => $source->wilayah,
                    'jenis_publikasi'          => $source->jenis_publikasi,
                    'deskripsi_singkat'        => $source->deskripsi_singkat,
                    'status'                   => 'penyusunan',
                ]);

                // Copy team allocations if selected
                if ($copyTeam && $source->team) {
                    $teamName = $source->team->name ?: ('Tim Kerja: ' . $adjustedTitle);
                    $adjustedTeamName = self::adjustPublicationTitle($teamName, (int)$source->tahun, $targetYear);

                    $newTeam = PublicationTeam::create([
                        'publication_id' => $newPub->id,
                        'name'           => $adjustedTeamName,
                        'created_by'     => Auth::id(),
                        'notes'          => "Roll-over dari tim publikasi tahun {$source->tahun}",
                    ]);

                    $snapshot = [];
                    foreach ($source->team->assignments as $assignment) {
                        PublicationTeamAssignment::create([
                            'publication_id'      => $newPub->id,
                            'publication_team_id' => $newTeam->id,
                            'user_id'             => $assignment->user_id,
                            'assignment_role'     => $assignment->assignment_role,
                            'assigned_by'         => Auth::id(),
                            'notes'               => "Dialokasikan dari roll-over publikasi tahun {$source->tahun}",
                        ]);

                        $userName = optional($assignment->user)->name ?? 'User #' . $assignment->user_id;
                        $snapshot[$assignment->assignment_role][] = $userName;
                    }

                    // Record history log
                    PublicationTeamAssignmentHistory::create([
                        'publication_id' => $newPub->id,
                        'action'         => 'Alokasi tim kerja dari roll-over tahun sebelumnya',
                        'old_value'      => json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        'new_value'      => json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        'notes'          => "Roll-over dari publikasi: {$source->nama_publikasi} ({$source->tahun})",
                        'changed_by'     => Auth::id(),
                    ]);
                }

                $clonedCount++;
            }
        });

        return redirect()
            ->route('tenant.release-planning.roll-over', [
                'source_year' => $validated['source_year'],
                'target_year' => $targetYear,
            ])
            ->with('success', "Berhasil me-roll over {$clonedCount} publikasi ke tahun target {$targetYear}. Publikasi siap masuk tahap pengusulan jadwal.");
    }

    /**
     * Delete/reset a release planning period.
     */
    public function destroy(ReleasePlanningPeriod $period)
    {
        if ($period->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $year = $period->target_year;
        $period->scheduleProposals()->delete();
        $period->delete();

        return redirect()
            ->route('tenant.release-planning.index')
            ->with('success', "Periode perencanaan rilis tahun {$year} berhasil dihapus/direset.");
    }

    /**
     * Reset/delete rolled over publications for target year so admin can re-test.
     */
    public function resetRollOver(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $targetYear = (int)$request->input('target_year');

        $pubs = Publication::where('tenant_id', $tenantId)
            ->where('tahun', $targetYear)
            ->whereNotNull('source_publication_id')
            ->get();

        $count = $pubs->count();
        foreach ($pubs as $pub) {
            if ($pub->team) {
                $pub->team->assignments()->delete();
                $pub->team->delete();
            }
            $pub->scheduleProposals()->delete();
            $pub->delete();
        }

        return redirect()
            ->route('tenant.release-planning.roll-over', [
                'target_year' => $targetYear,
                'source_year' => $targetYear - 1,
            ])
            ->with('success', "Berhasil mereset {$count} publikasi hasil roll-over tahun {$targetYear}. Anda dapat melakukan uji coba roll-over kembali.");
    }

    /**
     * Admin monitoring dashboard for all proposals in a release planning period.
     */
    public function monitoring(ReleasePlanningPeriod $period)
    {
        $tenantId = Auth::user()->tenant_id;
        if ($period->tenant_id !== $tenantId) {
            abort(403);
        }

        $publications = Publication::query()
            ->where('tenant_id', $tenantId)
            ->where('tahun', $period->target_year)
            ->with(['teamAssignments.user', 'scheduleProposals.user'])
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_publikasi', 'asc')
            ->get();

        $stats = [
            'total_publications' => $publications->count(),
            'finalized'          => 0,
            'complete_agreed'    => 0,
            'complete_conflict'  => 0,
            'partial'            => 0,
            'no_proposals'       => 0,
            'cancelled_t1'       => 0,
            'complete_drop'      => 0,
        ];

        foreach ($publications as $pub) {
            $pub->summary = \App\Support\ScheduleProposalService::getSummary($pub, $period);
            $pub->hint = \App\Support\ScheduleHintService::getHint($pub);

            if ($pub->planning_status === 'cancelled_t1') {
                $stats['cancelled_t1']++;
            } elseif ($pub->jadwal_rilis) {
                $stats['finalized']++;
            }

            $code = $pub->summary['status_code'];
            if (isset($stats[$code])) {
                $stats[$code]++;
            }
        }

        return view('tenant.release_planning.monitoring', compact('period', 'publications', 'stats'));
    }

    /**
     * Adjust year references in a publication title based on source and target years.
     */
    public static function adjustPublicationTitle(string $title, int $sourceYear, int $targetYear): string
    {
        $diff = $targetYear - $sourceYear;
        if ($diff === 0) {
            return $title;
        }

        // Increment 4-digit years (e.g. 2024 -> 2025, 2025 -> 2026, 2026 -> 2027)
        return preg_replace_callback('/\b(20\d\d)\b/', function ($matches) use ($diff) {
            return (int)$matches[1] + $diff;
        }, $title);
    }
}
