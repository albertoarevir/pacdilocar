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
        $combustibles = ['GASOLINA', 'DIESEL', 'GAS', 'ELÉCTRICO', 'HÍBRIDO', 'GNV'];
        foreach ($combustibles as $nombre) {
            FuelType::firstOrCreate(['nombre' => $nombre]);
        }

        // Colores (extraídos del Excel hoja 9.Color)
        $colores = [
            'ALUMINIO TITANIO', 'AMARILLO', 'AMARILLO OCRE', 'AZUL', 'BEIGE',
            'BLANCO', 'CAFE', 'CELESTE', 'GRIS', 'GRIS OSCURO', 'METALIZADO',
            'NARANJO', 'NEGRO', 'PLATEADO', 'ROJO', 'VERDE', 'VERDE MILITAR',
            'VERDE OSCURO', 'VINO', 'BLANCO PERLA',
        ];
        foreach ($colores as $nombre) {
            Color::firstOrCreate(['nombre' => $nombre]);
        }

        // Orígenes de financiamiento (extraídos del Excel hoja 8.Origen)
        $origenes = [
            'FISCAL', 'COMODATO', 'FISCAL F.O.R.A', 'INTERNO', 'DONACION',
            'FONDOS EXTRAORDINARIOS', 'LEY 19.366', 'FACILITADOS', 'ARRIENDO',
            'LEY 20000', 'FISCAL-FONDOS EXTRAORDINARIOS',
            'FISCAL-DONACION ARMADA DE CHILE', 'FISCAL-TRASPASO ARMADA',
            'DONACION PARTICULAR', 'TRASPASO', 'TRASPASO-EJERCITO DE CHILE',
        ];
        foreach ($origenes as $nombre) {
            FundingOrigin::firstOrCreate(['nombre' => $nombre]);
        }

        // Categorías de falla (extraídas del Excel)
        $categorias = [
            ['nombre' => 'Motor',         'descripcion' => 'Fallas en el motor y sus componentes'],
            ['nombre' => 'Frenos',        'descripcion' => 'Fallas en el sistema de frenos'],
            ['nombre' => 'Transmisión',   'descripcion' => 'Fallas en la transmisión y caja de cambios'],
            ['nombre' => 'Eléctrico',     'descripcion' => 'Fallas en el sistema eléctrico'],
            ['nombre' => 'Neumáticos',    'descripcion' => 'Cambio y reparación de neumáticos'],
            ['nombre' => 'Suspensión',    'descripcion' => 'Fallas en la suspensión y dirección'],
            ['nombre' => 'Combustible',   'descripcion' => 'Fallas en el sistema de combustible'],
            ['nombre' => 'Carrocería',    'descripcion' => 'Daños y reparaciones de carrocería'],
            ['nombre' => 'Refrigeración', 'descripcion' => 'Fallas en el sistema de refrigeración'],
            ['nombre' => 'Escape',        'descripcion' => 'Fallas en el sistema de escape'],
            ['nombre' => 'Otros',         'descripcion' => 'Otras fallas no categorizadas'],
        ];
        foreach ($categorias as $cat) {
            MaintenanceCategory::firstOrCreate(['nombre' => $cat['nombre']], $cat);
        }

        // Talleres
        $talleres = [
            ['tipo' => 'interno',    'nombre' => 'TALLER L.3.',         'activo' => true],
            ['tipo' => 'particular', 'nombre' => 'TALLER PARTICULAR',   'activo' => true],
            ['tipo' => 'zonal',      'nombre' => 'TALLER ZONAL',        'activo' => true],
            ['tipo' => 'otro',       'nombre' => 'TALLER EXTERNO',      'activo' => true],
        ];
        foreach ($talleres as $taller) {
            Workshop::firstOrCreate(['nombre' => $taller['nombre']], $taller);
        }
    }
}
