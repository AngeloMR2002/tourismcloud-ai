<?php

namespace Database\Factories;

use App\Modules\Rutas\Models\Ruta;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Ruta> */
class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        return [
            'destino_id' => null,
            'nombre' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'distancia_km' => fake()->randomFloat(2, 0, 50),
            'duracion_estimada_min' => fake()->numberBetween(30, 480),
            'estado' => 'activo',
            'imagen_portada' => null,
        ];
    }
}
