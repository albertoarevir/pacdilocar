<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleFunction extends Model
{
    protected $fillable = ['nombre'];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'funcion_id');
    }
}
