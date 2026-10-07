<?php

namespace App\Modules\Actividades\Database\Factories;

use App\Models\Destino;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinoFactory extends Factory
{
    protected $model = Destino::class;

    public function definition(): array
    {
        return ['nombre' => fake()->unique()->city(), 'pais' => 'Perú', 'ciudad' => 'Nuevo Chimbote', 'region' => 'Áncash', 'estado' => 'activo'];
    }
}
