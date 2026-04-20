<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['codigo' => 'A',    'nombre' => 'AMBULANCIA'],
            ['codigo' => 'AV',   'nombre' => 'AVIÓN'],
            ['codigo' => 'BOT',  'nombre' => 'BALSAS Y BOTES'],
            ['codigo' => 'BC',   'nombre' => 'BICICLETA / CICLO / BICICLETA ELÉCTRICA'],
            ['codigo' => 'BL',   'nombre' => 'BUS / MINIBÚS TRASLADO PERSONAL'],
            ['codigo' => 'BT',   'nombre' => 'BUS / MINIBÚS TÁCTICO'],
            ['codigo' => 'AP',   'nombre' => 'CAMIONETA'],
            ['codigo' => 'C',    'nombre' => 'CAMIÓN'],
            ['codigo' => 'CR',   'nombre' => 'CARRO ARRASTRE'],
            ['codigo' => 'MCP',  'nombre' => 'CORTA PASTO'],
            ['codigo' => '4M',   'nombre' => 'CUADRIMOTO / TRIMOTO / A.T.V. / U.T.V'],
            ['codigo' => 'CM',   'nombre' => 'CUARTEL MÓVIL'],
            ['codigo' => 'Z',    'nombre' => 'FURGÓN, CAMIONETA CARROZADA O SUV POLICIAL'],
            ['codigo' => 'AG',   'nombre' => 'GRÚA'],
            ['codigo' => 'GH',   'nombre' => 'GRÚA HORQUILLA'],
            ['codigo' => 'H',    'nombre' => 'HELICÓPTERO'],
            ['codigo' => 'LM',   'nombre' => 'LABORATORIO MÓVIL'],
            ['codigo' => 'LP',   'nombre' => 'LANCHA'],
            ['codigo' => 'LA',   'nombre' => 'LANZA AGUA'],
            ['codigo' => 'MCF',  'nombre' => 'MINICARGADOR FRONTAL'],
            ['codigo' => 'MA',   'nombre' => 'MOTO DE AGUA'],
            ['codigo' => 'MC',   'nombre' => 'MOTOCICLETA POLICIAL COLOR FÁBRICA'],
            ['codigo' => 'MTT',  'nombre' => 'MOTOCICLETA TODO TERRENO'],
            ['codigo' => 'MTTO', 'nombre' => 'MOTOCICLETA TRÁNSITO'],
            ['codigo' => 'MOT',  'nombre' => 'MOTOR'],
            ['codigo' => 'K9',   'nombre' => 'PATRULLA CANINA'],
            ['codigo' => 'PC',   'nombre' => 'PATRULLA COMUNITARIA'],
            ['codigo' => 'RP',   'nombre' => 'RADIOPATRULLA'],
            ['codigo' => 'RE',   'nombre' => 'RETROEXCAVADORA'],
            ['codigo' => 'DR',   'nombre' => 'AERONAVE REMOTAMENTE PILOTADA'],
            ['codigo' => 'TPB',  'nombre' => 'TRANSPORTE PERSONAL BLINDADO'],
            ['codigo' => 'T',    'nombre' => 'TRACTOR'],
            ['codigo' => 'TI',   'nombre' => 'TRASLADO DE IMPUTADOS'],
            ['codigo' => 'AC',   'nombre' => 'VEHÍCULO COMANDO'],
            ['codigo' => 'VL',   'nombre' => 'VEHÍCULO LOGÍSTICO'],
            ['codigo' => 'VPC',  'nombre' => 'VEHÍCULO POLICIAL COLOR FÁBRICA'],
            ['codigo' => 'J',    'nombre' => 'VEHÍCULO TÁCTICO CONTROL ÓRDEN PÚBLICO'],
            ['codigo' => 'TK9',  'nombre' => 'VEHÍCULO TRASLADO CANES'],
        ];

        foreach ($tipos as $tipo) {
            VehicleType::firstOrCreate(['codigo' => $tipo['codigo']], $tipo);
        }
    }
}
