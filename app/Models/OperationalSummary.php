<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalSummary extends Model
{
    protected $fillable = [
        'vehiculo_id', 'dias_servicio_total', 'dias_taller_total',
        'dias_operativos', 'pct_disponibilidad', 'pct_paralizado',
        'ingresos_taller', 'costo_mantenimiento_total', 'dias_mttr',
        'ultima_actualizacion',
    ];

    protected $casts = [
        'pct_disponibilidad'        => 'decimal:4',
        'pct_paralizado'            => 'decimal:4',
        'costo_mantenimiento_total' => 'decimal:2',
        'dias_mttr'                 => 'decimal:2',
        'ultima_actualizacion'      => 'datetime',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function getPorcentajeDisponibilidadAttribute(): string
    {
        return number_format($this->pct_disponibilidad * 100, 2) . '%';
    }

    public function getPorcentajeParalizadoAttribute(): string
    {
        return number_format($this->pct_paralizado * 100, 2) . '%';
    }
}
