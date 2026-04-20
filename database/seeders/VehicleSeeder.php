<?php

namespace Database\Seeders;

use App\Models\FundingOrigin;
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
        $rpType  = VehicleType::where('code', 'RP')->first();
        $zType   = VehicleType::where('code', 'Z')->first();
        $apType  = VehicleType::where('code', 'AP')->first();
        $tpbType = VehicleType::where('code', 'TPB')->first();
        $cType   = VehicleType::where('code', 'C')->first();
        $cmType  = VehicleType::where('code', 'CM')->first();

        $vehicles = [
            [
                'patente'          => 'Z-1234',
                'vehicle_type_id'  => $cType?->id,
                'brand'            => 'Ford',
                'model'            => 'Transit',
                'year'             => 2021,
                'service_start_date' => '2021-05-15',
                'status'           => 'OPERATIVO',
            ],
            [
                'patente'          => 'RP-9012',
                'vehicle_type_id'  => $tpbType?->id,
                'brand'            => 'Hyundai',
                'model'            => 'Accent',
                'year'             => 2020,
                'service_start_date' => '2020-08-01',
                'status'           => 'OPERATIVO',
            ],
            [
                'patente'          => 'RP-0009',
                'vehicle_type_id'  => $rpType?->id,
                'year'             => null,
                'service_start_date' => '2025-05-13',
                'status'           => 'PANNE',
            ],
            [
                'patente'          => 'Z-0001',
                'vehicle_type_id'  => $zType?->id,
                'year'             => null,
                'service_start_date' => '2022-11-05',
                'status'           => 'PANNE',
            ],
            [
                'patente'          => 'Z-0003',
                'vehicle_type_id'  => $zType?->id,
                'year'             => null,
                'service_start_date' => '2025-10-01',
                'status'           => 'OPERATIVO',
            ],
            [
                'patente'          => 'RP-9000',
                'vehicle_type_id'  => $rpType?->id,
                'year'             => null,
                'service_start_date' => '2025-05-13',
                'status'           => 'FUERA_DE_SERVICIO',
            ],
            [
                'patente'          => 'RP-279',
                'vehicle_type_id'  => $rpType?->id,
                'year'             => null,
                'service_start_date' => '2020-01-01',
                'status'           => 'OPERATIVO',
            ],
            [
                'patente'          => 'RP-280',
                'vehicle_type_id'  => $rpType?->id,
                'year'             => null,
                'service_start_date' => '2020-01-01',
                'status'           => 'OPERATIVO',
            ],
            [
                'patente'          => 'CM-357',
                'vehicle_type_id'  => $cmType?->id,
                'year'             => null,
                'service_start_date' => '2025-10-01',
                'status'           => 'OPERATIVO',
            ],
        ];

        foreach ($vehicles as $data) {
            Vehicle::firstOrCreate(['patente' => $data['patente']], $data);
        }

        // Recalcula indicadores
        app(OperationalSummaryService::class)->refreshAll();
    }
}
