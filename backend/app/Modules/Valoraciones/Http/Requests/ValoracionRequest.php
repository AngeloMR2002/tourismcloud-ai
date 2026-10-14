<?php

namespace App\Modules\Valoraciones\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ValoracionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Reglas de validación para registrar o actualizar una reseña.
     */
    public function rules(): array
    {
        return [
            'valorable_type' => [
                'required',
                'string',
                Rule::in(['atractivo', 'establecimiento']),
            ],
            'valorable_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'puntuacion' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'puntuacion.required' => 'Debes seleccionar una puntuación de 1 a 5 estrellas.',
            'puntuacion.between'  => 'La puntuación debe estar comprendida entre 1 y 5 estrellas.',
            'comentario.max'      => 'El comentario no puede superar los 1000 caracteres.',
        ];
    }
}
