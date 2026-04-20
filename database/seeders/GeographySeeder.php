<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class GeographySeeder extends Seeder
{
    public function run(): void
    {
        // Zonas (Jefaturas de Zona — estructura interna)
        $zones = [
            'ARICA Y PARINACOTA', 'TARAPACÁ', 'ANTOFAGASTA', 'ATACAMA',
            'COQUIMBO', 'VALPARAÍSO', 'METROPOLITANA', 'LIBERTADOR GRAL. B. O\'HIGGINS',
            'MAULE', 'ÑUBLE', 'BIOBÍO', 'ARAUCANÍA', 'LOS RÍOS', 'LOS LAGOS',
            'AYSÉN', 'MAGALLANES',
        ];
        foreach ($zones as $z) {
            Zone::firstOrCreate(['name' => $z]);
        }

        // Regiones de Chile (15 regiones + Metropolitana)
        $regions = [
            ['number' => 1,  'name' => 'ARICA Y PARINACOTA'],
            ['number' => 2,  'name' => 'TARAPACÁ'],
            ['number' => 3,  'name' => 'ANTOFAGASTA'],
            ['number' => 4,  'name' => 'ATACAMA'],
            ['number' => 5,  'name' => 'COQUIMBO'],
            ['number' => 6,  'name' => 'VALPARAÍSO'],
            ['number' => 7,  'name' => 'METROPOLITANA DE SANTIAGO'],
            ['number' => 8,  'name' => 'LIB GRAL BDO. O\'HIGGINS'],
            ['number' => 9,  'name' => 'MAULE'],
            ['number' => 10, 'name' => 'ÑUBLE'],
            ['number' => 11, 'name' => 'BIOBÍO'],
            ['number' => 12, 'name' => 'ARAUCANÍA'],
            ['number' => 13, 'name' => 'LOS RÍOS'],
            ['number' => 14, 'name' => 'LOS LAGOS'],
            ['number' => 15, 'name' => 'AYSÉN'],
            ['number' => 16, 'name' => 'MAGALLANES Y ANTÁRTICA CHILENA'],
        ];
        foreach ($regions as $r) {
            Region::firstOrCreate(['number' => $r['number']], $r);
        }
    }
}
