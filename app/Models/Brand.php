<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['nombre'];

    public function modelos(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'marca_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'marca_id');
    }
}
