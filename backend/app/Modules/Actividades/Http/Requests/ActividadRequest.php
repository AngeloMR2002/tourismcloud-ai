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
            'duracion_estimada_min' => ['nullable', 'integer', 'min:1', 'max:10080'],
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
            'destino_id.required' => 'Debes seleccionar un destino.',
            'destino_id.exists' => 'El destino seleccionado no existe.',
            'nombre.required' => 'El nombre de la actividad es obligatorio.',
            'nombre.max' => 'El nombre no puede superar 150 caracteres.',
            'costo.required' => 'Debes indicar el costo estimado; usa 0 si es gratuita.',
            'costo.min' => 'El costo no puede ser negativo.',
            'duracion_estimada_min.min' => 'La duración debe ser de al menos un minuto.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
            'latitud.required_with' => 'Debes indicar latitud y longitud juntas.',
            'longitud.required_with' => 'Debes indicar latitud y longitud juntas.',
            'imagen_portada.image' => 'La portada debe ser una imagen válida.',
            'imagen_portada.mimes' => 'La portada debe estar en formato JPG, PNG o WebP.',
            'imagen_portada.max' => 'La portada no puede superar 5 MB.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
}
