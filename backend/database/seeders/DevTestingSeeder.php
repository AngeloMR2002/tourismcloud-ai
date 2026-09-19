<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * =====================================================================
 * DevTestingSeeder — SOLO PARA PRUEBAS LOCALES EN DESARROLLO
 * =====================================================================
 *
 * PROPÓSITO: Inserta los datos mínimos necesarios (destinos, usuarios
 * y categorías) para poder probar el CRUD de Atractivos y Establecimientos
 * de punta a punta, mientras las ramas feature/destinos y feature/auth
 * no han llegado a develop.
 *
 * INSTRUCCIONES:
 *   php artisan db:seed --class=DevTestingSeeder
 *
 * ⚠️  ELIMINAR este seeder cuando feature/destinos y feature/auth
 *     hagan merge a develop y sus seeders propios estén disponibles.
 *     NO registrar en DatabaseSeeder.php.
 * =====================================================================
 */
class DevTestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 DevTestingSeeder: insertando datos de prueba temporales...');

        // ─── 1. Categorías de interés ────────────────────────────────────────
        // (tabla de feature/destinos — stub temporal)
        DB::table('categorias_interes')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Naturaleza y ecoturismo'],
            ['id' => 2, 'nombre' => 'Historia y cultura'],
            ['id' => 3, 'nombre' => 'Gastronomía'],
            ['id' => 4, 'nombre' => 'Aventura y deportes'],
            ['id' => 5, 'nombre' => 'Relax y bienestar'],
        ]);
        $this->command->line('  ✓ Categorías de interés insertadas.');

        // ─── 2. Usuarios de prueba ───────────────────────────────────────────
        // (tabla de feature/auth — stub temporal)
        // NOTA: La tabla del schema real se llama 'usuarios', no 'users'.
        DB::table('usuarios')->insertOrIgnore([
            [
                'id'              => 1,
                'organizacion_id' => null,
                'nombre'          => 'Operador Demo',
                'email'           => 'operador@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'operador_turistico',
                'telefono'        => '+51 999 000 001',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 2,
                'organizacion_id' => null,
                'nombre'          => 'Proveedor Demo',
                'email'           => 'proveedor@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'proveedor',
                'telefono'        => '+51 999 000 002',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
        $this->command->line('  ✓ Usuarios de prueba insertados (operador ID=1, proveedor ID=2).');

        // ─── 3. Destinos de prueba ───────────────────────────────────────────
        // (tabla de feature/destinos — stub temporal)
        DB::table('destinos')->insertOrIgnore([
            [
                'id'              => 1,
                'organizacion_id' => 1, // se creará con ID 1 si no existe
                'operador_id'     => 1, // operador demo
                'nombre'          => 'Cusco',
                'descripcion'     => 'Capital histórica del Imperio Inca, declarada Patrimonio de la Humanidad.',
                'pais'            => 'Perú',
                'region'          => 'Cusco',
                'ciudad'          => 'Cusco',
                'latitud'         => -13.5319981,
                'longitud'        => -71.9674626,
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 2,
                'organizacion_id' => 1,
                'operador_id'     => 1, // operador demo también gestiona este destino
                'nombre'          => 'Arequipa',
                'descripcion'     => 'La Ciudad Blanca, conocida por su arquitectura de sillar.',
                'pais'            => 'Perú',
                'region'          => 'Arequipa',
                'ciudad'          => 'Arequipa',
                'latitud'         => -16.4090474,
                'longitud'        => -71.537450,
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
        $this->command->line('  ✓ Destinos de prueba insertados (Cusco ID=1, Arequipa ID=2).');

        // ─── 4. Atractivo de muestra ─────────────────────────────────────────
        DB::table('atractivos')->insertOrIgnore([
            [
                'id'                    => 1,
                'destino_id'            => 1,
                'nombre'                => 'Machu Picchu',
                'descripcion'           => 'Ciudadela inca del siglo XV ubicada en las montañas de los Andes.',
                'horarios'              => json_encode([
                    'lunes'     => ['abre' => '06:00', 'cierra' => '17:30'],
                    'martes'    => ['abre' => '06:00', 'cierra' => '17:30'],
                    'miercoles' => ['abre' => '06:00', 'cierra' => '17:30'],
                    'jueves'    => ['abre' => '06:00', 'cierra' => '17:30'],
                    'viernes'   => ['abre' => '06:00', 'cierra' => '17:30'],
                    'sabado'    => ['abre' => '06:00', 'cierra' => '17:30'],
                    'domingo'   => ['abre' => '06:00', 'cierra' => '17:30'],
                ]),
                'costo_entrada'         => 152.00,
                'duracion_estimada_min' => 240,
                'latitud'               => -13.1631412,
                'longitud'              => -72.5449629,
                'imagen_portada'        => null,
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
        ]);
        DB::table('atractivo_categoria')->insertOrIgnore([
            ['atractivo_id' => 1, 'categoria_id' => 2], // Historia y cultura
            ['atractivo_id' => 1, 'categoria_id' => 4], // Aventura y deportes
        ]);
        $this->command->line('  ✓ Atractivo de muestra insertado (Machu Picchu ID=1).');

        // ─── 5. Establecimiento de muestra ───────────────────────────────────
        DB::table('establecimientos')->insertOrIgnore([
            [
                'id'            => 1,
                'destino_id'    => 1,
                'proveedor_id'  => 2, // proveedor demo
                'tipo'          => 'hotel',
                'nombre'        => 'Belmond Sanctuary Lodge',
                'descripcion'   => 'El único hotel ubicado junto a Machu Picchu, con vistas privilegiadas.',
                'direccion'     => 'Machu Picchu, Cusco, Perú',
                'horarios'      => json_encode([
                    'lunes'     => ['abre' => '00:00', 'cierra' => '23:59'],
                    'martes'    => ['abre' => '00:00', 'cierra' => '23:59'],
                    'miercoles' => ['abre' => '00:00', 'cierra' => '23:59'],
                    'jueves'    => ['abre' => '00:00', 'cierra' => '23:59'],
                    'viernes'   => ['abre' => '00:00', 'cierra' => '23:59'],
                    'sabado'    => ['abre' => '00:00', 'cierra' => '23:59'],
                    'domingo'   => ['abre' => '00:00', 'cierra' => '23:59'],
                ]),
                'latitud'       => -13.1639,
                'longitud'      => -72.5449,
                'rango_precio'  => 'lujo',
                'imagen_portada' => null,
                'estado'        => 'activo',
                'created_at'    => now(),
                'updated_at'    => now(),
                'deleted_at'    => null,
            ],
        ]);
        DB::table('establecimiento_categoria')->insertOrIgnore([
            ['establecimiento_id' => 1, 'categoria_id' => 5], // Relax y bienestar
        ]);
        $this->command->line('  ✓ Establecimiento de muestra insertado (Belmond ID=1).');

        // ─── 6. Sincronizar secuencias de PostgreSQL ─────────────────────────
        // OBLIGATORIO: los inserts con 'id' explícito no avanzan las secuencias
        // BIGSERIAL. Sin esto el siguiente INSERT real desde un formulario
        // colisiona con un ID ya existente (SQLSTATE 23505, unique violation pk).
        // Se ejecuta cada vez que corra el seeder para dejar las secuencias
        // siempre en MAX(id) de cada tabla.
        $tablasConSecuencia = [
            'categorias_interes',
            'destinos',
            'usuarios',
            'atractivos',
            'establecimientos',
            'imagenes',
        ];

        foreach ($tablasConSecuencia as $tabla) {
            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('{$tabla}', 'id'),
                    COALESCE((SELECT MAX(id) FROM \"{$tabla}\"), 1)
                )
            ");
        }
        $this->command->line('  ✓ Secuencias de PostgreSQL sincronizadas (MAX id en cada tabla).');

        $this->command->newLine();
        $this->command->info('✅ DevTestingSeeder completado. Credenciales de prueba:');
        $this->command->table(
            ['Rol', 'Email', 'Password', 'Notas'],
            [
                ['operador_turistico', 'operador@tourismcloud.test', 'password', 'Panel: /operador/atractivos?_operador_id_test=1'],
                ['proveedor',          'proveedor@tourismcloud.test', 'password', 'Panel: /proveedor/establecimientos?_proveedor_id_test=2'],
            ]
        );
        $this->command->warn('⚠️  Eliminar este seeder cuando feature/destinos y feature/auth hagan merge.');
    }
}
