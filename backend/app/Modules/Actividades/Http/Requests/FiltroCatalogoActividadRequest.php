<?php

namespace App\Modules\Actividades\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'costo_min' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'costo_max' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', Rule::when($this->filled('costo_min'), 'gte:costo_min')],
            'duracion_min' => ['nullable', 'integer', 'min:0', 'max:14400'],
            'duracion_max' => ['nullable', 'integer', 'min:1', 'max:14400', Rule::when($this->filled('duracion_min'), 'gte:duracion_min')],
        ];
    }
}
