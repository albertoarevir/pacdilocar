<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingOrigin extends Model
{
    protected $fillable = ['nombre'];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'origen_financiamiento_id');
    }
}
