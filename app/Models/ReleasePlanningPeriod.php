<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleasePlanningPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'target_year',
        'is_open',
        'opened_at',
        'closed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_open'   => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function proposals()
    {
        return $this->hasMany(PublicationScheduleProposal::class);
    }

    public function scheduleProposals()
    {
        return $this->hasMany(PublicationScheduleProposal::class);
    }

    /**
     * Apakah periode ini sedang terbuka untuk pengisian usulan.
     */
    public function isAcceptingProposals(): bool
    {
        return $this->is_open;
    }
}
