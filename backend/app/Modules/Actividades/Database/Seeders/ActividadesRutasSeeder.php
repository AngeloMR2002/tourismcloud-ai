<?php

namespace App\Modules\Actividades\Database\Seeders;

use App\Modules\Actividades\Models\Actividad;
use App\Models\Atractivo;
use App\Models\Destino;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActividadesRutasSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $destinos = [];
            foreach (['Chimbote', 'Nuevo Chimbote'] as $ciudad) {
                $destinos[$ciudad] = Destino::firstOrCreate(['nombre' => $ciudad], [
                    'pais' => 'Perú', 'region' => 'Áncash', 'ciudad' => $ciudad, 'estado' => 'activo',
                ]);
            }
            $base = 'https://consultasenlinea.mincetur.gob.pe/fichaInventario/index.aspx?cod_Ficha=';
            $resources = [
                [
                    'codigo' => 3146, 'ciudad' => 'Chimbote', 'nombre' => 'Vivero Forestal de Chimbote',
                    'actividad' => 'Observación de flora en el Vivero Forestal',
                    'descripcion' => 'Recurso turístico registrado por MINCETUR en Chimbote. Su inventario describe jardines y áreas de vegetación, y registra la observación de flora entre las actividades del lugar.',
                    'categoria' => 'ecoturismo',
                    'imagen_portada' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/4/4f/Ingreso_al_vivero_forestal.jpg/960px-Ingreso_al_vivero_forestal.jpg',
                    'imagen_fuente_url' => 'https://commons.wikimedia.org/wiki/File:Ingreso_al_vivero_forestal.jpg',
                    'imagen_autor' => 'Candy Lopez', 'imagen_licencia' => 'CC BY-SA 4.0',
                ],
                [
                    'codigo' => 10930, 'ciudad' => 'Nuevo Chimbote', 'nombre' => 'Plaza Mayor de Nuevo Chimbote',
                    'actividad' => 'Recorrido por la Plaza Mayor de Nuevo Chimbote',
                    'descripcion' => 'Espacio urbano registrado en el inventario turístico de MINCETUR. La plaza incluye una pileta y un monumento a la garza, y se encuentra frente a la catedral del distrito.',
                    'categoria' => 'cultural',
                    'imagen_portada' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/9/96/Plaza_mayor_de_nuevo_chimbote_y_catedral.JPG/960px-Plaza_mayor_de_nuevo_chimbote_y_catedral.JPG',
                    'imagen_fuente_url' => 'https://commons.wikimedia.org/wiki/File:Plaza_mayor_de_nuevo_chimbote_y_catedral.JPG',
                    'imagen_autor' => 'Ed Pax; ajuste de brillo y contraste por Lamder', 'imagen_licencia' => 'CC BY 3.0',
                ],
                [
                    'codigo' => 11267, 'ciudad' => 'Nuevo Chimbote', 'nombre' => 'Catedral de la Diócesis de Chimbote',
                    'actividad' => 'Visita cultural a la Catedral de Nuevo Chimbote',
                    'descripcion' => 'La Catedral de Nuestra Señora del Carmen y San Pedro Apóstol está situada frente a la Plaza Mayor de Nuevo Chimbote. MINCETUR la registra como una manifestación cultural de arquitectura religiosa.',
                    'categoria' => 'cultural',
                    'imagen_portada' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/4/44/Catedral_de_Chimbote_-_Di%C3%B3cesis.jpg/960px-Catedral_de_Chimbote_-_Di%C3%B3cesis.jpg',
                    'imagen_fuente_url' => 'https://commons.wikimedia.org/wiki/File:Catedral_de_Chimbote_-_Di%C3%B3cesis.jpg',
                    'imagen_autor' => 'Rodavía17 / E.Rod', 'imagen_licencia' => 'CC BY-SA 4.0',
                ],
                [
                    'codigo' => 10935, 'ciudad' => 'Nuevo Chimbote', 'nombre' => 'Playa El Dorado',
                    'actividad' => 'Observación del paisaje en Playa El Dorado',
                    'descripcion' => 'Playa del distrito de Nuevo Chimbote registrada por MINCETUR. Su ficha describe el paisaje costero y enumera la observación de paisaje entre las actividades desarrolladas en el recurso.',
                    'categoria' => 'ecoturismo',
                ],
                [
                    'codigo' => 10942, 'ciudad' => 'Nuevo Chimbote', 'nombre' => 'Caleta Colorada',
                    'actividad' => 'Visita al paisaje de Caleta Colorada',
                    'descripcion' => 'Caleta del distrito de Nuevo Chimbote, próxima a Playa El Dorado, registrada en el inventario de MINCETUR. Su ficha describe el acceso terrestre y marítimo. Confirma las condiciones de acceso antes del viaje.',
                    'categoria' => 'ecoturismo',
                ],
            ];
            $atractivos = [];
            foreach ($resources as $resource) {
                $source = $base.$resource['codigo'];
                $atractivo = Atractivo::firstOrCreate(['origen_id' => 'mincetur/'.$resource['codigo']], [
                    'destino_id' => $destinos[$resource['ciudad']]->id,
                    'nombre' => $resource['nombre'], 'descripcion' => $resource['descripcion'],
                    'fuente_url' => $source, 'fecha_verificacion' => '2026-10-07',
                    'estado_verificacion' => 'aprobado', 'estado' => 'activo', 'costo_entrada' => null,
                ]);
                $atractivos[$resource['codigo']] = $atractivo;
                $photos = array_intersect_key($resource, array_flip(['imagen_portada', 'imagen_fuente_url', 'imagen_autor', 'imagen_licencia']));
                Actividad::firstOrCreate(['nombre' => $resource['actividad'], 'fuente_url' => $source], array_merge($photos, [
                    'destino_id' => $destinos[$resource['ciudad']]->id, 'atractivo_id' => $atractivo->id,
                    'descripcion' => $resource['descripcion'], 'categoria' => $resource['categoria'],
                    'tipo_registro' => 'visita', 'tipo_precio' => 'variable', 'precio' => null,
                    'duracion_min' => null, 'nivel_dificultad' => null, 'cupo_maximo' => null,
                    'estado' => 'activo', 'estado_verificacion' => 'aprobado', 'fecha_verificacion' => '2026-10-07',
                ]));
            }
            $ruta = Ruta::firstOrCreate(['nombre' => 'Propuesta cultural: Plaza Mayor y Catedral', 'fuente_url' => $base.'11267'], [
                'destino_id' => $destinos['Nuevo Chimbote']->id, 'tipo_ruta' => 'lineal', 'es_propuesta' => true,
                'descripcion' => 'Recorrido sugerido por TourismCloud entre dos recursos registrados por MINCETUR: la Plaza Mayor y la Catedral de Nuevo Chimbote. El orden de las visitas es una propuesta de planificación; confirma horarios y acceso con los responsables.',
                'duracion_estimada_horas' => null, 'distancia_km' => null, 'nivel_dificultad' => null,
                'transporte_recomendado' => 'a_pie', 'costo_estimado' => null, 'tipo_precio' => 'variable',
                'estado' => 'activo', 'estado_verificacion' => 'aprobado', 'fecha_verificacion' => '2026-10-07',
            ]);
            foreach ([10930, 11267] as $index => $codigo) {
                $ruta->puntos()->firstOrCreate(['orden' => $index + 1], [
                    'atractivo_id' => $atractivos[$codigo]->id, 'nombre_parada' => $atractivos[$codigo]->nombre,
                    'tiempo_estadia_min' => null, 'tiempo_traslado_min' => null, 'distancia_desde_anterior_km' => null,
                ]);
            }
        });
    }
}
