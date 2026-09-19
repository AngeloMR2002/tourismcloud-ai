<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroEstablecimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id'   => ['nullable', 'integer', 'min:1'],
            'tipo'         => ['nullable', Rule::in(['hotel', 'restaurante', 'transporte', 'agencia', 'otro'])],
            'rango_precio' => ['nullable', Rule::in(['bajo', 'medio', 'alto', 'lujo'])],
            'categoria_id' => ['nullable', 'integer', 'min:1'],
            'busqueda'     => ['nullable', 'string', 'max:100'],
            'por_pagina'   => ['nullable', 'integer', Rule::in([9, 15, 30, 60])],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in'         => 'El tipo debe ser: hotel, restaurante, transporte, agencia u otro.',
            'rango_precio.in' => 'El rango de precio debe ser: bajo, medio, alto o lujo.',
            'por_pagina.in'   => 'El número de resultados por página debe ser 9, 15, 30 o 60.',
        ];
    }
}
