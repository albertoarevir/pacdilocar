<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'name', 'address', 'phone', 'contact_person', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
