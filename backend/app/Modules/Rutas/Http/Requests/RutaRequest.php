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
            'duracion_estimada_min' => ['nullable', 'integer', 'min:1', 'max:14400'],
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
            'destino_id.required' => __('operator_routes.destination_required'),
            'nombre.required' => __('operator_routes.route_name_required'),
            'distancia_km.min' => __('operator_routes.distance_nonnegative'),
            'duracion_estimada_min.min' => __('operator_routes.duration_minimum'),
            'imagen_portada.image' => __('operator_routes.cover_valid_image'),
            'imagen_portada.mimes' => __('operator_routes.cover_valid_format'),
            'imagen_portada.max' => __('operator_routes.cover_max_size'),
            'paradas.required' => __('operator_routes.stops_required'),
            'paradas.min' => __('operator_routes.stops_minimum'),
            'paradas.*.distinct' => __('operator_routes.stops_distinct'),
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
                    __('operator_routes.stops_valid')
                );
            }
        });
    }
}
