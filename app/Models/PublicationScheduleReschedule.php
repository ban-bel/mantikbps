<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationScheduleReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'action_type',
        'jadwal_rilis_semula',
        'jadwal_rilis_baru',
        'jadwal_upload_semula',
        'jadwal_upload_baru',
        'jadwal_mulai_pemeriksaan_semula',
        'jadwal_mulai_pemeriksaan_baru',
        'jadwal_mulai_penyusunan_semula',
        'jadwal_mulai_penyusunan_baru',
        'reason',
        'letter_number',
        'letter_file_path',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'jadwal_rilis_semula'              => 'date',
        'jadwal_rilis_baru'                => 'date',
        'jadwal_upload_semula'             => 'date',
        'jadwal_upload_baru'               => 'date',
        'jadwal_mulai_pemeriksaan_semula'  => 'date',
        'jadwal_mulai_pemeriksaan_baru'    => 'date',
        'jadwal_mulai_penyusunan_semula'   => 'date',
        'jadwal_mulai_penyusunan_baru'     => 'date',
        'approved_at'                      => 'datetime',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Label jenis tindakan perubahan jadwal dalam Bahasa Indonesia.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action_type) {
            'dimajukan'  => 'Dimajukan',
            'diundur'    => 'Diundur',
            'dibatalkan' => 'Dibatalkan',
            default      => '-',
        };
    }

    /**
     * Badge warna Bootstrap untuk tampilan di tabel/badge UI.
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action_type) {
            'dimajukan'  => 'bg-success',
            'diundur'    => 'bg-warning text-dark',
            'dibatalkan' => 'bg-danger',
            default      => 'bg-secondary',
        };
    }
}
