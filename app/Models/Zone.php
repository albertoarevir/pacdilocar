<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name'];

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    public function prefectures(): HasMany
    {
        return $this->hasMany(Prefecture::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
