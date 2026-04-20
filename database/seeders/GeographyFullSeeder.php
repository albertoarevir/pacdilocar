<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Province;
use App\Models\Region;
use App\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * Siembra la jerarquía completa: Zona → Provincia → Comuna.
 * La zona en `municipalities.zone_id` es la Jefatura de Zona policial
 * que coincide con la región administrativa de Chile.
 */
class GeographyFullSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->getData() as $zoneName => $provinces) {
            $zone   = Zone::firstOrCreate(['name' => $zoneName]);
            $region = Region::firstOrCreate(['name' => $zoneName], ['number' => 0]);

            foreach ($provinces as $provinceName => $communes) {
                $province = Province::firstOrCreate(
                    ['name' => $provinceName, 'region_id' => $region->id]
                );

                foreach ($communes as $communeName) {
                    Municipality::firstOrCreate(
                        ['name' => $communeName, 'province_id' => $province->id],
                        ['zone_id' => $zone->id]
                    );
                }
            }
        }
    }

    private function getData(): array
    {
        return [
            'ARICA Y PARINACOTA' => [
                'ARICA'      => ['ARICA', 'CAMARONES'],
                'PARINACOTA' => ['PUTRE', 'GENERAL LAGOS'],
            ],
            'TARAPACÁ' => [
                'IQUIQUE'      => ['IQUIQUE', 'ALTO HOSPICIO'],
                'EL TAMARUGAL' => ['POZO ALMONTE', 'CAMIÑA', 'COLCHANE', 'HUARA', 'PICA'],
            ],
            'ANTOFAGASTA' => [
                'ANTOFAGASTA' => ['ANTOFAGASTA', 'MEJILLONES', 'SIERRA GORDA', 'TALTAL'],
                'EL LOA'      => ['CALAMA', 'OLLAGÜE', 'SAN PEDRO DE ATACAMA'],
                'TOCOPILLA'   => ['TOCOPILLA', 'MARÍA ELENA'],
            ],
            'ATACAMA' => [
                'COPIAPÓ'  => ['COPIAPÓ', 'CALDERA', 'TIERRA AMARILLA'],
                'CHAÑARAL' => ['CHAÑARAL', 'DIEGO DE ALMAGRO'],
                'HUASCO'   => ['VALLENAR', 'ALTO DEL CARMEN', 'FREIRINA', 'HUASCO'],
            ],
            'COQUIMBO' => [
                'ELQUI'  => ['LA SERENA', 'COQUIMBO', 'ANDACOLLO', 'LA HIGUERA', 'PAIGUANO', 'VICUÑA'],
                'CHOAPA' => ['ILLAPEL', 'CANELA', 'LOS VILOS', 'SALAMANCA'],
                'LIMARÍ' => ['OVALLE', 'COMBARBALÁ', 'MONTE PATRIA', 'PUNITAQUI', 'RÍO HURTADO'],
            ],
            'VALPARAÍSO' => [
                'VALPARAÍSO'           => ['VALPARAÍSO', 'CASABLANCA', 'CONCÓN', 'JUAN FERNÁNDEZ', 'PUCHUNCAVÍ', 'QUINTERO', 'VIÑA DEL MAR'],
                'ISLA DE PASCUA'       => ['ISLA DE PASCUA'],
                'LOS ANDES'            => ['LOS ANDES', 'CALLE LARGA', 'RINCONADA', 'SAN ESTEBAN'],
                'PETORCA'              => ['LA LIGUA', 'CABILDO', 'PAPUDO', 'PETORCA', 'ZAPALLAR'],
                'QUILLOTA'             => ['QUILLOTA', 'CALERA', 'HIJUELAS', 'LA CRUZ', 'NOGALES'],
                'SAN ANTONIO'          => ['SAN ANTONIO', 'ALGARROBO', 'CARTAGENA', 'EL QUISCO', 'EL TABO', 'SANTO DOMINGO'],
                'SAN FELIPE DE ACONCAGUA' => ['SAN FELIPE', 'CATEMU', 'LLAILLAY', 'PANQUEHUE', 'PUTAENDO', 'SANTA MARÍA'],
                'MARGA MARGA'          => ['QUILPUÉ', 'LIMACHE', 'OLMUÉ', 'VILLA ALEMANA'],
            ],
            'METROPOLITANA' => [
                'SANTIAGO'  => [
                    'SANTIAGO', 'CERRILLOS', 'CERRO NAVIA', 'CONCHALÍ', 'EL BOSQUE',
                    'ESTACIÓN CENTRAL', 'HUECHURABA', 'INDEPENDENCIA', 'LA CISTERNA',
                    'LA FLORIDA', 'LA GRANJA', 'LA PINTANA', 'LA REINA', 'LAS CONDES',
                    'LO BARNECHEA', 'LO ESPEJO', 'LO PRADO', 'MACUL', 'MAIPÚ', 'ÑUÑOA',
                    'PEDRO AGUIRRE CERDA', 'PEÑALOLÉN', 'PROVIDENCIA', 'PUDAHUEL',
                    'QUILICURA', 'QUINTA NORMAL', 'RECOLETA', 'RENCA', 'SAN JOAQUÍN',
                    'SAN MIGUEL', 'SAN RAMÓN', 'VITACURA',
                ],
                'CHACABUCO' => ['COLINA', 'LAMPA', 'TILTIL'],
                'CORDILLERA' => ['PUENTE ALTO', 'PIRQUE', 'SAN JOSÉ DE MAIPO'],
                'MAIPO'      => ['SAN BERNARDO', 'BUIN', 'CALERA DE TANGO', 'PAINE'],
                'MELIPILLA'  => ['MELIPILLA', 'ALHUÉ', 'CURACAVÍ', 'MARÍA PINTO', 'SAN PEDRO'],
                'TALAGANTE'  => ['TALAGANTE', 'EL MONTE', 'ISLA DE MAIPO', 'PADRE HURTADO', 'PEÑAFLOR'],
            ],
            "LIB GRAL BDO. O'HIGGINS" => [
                'CACHAPOAL'    => ['RANCAGUA', 'COINCO', 'COLTAUCO', 'DOÑIHUE', 'GRANEROS', 'LAS CABRAS', 'MACHALÍ', 'MALLOA', 'MOSTAZAL', 'OLIVAR', 'PEUMO', 'PICHIDEGUA', 'QUINTA DE TILCOCO', 'RENGO', 'REQUÍNOA', 'SAN VICENTE'],
                'CARDENAL CARO' => ['PICHILEMU', 'LA ESTRELLA', 'LITUECHE', 'MARCHIGÜE', 'NAVIDAD', 'PAREDONES'],
                'COLCHAGUA'    => ['SAN FERNANDO', 'CHÉPICA', 'CHIMBARONGO', 'LOLOL', 'NANCAGUA', 'PALMILLA', 'PERALILLO', 'PLACILLA', 'PUMANQUE', 'SANTA CRUZ'],
            ],
            'MAULE' => [
                'TALCA'     => ['TALCA', 'CONSTITUCIÓN', 'CUREPTO', 'EMPEDRADO', 'MAULE', 'PELARCO', 'PENCAHUE', 'RÍO CLARO', 'SAN CLEMENTE', 'SAN RAFAEL'],
                'CAUQUENES' => ['CAUQUENES', 'CHANCO', 'PELLUHUE'],
                'CURICÓ'    => ['CURICÓ', 'HUALAÑÉ', 'LICANTÉN', 'MOLINA', 'RAUCO', 'ROMERAL', 'SAGRADA FAMILIA', 'TENO', 'VICHUQUÉN'],
                'LINARES'   => ['LINARES', 'COLBÚN', 'LONGAVÍ', 'PARRAL', 'RETIRO', 'SAN JAVIER', 'VILLA ALEGRE', 'YERBAS BUENAS'],
            ],
            'ÑUBLE' => [
                'DIGUILLÍN' => ['CHILLÁN', 'BULNES', 'CHILLÁN VIEJO', 'EL CARMEN', 'PEMUCO', 'PINTO', 'QUILLÓN', 'SAN IGNACIO', 'YUNGAY'],
                'ITATA'     => ['QUIRIHUE', 'COBQUECURA', 'COELEMU', 'NINHUE', 'PORTEZUELO', 'RÁNQUIL', 'TREHUACO'],
                'PUNILLA'   => ['SAN CARLOS', 'COIHUECO', 'ÑIQUÉN', 'SAN FABIÁN', 'SAN NICOLÁS'],
            ],
            'BIOBÍO' => [
                'CONCEPCIÓN' => ['CONCEPCIÓN', 'CORONEL', 'CHIGUAYANTE', 'FLORIDA', 'HUALPÉN', 'HUALQUI', 'LOTA', 'PENCO', 'SAN PEDRO DE LA PAZ', 'SANTA JUANA', 'TALCAHUANO', 'TOMÉ'],
                'ARAUCO'     => ['LEBU', 'ARAUCO', 'CAÑETE', 'CONTULMO', 'CURANILAHUE', 'LOS ÁLAMOS', 'TIRÚA'],
                'BIOBÍO'     => ['LOS ÁNGELES', 'ANTUCO', 'CABRERO', 'LAJA', 'MULCHÉN', 'NACIMIENTO', 'NEGRETE', 'QUILACO', 'QUILLECO', 'SAN ROSENDO', 'SANTA BÁRBARA', 'TUCAPEL', 'YUMBEL', 'ALTO BIOBÍO'],
            ],
            'ARAUCANÍA' => [
                'CAUTÍN'  => ['TEMUCO', 'CARAHUE', 'CUNCO', 'CURARREHUE', 'FREIRE', 'GALVARINO', 'GORBEA', 'LAUTARO', 'LONCOCHE', 'NUEVA IMPERIAL', 'PADRE LAS CASAS', 'PERQUENCO', 'PITRUFQUÉN', 'PUERTO SAAVEDRA', 'TEODORO SCHMIDT', 'TOLTÉN', 'VILCÚN', 'VILLARRICA', 'CHOLCHOL'],
                'MALLECO' => ['ANGOL', 'COLLIPULLI', 'CURACAUTÍN', 'ERCILLA', 'LONQUIMAY', 'LOS SAUCES', 'LUMACO', 'PURÉN', 'RENAICO', 'TRAIGUÉN', 'VICTORIA'],
            ],
            'LOS RÍOS' => [
                'VALDIVIA' => ['VALDIVIA', 'CORRAL', 'LANCO', 'LOS LAGOS', 'MÁFIL', 'MARIQUINA', 'PAILLACO', 'PANGUIPULLI'],
                'RANCO'    => ['LA UNIÓN', 'FUTRONO', 'LAGO RANCO', 'RÍO BUENO'],
            ],
            'LOS LAGOS' => [
                'LLANQUIHUE' => ['PUERTO MONTT', 'CALBUCO', 'COCHAMÓ', 'FRESIA', 'FRUTILLAR', 'LOS MUERMOS', 'MAULLÍN', 'PUERTO VARAS', 'LLANQUIHUE'],
                'CHILOÉ'     => ['CASTRO', 'ANCUD', 'CHONCHI', 'CURACO DE VÉLEZ', 'DALCAHUE', 'PUQUELDÓN', 'QUEILÉN', 'QUELLÓN', 'QUEMCHI', 'QUINCHAO'],
                'OSORNO'     => ['OSORNO', 'PUERTO OCTAY', 'PURRANQUE', 'PUYEHUE', 'RÍO NEGRO', 'SAN JUAN DE LA COSTA', 'SAN PABLO'],
                'PALENA'     => ['CHAITÉN', 'FUTALEUFÚ', 'HUALAIHUÉ', 'PALENA'],
            ],
            'AYSÉN' => [
                'COYHAIQUE'       => ['COYHAIQUE', 'LAGO VERDE'],
                'AISÉN'           => ['AISÉN', 'CISNES', 'GUAITECAS'],
                'CAPITÁN PRAT'    => ['COCHRANE', "O'HIGGINS", 'TORTEL'],
                'GENERAL CARRERA' => ['CHILE CHICO', 'RÍO IBÁÑEZ'],
            ],
            'MAGALLANES' => [
                'MAGALLANES'        => ['PUNTA ARENAS', 'LAGUNA BLANCA', 'RÍO VERDE', 'SAN GREGORIO'],
                'ANTÁRTICA CHILENA' => ['CABO DE HORNOS', 'ANTÁRTICA'],
                'TIERRA DEL FUEGO'  => ['PORVENIR', 'PRIMAVERA', 'TIMAUKEL'],
                'ÚLTIMA ESPERANZA'  => ['NATALES', 'TORRES DEL PAINE'],
            ],
        ];
    }
}
