<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublicationWorkflowMailNotification extends Notification
{

    public function __construct(
        protected string $title,
        protected string $message,
        protected ?string $actionUrl = null,
        protected ?string $actionLabel = null,
        protected ?string $mailSubject = null,
        protected ?string $stage = null,
        protected ?string $role = null,
        protected ?string $nextAction = null
    ) {
    }

    public function via(object $notifiable): array
    {
        if (empty($notifiable->email) || !filter_var($notifiable->email, FILTER_VALIDATE_EMAIL)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->mailSubject ?: $this->title)
            ->greeting('Yth. ' . ($notifiable->name ?? 'Pengguna') . ',')
            ->line($this->message);

        if ($this->stage) {
            $mail->line('Tahap: ' . $this->stage);
        }

        if ($this->role) {
            $mail->line('Peran: ' . $this->role);
        }

        if ($this->nextAction) {
            $mail->line('Tindakan: ' . $this->nextAction);
        }

        if ($this->actionUrl) {
            $mail->action($this->actionLabel ?: 'Buka Sistem', $this->actionUrl);
        }

        return $mail->line('Pemberitahuan ini dikirim otomatis oleh Manajemen Publikasi Statistik.');
    }
}
