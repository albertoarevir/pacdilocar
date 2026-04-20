<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    protected $fillable = ['codigo', 'nombre'];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'tipo_vehiculo_id');
    }
}
