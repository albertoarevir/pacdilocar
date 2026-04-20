<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prefecture extends Model
{
    use SoftDeletes;

    protected $fillable = ['zona_id', 'nombre'];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zona_id');
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(Unit::class, 'prefectura_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'prefectura_id');
    }

    public function vehiculosAgregados(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'prefectura_agregado_id');
    }
}
