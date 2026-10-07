<?php

namespace App\Modules\Actividades\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiltroActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:activo,inactivo'],
            'destino_id' => ['nullable', 'integer'],
            'atractivo_id' => ['nullable', 'integer'],
            'categoria' => ['nullable', 'string', 'in:aventura,cultural,gastronomica,ecoturismo,relax,deportiva'],
            'nivel_dificultad' => ['nullable', 'string', 'in:baja,media,alta'],
            'precio_max' => ['nullable', 'numeric', 'min:0'],
            'duracion_max' => ['nullable', 'integer', 'min:0'],
            'orden' => ['nullable', 'string', 'in:recientes,precio_asc,precio_desc,duracion_asc,nombre_asc'],
        ];
    }
}
