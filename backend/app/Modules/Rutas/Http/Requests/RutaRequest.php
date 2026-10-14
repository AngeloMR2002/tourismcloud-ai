<?php

namespace App\Modules\Rutas\Http\Requests;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();

        if ($usuario === null || ! $usuario->isOperador()) {
            return false;
        }

        $ruta = $this->route('ruta');

        if (! $ruta instanceof Ruta) {
            return true;
        }

        return $ruta->destino()
            ->where('operador_id', $usuario->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'destino_id' => ['bail', 'required', 'integer', 'exists:destinos,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'distancia_km' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'duracion_estimada_min' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'quitar_portada' => ['nullable', 'boolean'],
            'paradas' => ['bail', 'required', 'array', 'min:2'],
            'paradas.*' => ['bail', 'required', 'integer', 'distinct:strict', 'exists:atractivos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' => 'Debes seleccionar un destino.',
            'nombre.required' => 'El nombre de la ruta es obligatorio.',
            'distancia_km.min' => 'La distancia no puede ser negativa.',
            'duracion_estimada_min.min' => 'La duración debe ser mayor que cero.',
            'imagen_portada.image' => 'La portada debe ser una imagen válida.',
            'imagen_portada.mimes' => 'La portada debe estar en formato JPG, PNG o WebP.',
            'imagen_portada.max' => 'La portada no puede superar 5 MB.',
            'paradas.required' => 'Debes agregar al menos dos paradas.',
            'paradas.min' => 'Una ruta debe tener al menos dos paradas.',
            'paradas.*.distinct' => 'No puedes repetir un atractivo dentro de la misma ruta.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'quitar_portada' => $this->boolean('quitar_portada'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $destinoId = $this->integer('destino_id');
            $paradas = $this->input('paradas', []);

            if ($destinoId <= 0 || ! is_array($paradas) || count($paradas) < 2) {
                return;
            }

            $ids = array_values(array_unique(array_map('intval', $paradas)));
            $cantidadValidas = Atractivo::query()
                ->whereIn('id', $ids)
                ->where('destino_id', $destinoId)
                ->where('estado', 'activo')
                ->count();

            if ($cantidadValidas !== count($ids)) {
                $validator->errors()->add(
                    'paradas',
                    'Cada parada debe ser un atractivo activo, no eliminado y perteneciente al destino seleccionado.'
                );
            }
        });
    }
}
