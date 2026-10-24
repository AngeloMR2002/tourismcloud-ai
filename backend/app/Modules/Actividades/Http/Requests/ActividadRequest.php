<?php

namespace App\Modules\Actividades\Http\Requests;

use App\Modules\Actividades\Models\Actividad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actividad = $this->route('actividad');

        if (! $actividad instanceof Actividad) {
            return true;
        }

        return $actividad->destino()
            ->where('operador_id', auth()->id())
            ->exists();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'destino_id' => ['bail', 'required', 'integer', 'exists:destinos,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duracion_estimada_min' => ['nullable', 'integer', 'min:1', 'max:14400'],
            'latitud' => ['nullable', 'required_with:longitud', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'required_with:latitud', 'numeric', 'between:-180,180'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'quitar_portada' => ['nullable', 'boolean'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destino_id.required' => __('operator.destination_required'),
            'destino_id.exists' => __('operator.destination_not_found'),
            'nombre.required' => __('operator.activity_name_required'),
            'nombre.max' => __('operator.activity_name_max'),
            'costo.required' => __('operator.cost_required'),
            'costo.min' => __('operator.cost_nonnegative'),
            'duracion_estimada_min.min' => __('operator.duration_minimum'),
            'latitud.between' => __('operator.latitude_range'),
            'longitud.between' => __('operator.longitude_range'),
            'latitud.required_with' => __('operator.coordinates_together'),
            'longitud.required_with' => __('operator.coordinates_together'),
            'imagen_portada.image' => __('operator.cover_valid_image'),
            'imagen_portada.mimes' => __('operator.cover_valid_format'),
            'imagen_portada.max' => __('operator.cover_max_size'),
            'estado.in' => __('operator.status_valid'),
        ];
    }
}
