<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'maintenance_category_id', 'workshop_id',
        'entry_date', 'exit_date', 'downtime_days', 'record_status',
        'maintenance_type', 'technical_description', 'total_cost',
        'mileage_entry', 'work_order_number', 'observations',
    ];

    protected $casts = [
        'entry_date'   => 'date',
        'exit_date'    => 'date',
        'total_cost'   => 'decimal:2',
        'downtime_days' => 'integer',
        'mileage_entry' => 'integer',
    ];

    // ─── Boot: cálculo automático de downtime_days ────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $record) {
            if ($record->entry_date && $record->exit_date) {
                $record->downtime_days = $record->entry_date->diffInDays($record->exit_date);
            }
        });
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceCategory(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeAbiertos($query)
    {
        return $query->where('record_status', 'Abierto');
    }

    public function scopeCerrados($query)
    {
        return $query->where('record_status', 'Cerrado');
    }

    public function scopeEnDiagnostico($query)
    {
        return $query->where('record_status', 'En Diagnóstico');
    }

    public function scopeCorrectivos($query)
    {
        return $query->where('maintenance_type', 'Correctivo');
    }

    public function scopePreventivos($query)
    {
        return $query->where('maintenance_type', 'Preventivo');
    }

    public function scopeEmergencias($query)
    {
        return $query->where('maintenance_type', 'Emergencia');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getIsOpenAttribute(): bool
    {
        return in_array($this->record_status, ['Abierto', 'En Diagnóstico']);
    }
}
