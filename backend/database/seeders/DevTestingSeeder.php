<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * =====================================================================
 * DevTestingSeeder — DATOS INICIALES Y DE PRUEBA
 * =====================================================================
 *
 * Basado al 100% en el schema de la BD PostgreSQL v2 (22 tablas).
 * Inserta los datos base necesarios: organizaciones, usuarios, destinos,
 * categorías, atractivos, establecimientos y horarios.
 *
 * INSTRUCCIONES:
 *   php artisan db:seed
 *   o
 *   php artisan db:seed --class=DevTestingSeeder
 * =====================================================================
 */
class DevTestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 DevTestingSeeder: insertando datos iniciales y de prueba...');

        // ─── 1. Organizaciones ───────────────────────────────────────────────
        DB::table('organizaciones')->insertOrIgnore([
            [
                'id'          => 1,
                'nombre'      => 'Organización Principal',
                'tipo'        => 'empresa_turismo',
                'descripcion' => 'Organización principal de prueba para desarrollo local.',
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
        $this->command->line('  ✓ Organizaciones insertadas.');

        // ─── 2. Categorías de interés ────────────────────────────────────────
        DB::table('categorias_interes')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Naturaleza y ecoturismo', 'descripcion' => 'Actividades y atractivos al aire libre.'],
            ['id' => 2, 'nombre' => 'Historia y cultura',     'descripcion' => 'Sitios arqueológicos, monumentos y museos.'],
            ['id' => 3, 'nombre' => 'Gastronomía',            'descripcion' => 'Restaurantes, ferias y experiencias culinarias.'],
            ['id' => 4, 'nombre' => 'Aventura y deportes',    'descripcion' => 'Trekking, deportes acuáticos y experiencias extremas.'],
            ['id' => 5, 'nombre' => 'Relax y bienestar',       'descripcion' => 'Spas, aguas termales y desconexión.'],
        ]);
        $this->command->line('  ✓ Categorías de interés insertadas.');

        // ─── 3. Usuarios de prueba ───────────────────────────────────────────
        // Tabla 'usuarios' (no 'users'): nombre, apellido, email, password, rol, estado.
        DB::table('usuarios')->insertOrIgnore([
            [
                'id'              => 1,
                'organizacion_id' => 1,
                'nombre'          => 'Operador',
                'apellido'        => 'Demo',
                'email'           => 'operador@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'operador_turistico',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 2,
                'organizacion_id' => 1,
                'nombre'          => 'Proveedor',
                'apellido'        => 'Demo',
                'email'           => 'proveedor@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'proveedor',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 3,
                'organizacion_id' => 1,
                'nombre'          => 'Admin',
                'apellido'        => 'Demo',
                'email'           => 'admin@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'administrador',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 4,
                'organizacion_id' => null,
                'nombre'          => 'Turista',
                'apellido'        => 'Demo',
                'email'           => 'turista@tourismcloud.test',
                'password'        => Hash::make('password'),
                'rol'             => 'turista',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
        $this->command->line('  ✓ Usuarios de prueba insertados (operador ID=1, proveedor ID=2, admin ID=3, turista ID=4).');

        // ─── 4. Destinos de prueba ───────────────────────────────────────────
        DB::table('destinos')->insertOrIgnore([
            [
                'id'              => 1,
                'organizacion_id' => 1,
                'operador_id'     => 1,
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
                'operador_id'     => 1,
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

        // ─── 5. Atractivo de muestra ─────────────────────────────────────────
        DB::table('atractivos')->insertOrIgnore([
            [
                'id'                    => 1,
                'destino_id'            => 1,
                'nombre'                => 'Machu Picchu',
                'descripcion'           => 'Ciudadela inca del siglo XV ubicada en las montañas de los Andes.',
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

        // ─── 6. Establecimiento de muestra ───────────────────────────────────
        DB::table('establecimientos')->insertOrIgnore([
            [
                'id'             => 1,
                'destino_id'     => 1,
                'proveedor_id'   => 2, // proveedor demo
                'tipo'           => 'hotel',
                'nombre'         => 'Belmond Sanctuary Lodge',
                'descripcion'    => 'El único hotel ubicado junto a Machu Picchu, con vistas privilegiadas.',
                'direccion'      => 'Machu Picchu, Cusco, Perú',
                'latitud'        => -13.1639,
                'longitud'       => -72.5449,
                'rango_precio'   => 'lujo',
                'imagen_portada' => null,
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
        ]);
        DB::table('establecimiento_categoria')->insertOrIgnore([
            ['establecimiento_id' => 1, 'categoria_id' => 5], // Relax y bienestar
        ]);
        $this->command->line('  ✓ Establecimiento de muestra insertado (Belmond ID=1).');

        // ─── 7. Horarios de muestra en tabla 'horarios' ──────────────────────
        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        foreach ($diasSemana as $dia) {
            DB::table('horarios')->insertOrIgnore([
                'establecimiento_id' => null,
                'atractivo_id'       => 1,
                'dia'                => $dia,
                'hora_inicio'        => '06:00:00',
                'hora_fin'           => '17:30:00',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            DB::table('horarios')->insertOrIgnore([
                'establecimiento_id' => 1,
                'atractivo_id'       => null,
                'dia'                => $dia,
                'hora_inicio'        => '00:00:00',
                'hora_fin'           => '23:59:00',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
        $this->command->line('  ✓ Horarios de muestra insertados en la tabla horarios.');

        // ─── 8. Sincronizar secuencias de PostgreSQL ─────────────────────────
        $tablasConSecuencia = [
            'organizaciones',
            'categorias_interes',
            'destinos',
            'usuarios',
            'atractivos',
            'establecimientos',
            'horarios',
            'imagenes',
            'actividades',
            'resenas',
            'rutas',
            'ruta_paradas',
            'servicios_turisticos',
            'preferencias_turista',
            'itinerarios',
            'itinerario_dias',
            'itinerario_items',
            'itinerario_servicios',
            'solicitudes_ia',
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
                ['operador_turistico', 'operador@tourismcloud.test',  'password', 'Panel: /operador/atractivos?_operador_id_test=1'],
                ['proveedor',          'proveedor@tourismcloud.test', 'password', 'Panel: /proveedor/establecimientos?_proveedor_id_test=2'],
                ['administrador',      'admin@tourismcloud.test',     'password', 'Administrador'],
                ['turista',            'turista@tourismcloud.test',   'password', 'Turista'],
            ]
        );
    }
}
