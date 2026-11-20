<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Collection;

class PublicationStageResponsibility
{
    /**
     * Menentukan tahap, petugas, peran, tindakan, dan tautan tugas dari satu sumber logika.
     * Hasil ini digunakan oleh reminder Log History dan notifikasi workflow.
     */
    public static function resolve(Publication $publication): array
    {
        $publication->loadMissing('team.assignments.user');

        $team = $publication->team;
        $assignments = $team?->assignments ?? collect();
        $status = (string) $publication->status;
        $stage = $publication->status_label;
        $role = '-';
        $action = '-';
        $title = 'Tugas Publikasi Tersedia';
        $icon = 'bi-bell';
        $recipients = collect();

        switch ($status) {
            case 'penyusunan':
                $role = 'Tim Penyusun';
                $icon = $publication->revision_return_stage ? 'bi-arrow-counterclockwise' : 'bi-file-earmark-arrow-up';

                if ($publication->revision_return_stage) {
                    $stage = 'Perbaikan Publikasi';
                    $title = 'Revisi Publikasi Perlu Dikerjakan';
                    $action = 'Memperbaiki dokumen sesuai catatan ' . $publication->revision_source_label
                        . ' dan mengirim ulang ke ' . $publication->revision_destination_label . '.';
                } else {
                    $title = 'Tugas Penyusunan Publikasi Tersedia';
                    $action = 'Melengkapi dokumen penyusunan dan mengirim naskah ke Pemeriksaan Konten.';
                }

                $recipients = self::assignmentRecipients(
                    $assignments,
                    ['penyusun_naskah'],
                    $team,
                    'Buka Tugas Penyusunan'
                );
                break;

            case 'pemeriksaan_konten':
                $role = 'Pemeriksa Konten';
                $action = 'Memeriksa konten naskah dan menyimpan keputusan pemeriksaan.';
                $title = 'Tugas Pemeriksaan Konten Tersedia';
                $icon = 'bi-clipboard-check';
                $recipients = self::assignmentRecipients(
                    $assignments,
                    ['ketua_pemeriksa_konten', 'anggota_pemeriksa_konten'],
                    $team,
                    'Buka Pemeriksaan Konten'
                );
                break;

            case 'pemeriksaan_layout':
                $role = 'Pemeriksa Layout';
                $action = 'Memeriksa layout naskah dan menyimpan keputusan pemeriksaan.';
                $title = 'Tugas Pemeriksaan Layout Tersedia';
                $icon = 'bi-layout-text-window-reverse';
                $recipients = self::assignmentRecipients(
                    $assignments,
                    ['ketua_pemeriksa_layout', 'anggota_pemeriksa_layout'],
                    $team,
                    'Buka Pemeriksaan Layout'
                );
                break;

            case 'pemeriksaan_infografis':
                $role = 'Operator Infografis';
                $action = 'Memeriksa paket infografis serta daftar tabel dan gambar.';
                $title = 'Tugas Pemeriksaan Infografis Tersedia';
                $icon = 'bi-images';
                $recipients = self::assignmentRecipients(
                    $assignments,
                    ['operator_infografis'],
                    $team,
                    'Buka Pemeriksaan Infografis'
                );
                break;

            case 'persetujuan_pimpinan':
                $role = 'Pimpinan';
                $action = 'Meninjau hasil pemeriksaan dan memberikan keputusan persetujuan.';
                $title = 'Publikasi Menunggu Persetujuan';
                $icon = 'bi-patch-check';
                $recipients = User::query()
                    ->where('tenant_id', $publication->tenant_id)
                    ->where('role', 'pimpinan')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->map(fn (User $user) => [
                        'user' => $user,
                        'role' => 'Pimpinan',
                        'action_url' => route('leader.approvals.show', $publication),
                        'action_label' => 'Buka Persetujuan',
                    ]);
                break;

            case 'operator_website':
                $role = 'Operator Website';
                $action = 'Melengkapi Surat Persetujuan Rilis dan menyiapkan publikasi siap rilis.';
                $title = 'Tugas Finalisasi Publikasi Tersedia';
                $icon = 'bi-globe2';
                $recipients = self::assignmentRecipients(
                    $assignments,
                    ['operator_website'],
                    $team,
                    'Buka Finalisasi Publikasi'
                );
                break;

            case 'siap_rilis':
            case 'rilis_selesai':
                $role = 'Tidak ada';
                $action = 'Tidak ada tindakan lanjutan yang sedang menunggu.';
                $title = 'Publikasi Siap Rilis';
                $icon = 'bi-box-seam';
                $recipients = User::query()
                    ->where('tenant_id', $publication->tenant_id)
                    ->whereIn('role', ['admin_provinsi', 'admin_kabkota'])
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->map(fn (User $user) => [
                        'user' => $user,
                        'role' => $user->role === 'admin_provinsi' ? 'Admin Provinsi' : 'Admin Kabupaten/Kota',
                        'action_url' => route('tenant.ready-release.index', ['tahun' => $publication->tahun]),
                        'action_label' => 'Lihat Publikasi Siap Rilis',
                    ]);
                break;

            default:
                $action = 'Menindaklanjuti publikasi sesuai status saat ini.';
                break;
        }

        $recipients = $recipients
            ->filter(fn ($recipient) => data_get($recipient, 'user') instanceof User)
            ->unique(fn ($recipient) => data_get($recipient, 'user.id'))
            ->values();

        $names = $recipients
            ->map(fn ($recipient) => data_get($recipient, 'user.name'))
            ->filter()
            ->unique()
            ->values();

        return [
            'stage' => $stage,
            'role' => $role,
            'names' => in_array($status, ['siap_rilis', 'rilis_selesai'], true)
                ? 'Tidak ada'
                : ($names->isNotEmpty() ? $names->implode(', ') : 'Belum ditentukan'),
            'action' => $action,
            'title' => $title,
            'icon' => $icon,
            'recipients' => $recipients,
            'updated_at' => $publication->updated_at,
        ];
    }

    protected static function assignmentRecipients(
        Collection $assignments,
        array $roles,
        $team,
        string $actionLabel
    ): Collection {
        if (!$team) {
            return collect();
        }

        return $assignments
            ->whereIn('assignment_role', $roles)
            ->filter(fn ($assignment) => $assignment->user && $assignment->user->is_active)
            ->map(fn ($assignment) => [
                'user' => $assignment->user,
                'role' => $assignment->assignment_role_label,
                'action_url' => route('employee.tasks.show', [
                    'publicationTeam' => $team,
                    'role' => $assignment->assignment_role,
                ]),
                'action_label' => $actionLabel,
            ])
            ->values();
    }
}
