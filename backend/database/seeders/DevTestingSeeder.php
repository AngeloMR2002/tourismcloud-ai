<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Datos de prueba locales para destinos, atractivos y establecimientos.
 *
 * Ejecutar desde backend con:
 * php artisan db:seed --class=DevTestingSeeder
 */
class DevTestingSeeder extends Seeder
{
    public function run(): void
    {
        $seededIds = DB::transaction(function (): array {
            $now = now();
            $password = Hash::make('password');

            $organizationId = $this->findOrCreateId(
                'organizaciones',
                ['nombre' => 'TourismCloud Demo', 'tipo' => 'agencia_turistica'],
                [
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $operatorId = $this->findOrCreateId(
                'usuarios',
                ['email' => 'operador@tourismcloud.test'],
                [
                    'organizacion_id' => $organizationId,
                    'nombre' => 'Operador Demo',
                    'password' => $password,
                    'rol' => 'operador_turistico',
                    'telefono' => '+51 999 000 001',
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $providerId = $this->findOrCreateId(
                'usuarios',
                ['email' => 'proveedor@tourismcloud.test'],
                [
                    'organizacion_id' => $organizationId,
                    'nombre' => 'Proveedor Demo',
                    'password' => $password,
                    'rol' => 'proveedor',
                    'telefono' => '+51 999 000 002',
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $categoryIds = [];
            foreach ([
                'Naturaleza y ecoturismo',
                'Historia y cultura',
                'Gastronomía',
                'Aventura y deportes',
                'Relax y bienestar',
            ] as $categoryName) {
                $categoryIds[$categoryName] = $this->findOrCreateId(
                    'categorias_interes',
                    ['nombre' => $categoryName]
                );
            }

            $cuscoId = $this->findOrCreateId(
                'destinos',
                ['organizacion_id' => $organizationId, 'nombre' => 'Cusco'],
                [
                    'operador_id' => $operatorId,
                    'descripcion' => 'Capital histórica del Imperio Inca, declarada Patrimonio de la Humanidad.',
                    'pais' => 'Perú',
                    'region' => 'Cusco',
                    'ciudad' => 'Cusco',
                    'latitud' => -13.5319981,
                    'longitud' => -71.9674626,
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $this->findOrCreateId(
                'destinos',
                ['organizacion_id' => $organizationId, 'nombre' => 'Arequipa'],
                [
                    'operador_id' => $operatorId,
                    'descripcion' => 'La Ciudad Blanca, conocida por su arquitectura de sillar.',
                    'pais' => 'Perú',
                    'region' => 'Arequipa',
                    'ciudad' => 'Arequipa',
                    'latitud' => -16.4090474,
                    'longitud' => -71.5374500,
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $attractionId = $this->findOrCreateId(
                'atractivos',
                ['destino_id' => $cuscoId, 'nombre' => 'Machu Picchu'],
                [
                    'descripcion' => 'Ciudadela inca del siglo XV ubicada en las montañas de los Andes.',
                    'costo_entrada' => 152.00,
                    'duracion_estimada_min' => 240,
                    'latitud' => -13.1631412,
                    'longitud' => -72.5449629,
                    'imagen_portada' => null,
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]
            );

            DB::table('atractivo_categoria')->insertOrIgnore([
                ['atractivo_id' => $attractionId, 'categoria_id' => $categoryIds['Historia y cultura']],
                ['atractivo_id' => $attractionId, 'categoria_id' => $categoryIds['Aventura y deportes']],
            ]);

            foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day) {
                DB::table('horarios')->updateOrInsert(
                    ['atractivo_id' => $attractionId, 'dia' => $day],
                    [
                        'establecimiento_id' => null,
                        'hora_inicio' => '06:00',
                        'hora_fin' => '17:30',
                    ]
                );
            }

            $establishmentId = $this->findOrCreateId(
                'establecimientos',
                ['destino_id' => $cuscoId, 'nombre' => 'Belmond Sanctuary Lodge'],
                [
                    'proveedor_id' => $providerId,
                    'tipo' => 'hotel',
                    'descripcion' => 'El único hotel ubicado junto a Machu Picchu, con vistas privilegiadas.',
                    'direccion' => 'Machu Picchu, Cusco, Perú',
                    'latitud' => -13.1639,
                    'longitud' => -72.5449,
                    'rango_precio' => 'lujo',
                    'imagen_portada' => null,
                    'estado' => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]
            );

            DB::table('establecimiento_categoria')->insertOrIgnore([
                [
                    'establecimiento_id' => $establishmentId,
                    'categoria_id' => $categoryIds['Relax y bienestar'],
                ],
            ]);

            foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day) {
                DB::table('horarios')->updateOrInsert(
                    ['establecimiento_id' => $establishmentId, 'dia' => $day],
                    [
                        'atractivo_id' => null,
                        'hora_inicio' => '00:00',
                        'hora_fin' => '23:59',
                    ]
                );
            }

            return [
                'operator' => $operatorId,
                'provider' => $providerId,
            ];
        });

        $this->command->info('DevTestingSeeder completado.');
        $this->command->table(
            ['Rol', 'Email', 'Password', 'Notas'],
            [
                [
                    'operador_turistico',
                    'operador@tourismcloud.test',
                    'password',
                    "Panel: /operador/atractivos?_operador_id_test={$seededIds['operator']}",
                ],
                [
                    'proveedor',
                    'proveedor@tourismcloud.test',
                    'password',
                    "Panel: /proveedor/establecimientos?_proveedor_id_test={$seededIds['provider']}",
                ],
            ]
        );
        $this->command->warn('Usar únicamente para pruebas locales.');
    }

    private function findOrCreateId(string $table, array $identity, array $values = []): int
    {
        $query = DB::table($table);
        foreach ($identity as $column => $value) {
            $query->where($column, $value);
        }

        $existing = $query->first(['id']);
        if ($existing !== null) {
            if ($values !== []) {
                $updates = $values;
                unset($updates['created_at']);
                if ($updates !== []) {
                    DB::table($table)->where('id', $existing->id)->update($updates);
                }
            }

            return (int) $existing->id;
        }

        return (int) DB::table($table)->insertGetId(array_merge($identity, $values));
    }
}
