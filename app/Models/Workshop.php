<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tipo', 'nombre', 'direccion', 'telefono', 'persona_contacto', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function registrosMantenimiento(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'taller_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
