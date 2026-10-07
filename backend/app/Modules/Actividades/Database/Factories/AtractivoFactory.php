<?php

namespace App\Modules\Actividades\Database\Factories;

use App\Models\Atractivo;
use App\Models\Destino;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtractivoFactory extends Factory
{
    protected $model = Atractivo::class;

    public function definition(): array
    {
        return [
            'destino_id' => DestinoFactory::new(),
            'nombre' => fake()->words(3, true),
            'descripcion' => 'Descripción de prueba, no es información de producción.',
            'costo_entrada' => null,
            'estado' => 'activo',
            'estado_verificacion' => 'aprobado',
            'fuente_url' => 'https://example.org/lugar',
            'fecha_verificacion' => now(),
        ];
    }
}
