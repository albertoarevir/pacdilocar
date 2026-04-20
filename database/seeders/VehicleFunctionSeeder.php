<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleFunctionSeeder extends Seeder
{
    public function run(): void
    {
        $funciones = [
            'COMANDO',
            'POLICIAL',
            'APOYO TÁCTICO',
            'APOYO LOGÍSTICO',
        ];

        foreach ($funciones as $nombre) {
            DB::table('vehicle_functions')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
