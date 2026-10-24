<?php

namespace App\Modules\Rutas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroRutaPublicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id' => ['bail', 'nullable', 'integer', 'exists:destinos,id'],
            'duracion_min' => ['nullable', 'integer', 'min:0', 'max:14400'],
            'duracion_max' => ['bail', 'nullable', 'integer', 'min:1', 'max:14400', Rule::when($this->filled('duracion_min'), 'gte:duracion_min')],
            'costo_min' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'costo_max' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', Rule::when($this->filled('costo_min'), 'gte:costo_min')],
            'distancia_max' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'buscar' => ['nullable', 'string', 'max:100'],
            'por_pagina' => ['bail', 'nullable', 'integer', Rule::in([9, 15, 30, 60])],
        ];
    }
}
