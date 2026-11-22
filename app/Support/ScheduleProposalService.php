<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\PublicationScheduleProposal;
use App\Models\PublicationTeamAssignment;
use App\Models\ReleasePlanningPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ScheduleProposalService
 *
 * Mengelola logika bisnis pengusulan jadwal rilis T+1:
 *  - Siapa yang berhak mengisi usulan untuk tiap publikasi
 *  - Siapa yang berhak melihat kondisi terkini pengisian
 *  - Mengambil ringkasan status pengisian per publikasi
 */
class ScheduleProposalService
{
    /**
     * Role assignment_role dari team yang BERHAK mengisi usulan jadwal.
     */
    public const PROPOSER_ROLES = [
        'penyusun_naskah',
        'anggota_pemeriksa_konten',
        'ketua_pemeriksa_konten',
    ];

    /**
     * Role yang berhak MELIHAT kondisi pengisian terkini.
     */
    public const VIEWER_ROLES = [
        'penyusun_naskah',
        'anggota_pemeriksa_konten',
        'ketua_pemeriksa_konten',
    ];

    /**
     * Apakah user tertentu berhak mengisi usulan jadwal untuk publikasi ini?
     */
    public static function canPropose(Publication $publication, User $user): bool
    {
        if ($publication->relationLoaded('teamAssignments')) {
            return $publication->teamAssignments
                ->where('user_id', $user->id)
                ->whereIn('assignment_role', self::PROPOSER_ROLES)
                ->isNotEmpty();
        }

        return $publication->teamAssignments()
            ->where('user_id', $user->id)
            ->whereIn('assignment_role', self::PROPOSER_ROLES)
            ->exists();
    }

    /**
     * Apakah user berhak menetapkan jadwal final (hanya ketua_pemeriksa_konten)?
     */
    public static function canFinalize(Publication $publication, User $user): bool
    {
        if ($publication->relationLoaded('teamAssignments')) {
            return $publication->teamAssignments
                ->where('user_id', $user->id)
                ->where('assignment_role', 'ketua_pemeriksa_konten')
                ->isNotEmpty();
        }

        return $publication->teamAssignments()
            ->where('user_id', $user->id)
            ->where('assignment_role', 'ketua_pemeriksa_konten')
            ->exists();
    }

    /**
     * Apakah user memiliki peran/otoritas Ketua Tim di satker/tenant ini?
     */
    public static function isKetuaTim(User $user, int $tenantId): bool
    {
        if (in_array($user->role, ['admin_sistem', 'admin_provinsi', 'admin_kabkota', 'admin', 'pimpinan'], true)) {
            return true;
        }

        try {
            return PublicationTeamAssignment::where('user_id', $user->id)
                ->where('assignment_role', 'ketua_pemeriksa_konten')
                ->whereHas('publication', fn($q) => $q->where('tenant_id', $tenantId))
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Apakah user berhak melihat kondisi pengisian terkini?
     */
    public static function canView(Publication $publication, User $user): bool
    {
        if ($publication->relationLoaded('teamAssignments')) {
            return $publication->teamAssignments
                ->where('user_id', $user->id)
                ->whereIn('assignment_role', self::VIEWER_ROLES)
                ->isNotEmpty();
        }

        return $publication->teamAssignments()
            ->where('user_id', $user->id)
            ->whereIn('assignment_role', self::VIEWER_ROLES)
            ->exists();
    }

    /**
     * Ambil role pengisian user pada publikasi ini.
     * Hierarki: ketua_pemeriksa_konten > anggota_pemeriksa_konten > penyusun_naskah
     */
    public static function getProposerRole(Publication $publication, User $user): ?string
    {
        $priority = [
            'ketua_pemeriksa_konten',
            'anggota_pemeriksa_konten',
            'penyusun_naskah',
        ];

        if ($publication->relationLoaded('teamAssignments')) {
            $userRoles = $publication->teamAssignments
                ->where('user_id', $user->id)
                ->whereIn('assignment_role', self::PROPOSER_ROLES)
                ->pluck('assignment_role')
                ->toArray();
        } else {
            $userRoles = $publication->teamAssignments()
                ->where('user_id', $user->id)
                ->whereIn('assignment_role', self::PROPOSER_ROLES)
                ->pluck('assignment_role')
                ->toArray();
        }

        foreach ($priority as $role) {
            if (in_array($role, $userRoles, true)) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Ringkasan status pengisian usulan untuk satu publikasi.
     */
    public static function getSummary(Publication $publication, ?ReleasePlanningPeriod $period = null): array
    {
        // 1. Get team assignments eligible for proposal
        if ($publication->relationLoaded('teamAssignments')) {
            $teamMembers = $publication->teamAssignments
                ->whereIn('assignment_role', self::PROPOSER_ROLES);
        } else {
            $teamMembers = $publication->teamAssignments()
                ->whereIn('assignment_role', self::PROPOSER_ROLES)
                ->with('user')
                ->get();
        }

        // 2. Get proposals
        if ($publication->relationLoaded('scheduleProposals')) {
            $proposals = $publication->scheduleProposals;
            if ($period) {
                $proposals = $proposals->where('release_planning_period_id', $period->id);
            }
        } else {
            $query = $publication->scheduleProposals()->with('user');
            if ($period) {
                $query->where('release_planning_period_id', $period->id);
            }
            $proposals = $query->get();
        }

        $uniqueUsersCount = $teamMembers->pluck('user_id')->unique()->count();
        $proposerCount = $proposals->pluck('user_id')->unique()->count();
        $isComplete = $uniqueUsersCount > 0 && $proposerCount >= $uniqueUsersCount;

        $dropProposals = $proposals->filter(fn ($p) => $p->will_release === false || $p->will_release === 0 || $p->will_release === '0');
        $keepProposals = $proposals->filter(fn ($p) => !($p->will_release === false || $p->will_release === 0 || $p->will_release === '0'));

        // Check conflicts in will_release, dates, or category
        $hasConflict = false;
        if ($proposals->count() > 1) {
            if ($dropProposals->isNotEmpty() && $keepProposals->isNotEmpty()) {
                $hasConflict = true;
            } else {
                $dates = $keepProposals->map(function ($p) {
                    $d = $p->proposed_jadwal_rilis;
                    return $d instanceof Carbon ? $d->format('Y-m-d') : (string)$d;
                })->unique();

                $kategori = $keepProposals->pluck('proposed_kategori')->unique();

                if ($dates->count() > 1 || $kategori->count() > 1) {
                    $hasConflict = true;
                }
            }
        }

        // Determine status code
        if ($publication->planning_status === 'cancelled_t1') {
            $statusCode = 'cancelled_t1';
        } elseif ($publication->planning_status === 'scheduled' || $publication->jadwal_rilis) {
            $statusCode = 'scheduled';
        } elseif ($proposerCount === 0) {
            $statusCode = 'no_proposals';
        } elseif (!$isComplete) {
            $statusCode = 'partial';
        } elseif ($hasConflict) {
            $statusCode = 'complete_conflict';
        } elseif ($dropProposals->count() === $proposerCount) {
            $statusCode = 'complete_drop';
        } else {
            $statusCode = 'complete_agreed';
        }

        // Build member summary details
        $memberList = [];
        foreach ($teamMembers as $assignment) {
            $u = $assignment->user;
            $userProp = $proposals->firstWhere('user_id', $assignment->user_id);
            $roleLabel = match($assignment->assignment_role) {
                'penyusun_naskah' => 'Penyusun Naskah',
                'anggota_pemeriksa_konten' => 'Anggota Pemeriksa Konten',
                'ketua_pemeriksa_konten' => 'Ketua Pemeriksa Konten',
                default => $assignment->assignment_role,
            };

            $memberList[] = [
                'user_id'      => $assignment->user_id,
                'name'         => $u ? $u->name : 'Pegawai',
                'role'         => $assignment->assignment_role,
                'role_label'   => $roleLabel,
                'has_proposed' => $userProp !== null,
                'proposal'     => $userProp,
            ];
        }

        return [
            'proposals'       => $proposals,
            'team_members'    => $memberList,
            'proposer_count'  => $proposerCount,
            'total_proposers' => $uniqueUsersCount,
            'is_complete'     => $isComplete,
            'has_conflict'    => $hasConflict,
            'status_code'     => $statusCode,
        ];
    }

    /**
     * Label status pengisian.
     */
    public static function getStatusLabel(array|string $status): string
    {
        $code = is_array($status) ? ($status['status_code'] ?? 'no_proposals') : $status;

        return match ($code) {
            'cancelled_t1'      => 'Dikonfirmasi Tidak Dirilis di T+1',
            'scheduled'         => 'Jadwal Telah Ditetapkan',
            'no_proposals'      => 'Belum Ada Usulan',
            'partial'           => is_array($status) ? "Sebagian ({$status['proposer_count']}/{$status['total_proposers']})" : 'Sebagian Mengisi',
            'complete_conflict' => 'Perlu Rekonsiliasi (Beda Usulan)',
            'complete_drop'     => 'Sepakat Usul Tidak Dirilis',
            'complete_agreed'   => 'Lengkap (Semua Sepakat)',
            default             => 'Menunggu Pengisian',
        };
    }

    /**
     * Badge class Bootstrap berdasarkan status.
     */
    public static function getStatusBadgeClass(array|string $status): string
    {
        $code = is_array($status) ? ($status['status_code'] ?? 'no_proposals') : $status;

        return match ($code) {
            'cancelled_t1'      => 'bg-danger-subtle text-danger border border-danger-subtle',
            'scheduled'         => 'bg-success text-white',
            'no_proposals'      => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'partial'           => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'complete_conflict' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'complete_drop'     => 'bg-danger-subtle text-danger border border-danger-subtle',
            'complete_agreed'   => 'bg-success-subtle text-success border border-success-subtle',
            default             => 'bg-light text-dark border',
        };
    }
}
