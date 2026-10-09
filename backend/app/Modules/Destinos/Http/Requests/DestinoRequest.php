<?php

namespace App\Modules\Destinos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestinoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitimos la validación (luego el middleware auth protege la ruta)
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'pais' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'estado' => 'required|in:activo,inactivo',
            'operador_id' => 'nullable|exists:usuarios,id',
        ];
    }
}