<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValoracionesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌟 Sembrando valoraciones y comentarios de prueba...');

        $valoraciones = [
            // ── Cañón del Colca (Atractivo ID 5) ────────────────────────
            [
                'usuario_id'        => 4,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 5,
                'puntuacion'        => 5,
                'comentario'        => 'Un lugar impresionante. Los cóndores se aprecian con una majestuosidad única en las primeras horas de la mañana desde la Cruz del Cóndor. Muy recomendado llevar abrigo y una buena cámara.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(6),
                'updated_at'        => Carbon::now()->subDays(6),
            ],
            [
                'usuario_id'        => 6,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 5,
                'puntuacion'        => 4,
                'comentario'        => 'El paisaje y la profundidad geológica del cañón son imponentes. El trayecto desde Chivay tiene curvas pronunciadas pero la vista de los volcanes compensa todo el viaje.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(3),
                'updated_at'        => Carbon::now()->subDays(3),
            ],
            [
                'usuario_id'        => 5,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 5,
                'puntuacion'        => 5,
                'comentario'        => 'Una experiencia inolvidable en Arequipa. Pudimos ver más de cuatro cóndores planeando a pocos metros. Los pobladores locales de Yanque y Maca son muy amables.',
                'visita_verificada' => false,
                'created_at'        => Carbon::now()->subDays(1),
                'updated_at'        => Carbon::now()->subDays(1),
            ],

            // ── Machu Picchu (Atractivo ID 1) ───────────────────────────
            [
                'usuario_id'        => 4,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 1,
                'puntuacion'        => 5,
                'comentario'        => 'Una de las 7 maravillas con justa razón. Espectacular misticismo, conservación y energía. Recomiendo el Circuito 2 para fotos panorámicas.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(10),
                'updated_at'        => Carbon::now()->subDays(10),
            ],
            [
                'usuario_id'        => 6,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 1,
                'puntuacion'        => 5,
                'comentario'        => 'Visita mágica. Llegar temprano por la mañana para ver cómo se disipa la niebla sobre la ciudadela inca es una experiencia que recordarás toda la vida.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(5),
                'updated_at'        => Carbon::now()->subDays(5),
            ],
            [
                'usuario_id'        => 5,
                'valorable_type'    => 'atractivo',
                'valorable_id'      => 1,
                'puntuacion'        => 4,
                'comentario'        => 'Increíble arquitectura e historia. Contratar un guía local acreditado en la entrada marca una diferencia gigante en la experiencia.',
                'visita_verificada' => false,
                'created_at'        => Carbon::now()->subDays(2),
                'updated_at'        => Carbon::now()->subDays(2),
            ],

            // ── Chicha por Gastón Acurio (Establecimiento ID 2) ─────────
            [
                'usuario_id'        => 4,
                'valorable_type'    => 'establecimiento',
                'valorable_id'      => 2,
                'puntuacion'        => 5,
                'comentario'        => 'La mejor gastronomía cuzqueña con el toque de autor de Gastón. El lechón confitado y el rocoto relleno estaban sublimes. Atención de primer nivel.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(4),
                'updated_at'        => Carbon::now()->subDays(4),
            ],
            [
                'usuario_id'        => 5,
                'valorable_type'    => 'establecimiento',
                'valorable_id'      => 2,
                'puntuacion'        => 4,
                'comentario'        => 'Muy buena comida y ambiente colonial frente a la Plaza Regocijo. Se recomienda reservar con al menos un día de anticipación.',
                'visita_verificada' => false,
                'created_at'        => Carbon::now()->subDays(2),
                'updated_at'        => Carbon::now()->subDays(2),
            ],

            // ── Hotel Colca Lodge Spa (Establecimiento ID 4) ────────────
            [
                'usuario_id'        => 6,
                'valorable_type'    => 'establecimiento',
                'valorable_id'      => 4,
                'puntuacion'        => 5,
                'comentario'        => 'Las pozas termales privadas al costado del río Colca son un paraíso total. Ideal para desconectarse y descansar después de las caminatas.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(7),
                'updated_at'        => Carbon::now()->subDays(7),
            ],
            [
                'usuario_id'        => 4,
                'valorable_type'    => 'establecimiento',
                'valorable_id'      => 4,
                'puntuacion'        => 4,
                'comentario'        => 'Hermoso hotel ecológico integrado con la naturaleza. Desayuno buffet variado y personal muy atento.',
                'visita_verificada' => true,
                'created_at'        => Carbon::now()->subDays(3),
                'updated_at'        => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($valoraciones as $val) {
            DB::table('valoraciones')->updateOrInsert(
                [
                    'usuario_id'     => $val['usuario_id'],
                    'valorable_type' => $val['valorable_type'],
                    'valorable_id'   => $val['valorable_id'],
                ],
                $val
            );
        }

        // Sincronizar secuencia en PostgreSQL
        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('valoraciones', 'id'),
                COALESCE((SELECT MAX(id) FROM valoraciones), 1)
            )
        ");

        $this->command->info('✅ ValoracionesDemoSeeder completado: ' . count($valoraciones) . ' reseñas sembradas.');
    }
}
