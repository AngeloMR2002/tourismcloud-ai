<?php

namespace App\Modules\Actividades\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroOperadorActividadRequest extends FormRequest
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
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
