<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleModel extends Model
{
    protected $fillable = ['marca_id', 'nombre'];

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'marca_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'modelo_id');
    }
}
