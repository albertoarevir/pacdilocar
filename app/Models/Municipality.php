<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    protected $fillable = ['province_id', 'zona_id', 'nombre'];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zona_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'municipio_id');
    }
}
