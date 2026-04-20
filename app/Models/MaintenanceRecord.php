<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehiculo_id', 'categoria_id', 'taller_id',
        'fecha_ingreso', 'fecha_salida', 'dias_paralizado', 'estado',
        'tipo_mantenimiento', 'descripcion_tecnica', 'costo_total',
        'kilometraje_ingreso', 'numero_orden', 'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso'    => 'date',
        'fecha_salida'     => 'date',
        'costo_total'      => 'decimal:2',
        'dias_paralizado'  => 'integer',
        'kilometraje_ingreso' => 'integer',
    ];

    // ─── Boot: cálculo automático de dias_paralizado ────────────────────────

    protected static function booted(): void
    {
        static::saving(function (self $registro) {
            if ($registro->fecha_ingreso && $registro->fecha_salida) {
                $registro->dias_paralizado = $registro->fecha_ingreso->diffInDays($registro->fecha_salida);
            }
        });
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehiculo_id');
    }

    public function categoriaMantenimiento(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class, 'categoria_id');
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Workshop::class, 'taller_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'Abierto');
    }

    public function scopeCerrados($query)
    {
        return $query->where('estado', 'Cerrado');
    }

    public function scopeEnDiagnostico($query)
    {
        return $query->where('estado', 'En Diagnóstico');
    }

    public function scopeCorrectivos($query)
    {
        return $query->where('tipo_mantenimiento', 'Correctivo');
    }

    public function scopePreventivos($query)
    {
        return $query->where('tipo_mantenimiento', 'Preventivo');
    }

    public function scopeEmergencias($query)
    {
        return $query->where('tipo_mantenimiento', 'Emergencia');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getEstaAbiertoAttribute(): bool
    {
        return in_array($this->estado, ['Abierto', 'En Diagnóstico']);
    }
}
