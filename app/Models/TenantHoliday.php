<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'holiday_date',
        'description',
        'year',
        'created_by',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
