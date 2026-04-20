<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GeographySeeder::class,       // 1. Zonas y Regiones
            VehicleTypeSeeder::class,     // 2. Tipos de vehículo (Siglas)
            CatalogSeeder::class,         // 3. Colores, combustibles, orígenes, talleres, categorías
            VehicleSeeder::class,         // 4. Vehículos de ejemplo
        ]);
    }
}
