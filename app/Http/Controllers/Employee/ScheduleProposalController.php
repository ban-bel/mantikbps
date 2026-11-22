<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\PublicationScheduleProposal;
use App\Models\PublicationTeam;
use App\Models\PublicationTeamAssignment;
use App\Models\ReleasePlanningPeriod;
use App\Models\User;
use App\Support\ScheduleGuardrailValidator;
use App\Support\ScheduleHintService;
use App\Support\ScheduleProposalService;
use App\Support\WorkingDayHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleProposalController extends Controller
{
    /**
     * List publications in open planning periods for current user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Fetch all periods for this tenant
        $periods = ReleasePlanningPeriod::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('target_year', 'desc')
            ->get();

        $activePeriod = $periods->firstWhere('is_open', true) ?: $periods->first();

        $selectedYear = (int)$request->input('year', $activePeriod ? $activePeriod->target_year : Carbon::now()->year + 1);
        $currentPeriod = $periods->firstWhere('target_year', $selectedYear);

        // Fetch publications where user has viewer/proposer role
        $publications = Publication::query()
            ->where('tenant_id', $tenantId)
            ->where('tahun', $selectedYear)
            ->whereHas('teamAssignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('assignment_role', ScheduleProposalService::VIEWER_ROLES);
            })
            ->with(['teamAssignments.user', 'scheduleProposals.user'])
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_publikasi', 'asc')
            ->get();

        // Enhance publications with summary, user role, and user proposal
        foreach ($publications as $pub) {
            $pub->userRole = ScheduleProposalService::getProposerRole($pub, $user);
            $pub->canPropose = ScheduleProposalService::canPropose($pub, $user) && ($currentPeriod && $currentPeriod->is_open);
            $pub->canFinalize = ScheduleProposalService::canFinalize($pub, $user);
            $pub->userProposal = $pub->scheduleProposals->where('user_id', $user->id)->first();
            $pub->summary = ScheduleProposalService::getSummary($pub);
        }

        $isKetuaTim = ScheduleProposalService::isKetuaTim($user, (int)$tenantId);

        return view('employee.schedule_proposals.index', compact(
            'publications',
            'periods',
            'currentPeriod',
            'selectedYear',
            'isKetuaTim'
        ));
    }

    /**
     * Show form for Ketua Tim to propose a brand new publication for T+1.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        if (!ScheduleProposalService::isKetuaTim($user, (int)$tenantId)) {
            abort(403, 'Hanya Ketua Tim yang memiliki hak untuk mengusulkan publikasi baru.');
        }

        $periods = ReleasePlanningPeriod::query()
            ->where('tenant_id', $tenantId)
            ->where('is_open', true)
            ->orderBy('target_year', 'asc')
            ->get();

        if ($periods->isEmpty()) {
            return redirect()
                ->route('employee.schedule-proposals.index')
                ->with('error', 'Tidak ada periode perencanaan rilis T+1 yang sedang aktif / terbuka.');
        }

        $selectedYear = (int)$request->input('year', $periods->first()->target_year);
        $currentPeriod = $periods->firstWhere('target_year', $selectedYear) ?: $periods->first();

        // Get tenant employees for team member assignment dropdowns
        $employees = User::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'login_id', 'email']);

        return view('employee.schedule_proposals.create', compact(
            'currentPeriod',
            'periods',
            'selectedYear',
            'employees',
            'user'
        ));
    }

    /**
     * Store new publication initiated by Ketua Tim for T+1.
     */
    public function storeNewPublication(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        if (!ScheduleProposalService::isKetuaTim($user, (int)$tenantId)) {
            abort(403, 'Hanya Ketua Tim yang memiliki hak untuk mengusulkan publikasi baru.');
        }

        $validated = $request->validate([
            'target_year'                       => 'required|integer',
            'nama_publikasi'                    => 'required|string|max:255',
            'kategori'                          => 'required|in:ARC,Non-ARC',
            'periode'                           => 'required|string|max:50',
            'deskripsi_singkat'                 => 'nullable|string|max:1000',
            'penyusun_user_ids'                 => 'required|array|min:1',
            'penyusun_user_ids.*'               => 'required|integer|exists:users,id',
            'anggota_user_ids'                  => 'nullable|array',
            'anggota_user_ids.*'                => 'nullable|integer|exists:users,id',
            'proposed_jadwal_rilis'             => 'required|date',
            'proposed_jadwal_upload'            => 'required|date',
            'proposed_jadwal_mulai_pemeriksaan' => 'required|date',
            'proposed_jadwal_mulai_penyusunan'  => 'required|date',
            'notes'                             => 'nullable|string|max:1000',
        ], [
            'nama_publikasi.required'           => 'Judul publikasi baru wajib diisi.',
            'kategori.required'                 => 'Kategori publikasi wajib dipilih.',
            'penyusun_user_ids.required'        => 'Minimal satu Penyusun Naskah wajib ditugaskan.',
            'penyusun_user_ids.min'             => 'Minimal satu Penyusun Naskah wajib ditugaskan.',
            'penyusun_user_ids.*.required'      => 'Pegawai Penyusun Naskah wajib dipilih.',
            'penyusun_user_ids.*.exists'        => 'Pegawai Penyusun Naskah tidak valid.',
            'proposed_jadwal_rilis.required'    => 'Usulan jadwal rilis wajib diisi.',
            'proposed_jadwal_upload.required'   => 'Usulan jadwal upload wajib diisi.',
            'proposed_jadwal_mulai_pemeriksaan.required' => 'Usulan jadwal mulai pemeriksaan wajib diisi.',
            'proposed_jadwal_mulai_penyusunan.required'  => 'Usulan jadwal mulai penyusunan wajib diisi.',
        ]);

        $period = ReleasePlanningPeriod::query()
            ->where('tenant_id', $tenantId)
            ->where('target_year', $validated['target_year'])
            ->first();

        if (!$period || !$period->is_open) {
            return back()->withInput()->with('error', 'Periode perencanaan rilis tahun ' . $validated['target_year'] . ' sedang ditutup.');
        }

        // Validate schedule guardrails
        ScheduleGuardrailValidator::validateOrFail([
            'jadwal_rilis'             => $validated['proposed_jadwal_rilis'],
            'jadwal_upload'            => $validated['proposed_jadwal_upload'],
            'jadwal_mulai_pemeriksaan' => $validated['proposed_jadwal_mulai_pemeriksaan'],
            'jadwal_mulai_penyusunan'  => $validated['proposed_jadwal_mulai_penyusunan'],
        ], (int)$tenantId);

        DB::transaction(function () use ($validated, $tenantId, $user, $period) {
            $nextNomor = (Publication::where('tenant_id', $tenantId)->lockForUpdate()->max('nomor') ?? 0) + 1;

            // 1. Create Publication (source_publication_id is null indicating new initiative)
            $publication = Publication::create([
                'tenant_id'                => $tenantId,
                'created_by'               => $user->id,
                'source_publication_id'    => null,
                'nomor'                    => $nextNomor,
                'nama_publikasi'           => $validated['nama_publikasi'],
                'estimasi_nomor_publikasi' => null,
                'pembuat_publikasi'        => $user->name,
                'kategori'                 => $validated['kategori'],
                'jadwal_rilis'             => null, // awaiting reconciliation/finalization
                'jadwal_upload'            => null,
                'jadwal_mulai_pemeriksaan' => null,
                'jadwal_mulai_penyusunan'  => null,
                'tahun'                    => (int)$validated['target_year'],
                'periode'                  => $validated['periode'],
                'deskripsi_singkat'        => $validated['deskripsi_singkat'] ?? null,
                'planning_status'          => 'draft',
                'status'                   => 'penyusunan',
            ]);

            // 2. Create Team in PublicationTeams
            $team = PublicationTeam::create([
                'publication_id' => $publication->id,
                'name'           => 'Tim Kerja: ' . $publication->nama_publikasi,
                'created_by'     => $user->id,
                'notes'          => 'Inisiatif publikasi baru T+1 diusulkan oleh Ketua Tim ' . $user->name,
            ]);

            // 3. Assign Ketua Tim (Current User)
            PublicationTeamAssignment::create([
                'publication_id'      => $publication->id,
                'publication_team_id' => $team->id,
                'user_id'             => $user->id,
                'assignment_role'     => 'ketua_pemeriksa_konten',
                'assigned_by'         => $user->id,
            ]);

            // 4. Assign Tim Penyusun Naskah (Support Multiple Authors)
            $penyusunIds = array_unique(array_filter($validated['penyusun_user_ids']));
            foreach ($penyusunIds as $penyusunId) {
                PublicationTeamAssignment::create([
                    'publication_id'      => $publication->id,
                    'publication_team_id' => $team->id,
                    'user_id'             => (int)$penyusunId,
                    'assignment_role'     => 'penyusun_naskah',
                    'assigned_by'         => $user->id,
                ]);
            }

            // 5. Assign Anggota Pemeriksa Konten (Support Multiple Reviewers)
            $anggotaIds = array_unique(array_filter($validated['anggota_user_ids'] ?? []));
            foreach ($anggotaIds as $anggotaId) {
                if ((int)$anggotaId !== (int)$user->id && !in_array((int)$anggotaId, $penyusunIds, true)) {
                    PublicationTeamAssignment::create([
                        'publication_id'      => $publication->id,
                        'publication_team_id' => $team->id,
                        'user_id'             => (int)$anggotaId,
                        'assignment_role'     => 'anggota_pemeriksa_konten',
                        'assigned_by'         => $user->id,
                    ]);
                }
            }

            // 6. Record Initial Proposal from Ketua Tim
            PublicationScheduleProposal::create([
                'publication_id'             => $publication->id,
                'release_planning_period_id' => $period->id,
                'user_id'                    => $user->id,
                'role'                       => 'ketua_pemeriksa_konten',
                'will_release'               => true,
                'proposed_kategori'          => $validated['kategori'],
                'proposed_jadwal_rilis'      => $validated['proposed_jadwal_rilis'],
                'proposed_jadwal_upload'     => $validated['proposed_jadwal_upload'],
                'proposed_mulai_pemeriksaan' => $validated['proposed_jadwal_mulai_pemeriksaan'],
                'proposed_mulai_penyusunan'  => $validated['proposed_jadwal_mulai_penyusunan'],
                'notes'                      => $validated['notes'] ?? 'Usulan awal inisiatif publikasi baru T+1',
            ]);
        });

        return redirect()
            ->route('employee.schedule-proposals.index', ['year' => $validated['target_year']])
            ->with('success', 'Rencana publikasi baru "' . $validated['nama_publikasi'] . '" berhasil ditambahkan untuk tahun target ' . $validated['target_year'] . '!');
    }

    /**
     * Show proposal form and status for a specific publication.
     */
    public function show(Publication $publication)
    {
        $user = Auth::user();

        if (!ScheduleProposalService::canView($publication, $user)) {
            abort(403, 'Anda tidak terdaftar dalam tim publikasi ini untuk melihat atau mengusulkan jadwal.');
        }

        $period = ReleasePlanningPeriod::query()
            ->where('tenant_id', $publication->tenant_id)
            ->where('target_year', $publication->tahun)
            ->first();

        $canPropose = ScheduleProposalService::canPropose($publication, $user) && ($period && $period->is_open);
        $canFinalize = ScheduleProposalService::canFinalize($publication, $user);
        $proposerRole = ScheduleProposalService::getProposerRole($publication, $user);

        $userProposal = $publication->scheduleProposals()
            ->where('user_id', $user->id)
            ->first();

        // If current user hasn't proposed yet, get latest proposal from peer to prefill
        $latestPeerProposal = null;
        if (!$userProposal) {
            $latestPeerProposal = $publication->scheduleProposals()
                ->where('user_id', '!=', $user->id)
                ->with('user')
                ->latest('updated_at')
                ->first();
        }

        $summary = ScheduleProposalService::getSummary($publication);
        $hint = ScheduleHintService::getHint($publication);

        return view('employee.schedule_proposals.show', compact(
            'publication',
            'period',
            'canPropose',
            'canFinalize',
            'proposerRole',
            'userProposal',
            'latestPeerProposal',
            'summary',
            'hint'
        ));
    }

    /**
     * Store or update user's own proposal.
     */
    public function store(Request $request, Publication $publication)
    {
        $user = Auth::user();

        if (!ScheduleProposalService::canPropose($publication, $user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengisi usulan jadwal publikasi ini.');
        }

        $period = ReleasePlanningPeriod::query()
            ->where('tenant_id', $publication->tenant_id)
            ->where('target_year', $publication->tahun)
            ->first();

        if (!$period || !$period->is_open) {
            return back()->with('error', 'Periode perencanaan rilis untuk publikasi ini sedang ditutup.');
        }

        $willRelease = $request->boolean('will_release', true);

        if (!$willRelease) {
            $validated = $request->validate([
                'drop_reason' => 'required|string|max:1000',
            ], [
                'drop_reason.required' => 'Alasan usulan tidak merilis publikasi wajib diisi.',
            ]);

            $proposerRole = ScheduleProposalService::getProposerRole($publication, $user);

            PublicationScheduleProposal::updateOrCreate(
                [
                    'publication_id'             => $publication->id,
                    'release_planning_period_id' => $period->id,
                    'user_id'                    => $user->id,
                    'role'                       => $proposerRole,
                ],
                [
                    'will_release'               => false,
                    'drop_reason'                => $validated['drop_reason'],
                    'proposed_kategori'          => $publication->kategori ?? 'Non-ARC',
                    'proposed_jadwal_rilis'      => null,
                    'proposed_jadwal_upload'     => null,
                    'proposed_mulai_pemeriksaan' => null,
                    'proposed_mulai_penyusunan'  => null,
                    'notes'                      => $validated['drop_reason'],
                ]
            );

            return redirect()
                ->route('employee.schedule-proposals.show', $publication)
                ->with('success', 'Usulan bahwa publikasi ini tidak dirilis di tahun ' . $publication->tahun . ' berhasil disimpan.');
        }

        $validated = $request->validate([
            'proposed_kategori'                 => 'required|in:ARC,Non-ARC',
            'proposed_jadwal_rilis'             => 'required|date',
            'proposed_jadwal_upload'            => 'required|date',
            'proposed_jadwal_mulai_pemeriksaan' => 'required|date',
            'proposed_jadwal_mulai_penyusunan'  => 'required|date',
            'notes'                             => 'nullable|string|max:1000',
        ], [
            'proposed_kategori.required'                 => 'Usulan kategori publikasi wajib dipilih.',
            'proposed_jadwal_rilis.required'             => 'Usulan jadwal rilis wajib diisi.',
            'proposed_jadwal_upload.required'            => 'Usulan jadwal upload wajib diisi.',
            'proposed_jadwal_mulai_pemeriksaan.required' => 'Usulan jadwal mulai pemeriksaan wajib diisi.',
            'proposed_jadwal_mulai_penyusunan.required'  => 'Usulan jadwal mulai penyusunan wajib diisi.',
        ]);

        // Validate schedule guardrails
        ScheduleGuardrailValidator::validateOrFail([
            'jadwal_rilis'             => $validated['proposed_jadwal_rilis'],
            'jadwal_upload'            => $validated['proposed_jadwal_upload'],
            'jadwal_mulai_pemeriksaan' => $validated['proposed_jadwal_mulai_pemeriksaan'],
            'jadwal_mulai_penyusunan'  => $validated['proposed_jadwal_mulai_penyusunan'],
        ], (int)$publication->tenant_id);

        $proposerRole = ScheduleProposalService::getProposerRole($publication, $user);

        PublicationScheduleProposal::updateOrCreate(
            [
                'publication_id'             => $publication->id,
                'release_planning_period_id' => $period->id,
                'user_id'                    => $user->id,
                'role'                       => $proposerRole,
            ],
            [
                'will_release'               => true,
                'drop_reason'                => null,
                'proposed_kategori'          => $validated['proposed_kategori'],
                'proposed_jadwal_rilis'      => $validated['proposed_jadwal_rilis'],
                'proposed_jadwal_upload'     => $validated['proposed_jadwal_upload'],
                'proposed_mulai_pemeriksaan' => $validated['proposed_jadwal_mulai_pemeriksaan'],
                'proposed_mulai_penyusunan'  => $validated['proposed_jadwal_mulai_penyusunan'],
                'notes'                      => $validated['notes'] ?? null,
            ]
        );

        return redirect()
            ->route('employee.schedule-proposals.show', $publication)
            ->with('success', 'Usulan jadwal Anda berhasil disimpan.');
    }

    /**
     * Reconciliation view for Ketua Pemeriksa Konten.
     */
    public function reconcile(Publication $publication)
    {
        $user = Auth::user();

        if (!ScheduleProposalService::canFinalize($publication, $user)) {
            abort(403, 'Hanya Ketua Pemeriksa Konten yang berhak melakukan rekonsiliasi dan finalisasi jadwal.');
        }

        $period = ReleasePlanningPeriod::query()
            ->where('tenant_id', $publication->tenant_id)
            ->where('target_year', $publication->tahun)
            ->first();

        $summary = ScheduleProposalService::getSummary($publication);
        $hint = ScheduleHintService::getHint($publication);

        return view('employee.schedule_proposals.reconcile', compact(
            'publication',
            'period',
            'summary',
            'hint'
        ));
    }

    /**
     * Finalize & lock publication schedule or confirm cancellation by Ketua Pemeriksa Konten.
     */
    public function finalize(Request $request, Publication $publication)
    {
        $user = Auth::user();

        if (!ScheduleProposalService::canFinalize($publication, $user)) {
            abort(403, 'Hanya Ketua Pemeriksa Konten yang berhak menetapkan jadwal definitif.');
        }

        $decision = $request->input('action_decision', 'schedule');

        if ($decision === 'cancel_t1') {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ], [
                'cancellation_reason.required' => 'Alasan keputusan tidak merilis publikasi wajib diisi.',
            ]);

            $publication->update([
                'planning_status'     => 'cancelled_t1',
                'cancellation_reason' => $validated['cancellation_reason'],
                'finalized_by'        => $user->id,
                'finalized_at'        => now(),
                'jadwal_rilis'        => null,
                'jadwal_upload'       => null,
                'jadwal_mulai_pemeriksaan' => null,
                'jadwal_mulai_penyusunan'  => null,
            ]);

            return redirect()
                ->route('employee.schedule-proposals.show', $publication)
                ->with('success', "Publikasi {$publication->nama_publikasi} resmi ditetapkan TIDAK DIRILIS pada tahun {$publication->tahun}. Keputusan telah dikunci.");
        }

        $validated = $request->validate([
            'nama_publikasi'           => 'required|string|max:255',
            'kategori'                 => 'required|in:ARC,Non-ARC',
            'jadwal_rilis'             => 'required|date',
            'jadwal_upload'            => 'required|date',
            'jadwal_mulai_pemeriksaan' => 'required|date',
            'jadwal_mulai_penyusunan'  => 'required|date',
        ], [
            'nama_publikasi.required'           => 'Judul publikasi definitif wajib diisi.',
            'kategori.required'                 => 'Kategori publikasi final wajib dipilih.',
            'jadwal_rilis.required'             => 'Jadwal rilis final wajib diisi.',
            'jadwal_upload.required'            => 'Jadwal upload final wajib diisi.',
            'jadwal_mulai_pemeriksaan.required' => 'Jadwal mulai pemeriksaan final wajib diisi.',
            'jadwal_mulai_penyusunan.required'  => 'Jadwal mulai penyusunan final wajib diisi.',
        ]);

        // Validate schedule guardrails
        ScheduleGuardrailValidator::validateOrFail($validated, (int)$publication->tenant_id);

        $publication->update([
            'planning_status'          => 'scheduled',
            'cancellation_reason'      => null,
            'finalized_by'             => $user->id,
            'finalized_at'             => now(),
            'nama_publikasi'           => $validated['nama_publikasi'],
            'kategori'                 => $validated['kategori'],
            'jadwal_rilis'             => $validated['jadwal_rilis'],
            'jadwal_upload'            => $validated['jadwal_upload'],
            'jadwal_mulai_pemeriksaan' => $validated['jadwal_mulai_pemeriksaan'],
            'jadwal_mulai_penyusunan'  => $validated['jadwal_mulai_penyusunan'],
        ]);

        return redirect()
            ->route('employee.schedule-proposals.show', $publication)
            ->with('success', "Jadwal resmi publikasi {$publication->nama_publikasi} untuk tahun {$publication->tahun} berhasil ditetapkan dan dikunci.");
    }

    /**
     * Helper API endpoint to calculate suggested dates dynamically via AJAX.
     */
    public function calculateSchedule(Request $request)
    {
        $request->validate(['jadwal_rilis' => 'required|date']);

        $user = Auth::user();
        $suggested = WorkingDayHelper::suggestSchedule($request->input('jadwal_rilis'), $user->tenant_id);

        return response()->json($suggested);
    }
}
