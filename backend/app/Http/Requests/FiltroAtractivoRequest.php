<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroAtractivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id'   => ['nullable', 'integer', 'min:1'],
            'categoria_id' => ['nullable', 'integer', 'min:1'],
            'costo_min'    => ['nullable', 'numeric', 'min:0'],
            'costo_max'    => ['nullable', 'numeric', 'min:0', 'gte:costo_min'],
            'busqueda'     => ['nullable', 'string', 'max:100'],
            'por_pagina'   => ['nullable', 'integer', Rule::in([9, 15, 30, 60])],
        ];
    }

    public function messages(): array
    {
        return [
            'costo_max.gte'   => 'El costo máximo debe ser mayor o igual al mínimo.',
            'por_pagina.in'   => 'El número de resultados por página debe ser 9, 15, 30 o 60.',
        ];
    }
}
