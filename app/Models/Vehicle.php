<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patente', 'vehicle_type_id', 'brand_id', 'vehicle_model_id', 'color_id', 'year',
        'service_start_date', 'vehicle_function_id', 'fuel_type_id', 'engine_number',
        'chassis_number', 'funding_origin_id', 'zone_id', 'province_id',
        'municipality_id', 'prefecture_id', 'unit_id', 'is_aggregated',
        'aggregate_prefecture_id', 'aggregate_unit_id', 'vehicle_status_id', 'observations',
    ];

    protected $casts = [
        'service_start_date' => 'date',
        'is_aggregated'      => 'boolean',
        'year'               => 'integer',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function vehicleStatus(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class);
    }

    public function vehicleFunction(): BelongsTo
    {
        return $this->belongsTo(VehicleFunction::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function fundingOrigin(): BelongsTo
    {
        return $this->belongsTo(FundingOrigin::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function aggregatePrefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'aggregate_prefecture_id');
    }

    public function aggregateUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'aggregate_unit_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function openMaintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class)
            ->whereIn('record_status', ['Abierto', 'En Diagnóstico']);
    }

    public function operationalSummary(): HasOne
    {
        return $this->hasOne(OperationalSummary::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeOperativo($query)
    {
        return $query->whereHas('vehicleStatus', fn($q) => $q->where('code', 'OPERATIVO'));
    }

    public function scopeEnPanne($query)
    {
        return $query->whereHas('vehicleStatus', fn($q) => $q->where('code', 'PANNE'));
    }

    public function scopeActivos($query)
    {
        return $query->whereHas('vehicleStatus', fn($q) => $q->whereNotIn('code', ['BAJA', 'ENAJENADO']));
    }

    public function scopeByZone($query, int $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeByPrefecture($query, int $prefectureId)
    {
        return $query->where('prefecture_id', $prefectureId);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getIsAvailableAttribute(): bool
    {
        return $this->vehicleStatus?->code === 'OPERATIVO';
    }

    public function getIsGeneratingDowntimeAttribute(): bool
    {
        return (bool) $this->vehicleStatus?->generates_downtime;
    }

    public function getServiceDaysAttribute(): int
    {
        if (! $this->service_start_date) {
            return 0;
        }

        $statusCode = $this->vehicleStatus?->code;
        $end = in_array($statusCode, ['BAJA', 'ENAJENADO'])
            ? $this->updated_at->toDateString()
            : now()->toDateString();

        return $this->service_start_date->diffInDays($end);
    }
}
