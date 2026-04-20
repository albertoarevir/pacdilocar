<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'A',    'name' => 'AMBULANCIA'],
            ['code' => 'AV',   'name' => 'AVIÓN'],
            ['code' => 'BOT',  'name' => 'BALSAS Y BOTES'],
            ['code' => 'BC',   'name' => 'BICICLETA / CICLO / BICICLETA ELÉCTRICA'],
            ['code' => 'BL',   'name' => 'BUS / MINIBÚS TRASLADO PERSONAL'],
            ['code' => 'BT',   'name' => 'BUS / MINIBÚS TÁCTICO'],
            ['code' => 'AP',   'name' => 'CAMIONETA'],
            ['code' => 'C',    'name' => 'CAMIÓN'],
            ['code' => 'CR',   'name' => 'CARRO ARRASTRE'],
            ['code' => 'MCP',  'name' => 'CORTA PASTO'],
            ['code' => '4M',   'name' => 'CUADRIMOTO / TRIMOTO / A.T.V. / U.T.V'],
            ['code' => 'CM',   'name' => 'CUARTEL MÓVIL'],
            ['code' => 'Z',    'name' => 'FURGÓN, CAMIONETA CARROZADA O SUV POLICIAL'],
            ['code' => 'AG',   'name' => 'GRÚA'],
            ['code' => 'GH',   'name' => 'GRÚA HORQUILLA'],
            ['code' => 'H',    'name' => 'HELICÓPTERO'],
            ['code' => 'LM',   'name' => 'LABORATORIO MÓVIL'],
            ['code' => 'LP',   'name' => 'LANCHA'],
            ['code' => 'LA',   'name' => 'LANZA AGUA'],
            ['code' => 'MCF',  'name' => 'MINICARGADOR FRONTAL'],
            ['code' => 'MA',   'name' => 'MOTO DE AGUA'],
            ['code' => 'MC',   'name' => 'MOTOCICLETA POLICIAL COLOR FÁBRICA'],
            ['code' => 'MTT',  'name' => 'MOTOCICLETA TODO TERRENO'],
            ['code' => 'MTTO', 'name' => 'MOTOCICLETA TRÁNSITO'],
            ['code' => 'MOT',  'name' => 'MOTOR'],
            ['code' => 'K9',   'name' => 'PATRULLA CANINA'],
            ['code' => 'PC',   'name' => 'PATRULLA COMUNITARIA'],
            ['code' => 'RP',   'name' => 'RADIOPATRULLA'],
            ['code' => 'RE',   'name' => 'RETROEXCAVADORA'],
            ['code' => 'DR',   'name' => 'AERONAVE REMOTAMENTE PILOTADA'],
            ['code' => 'TPB',  'name' => 'TRANSPORTE PERSONAL BLINDADO'],
            ['code' => 'T',    'name' => 'TRACTOR'],
            ['code' => 'TI',   'name' => 'TRASLADO DE IMPUTADOS'],
            ['code' => 'AC',   'name' => 'VEHÍCULO COMANDO'],
            ['code' => 'VL',   'name' => 'VEHÍCULO LOGÍSTICO'],
            ['code' => 'VPC',  'name' => 'VEHÍCULO POLICIAL COLOR FÁBRICA'],
            ['code' => 'J',    'name' => 'VEHÍCULO TÁCTICO CONTROL ÓRDEN PÚBLICO'],
            ['code' => 'TK9',  'name' => 'VEHÍCULO TRASLADO CANES'],
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(['code' => $type['code']], $type);
        }
    }
}
