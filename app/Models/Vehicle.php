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
        'patente', 'tipo_vehiculo_id', 'marca_id', 'modelo_id', 'color_id', 'anio',
        'fecha_inicio_servicio', 'funcion_id', 'tipo_combustible_id', 'numero_motor',
        'numero_chasis', 'origen_financiamiento_id', 'zona_id', 'province_id',
        'municipio_id', 'prefectura_id', 'unidad_id', 'es_agregado',
        'prefectura_agregado_id', 'unidad_agregado_id', 'estado_vehiculo_id', 'observaciones',
    ];

    protected $casts = [
        'fecha_inicio_servicio' => 'date',
        'es_agregado'           => 'boolean',
        'anio'                  => 'integer',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function tipoVehiculo(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'tipo_vehiculo_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'marca_id');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'modelo_id');
    }

    public function estadoVehiculo(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class, 'estado_vehiculo_id');
    }

    public function funcion(): BelongsTo
    {
        return $this->belongsTo(VehicleFunction::class, 'funcion_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function tipoCombustible(): BelongsTo
    {
        return $this->belongsTo(FuelType::class, 'tipo_combustible_id');
    }

    public function origenFinanciamiento(): BelongsTo
    {
        return $this->belongsTo(FundingOrigin::class, 'origen_financiamiento_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zona_id');
    }

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'municipio_id');
    }

    public function prefectura(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefectura_id');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unidad_id');
    }

    public function prefecturaAgregado(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefectura_agregado_id');
    }

    public function unidadAgregado(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unidad_agregado_id');
    }

    public function registrosMantenimiento(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'vehiculo_id');
    }

    public function registrosMantenimientoAbiertos(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'vehiculo_id')
            ->whereIn('estado', ['Abierto', 'En Diagnóstico']);
    }

    public function resumenOperativo(): HasOne
    {
        return $this->hasOne(OperationalSummary::class, 'vehiculo_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeOperativo($query)
    {
        return $query->whereHas('estadoVehiculo', fn($q) => $q->where('codigo', 'OPERATIVO'));
    }

    public function scopeEnPanne($query)
    {
        return $query->whereHas('estadoVehiculo', fn($q) => $q->where('codigo', 'PANNE'));
    }

    public function scopeActivos($query)
    {
        return $query->whereHas('estadoVehiculo', fn($q) => $q->whereNotIn('codigo', ['BAJA', 'ENAJENADO']));
    }

    public function scopePorZona($query, int $zonaId)
    {
        return $query->where('zona_id', $zonaId);
    }

    public function scopePorPrefectura($query, int $prefecturaId)
    {
        return $query->where('prefectura_id', $prefecturaId);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getEstaDisponibleAttribute(): bool
    {
        return $this->estadoVehiculo?->codigo === 'OPERATIVO';
    }

    public function getGeneraParalizadoAttribute(): bool
    {
        return (bool) $this->estadoVehiculo?->genera_paralizado;
    }

    public function getDiasServicioAttribute(): int
    {
        if (! $this->fecha_inicio_servicio) {
            return 0;
        }

        $codigoEstado = $this->estadoVehiculo?->codigo;
        $fin = in_array($codigoEstado, ['BAJA', 'ENAJENADO'])
            ? $this->updated_at->toDateString()
            : now()->toDateString();

        return $this->fecha_inicio_servicio->diffInDays($fin);
    }
}
