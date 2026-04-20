<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleFunctionSeeder extends Seeder
{
    public function run(): void
    {
        $functions = [
            'COMANDO',
            'POLICIAL',
            'APOYO TÁCTICO',
            'APOYO LOGÍSTICO',
        ];

        foreach ($functions as $name) {
            DB::table('vehicle_functions')->updateOrInsert(
                ['name' => $name],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
