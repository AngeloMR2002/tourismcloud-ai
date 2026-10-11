<?php

namespace Database\Factories;

use App\Modules\Actividades\Models\Actividad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Actividad>
 */
class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'destino_id' => null,
            'nombre' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'costo' => fake()->randomFloat(2, 0, 300),
            'duracion_estimada_min' => fake()->numberBetween(30, 480),
            'latitud' => fake()->latitude(-18, -1),
            'longitud' => fake()->longitude(-82, -68),
            'imagen_portada' => null,
            'estado' => 'activo',
        ];
    }
}
