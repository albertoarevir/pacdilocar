<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalSummary extends Model
{
    protected $fillable = [
        'vehicle_id', 'total_service_days', 'total_workshop_days',
        'operational_days', 'availability_pct', 'downtime_pct',
        'workshop_entries', 'total_maintenance_cost', 'mttr_days',
        'last_computed_at',
    ];

    protected $casts = [
        'availability_pct'       => 'decimal:4',
        'downtime_pct'           => 'decimal:4',
        'total_maintenance_cost' => 'decimal:2',
        'mttr_days'              => 'decimal:2',
        'last_computed_at'       => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getAvailabilityPercentAttribute(): string
    {
        return number_format($this->availability_pct * 100, 2) . '%';
    }

    public function getDowntimePercentAttribute(): string
    {
        return number_format($this->downtime_pct * 100, 2) . '%';
    }
}
