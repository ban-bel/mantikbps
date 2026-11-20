<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionGuideline extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'anatomy_section',
        'inspection_item',
        'requirement_detail',
        'sort_order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getRequirementStatusLabelAttribute(): string
    {
        return $this->is_required ? 'Wajib' : 'Tidak Wajib';
    }

    public function getRequirementMarkAttribute(): string
    {
        return $this->is_required ? '*' : '';
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'konten' ? 'Pemeriksaan Konten' : 'Pemeriksaan Layout';
    }
}
