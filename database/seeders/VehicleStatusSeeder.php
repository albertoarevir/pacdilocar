<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'OPERATIVO',        'name' => 'Operativo',          'description' => 'Vehículo en servicio normal',                  'generates_downtime' => false, 'sort_order' => 1],
            ['code' => 'PANNE',            'name' => 'En Panne',           'description' => 'Vehículo averiado, genera tiempo de baja',      'generates_downtime' => true,  'sort_order' => 2],
            ['code' => 'MANTENIMIENTO',    'name' => 'En Mantenimiento',   'description' => 'Vehículo en taller por mantenimiento preventivo','generates_downtime' => false, 'sort_order' => 3],
            ['code' => 'BAJA',             'name' => 'De Baja',            'description' => 'Vehículo dado de baja definitivamente',         'generates_downtime' => false, 'sort_order' => 4],
            ['code' => 'FUERA_DE_SERVICIO','name' => 'Fuera de Servicio',  'description' => 'Vehículo temporalmente fuera de servicio',      'generates_downtime' => false, 'sort_order' => 5],
            ['code' => 'ENAJENADO',        'name' => 'Enajenado',          'description' => 'Vehículo transferido o vendido',                'generates_downtime' => false, 'sort_order' => 6],
        ];

        foreach ($statuses as $status) {
            DB::table('vehicle_statuses')->updateOrInsert(
                ['code' => $status['code']],
                array_merge($status, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
