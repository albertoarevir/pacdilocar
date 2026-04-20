<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['nombre'];

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipality::class, 'zona_id');
    }

    public function prefecturas(): HasMany
    {
        return $this->hasMany(Prefecture::class, 'zona_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'zona_id');
    }
}
