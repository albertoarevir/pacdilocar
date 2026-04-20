<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['region_id', 'nombre'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipality::class, 'province_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'province_id');
    }
}
