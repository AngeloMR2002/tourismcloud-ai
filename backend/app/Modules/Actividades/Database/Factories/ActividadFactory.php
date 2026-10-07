<?php

namespace App\Modules\Actividades\Database\Factories;

use App\Models\Destino;
use App\Modules\Actividades\Models\Actividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition(): array
    {
        return [
            'destino_id' => DestinoFactory::new(),
            'nombre' => fake()->words(3, true),
            'descripcion' => 'Actividad de prueba, no se publica como información real.',
            'categoria' => 'cultural',
            'tipo_registro' => 'visita',
            'precio' => null,
            'tipo_precio' => 'variable',
            'duracion_min' => null,
            'cupo_maximo' => null,
            'nivel_dificultad' => null,
            'estado' => 'activo',
            'estado_verificacion' => 'aprobado',
            'fuente_url' => 'https://example.org/actividad',
            'fecha_verificacion' => now(),
        ];
    }
}
