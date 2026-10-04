<?php

namespace App\Modules\Recomendaciones\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GenerarRecomendacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        return $usuario?->rol === 'turista' && $usuario->estado === 'activo';
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('solicitud'))) {
            $this->merge([
                'solicitud' => Str::squish($this->input('solicitud')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'destino_id' => [
                'required',
                'integer',
                Rule::exists('destinos', 'id')->where('estado', 'activo'),
            ],
            'solicitud' => [
                'required',
                'string',
                'min:5',
                'max:' . config('recommendations.limits.max_solicitud_chars', 1000),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' => 'Debes indicar el destino.',
            'destino_id.exists' => 'El destino no existe o no está activo.',
            'solicitud.required' => 'Describe qué tipo de itinerario buscas.',
            'solicitud.min' => 'La solicitud es demasiado corta.',
            'solicitud.max' => 'La solicitud es demasiado larga.',
        ];
    }
}
