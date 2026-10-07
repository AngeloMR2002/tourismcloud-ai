<?php

namespace App\Modules\Rutas\Database\Factories;

use App\Models\Destino;
use App\Modules\Actividades\Database\Factories\DestinoFactory;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Database\Eloquent\Factories\Factory;

class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        return [
            'destino_id' => DestinoFactory::new(),
            'nombre' => fake()->words(3, true),
            'descripcion' => 'Ruta de prueba, no se publica como información real.',
            'tipo_ruta' => 'lineal',
            'es_propuesta' => true,
            'transporte_recomendado' => 'a_pie',
            'duracion_estimada_horas' => null,
            'distancia_km' => null,
            'nivel_dificultad' => null,
            'costo_estimado' => null,
            'tipo_precio' => 'variable',
            'estado' => 'activo',
            'estado_verificacion' => 'aprobado',
            'fuente_url' => 'https://example.org/ruta',
            'fecha_verificacion' => now(),
        ];
    }
}
