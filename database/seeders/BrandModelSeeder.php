<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandModelSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Toyota'        => ['Hilux','Land Cruiser 70','Land Cruiser 200','Land Cruiser Prado','Fortuner','4Runner','RAV4','Corolla','Yaris','Hiace','Coaster','Dyna','Camry'],
            'Ford'          => ['Ranger','F-150','F-250','F-350','Transit','Transit Connect','Explorer','Maverick','Bronco','Mustang','Edge'],
            'Nissan'        => ['Navara','Frontier','Patrol','Terrano','NP300','X-Trail','Urvan','Murano','Pathfinder','Titan','Kicks'],
            'Chevrolet'     => ['D-MAX','Colorado','Silverado 1500','Silverado 2500','Silverado 3500','Express','Captiva','Trailblazer','Traverse','Equinox','Tahoe','Suburban'],
            'Hyundai'       => ['Accent','Tucson','Santa Fe','H-1','H-100','Creta','Ioniq','Staria','County','Porter','Mighty'],
            'Kia'           => ['Sportage','Sorento','Stinger','K2500','K3000','Carnival','Telluride','Seltos','Bongo'],
            'Mitsubishi'    => ['L200','Outlander','Pajero','Pajero Sport','Eclipse Cross','ASX','Montero','Canter','Fighter'],
            'Volkswagen'    => ['Amarok','Transporter','Crafter','Caravelle','Multivan','Tiguan','Touareg','Passat','Polo','Golf'],
            'Mercedes-Benz' => ['Sprinter','Vito','Viano','Atego','Actros','Axor','Accelo','GLE','GLC','Clase G','Clase C','Clase E'],
            'Isuzu'         => ['D-MAX','MU-X','NPR','NQR','NMR','FVR','FTR','CYZ','EXZ'],
            'RAM'           => ['1500','2500','3500','700','ProMaster','ProMaster City'],
            'Jeep'          => ['Wrangler','Cherokee','Grand Cherokee','Gladiator','Compass','Renegade','Commander'],
            'Land Rover'    => ['Defender','Discovery','Discovery Sport','Range Rover','Range Rover Sport','Range Rover Evoque','Freelander'],
            'Renault'       => ['Duster','Oroch','Alaskan','Master','Kangoo','Trafic','Fluence','Koleos','Captur','Logan'],
            'Suzuki'        => ['Jimny','Vitara','Grand Vitara','Swift','Ertiga','Carry','Super Carry','S-Presso'],
            'Subaru'        => ['Forester','Outback','XV','Legacy','Impreza','WRX','Ascent','BRZ'],
            'Honda'         => ['CR-V','HR-V','Pilot','Ridgeline','Civic','Jazz','Accord','Fit','City','Passport'],
            'Mazda'         => ['BT-50','CX-5','CX-9','CX-30','CX-3','Mazda3','Mazda6','MX-30'],
            'Fiat'          => ['Strada','Ducato','Doblò','Scudo','Fullback','Toro','Cronos','Mobi','500X'],
            'Citroën'       => ['Berlingo','Jumpy','Jumper','C3','C4','C5 Aircross','SpaceTourer'],
            'Peugeot'       => ['Landtrek','Expert','Boxer','Partner','3008','5008','2008','408','508'],
            'JAC'           => ['T6','T8','T9','Sunray','S2','S3','S4','X200'],
            'Great Wall'    => ['Wingle 5','Wingle 7','Poer','Haval H6','Haval H9','ORA'],
            'SsangYong'     => ['Actyon','Actyon Sports','Musso','Rexton','Korando','Tivoli','Torres'],
            'Mahindra'      => ['Pik-Up 2.2','Pik-Up 2.4','Bolero','Scorpio','XUV300','XUV700','Thar'],
            'Volvo'         => ['FH','FM','FMX','FE','FL','XC60','XC90','XC40','V60'],
            'Scania'        => ['R 450','R 500','R 560','G 410','P 310','P 360','L 280'],
            'BMW'           => ['X3','X5','X6','X7','Serie 3','Serie 5','Serie 7','GS 1200'],
            'Dodge'         => ['Ram 1500','Ram 2500','Ram 3500','Durango','Journey','Charger','Challenger'],
            'Tata'          => ['Xenon','Telcoline 4x4','Telcoline 4x2','Yodha','Storme'],
            'Dongfeng'      => ['Rich 6','Rich 7','S30','AX7'],
        ];

        foreach ($catalog as $brandName => $models) {
            // Insertar marca si no existe
            DB::table('brands')->insertOrIgnore([
                'name'       => $brandName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $brandId = DB::table('brands')->where('name', $brandName)->value('id');

            foreach ($models as $modelName) {
                DB::table('vehicle_models')->insertOrIgnore([
                    'brand_id'   => $brandId,
                    'name'       => $modelName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
