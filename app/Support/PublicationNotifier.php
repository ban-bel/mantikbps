<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\PublicationTeam;
use App\Models\PublicationTeamAssignment;
use App\Models\User;
use App\Notifications\PublicationWorkflowMailNotification;
use App\Notifications\PublicationWorkflowNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PublicationNotifier
{
    public static function notifyEmployeeAssignments(PublicationTeam $team, Collection $assignments): void
    {
        if ($assignments->isEmpty()) {
            return;
        }

        $team->loadMissing('publication');
        $publication = $team->publication;

        if (!$publication) {
            return;
        }

        foreach ($assignments as $assignment) {
            if (!$assignment instanceof PublicationTeamAssignment) {
                continue;
            }

            $assignment->loadMissing('user');
            $user = $assignment->user;

            if (!$user || $user->role !== 'pegawai' || !$user->is_active) {
                continue;
            }

            $roleLabel = $assignment->assignment_role_label;
            $message = 'Anda ditugaskan sebagai ' . $roleLabel . ' pada publikasi "' . $publication->nama_publikasi . '".';
            $actionUrl = route('employee.tasks.show', [
                'publicationTeam' => $team,
                'role' => $assignment->assignment_role,
            ]);

            self::sendToUser(
                user: $user,
                eventKey: 'assignment:' . $assignment->id . ':' . $assignment->updated_at?->timestamp,
                title: 'Penugasan Publikasi Baru',
                message: $message,
                actionUrl: $actionUrl,
                actionLabel: 'Buka Tugas',
                mailSubject: '[MANTIK BPS] Penugasan Publikasi Baru',
                icon: 'bi-person-check',
                stage: $publication->status_label,
                role: $roleLabel,
                nextAction: 'Membuka publikasi dan mengerjakan tugas sesuai peran yang diberikan.',
                metadata: [
                    'publication_id' => $publication->id,
                    'stage' => $publication->status,
                    'role' => $assignment->assignment_role,
                ]
            );
        }
    }

    /**
     * Mengirim notifikasi internal dan email kepada seluruh penanggung jawab tahap publikasi saat ini.
     * Sumber penerima dan tindakan sama dengan reminder pada Log History.
     */
    public static function notifyCurrentStage(Publication $publication): void
    {
        $publication = $publication->fresh(['team.assignments.user']) ?? $publication;
        $responsibility = PublicationStageResponsibility::resolve($publication);
        $recipients = $responsibility['recipients'];

        if (!$recipients instanceof Collection || $recipients->isEmpty()) {
            return;
        }

        $historyId = $publication->teamAssignmentHistories()->latest('id')->value('id') ?: 'none';
        $returnStage = $publication->revision_return_stage ?: 'none';
        $baseEventKey = implode(':', [
            'workflow',
            $publication->id,
            'history',
            $historyId,
            'status',
            $publication->status,
            'return',
            $returnStage,
        ]);

        foreach ($recipients as $recipient) {
            $user = data_get($recipient, 'user');

            if (!$user instanceof User || !$user->is_active) {
                continue;
            }

            $recipientRole = (string) data_get($recipient, 'role', $responsibility['role']);
            $message = self::stageMessage($publication, $responsibility, $recipientRole);

            self::sendToUser(
                user: $user,
                eventKey: $baseEventKey,
                title: $responsibility['title'],
                message: $message,
                actionUrl: data_get($recipient, 'action_url'),
                actionLabel: data_get($recipient, 'action_label', 'Buka Tugas'),
                mailSubject: '[MANTIK BPS] ' . $responsibility['title'],
                icon: $responsibility['icon'],
                stage: $responsibility['stage'],
                role: $recipientRole,
                nextAction: $responsibility['action'],
                metadata: [
                    'publication_id' => $publication->id,
                    'stage' => $publication->status,
                    'stage_label' => $responsibility['stage'],
                    'role' => $recipientRole,
                    'next_action' => $responsibility['action'],
                    'revision_return_stage' => $publication->revision_return_stage,
                ]
            );
        }
    }

    /**
     * Mengirim notifikasi internal ketika tugas suatu peran telah selesai.
     * Notifikasi ini tidak mengirim email karena hanya menjadi penanda status tugas di dalam sistem.
     */
    public static function notifyRoleCompleted(Publication $publication, string $completedStage): void
    {
        $publication = $publication->fresh(['team.assignments.user']) ?? $publication;
        $team = $publication->team;
        $assignments = $team?->assignments ?? collect();

        $definition = match ($completedStage) {
            'penyusunan' => [
                'title' => 'Tugas Tim Penyusun Selesai',
                'roles' => ['penyusun_naskah'],
                'role_label' => 'Tim Penyusun',
                'icon' => 'bi-file-earmark-check',
            ],
            'pemeriksaan_konten' => [
                'title' => 'Tugas Pemeriksaan Konten Selesai',
                'roles' => ['ketua_pemeriksa_konten', 'anggota_pemeriksa_konten'],
                'role_label' => 'Pemeriksa Konten',
                'icon' => 'bi-clipboard-check',
            ],
            'pemeriksaan_layout' => [
                'title' => 'Tugas Pemeriksaan Layout Selesai',
                'roles' => ['ketua_pemeriksa_layout', 'anggota_pemeriksa_layout'],
                'role_label' => 'Pemeriksa Layout',
                'icon' => 'bi-layout-text-window-reverse',
            ],
            'pemeriksaan_infografis' => [
                'title' => 'Tugas Pemeriksaan Infografis Selesai',
                'roles' => ['operator_infografis'],
                'role_label' => 'Operator Infografis',
                'icon' => 'bi-images',
            ],
            'persetujuan_pimpinan' => [
                'title' => 'Tugas Persetujuan Pimpinan Selesai',
                'roles' => [],
                'role_label' => 'Pimpinan',
                'icon' => 'bi-patch-check',
            ],
            'operator_website' => [
                'title' => 'Tugas Operator Website Selesai',
                'roles' => ['operator_website'],
                'role_label' => 'Operator Website',
                'icon' => 'bi-globe2',
            ],
            default => null,
        };

        if (!$definition) {
            return;
        }

        if ($completedStage === 'persetujuan_pimpinan') {
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
                    'action_label' => 'Lihat Publikasi',
                ]);
        } else {
            if (!$team) {
                return;
            }

            $recipients = $assignments
                ->whereIn('assignment_role', $definition['roles'])
                ->filter(fn ($assignment) => $assignment->user && $assignment->user->is_active)
                ->map(fn ($assignment) => [
                    'user' => $assignment->user,
                    'role' => $assignment->assignment_role_label,
                    'action_url' => route('employee.tasks.show', [
                        'publicationTeam' => $team,
                        'role' => $assignment->assignment_role,
                    ]),
                    'action_label' => 'Lihat Tugas',
                ]);
        }

        $recipients = $recipients
            ->filter(fn ($recipient) => data_get($recipient, 'user') instanceof User)
            ->unique(fn ($recipient) => data_get($recipient, 'user.id'))
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $historyId = $publication->teamAssignmentHistories()->latest('id')->value('id') ?: 'none';
        $baseEventKey = implode(':', [
            'completed',
            $publication->id,
            $completedStage,
            'history',
            $historyId,
        ]);

        foreach ($recipients as $recipient) {
            $user = data_get($recipient, 'user');
            $recipientRole = (string) data_get($recipient, 'role', $definition['role_label']);
            $nextStage = $publication->status_label;
            $message = 'Tugas Anda sebagai ' . $recipientRole . ' pada publikasi "'
                . $publication->nama_publikasi . '" telah selesai. Publikasi sekarang berada pada tahap '
                . $nextStage . '.';

            self::sendInternalToUser(
                user: $user,
                eventKey: $baseEventKey,
                title: $definition['title'],
                message: $message,
                actionUrl: data_get($recipient, 'action_url'),
                actionLabel: data_get($recipient, 'action_label', 'Lihat Tugas'),
                icon: $definition['icon'],
                metadata: [
                    'publication_id' => $publication->id,
                    'stage' => $publication->status,
                    'completed_stage' => $completedStage,
                    'role' => $recipientRole,
                ]
            );
        }
    }

    /**
     * Dipertahankan untuk kompatibilitas pemanggilan lama.
     */
    public static function notifyLeadersForApproval(Publication $publication): void
    {
        self::notifyCurrentStage($publication);
    }

    /**
     * Dipertahankan untuk kompatibilitas pemanggilan lama.
     */
    public static function notifyTenantAdminsPublicationReady(Publication $publication): void
    {
        self::notifyCurrentStage($publication);
    }

    /**
     * Dipertahankan untuk kompatibilitas pemanggilan lama.
     */
    public static function notifyPreparersForRevision(Publication $publication, string $stage): void
    {
        self::notifyCurrentStage($publication);
    }

    protected static function stageMessage(Publication $publication, array $responsibility, string $recipientRole): string
    {
        $publicationName = '"' . $publication->nama_publikasi . '"';

        if ($publication->status === 'penyusunan' && $publication->revision_return_stage) {
            return 'Publikasi ' . $publicationName . ' dikembalikan oleh '
                . $publication->revision_source_label
                . ' dan memerlukan perbaikan Anda sebagai ' . $recipientRole . '.';
        }

        if (in_array($publication->status, ['siap_rilis', 'rilis_selesai'], true)) {
            return 'Publikasi ' . $publicationName . ' telah selesai difinalisasi dan siap rilis.';
        }

        return 'Publikasi ' . $publicationName . ' telah sampai pada tahap '
            . $responsibility['stage'] . ' dan memerlukan tindakan Anda sebagai ' . $recipientRole . '.';
    }

    protected static function sendToUser(
        User $user,
        string $eventKey,
        string $title,
        string $message,
        ?string $actionUrl,
        ?string $actionLabel,
        ?string $mailSubject,
        string $icon,
        ?string $stage,
        ?string $role,
        ?string $nextAction,
        array $metadata = []
    ): void {
        $recipientEventKey = hash('sha256', $eventKey . ':user:' . $user->id);

        if (self::alreadyNotified($user, $recipientEventKey)) {
            return;
        }

        try {
            // Notifikasi internal disimpan langsung agar segera muncul pada ikon lonceng.
            $user->notify(new PublicationWorkflowNotification(
                title: $title,
                message: $message,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                icon: $icon,
                eventKey: $recipientEventKey,
                metadata: $metadata
            ));
        } catch (\Throwable $exception) {
            Log::warning('Notifikasi internal workflow publikasi gagal disimpan.', [
                'user_id' => $user->id,
                'event_key' => $recipientEventKey,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            // Email dikirim langsung agar notifikasi sampai tanpa queue worker. Kegagalan SMTP tetap ditangani agar proses publikasi tidak batal.
            $user->notify(new PublicationWorkflowMailNotification(
                title: $title,
                message: $message,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                mailSubject: $mailSubject,
                stage: $stage,
                role: $role,
                nextAction: $nextAction
            ));
        } catch (\Throwable $exception) {
            Log::warning('Email workflow publikasi gagal dikirim.', [
                'user_id' => $user->id,
                'event_key' => $recipientEventKey,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    protected static function sendInternalToUser(
        User $user,
        string $eventKey,
        string $title,
        string $message,
        ?string $actionUrl,
        ?string $actionLabel,
        string $icon,
        array $metadata = []
    ): void {
        $recipientEventKey = hash('sha256', $eventKey . ':user:' . $user->id);

        if (self::alreadyNotified($user, $recipientEventKey)) {
            return;
        }

        try {
            $user->notify(new PublicationWorkflowNotification(
                title: $title,
                message: $message,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                icon: $icon,
                eventKey: $recipientEventKey,
                metadata: $metadata
            ));
        } catch (\Throwable $exception) {
            Log::warning('Notifikasi internal penyelesaian tugas gagal disimpan.', [
                'user_id' => $user->id,
                'event_key' => $recipientEventKey,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    protected static function alreadyNotified(User $user, string $eventKey): bool
    {
        return $user->notifications()
            ->latest('created_at')
            ->limit(200)
            ->get()
            ->contains(fn ($notification) => data_get($notification->data, 'event_key') === $eventKey);
    }
}
