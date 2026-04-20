<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = ['prefecture_id', 'parent_id', 'type', 'name'];

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    /** Unidad padre (para destacamentos) */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function aggregatedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'aggregate_unit_id');
    }

    public function scopeUnidades($query)
    {
        return $query->where('type', 'unidad');
    }

    public function scopeDestacamentos($query)
    {
        return $query->where('type', 'destacamento');
    }
}
