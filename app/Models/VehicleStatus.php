<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleStatus extends Model
{
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'genera_paralizado', 'orden'];

    protected $casts = [
        'genera_paralizado' => 'boolean',
    ];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'estado_vehiculo_id');
    }
}
