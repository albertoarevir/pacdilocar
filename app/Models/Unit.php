<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = ['prefectura_id', 'unidad_padre_id', 'tipo', 'nombre'];

    public function prefectura(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefectura_id');
    }

    public function unidadPadre(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unidad_padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Unit::class, 'unidad_padre_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'unidad_id');
    }

    public function vehiculosAgregados(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'unidad_agregado_id');
    }

    public function scopeUnidades($query)
    {
        return $query->where('tipo', 'unidad');
    }

    public function scopeDestacamentos($query)
    {
        return $query->where('tipo', 'destacamento');
    }
}
