<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationScheduleProposal extends Model
{
    use HasFactory;

    protected $attributes = [
        'will_release' => true,
    ];

    protected $fillable = [
        'publication_id',
        'release_planning_period_id',
        'user_id',
        'role',
        'will_release',
        'drop_reason',
        'proposed_kategori',
        'proposed_jadwal_rilis',
        'proposed_jadwal_upload',
        'proposed_mulai_pemeriksaan',
        'proposed_mulai_penyusunan',
        'notes',
    ];

    public function getProposedJadwalMulaiPemeriksaanAttribute()
    {
        return $this->proposed_mulai_pemeriksaan;
    }

    public function setProposedJadwalMulaiPemeriksaanAttribute($value)
    {
        $this->attributes['proposed_mulai_pemeriksaan'] = $value;
    }

    public function getProposedJadwalMulaiPenyusunanAttribute()
    {
        return $this->proposed_mulai_penyusunan;
    }

    public function setProposedJadwalMulaiPenyusunanAttribute($value)
    {
        $this->attributes['proposed_mulai_penyusunan'] = $value;
    }

    protected $casts = [
        'will_release'                => 'boolean',
        'proposed_jadwal_rilis'       => 'date',
        'proposed_jadwal_upload'      => 'date',
        'proposed_mulai_pemeriksaan'  => 'date',
        'proposed_mulai_penyusunan'   => 'date',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function period()
    {
        return $this->belongsTo(ReleasePlanningPeriod::class, 'release_planning_period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Peran yang berhak mengisi usulan jadwal rilis.
     * Ketua pemeriksa konten bisa mengisi DAN menetapkan jadwal final.
     */
    public const PROPOSER_ROLES = [
        'penyusun_naskah',
        'anggota_pemeriksa_konten',
        'ketua_pemeriksa_konten',
    ];

    /**
     * Label peran pengusul dalam Bahasa Indonesia.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'penyusun_naskah'          => 'Penyusun Naskah',
            'anggota_pemeriksa_konten' => 'Pemeriksa Konten',
            'ketua_pemeriksa_konten'   => 'Ketua Pemeriksa Konten',
            default                    => '-',
        };
    }
}
