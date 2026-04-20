<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prefecture extends Model
{
    use SoftDeletes;

    protected $fillable = ['zone_id', 'name'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function aggregatedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'aggregate_prefecture_id');
    }
}
