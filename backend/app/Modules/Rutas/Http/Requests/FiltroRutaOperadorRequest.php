<?php

namespace App\Modules\Rutas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroRutaOperadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id' => ['bail', 'nullable', 'integer', 'exists:destinos,id'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
            'buscar' => ['nullable', 'string', 'max:100'],
            'por_pagina' => ['bail', 'nullable', 'integer', Rule::in([10, 20, 30, 60])],
        ];
    }
}
