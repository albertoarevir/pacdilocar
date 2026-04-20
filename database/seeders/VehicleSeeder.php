<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\OperationalSummaryService;
use Illuminate\Database\Seeder;

/**
 * Datos de ejemplo extraídos directamente del Excel Control_Flota_DeptoL.3.xlsx
 */
class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $tipoRP  = VehicleType::where('codigo', 'RP')->first();
        $tipoZ   = VehicleType::where('codigo', 'Z')->first();
        $tipoTPB = VehicleType::where('codigo', 'TPB')->first();
        $tipoC   = VehicleType::where('codigo', 'C')->first();
        $tipoCM  = VehicleType::where('codigo', 'CM')->first();

        $vehiculos = [
            [
                'patente'               => 'Z-1234',
                'tipo_vehiculo_id'      => $tipoC?->id,
                'marca'                 => 'Ford',
                'modelo'                => 'Transit',
                'anio'                  => 2021,
                'fecha_inicio_servicio' => '2021-05-15',
                'estado'                => 'OPERATIVO',
            ],
            [
                'patente'               => 'RP-9012',
                'tipo_vehiculo_id'      => $tipoTPB?->id,
                'marca'                 => 'Hyundai',
                'modelo'                => 'Accent',
                'anio'                  => 2020,
                'fecha_inicio_servicio' => '2020-08-01',
                'estado'                => 'OPERATIVO',
            ],
            [
                'patente'               => 'RP-0009',
                'tipo_vehiculo_id'      => $tipoRP?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2025-05-13',
                'estado'                => 'PANNE',
            ],
            [
                'patente'               => 'Z-0001',
                'tipo_vehiculo_id'      => $tipoZ?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2022-11-05',
                'estado'                => 'PANNE',
            ],
            [
                'patente'               => 'Z-0003',
                'tipo_vehiculo_id'      => $tipoZ?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2025-10-01',
                'estado'                => 'OPERATIVO',
            ],
            [
                'patente'               => 'RP-9000',
                'tipo_vehiculo_id'      => $tipoRP?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2025-05-13',
                'estado'                => 'FUERA_DE_SERVICIO',
            ],
            [
                'patente'               => 'RP-279',
                'tipo_vehiculo_id'      => $tipoRP?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2020-01-01',
                'estado'                => 'OPERATIVO',
            ],
            [
                'patente'               => 'RP-280',
                'tipo_vehiculo_id'      => $tipoRP?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2020-01-01',
                'estado'                => 'OPERATIVO',
            ],
            [
                'patente'               => 'CM-357',
                'tipo_vehiculo_id'      => $tipoCM?->id,
                'anio'                  => null,
                'fecha_inicio_servicio' => '2025-10-01',
                'estado'                => 'OPERATIVO',
            ],
        ];

        foreach ($vehiculos as $datos) {
            Vehicle::firstOrCreate(['patente' => $datos['patente']], $datos);
        }

        // Recalcula indicadores
        app(OperationalSummaryService::class)->refreshAll();
    }
}
