<?php

namespace App\Modules\Actividades\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiltroCatalogoActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'destino_id' => ['bail', 'nullable', 'integer', 'exists:destinos,id'],
            'costo_max' => ['nullable', 'numeric', 'min:0'],
            'duracion_max' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ];
    }
}
