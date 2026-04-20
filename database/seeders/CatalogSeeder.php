<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\FuelType;
use App\Models\FundingOrigin;
use App\Models\MaintenanceCategory;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de combustible
        $fuels = ['GASOLINA', 'DIESEL', 'GAS', 'ELÉCTRICO', 'HÍBRIDO', 'GNV'];
        foreach ($fuels as $f) {
            FuelType::firstOrCreate(['name' => $f]);
        }

        // Colores (extraídos del Excel hoja 9.Color)
        $colors = [
            'ALUMINIO TITANIO', 'AMARILLO', 'AMARILLO OCRE', 'AZUL', 'BEIGE',
            'BLANCO', 'CAFE', 'CELESTE', 'GRIS', 'GRIS OSCURO', 'METALIZADO',
            'NARANJO', 'NEGRO', 'PLATEADO', 'ROJO', 'VERDE', 'VERDE MILITAR',
            'VERDE OSCURO', 'VINO', 'BLANCO PERLA',
        ];
        foreach ($colors as $c) {
            Color::firstOrCreate(['name' => $c]);
        }

        // Orígenes de financiamiento (extraídos del Excel hoja 8.Origen)
        $origins = [
            'FISCAL', 'COMODATO', 'FISCAL F.O.R.A', 'INTERNO', 'DONACION',
            'FONDOS EXTRAORDINARIOS', 'LEY 19.366', 'FACILITADOS', 'ARRIENDO',
            'LEY 20000', 'FISCAL-FONDOS EXTRAORDINARIOS',
            'FISCAL-DONACION ARMADA DE CHILE', 'FISCAL-TRASPASO ARMADA',
            'DONACION PARTICULAR', 'TRASPASO', 'TRASPASO-EJERCITO DE CHILE',
        ];
        foreach ($origins as $o) {
            FundingOrigin::firstOrCreate(['name' => $o]);
        }

        // Categorías de falla (extraídas del Excel)
        $categories = [
            ['name' => 'Motor',       'description' => 'Fallas en el motor y sus componentes'],
            ['name' => 'Frenos',      'description' => 'Fallas en el sistema de frenos'],
            ['name' => 'Transmisión', 'description' => 'Fallas en la transmisión y caja de cambios'],
            ['name' => 'Eléctrico',   'description' => 'Fallas en el sistema eléctrico'],
            ['name' => 'Neumáticos',  'description' => 'Cambio y reparación de neumáticos'],
            ['name' => 'Suspensión',  'description' => 'Fallas en la suspensión y dirección'],
            ['name' => 'Combustible', 'description' => 'Fallas en el sistema de combustible'],
            ['name' => 'Carrocería',  'description' => 'Daños y reparaciones de carrocería'],
            ['name' => 'Refrigeración', 'description' => 'Fallas en el sistema de refrigeración'],
            ['name' => 'Escape',      'description' => 'Fallas en el sistema de escape'],
            ['name' => 'Otros',       'description' => 'Otras fallas no categorizadas'],
        ];
        foreach ($categories as $cat) {
            MaintenanceCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // Talleres
        $workshops = [
            ['type' => 'interno',    'name' => 'TALLER L.3.',         'is_active' => true],
            ['type' => 'particular', 'name' => 'TALLER PARTICULAR',   'is_active' => true],
            ['type' => 'zonal',      'name' => 'TALLER ZONAL',        'is_active' => true],
            ['type' => 'otro',       'name' => 'TALLER EXTERNO',      'is_active' => true],
        ];
        foreach ($workshops as $w) {
            Workshop::firstOrCreate(['name' => $w['name']], $w);
        }
    }
}
