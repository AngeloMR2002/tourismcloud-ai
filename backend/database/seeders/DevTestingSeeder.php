<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * =====================================================================
 * DevTestingSeeder — DATOS COMPLETOS DE PRUEBA PARA TODOS LOS MÓDULOS
 * =====================================================================
 *
 * Basado al 100% en el schema de la BD PostgreSQL v2 (22 tablas).
 * Inserta y actualiza datos integrales para probar:
 *   1. Organizaciones
 *   2. Categorías de Interés
 *   3. Usuarios con todos los roles (Admin, Operador, Proveedor, Turista)
 *   4. Destinos Turísticos
 *   5. Atractivos Turísticos y sus Categorías
 *   6. Establecimientos (Hoteles, Restaurantes, Cafés) y sus Categorías
 *   7. Horarios de Atención (Atractivos y Establecimientos)
 *   8. Imágenes de Catálogo
 *   9. Actividades Turísticas (Trekking, Canotaje, Gastronomía, etc.)
 *  10. Rutas Turísticas y Paradas de Ruta
 *  11. Servicios Turísticos (Guías, Transporte, Equipamiento, etc.)
 *  12. Reseñas y Calificaciones de Turistas
 *  13. Preferencias de Turistas y Categorías Preferidas
 *  14. Itinerarios (Maestro-Detalle), Días, Paradas/Items y Servicios
 *  15. Solicitudes y Respuestas de Inteligencia Artificial (IA)
 *  16. Sincronización de secuencias PostgreSQL (setval)
 *
 * EJECUCIÓN:
 *   php artisan db:seed
 *   o
 *   php artisan db:seed --class=DevTestingSeeder
 * =====================================================================
 */
class DevTestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 DevTestingSeeder: sembrando datos integrales para TODOS los módulos...');

        // ─── 1. Organizaciones ───────────────────────────────────────────────
        DB::table('organizaciones')->upsert([
            [
                'id'          => 1,
                'nombre'      => 'Organización Principal',
                'tipo'        => 'empresa_turismo',
                'descripcion' => 'Organización principal de prueba para desarrollo local.',
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 2,
                'nombre'      => 'Inka Expeditions Corp',
                'tipo'        => 'agencia_viajes',
                'descripcion' => 'Operadora especializada en rutas andinas y turismo de aventura.',
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 3,
                'nombre'      => 'Arequipa Gastro & Tours SAC',
                'tipo'        => 'proveedor_servicios',
                'descripcion' => 'Consorcio gastronómico y hotelero del sur del Perú.',
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ], ['id'], ['nombre', 'tipo', 'descripcion', 'estado', 'updated_at']);
        $this->command->line('  ✓ 1. Organizaciones sembradas.');

        // ─── 2. Categorías de interés ────────────────────────────────────────
        DB::table('categorias_interes')->upsert([
            ['id' => 1, 'nombre' => 'Naturaleza y ecoturismo', 'descripcion' => 'Actividades y atractivos al aire libre, montañas y reservas.'],
            ['id' => 2, 'nombre' => 'Historia y cultura',     'descripcion' => 'Sitios arqueológicos, monumentos, templos y museos.'],
            ['id' => 3, 'nombre' => 'Gastronomía',            'descripcion' => 'Restaurantes típicos, picanterías y experiencias culinarias.'],
            ['id' => 4, 'nombre' => 'Aventura y deportes',    'descripcion' => 'Trekking, canotaje, rafting y deportes extremos.'],
            ['id' => 5, 'nombre' => 'Relax y bienestar',       'descripcion' => 'Spas, aguas termales, hoteles boutique y descanso.'],
        ], ['id'], ['nombre', 'descripcion']);
        $this->command->line('  ✓ 2. Categorías de interés sembradas.');

        // ─── 3. Usuarios de prueba ───────────────────────────────────────────
        $passwordHash = Hash::make('password');
        DB::table('usuarios')->upsert([
            [
                'id'              => 1,
                'organizacion_id' => 1,
                'nombre'          => 'Operador',
                'apellido'        => 'Demo',
                'email'           => 'operador@tourismcloud.test',
                'password'        => $passwordHash,
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
                'password'        => $passwordHash,
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
                'password'        => $passwordHash,
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
                'password'        => $passwordHash,
                'rol'             => 'turista',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 6,
                'organizacion_id' => null,
                'nombre'          => 'Sofía',
                'apellido'        => 'Morales',
                'email'           => 'sofia@tourismcloud.test',
                'password'        => $passwordHash,
                'rol'             => 'turista',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 7,
                'organizacion_id' => 2,
                'nombre'          => 'Carlos',
                'apellido'        => 'Guía Andino',
                'email'           => 'carlos.guia@tourismcloud.test',
                'password'        => $passwordHash,
                'rol'             => 'operador_turistico',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 8,
                'organizacion_id' => 3,
                'nombre'          => 'Elena',
                'apellido'        => 'Restauradora',
                'email'           => 'elena.proveedor@tourismcloud.test',
                'password'        => $passwordHash,
                'rol'             => 'proveedor',
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ], ['id'], ['nombre', 'apellido', 'email', 'rol', 'estado', 'updated_at']);
        $this->command->line('  ✓ 3. Usuarios de prueba sembrados.');

        // ─── 4. Destinos de prueba ───────────────────────────────────────────
        DB::table('destinos')->upsert([
            [
                'id'              => 1,
                'organizacion_id' => 1,
                'operador_id'     => 1,
                'nombre'          => 'Cusco',
                'descripcion'     => 'Capital histórica del Imperio Inca, declarada Patrimonio de la Humanidad por la UNESCO.',
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
                'descripcion'     => 'La Ciudad Blanca, custodiada por volcanes y famosa por su arquitectura en piedra de sillar.',
                'pais'            => 'Perú',
                'region'          => 'Arequipa',
                'ciudad'          => 'Arequipa',
                'latitud'         => -16.4090474,
                'longitud'        => -71.5374500,
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 3,
                'organizacion_id' => 2,
                'operador_id'     => 7,
                'nombre'          => 'Puno',
                'descripcion'     => 'Cuna de la civilización andina, a orillas del mítico y navegable Lago Titicaca.',
                'pais'            => 'Perú',
                'region'          => 'Puno',
                'ciudad'          => 'Puno',
                'latitud'         => -15.8402218,
                'longitud'        => -70.0218805,
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id'              => 4,
                'organizacion_id' => 1,
                'operador_id'     => 1,
                'nombre'          => 'Lima',
                'descripcion'     => 'La Ciudad de los Reyes, capital gastronómica de América y metrópoli frente al Pacífico.',
                'pais'            => 'Perú',
                'region'          => 'Lima',
                'ciudad'          => 'Lima',
                'latitud'         => -12.0463740,
                'longitud'        => -77.0427934,
                'estado'          => 'activo',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ], ['id'], ['nombre', 'descripcion', 'pais', 'region', 'ciudad', 'latitud', 'longitud', 'estado', 'updated_at']);
        $this->command->line('  ✓ 4. Destinos sembrados (Cusco, Arequipa, Puno, Lima).');

        // ─── 5. Atractivos de prueba ─────────────────────────────────────────
        DB::table('atractivos')->upsert([
            [
                'id'                    => 1,
                'destino_id'            => 1,
                'nombre'                => 'Machu Picchu',
                'descripcion'           => 'Ciudadela inca del siglo XV enclavada en las montañas andinas, maravilla del mundo moderno.',
                'costo_entrada'         => 152.00,
                'duracion_estimada_min' => 240,
                'latitud'               => -13.1631412,
                'longitud'              => -72.5449629,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 2,
                'destino_id'            => 1,
                'nombre'                => 'Fortaleza de Sacsayhuamán',
                'descripcion'           => 'Imponente complejo ceremonial inca construido con megalitos ciclópeos que custodian Cusco.',
                'costo_entrada'         => 70.00,
                'duracion_estimada_min' => 120,
                'latitud'               => -13.5048800,
                'longitud'              => -71.9818800,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1589802829985-817e51171b92?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 3,
                'destino_id'            => 1,
                'nombre'                => 'Montaña de 7 Colores (Vinicunca)',
                'descripcion'           => 'Maravilla geológica a más de 5,000 msnm con franjas de tonalidades turquesa, dorado y fucsia.',
                'costo_entrada'         => 50.00,
                'duracion_estimada_min' => 360,
                'latitud'               => -13.8694000,
                'longitud'              => -71.3029000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1580619305218-8423a7ef79b4?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 4,
                'destino_id'            => 1,
                'nombre'                => 'Ollantaytambo y Valle Sagrado',
                'descripcion'           => 'Pueblo inca viviente y fortaleza ceremonial con terrazas agrícolas colosales.',
                'costo_entrada'         => 70.00,
                'duracion_estimada_min' => 180,
                'latitud'               => -13.2586000,
                'longitud'              => -72.2633000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 5,
                'destino_id'            => 2,
                'nombre'                => 'Cañón del Colca',
                'descripcion'           => 'Uno de los cañones más profundos del planeta, hábitat del majestuoso cóndor andino.',
                'costo_entrada'         => 70.00,
                'duracion_estimada_min' => 480,
                'latitud'               => -15.6000000,
                'longitud'              => -71.9000000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 6,
                'destino_id'            => 2,
                'nombre'                => 'Monasterio de Santa Catalina',
                'descripcion'           => 'Ciudadela religiosa del siglo XVI de claustros coloniales, patios empedrados y paredes de sillar azul y naranja.',
                'costo_entrada'         => 45.00,
                'duracion_estimada_min' => 120,
                'latitud'               => -16.3952000,
                'longitud'              => -71.5368000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 7,
                'destino_id'            => 3,
                'nombre'                => 'Islas Flotantes de los Uros',
                'descripcion'           => 'Comunidad ancestral que habita sobre plataformas tejidas a mano con totora sobre el Lago Titicaca.',
                'costo_entrada'         => 25.00,
                'duracion_estimada_min' => 180,
                'latitud'               => -15.8200000,
                'longitud'              => -69.9700000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 8,
                'destino_id'            => 4,
                'nombre'                => 'Huaca Pucllana',
                'descripcion'           => 'Gran pirámide ceremonial prehispánica de la cultura Lima en pleno corazón de Miraflores.',
                'costo_entrada'         => 18.00,
                'duracion_estimada_min' => 90,
                'latitud'               => -12.1111000,
                'longitud'              => -77.0336000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1531968455001-5c5272a41129?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
        ], ['id'], ['destino_id', 'nombre', 'descripcion', 'costo_entrada', 'duracion_estimada_min', 'latitud', 'longitud', 'imagen_portada', 'estado', 'updated_at']);

        // Categorías asociadas a los atractivos
        DB::table('atractivo_categoria')->upsert([
            ['atractivo_id' => 1, 'categoria_id' => 2], // Machu Picchu: Historia
            ['atractivo_id' => 1, 'categoria_id' => 4], // Machu Picchu: Aventura
            ['atractivo_id' => 2, 'categoria_id' => 2], // Sacsayhuamán: Historia
            ['atractivo_id' => 2, 'categoria_id' => 1], // Sacsayhuamán: Naturaleza
            ['atractivo_id' => 3, 'categoria_id' => 1], // Vinicunca: Naturaleza
            ['atractivo_id' => 3, 'categoria_id' => 4], // Vinicunca: Aventura
            ['atractivo_id' => 4, 'categoria_id' => 2], // Ollantaytambo: Historia
            ['atractivo_id' => 5, 'categoria_id' => 1], // Colca: Naturaleza
            ['atractivo_id' => 5, 'categoria_id' => 4], // Colca: Aventura
            ['atractivo_id' => 6, 'categoria_id' => 2], // Santa Catalina: Historia
            ['atractivo_id' => 7, 'categoria_id' => 1], // Uros: Naturaleza
            ['atractivo_id' => 7, 'categoria_id' => 2], // Uros: Cultura
            ['atractivo_id' => 8, 'categoria_id' => 2], // Pucllana: Historia
            ['atractivo_id' => 8, 'categoria_id' => 3], // Pucllana: Gastronomía
        ], ['atractivo_id', 'categoria_id'], ['categoria_id']);
        $this->command->line('  ✓ 5. Atractivos sembrados (8 atractivos con categorías).');

        // ─── 6. Establecimientos de prueba ───────────────────────────────────
        DB::table('establecimientos')->upsert([
            [
                'id'             => 1,
                'destino_id'     => 1,
                'proveedor_id'   => 2,
                'tipo'           => 'hotel',
                'nombre'         => 'Belmond Sanctuary Lodge',
                'descripcion'    => 'El único hotel ubicado a los pies de la ciudadela de Machu Picchu, con vistas privilegiadas.',
                'direccion'      => 'Carretera Hiram Bingham Km 7.5, Machu Picchu Pueblo',
                'latitud'        => -13.1639000,
                'longitud'       => -72.5449000,
                'rango_precio'   => 'Lujoso',
                'imagen_portada' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 2,
                'destino_id'     => 1,
                'proveedor_id'   => 2,
                'tipo'           => 'restaurante',
                'nombre'         => 'Chicha por Gastón Acurio - Cusco',
                'descripcion'    => 'Alta cocina cusqueña con insumos autóctonos del Valle Sagrado en una hermosa casona colonial.',
                'direccion'      => 'Plaza Regocijo 261, Centro Histórico, Cusco',
                'latitud'        => -13.5175000,
                'longitud'       => -71.9803000,
                'rango_precio'   => 'Medio',
                'imagen_portada' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 3,
                'destino_id'     => 1,
                'proveedor_id'   => 8,
                'tipo'           => 'cafeteria',
                'nombre'         => 'Café D\'Wasi Artesanal',
                'direccion'      => 'Calle Santa Catalina Ancha 366, Cusco',
                'descripcion'    => 'Café de especialidad de origen cusqueño galardonado internacionalmente.',
                'latitud'        => -13.5182000,
                'longitud'       => -71.9775000,
                'rango_precio'   => 'Bajo',
                'imagen_portada' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 4,
                'destino_id'     => 2,
                'proveedor_id'   => 2,
                'tipo'           => 'hotel',
                'nombre'         => 'Hotel Colca Lodge Spa & Hot Springs',
                'descripcion'    => 'Refugio rústico y exclusivo junto al río Colca con aguas termales privadas bajo las estrellas.',
                'direccion'      => 'Fundo Pumachiri s/n, Yanque, Cañón del Colca',
                'latitud'        => -15.6550000,
                'longitud'       => -71.6500000,
                'rango_precio'   => 'Lujoso',
                'imagen_portada' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 5,
                'destino_id'     => 2,
                'proveedor_id'   => 8,
                'tipo'           => 'restaurante',
                'nombre'         => 'Restaurante Zig Zag',
                'descripcion'    => 'Gastronomía andina a la piedra volcánica y cortes selectos en el centro histórico de Arequipa.',
                'direccion'      => 'Calle Zela 210, Arequipa',
                'latitud'        => -16.3948000,
                'longitud'       => -71.5360000,
                'rango_precio'   => 'Alto',
                'imagen_portada' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 6,
                'destino_id'     => 3,
                'proveedor_id'   => 2,
                'tipo'           => 'hotel',
                'nombre'         => 'GHL Hotel Lago Titicaca',
                'descripcion'    => 'Ubicado en la Isla Esteves con vistas panorámicas de 360 grados sobre el Lago Sagrado.',
                'direccion'      => 'Isla Esteves s/n, Bahía de Puno',
                'latitud'        => -15.8340000,
                'longitud'       => -69.9980000,
                'rango_precio'   => 'Lujoso',
                'imagen_portada' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
            [
                'id'             => 7,
                'destino_id'     => 4,
                'proveedor_id'   => 8,
                'tipo'           => 'restaurante',
                'nombre'         => 'La Mar Cebichería Peruana',
                'descripcion'    => 'Templo del cebiche clásico y pesca sustentable del día en Miraflores.',
                'direccion'      => 'Av. Mariscal La Mar 770, Miraflores, Lima',
                'latitud'        => -12.1170000,
                'longitud'       => -77.0450000,
                'rango_precio'   => 'Medio',
                'imagen_portada' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80',
                'estado'         => 'activo',
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ],
        ], ['id'], ['destino_id', 'proveedor_id', 'tipo', 'nombre', 'descripcion', 'direccion', 'latitud', 'longitud', 'rango_precio', 'imagen_portada', 'estado', 'updated_at']);

        // Categorías asociadas a los establecimientos
        DB::table('establecimiento_categoria')->upsert([
            ['establecimiento_id' => 1, 'categoria_id' => 5], // Belmond: Relax
            ['establecimiento_id' => 2, 'categoria_id' => 3], // Chicha: Gastronomía
            ['establecimiento_id' => 3, 'categoria_id' => 3], // D'Wasi: Gastronomía
            ['establecimiento_id' => 4, 'categoria_id' => 5], // Colca Lodge: Relax y Bienestar
            ['establecimiento_id' => 5, 'categoria_id' => 3], // Zig Zag: Gastronomía
            ['establecimiento_id' => 6, 'categoria_id' => 5], // GHL Titicaca: Relax
            ['establecimiento_id' => 7, 'categoria_id' => 3], // La Mar: Gastronomía
        ], ['establecimiento_id', 'categoria_id'], ['categoria_id']);
        $this->command->line('  ✓ 6. Establecimientos sembrados (7 establecimientos con categorías).');

        // ─── 7. Horarios de atención ─────────────────────────────────────────
        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        // Días de cierre por atractivo para pruebas reales de días cerrados
        $diasCerradosPorAtractivo = [
            2 => ['lunes'],   // Sacsayhuamán cerrado lunes por mantenimiento
            6 => ['lunes'],   // Monasterio Santa Catalina cerrado lunes
            7 => ['domingo'], // Islas Uros descanso comunal domingo
            8 => ['martes'],  // Huaca Pucllana cerrado martes
        ];

        // Limpiar horarios previos de prueba para refrescar con días cerrados
        DB::table('horarios')->delete();

        $horariosData = [];

        // Horarios para atractivos (1 al 8)
        foreach (range(1, 8) as $atractivoId) {
            $cerrados = $diasCerradosPorAtractivo[$atractivoId] ?? [];
            foreach ($diasSemana as $dia) {
                if (in_array($dia, $cerrados, true)) {
                    continue; // Día cerrado: no se crea registro de horario
                }
                $horariosData[] = [
                    'establecimiento_id' => null,
                    'atractivo_id'       => $atractivoId,
                    'dia'                => $dia,
                    'hora_inicio'        => '07:00:00',
                    'hora_fin'           => '17:30:00',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }

        // Horarios para establecimientos (1 al 7)
        foreach (range(1, 7) as $estabId) {
            foreach ($diasSemana as $dia) {
                $horariosData[] = [
                    'establecimiento_id' => $estabId,
                    'atractivo_id'       => null,
                    'dia'                => $dia,
                    'hora_inicio'        => '07:00:00',
                    'hora_fin'           => '23:00:00',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }
        DB::table('horarios')->insert($horariosData);
        $this->command->line('  ✓ 7. Horarios de atención sembrados (con días abiertos y cerrados para QA).');

        // ─── 8. Imágenes de Catálogo ─────────────────────────────────────────
        DB::table('imagenes')->upsert([
            ['id' => 1, 'entidad_tipo' => 'destino', 'entidad_id' => 1, 'url' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Panorámica de la ciudad imperial del Cusco', 'created_at' => now()],
            ['id' => 2, 'entidad_tipo' => 'destino', 'entidad_id' => 2, 'url' => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Arequipa y Volcán Misti al fondo', 'created_at' => now()],
            ['id' => 3, 'entidad_tipo' => 'atractivo', 'entidad_id' => 1, 'url' => 'https://images.unsplash.com/photo-1509299349698-dd22323b5963?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Machu Picchu al amanecer', 'created_at' => now()],
            ['id' => 4, 'entidad_tipo' => 'atractivo', 'entidad_id' => 3, 'url' => 'https://images.unsplash.com/photo-1580619305218-8423a7ef79b4?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Cumbre de la Montaña Vinicunca', 'created_at' => now()],
            ['id' => 5, 'entidad_tipo' => 'establecimiento', 'entidad_id' => 1, 'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Instalaciones exteriores de Belmond Sanctuary Lodge', 'created_at' => now()],
            ['id' => 6, 'entidad_tipo' => 'establecimiento', 'entidad_id' => 2, 'url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80', 'orden' => 1, 'alt_text' => 'Salón principal Chicha Gastón Acurio', 'created_at' => now()],
        ], ['id'], ['url', 'orden', 'alt_text']);
        $this->command->line('  ✓ 8. Galería de imágenes sembrada.');

        // ─── 9. Actividades Turísticas ───────────────────────────────────────
        DB::table('actividades')->upsert([
            [
                'id'                    => 1,
                'destino_id'            => 1,
                'nombre'                => 'Trekking de Altura a la Laguna Humantay',
                'descripcion'           => 'Caminata guiada hacia la sobrecogedora laguna de aguas turquesas al pie del glaciar Salkantay.',
                'costo'                 => 95.00,
                'duracion_estimada_min' => 480,
                'latitud'               => -13.4116000,
                'longitud'              => -72.5855000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 2,
                'destino_id'            => 1,
                'nombre'                => 'Canotaje y Rafting en el Río Urubamba',
                'descripcion'           => 'Adrenalina en rápidos de clase III y IV a través del cañón sagrado con equipo certificado.',
                'costo'                 => 110.00,
                'duracion_estimada_min' => 300,
                'latitud'               => -13.2900000,
                'longitud'              => -72.1500000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1530866495561-507c9faab2ed?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 3,
                'destino_id'            => 2,
                'nombre'                => 'Tour Gastronómico y Taller de Pisco Sour',
                'descripcion'           => 'Recorrido por el Mercado San Camilo y clase magistral de coctelería y ceviche arequipeño.',
                'costo'                 => 65.00,
                'duracion_estimada_min' => 180,
                'latitud'               => -16.4010000,
                'longitud'              => -71.5340000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 4,
                'destino_id'            => 2,
                'nombre'                => 'Avistamiento de Cóndores en Cruz del Cóndor',
                'descripcion'           => 'Observación al alba del planeo cercano de cóndores andinos sobre el abismo del cañón.',
                'costo'                 => 50.00,
                'duracion_estimada_min' => 240,
                'latitud'               => -15.6150000,
                'longitud'              => -71.9050000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 5,
                'destino_id'            => 3,
                'nombre'                => 'Paseo en Kayak al Amanecer en Lago Titicaca',
                'descripcion'           => 'Remo suave entre totorales apreciando las aves nativas y la tranquilidad matutina del lago.',
                'costo'                 => 45.00,
                'duracion_estimada_min' => 180,
                'latitud'               => -15.8300000,
                'longitud'              => -69.9800000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
            [
                'id'                    => 6,
                'destino_id'            => 4,
                'nombre'                => 'Vuelo en Parapente sobre Costa Verde',
                'descripcion'           => 'Vuelo biplaza con instructor certificado sobrevolando los acantilados de Miraflores.',
                'costo'                 => 80.00,
                'duracion_estimada_min' => 60,
                'latitud'               => -12.1250000,
                'longitud'              => -77.0370000,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ],
        ], ['id'], ['destino_id', 'nombre', 'descripcion', 'costo', 'duracion_estimada_min', 'latitud', 'longitud', 'imagen_portada', 'estado', 'updated_at']);
        $this->command->line('  ✓ 9. Actividades turísticas sembradas (6 actividades).');

        // ─── 10. Rutas Turísticas y Paradas ──────────────────────────────────
        DB::table('rutas')->upsert([
            [
                'id'                    => 1,
                'destino_id'            => 1,
                'nombre'                => 'Gran Circuito Arqueológico del Valle Sagrado',
                'descripcion'           => 'Ruta integral que conecta los sitios militares, agrícolas y religiosos cumbres del imperio.',
                'distancia_km'          => 65.50,
                'duracion_estimada_min' => 480,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1589802829985-817e51171b92?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'id'                    => 2,
                'destino_id'            => 2,
                'nombre'                => 'Ruta Geotermal y Paisajística del Cañón del Colca',
                'descripcion'           => 'Travesía desde los miradores de volcanes hasta las profundidades del cañón y baños termales.',
                'distancia_km'          => 145.00,
                'duracion_estimada_min' => 600,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'id'                    => 3,
                'destino_id'            => 3,
                'nombre'                => 'Travesía Sagrada de las Islas del Titicaca',
                'descripcion'           => 'Navegación tradicional visitando comunidades originarias de las islas flotantes.',
                'distancia_km'          => 28.00,
                'duracion_estimada_min' => 360,
                'imagen_portada'        => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ], ['id'], ['destino_id', 'nombre', 'descripcion', 'distancia_km', 'duracion_estimada_min', 'imagen_portada', 'estado', 'updated_at']);

        // Paradas de ruta (UQ: ruta_id, orden)
        DB::table('ruta_paradas')->upsert([
            ['id' => 1, 'ruta_id' => 1, 'atractivo_id' => 2, 'orden' => 1], // Sacsayhuamán
            ['id' => 2, 'ruta_id' => 1, 'atractivo_id' => 4, 'orden' => 2], // Ollantaytambo
            ['id' => 3, 'ruta_id' => 1, 'atractivo_id' => 1, 'orden' => 3], // Machu Picchu
            ['id' => 4, 'ruta_id' => 2, 'atractivo_id' => 6, 'orden' => 1], // Santa Catalina
            ['id' => 5, 'ruta_id' => 2, 'atractivo_id' => 5, 'orden' => 2], // Cañón del Colca
            ['id' => 6, 'ruta_id' => 3, 'atractivo_id' => 7, 'orden' => 1], // Islas Uros
        ], ['ruta_id', 'orden'], ['atractivo_id']);
        $this->command->line('  ✓ 10. Rutas turísticas y paradas sembradas.');

        // ─── 11. Servicios Turísticos ────────────────────────────────────────
        DB::table('servicios_turisticos')->upsert([
            [
                'id'          => 1,
                'destino_id'  => 1,
                'nombre'      => 'Guía Oficial de Turismo Bilingüe (Español/Inglés)',
                'tipo'        => 'guia',
                'descripcion' => 'Acompañamiento especializado en arqueología andina durante toda la jornada.',
                'costo'       => 160.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 2,
                'destino_id'  => 1,
                'nombre'      => 'Transporte Privado VIP en Mercedes Sprinter',
                'tipo'        => 'transporte',
                'descripcion' => 'Traslados con chofer profesional, aire acondicionado y seguro contra todo riesgo.',
                'costo'       => 120.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 3,
                'destino_id'  => 1,
                'nombre'      => 'Alquiler de Bastones y Equipo Técnico de Trekking',
                'tipo'        => 'equipo',
                'descripcion' => 'Juego de bastones de carbono, ponchos térmicos y mochila técnica de montaña.',
                'costo'       => 30.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 4,
                'destino_id'  => 2,
                'nombre'      => 'Circuito Termal y Sesión de Masoterapia Andina',
                'tipo'        => 'bienestar',
                'descripcion' => 'Acceso a pozas termales medicinales con masaje descontracturante con piedras calientes.',
                'costo'       => 75.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 5,
                'destino_id'  => 1,
                'nombre'      => 'Almuerzo Campestre Tradicional en Hacienda',
                'tipo'        => 'alimentacion',
                'descripcion' => 'Buffet andino gourmet con show de caballos de paso peruanos.',
                'costo'       => 55.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 6,
                'destino_id'  => 3,
                'nombre'      => 'Traslado Náutico Privado en Lancha Rápida Titicaca',
                'tipo'        => 'transporte',
                'descripcion' => 'Navegación segura y rápida con chalecos salvavidas y capitán experimentado.',
                'costo'       => 40.00,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ], ['id'], ['destino_id', 'nombre', 'tipo', 'descripcion', 'costo', 'estado', 'updated_at']);
        $this->command->line('  ✓ 11. Servicios turísticos sembrados (6 servicios).');

        // ─── 12. Reseñas y Calificaciones ────────────────────────────────────
        DB::table('resenas')->upsert([
            [
                'id'           => 1,
                'usuario_id'   => 4,
                'entidad_tipo' => 'atractivo',
                'entidad_id'   => 1, // Machu Picchu
                'calificacion' => 5,
                'comentario'   => 'Una experiencia transformadora. La energía y belleza arquitectónica no se comparan con nada.',
                'created_at'   => now()->subDays(5),
                'updated_at'   => now()->subDays(5),
            ],
            [
                'id'           => 2,
                'usuario_id'   => 4,
                'entidad_tipo' => 'establecimiento',
                'entidad_id'   => 2, // Chicha
                'calificacion' => 5,
                'comentario'   => 'El lomo saltado y los postres autóctonos superaron nuestras expectativas. Servicio impecable.',
                'created_at'   => now()->subDays(4),
                'updated_at'   => now()->subDays(4),
            ],
            [
                'id'           => 3,
                'usuario_id'   => 6,
                'entidad_tipo' => 'atractivo',
                'entidad_id'   => 5, // Cañón del Colca
                'calificacion' => 5,
                'comentario'   => 'Ver volar a los cóndores tan cerca en el Colca es un espectáculo único en la vida.',
                'created_at'   => now()->subDays(3),
                'updated_at'   => now()->subDays(3),
            ],
            [
                'id'           => 4,
                'usuario_id'   => 6,
                'entidad_tipo' => 'destino',
                'entidad_id'   => 1, // Cusco
                'calificacion' => 5,
                'comentario'   => 'Cusco es una ciudad mágica, segura y llena de vida tanto de día como de noche.',
                'created_at'   => now()->subDays(2),
                'updated_at'   => now()->subDays(2),
            ],
        ], ['id'], ['calificacion', 'comentario', 'updated_at']);
        $this->command->line('  ✓ 12. Reseñas y calificaciones sembradas.');

        // ─── 13. Preferencias de Turistas ────────────────────────────────────
        // Turista 4 (turista@tourismcloud.test)
        DB::table('preferencias_turista')->updateOrInsert(
            ['turista_id' => 4],
            [
                'presupuesto_max'       => 2800.00,
                'dias_disponibles'      => 5,
                'hora_inicio_preferida' => '07:30:00',
                'hora_fin_preferida'    => '21:00:00',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]
        );
        $prefTurista4Id = DB::table('preferencias_turista')->where('turista_id', 4)->value('id');

        // Turista 6 (sofia@tourismcloud.test)
        DB::table('preferencias_turista')->updateOrInsert(
            ['turista_id' => 6],
            [
                'presupuesto_max'       => 1500.00,
                'dias_disponibles'      => 3,
                'hora_inicio_preferida' => '08:00:00',
                'hora_fin_preferida'    => '19:30:00',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]
        );
        $prefTurista6Id = DB::table('preferencias_turista')->where('turista_id', 6)->value('id');

        if ($prefTurista4Id) {
            DB::table('preferencia_categoria')->upsert([
                ['preferencia_id' => $prefTurista4Id, 'categoria_id' => 1], // Naturaleza
                ['preferencia_id' => $prefTurista4Id, 'categoria_id' => 2], // Historia
                ['preferencia_id' => $prefTurista4Id, 'categoria_id' => 3], // Gastronomía
            ], ['preferencia_id', 'categoria_id'], ['categoria_id']);
        }

        if ($prefTurista6Id) {
            DB::table('preferencia_categoria')->upsert([
                ['preferencia_id' => $prefTurista6Id, 'categoria_id' => 1], // Naturaleza
                ['preferencia_id' => $prefTurista6Id, 'categoria_id' => 4], // Aventura
            ], ['preferencia_id', 'categoria_id'], ['categoria_id']);
        }
        $this->command->line('  ✓ 13. Preferencias de turistas sembradas.');

        // ─── 14. Itinerarios (Maestro-Detalle), Agenda y Calendario ───────────
        $hoy = Carbon::today();
        $manana = Carbon::tomorrow();
        $pasadoManana = Carbon::today()->addDays(2);
        $proximaSemana = Carbon::today()->addDays(6);
        $proximaSemana2 = Carbon::today()->addDays(7);

        // Itinerario 10: Cusco Activo con paradas y servicios
        DB::table('itinerarios')->upsert([
            [
                'id'                => 10,
                'turista_id'        => 4,
                'destino_id'        => 1, // Cusco
                'preferencia_id'    => $prefTurista4Id,
                'nombre'            => 'Gran Expedición Imperio Inca & Machu Picchu',
                'descripcion'       => 'Itinerario insignia de 3 días con arqueología, gastronomía típica y trekking panorámico.',
                'presupuesto_total' => 840.00,
                'dias'              => 3,
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id'                => 11,
                'turista_id'        => 4,
                'destino_id'        => 2, // Arequipa
                'preferencia_id'    => $prefTurista4Id,
                'nombre'            => 'Maravillas del Cañón del Colca y Ruta Blanca',
                'descripcion'       => 'Descanso y termalismo en Arequipa junto al Monasterio Santa Catalina y el Cañón.',
                'presupuesto_total' => 520.00,
                'dias'              => 2,
                'estado'            => 'guardado',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id'                => 12,
                'turista_id'        => 6,
                'destino_id'        => 3, // Puno
                'preferencia_id'    => $prefTurista6Id,
                'nombre'            => 'Aventura Mística en el Lago Titicaca',
                'descripcion'       => 'Paseos en kayak, visita a comunidades nativas y descanso junto al lago sagrado.',
                'presupuesto_total' => 380.00,
                'dias'              => 2,
                'estado'            => 'generado',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ], ['id'], ['turista_id', 'destino_id', 'preferencia_id', 'nombre', 'descripcion', 'presupuesto_total', 'dias', 'estado', 'updated_at']);

        // Días del Itinerario 10 (con fechas para Agenda)
        DB::table('itinerario_dias')->upsert([
            ['id' => 101, 'itinerario_id' => 10, 'numero_dia' => 1, 'fecha' => $hoy->toDateString()],
            ['id' => 102, 'itinerario_id' => 10, 'numero_dia' => 2, 'fecha' => $manana->toDateString()],
            ['id' => 103, 'itinerario_id' => 10, 'numero_dia' => 3, 'fecha' => $pasadoManana->toDateString()],
            // Días del Itinerario 11
            ['id' => 104, 'itinerario_id' => 11, 'numero_dia' => 1, 'fecha' => $proximaSemana->toDateString()],
            ['id' => 105, 'itinerario_id' => 11, 'numero_dia' => 2, 'fecha' => $proximaSemana2->toDateString()],
            // Días del Itinerario 12
            ['id' => 106, 'itinerario_id' => 12, 'numero_dia' => 1, 'fecha' => $hoy->addDays(10)->toDateString()],
            ['id' => 107, 'itinerario_id' => 12, 'numero_dia' => 2, 'fecha' => $hoy->addDays(11)->toDateString()],
        ], ['id'], ['itinerario_id', 'numero_dia', 'fecha']);

        // Paradas / Items de los días
        DB::table('itinerario_items')->upsert([
            // Día 101 (Cusco)
            [
                'id'                => 1001,
                'itinerario_dia_id' => 101,
                'entidad_tipo'      => 'atractivo',
                'entidad_id'        => 2, // Sacsayhuamán
                'hora_inicio'       => '08:30:00',
                'hora_fin'          => '11:30:00',
                'orden'             => 1,
                'costo_estimado'    => 70.00,
            ],
            [
                'id'                => 1002,
                'itinerario_dia_id' => 101,
                'entidad_tipo'      => 'establecimiento',
                'entidad_id'        => 3, // Café D'Wasi
                'hora_inicio'       => '12:00:00',
                'hora_fin'          => '13:30:00',
                'orden'             => 2,
                'costo_estimado'    => 25.00,
            ],
            [
                'id'                => 1003,
                'itinerario_dia_id' => 101,
                'entidad_tipo'      => 'atractivo',
                'entidad_id'        => 4, // Ollantaytambo
                'hora_inicio'       => '14:30:00',
                'hora_fin'          => '18:00:00',
                'orden'             => 3,
                'costo_estimado'    => 70.00,
            ],
            // Día 102 (Machu Picchu)
            [
                'id'                => 1004,
                'itinerario_dia_id' => 102,
                'entidad_tipo'      => 'atractivo',
                'entidad_id'        => 1, // Machu Picchu
                'hora_inicio'       => '07:00:00',
                'hora_fin'          => '13:00:00',
                'orden'             => 1,
                'costo_estimado'    => 152.00,
            ],
            [
                'id'                => 1005,
                'itinerario_dia_id' => 102,
                'entidad_tipo'      => 'establecimiento',
                'entidad_id'        => 1, // Belmond Sanctuary Lodge
                'hora_inicio'       => '13:30:00',
                'hora_fin'          => '16:00:00',
                'orden'             => 2,
                'costo_estimado'    => 220.00,
            ],
            // Día 103 (Aventura y Gastronomía)
            [
                'id'                => 1006,
                'itinerario_dia_id' => 103,
                'entidad_tipo'      => 'actividad',
                'entidad_id'        => 1, // Trekking Humantay
                'hora_inicio'       => '05:30:00',
                'hora_fin'          => '14:30:00',
                'orden'             => 1,
                'costo_estimado'    => 95.00,
            ],
            [
                'id'                => 1007,
                'itinerario_dia_id' => 103,
                'entidad_tipo'      => 'establecimiento',
                'entidad_id'        => 2, // Chicha Gastón Acurio
                'hora_inicio'       => '19:00:00',
                'hora_fin'          => '21:30:00',
                'orden'             => 2,
                'costo_estimado'    => 120.00,
            ],
            // Día 104 (Arequipa)
            [
                'id'                => 1008,
                'itinerario_dia_id' => 104,
                'entidad_tipo'      => 'atractivo',
                'entidad_id'        => 6, // Santa Catalina
                'hora_inicio'       => '09:00:00',
                'hora_fin'          => '12:00:00',
                'orden'             => 1,
                'costo_estimado'    => 45.00,
            ],
            [
                'id'                => 1009,
                'itinerario_dia_id' => 104,
                'entidad_tipo'      => 'establecimiento',
                'entidad_id'        => 5, // Zig Zag
                'hora_inicio'       => '13:00:00',
                'hora_fin'          => '15:00:00',
                'orden'             => 2,
                'costo_estimado'    => 85.00,
            ],
        ], ['id'], ['itinerario_dia_id', 'entidad_tipo', 'entidad_id', 'hora_inicio', 'hora_fin', 'orden', 'costo_estimado']);

        // Servicios Turísticos del Itinerario
        DB::table('itinerario_servicios')->upsert([
            [
                'id'             => 1,
                'itinerario_id'  => 10,
                'servicio_id'    => 1, // Guía Oficial Bilingüe
                'cantidad'       => 1,
                'costo_estimado' => 160.00,
            ],
            [
                'id'             => 2,
                'itinerario_id'  => 10,
                'servicio_id'    => 2, // Transporte VIP
                'cantidad'       => 1,
                'costo_estimado' => 120.00,
            ],
            [
                'id'             => 3,
                'itinerario_id'  => 11,
                'servicio_id'    => 4, // Circuito Termal
                'cantidad'       => 1,
                'costo_estimado' => 75.00,
            ],
        ], ['id'], ['itinerario_id', 'servicio_id', 'cantidad', 'costo_estimado']);
        $this->command->line('  ✓ 14. Itinerarios, días, items y servicios de agenda sembrados.');

        // ─── 15. Solicitudes de Inteligencia Artificial (IA) ─────────────────
        DB::table('solicitudes_ia')->upsert([
            [
                'id'                 => 1,
                'usuario_id'         => 4,
                'itinerario_id'      => 10,
                'solicitud'          => 'Diseña un itinerario de 3 días en Cusco enfocado en historia inca, gastronomía local y una caminata de altura con presupuesto moderado.',
                'respuesta'          => '{"destino":"Cusco","dias":3,"paradas_sugeridas":["Sacsayhuamán","Machu Picchu","Laguna Humantay"],"recomendacion_gastronomica":"Chicha por Gastón Acurio"}',
                'proveedor'          => 'google',
                'modelo'             => 'gemini-1.5-flash',
                'tokens_entrada'     => 420,
                'tokens_salida'      => 680,
                'tiempo_respuesta_ms' => 1140,
                'estado'             => 'completada',
                'created_at'         => now()->subDays(2),
            ],
            [
                'id'                 => 2,
                'usuario_id'         => 6,
                'itinerario_id'      => 12,
                'solicitud'          => 'Planifica una escapada de fin de semana a Puno y el Lago Titicaca con foco en naturaleza y ecoturismo.',
                'respuesta'          => '{"destino":"Puno","dias":2,"paradas_sugeridas":["Islas Flotantes de los Uros","Kayak al amanecer"],"alojamiento_recomendado":"GHL Hotel Lago Titicaca"}',
                'proveedor'          => 'google',
                'modelo'             => 'gemini-1.5-flash',
                'tokens_entrada'     => 350,
                'tokens_salida'      => 540,
                'tiempo_respuesta_ms' => 980,
                'estado'             => 'completada',
                'created_at'         => now()->subDay(),
            ],
            [
                'id'                 => 3,
                'usuario_id'         => 4,
                'itinerario_id'      => 11,
                'solicitud'          => 'Recomiéndame los mejores atractivos y baños termales para visitar en Arequipa durante 2 días.',
                'respuesta'          => '{"destino":"Arequipa","dias":2,"paradas_sugeridas":["Santa Catalina","Cañón del Colca","Colca Lodge Spa"]}',
                'proveedor'          => 'anthropic',
                'modelo'             => 'claude-3-5-sonnet',
                'tokens_entrada'     => 390,
                'tokens_salida'      => 610,
                'tiempo_respuesta_ms' => 1350,
                'estado'             => 'completada',
                'created_at'         => now()->subHours(6),
            ],
        ], ['id'], ['usuario_id', 'itinerario_id', 'solicitud', 'respuesta', 'proveedor', 'modelo', 'tokens_entrada', 'tokens_salida', 'tiempo_respuesta_ms', 'estado']);
        $this->command->line('  ✓ 15. Métricas y solicitudes de Inteligencia Artificial sembradas.');

        // ─── 16. Sincronizar secuencias de PostgreSQL ────────────────────────
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
        $this->command->line('  ✓ 16. Secuencias de PostgreSQL sincronizadas (MAX id por tabla).');

        $this->command->newLine();
        $this->command->info('✅ DevTestingSeeder completado con éxito. Todos los módulos cuentan con datos de prueba.');
        $this->command->table(
            ['Rol', 'Email', 'Password', 'Acceso / Panel'],
            [
                ['administrador',      'admin@tourismcloud.test',            'password', 'Acceso completo a todos los paneles'],
                ['operador_turistico', 'operador@tourismcloud.test',         'password', 'Gestión de atractivos, actividades y rutas'],
                ['proveedor',          'proveedor@tourismcloud.test',        'password', 'Gestión de establecimientos y tendencias'],
                ['turista',            'turista@tourismcloud.test',          'password', 'Catálogo, itinerarios, agenda y preferencias'],
                ['turista',            'sofia@tourismcloud.test',            'password', 'Catálogo, itinerarios y agenda'],
                ['turista',            'lucaspelucasm2005@gmail.com',        '(existente)', 'Usuario turista registrado'],
            ]
        );
    }
}
