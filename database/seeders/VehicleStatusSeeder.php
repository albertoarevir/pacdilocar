<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleStatusSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['codigo' => 'OPERATIVO',        'nombre' => 'Operativo',          'descripcion' => 'Vehículo en servicio normal',                  'genera_paralizado' => false, 'orden' => 1],
            ['codigo' => 'PANNE',            'nombre' => 'En Panne',           'descripcion' => 'Vehículo averiado, genera tiempo de baja',      'genera_paralizado' => true,  'orden' => 2],
            ['codigo' => 'MANTENIMIENTO',    'nombre' => 'En Mantenimiento',   'descripcion' => 'Vehículo en taller por mantenimiento preventivo','genera_paralizado' => false, 'orden' => 3],
            ['codigo' => 'BAJA',             'nombre' => 'De Baja',            'descripcion' => 'Vehículo dado de baja definitivamente',         'genera_paralizado' => false, 'orden' => 4],
            ['codigo' => 'FUERA_DE_SERVICIO','nombre' => 'Fuera de Servicio',  'descripcion' => 'Vehículo temporalmente fuera de servicio',      'genera_paralizado' => false, 'orden' => 5],
            ['codigo' => 'ENAJENADO',        'nombre' => 'Enajenado',          'descripcion' => 'Vehículo transferido o vendido',                'genera_paralizado' => false, 'orden' => 6],
        ];

        foreach ($estados as $estado) {
            DB::table('vehicle_statuses')->updateOrInsert(
                ['codigo' => $estado['codigo']],
                array_merge($estado, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
