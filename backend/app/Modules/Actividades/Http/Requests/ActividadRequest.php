<?php

namespace App\Modules\Actividades\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar-catalogo') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_precio' => $this->input('tipo_precio', 'variable'),
            'dias_operacion' => $this->input('dias_operacion', []),
        ]);
        if ($this->input('tipo_precio') === 'variable') {
            $this->merge(['precio' => null, 'precio_min' => null, 'precio_max' => null,
                'precio_fuente_url' => null, 'precio_verificado_en' => null]);
        }
        if ($this->input('tipo_registro') !== 'evento') {
            $this->merge(['evento_inicio' => null, 'evento_fin' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'destino_id' => ['required', 'integer', Rule::exists('destinos', 'id')->where('estado', 'activo')],
            'atractivo_id' => ['nullable', 'integer', Rule::exists('atractivos', 'id')->where(fn ($q) =>
                $q->where('destino_id', $this->integer('destino_id'))->where('estado_verificacion', 'aprobado')->whereNull('deleted_at'))],
            'categoria' => ['required', Rule::in(['aventura', 'cultural', 'gastronomica', 'ecoturismo', 'relax', 'deportiva'])],
            'tipo_registro' => ['required', Rule::in(['visita', 'tour', 'evento'])],
            'duracion_min' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'precio' => ['nullable', 'required_unless:tipo_precio,variable', 'numeric', 'min:0', 'max:99999999.99'],
            'tipo_precio' => ['required', Rule::in(['fijo', 'estimado', 'variable'])],
            'precio_fuente_url' => ['nullable', 'required_with:precio', 'url:http,https', 'max:2048'],
            'precio_verificado_en' => ['nullable', 'required_with:precio', 'date', 'before_or_equal:now', 'after_or_equal:'.now()->subDays(config('tourism.price_days'))->toDateString()],
            'nivel_dificultad' => ['nullable', Rule::in(['baja', 'media', 'alta'])],
            'cupo_maximo' => ['nullable', 'integer', 'min:1', 'max:500'],
            'horario_inicio' => ['nullable', 'date_format:H:i', 'required_with:horario_fin'],
            'horario_fin' => ['nullable', 'date_format:H:i', 'required_with:horario_inicio', 'after:horario_inicio'],
            'dias_operacion' => ['array', 'max:7'],
            'dias_operacion.*' => ['distinct', Rule::in(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'])],
            'evento_inicio' => ['nullable', 'required_if:tipo_registro,evento', 'date'],
            'evento_fin' => ['nullable', 'required_if:tipo_registro,evento', 'date', 'after_or_equal:evento_inicio'],
            'punto_encuentro' => ['nullable', 'string', 'max:255'],
            'incluye' => ['nullable', 'string', 'max:5000'],
            'no_incluye' => ['nullable', 'string', 'max:5000'],
            'requisitos' => ['nullable', 'string', 'max:5000'],
            'imagen_portada' => ['nullable', 'url:http,https', 'max:2048'],
            'imagen_fuente_url' => ['nullable', 'required_with:imagen_portada', 'url:http,https', 'max:2048'],
            'imagen_autor' => ['nullable', 'required_with:imagen_portada', 'string', 'max:150'],
            'imagen_licencia' => ['nullable', 'required_with:imagen_portada', 'string', 'max:100'],
            'sitio_web_oficial' => ['nullable', 'url:http,https', 'max:2048'],
            'url_reserva_oficial' => ['nullable', 'url:http,https', 'max:2048'],
            'fuente_url' => ['required', 'url:http,https', 'max:2048'],
            'latitud' => ['nullable', 'required_with:longitud', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'required_with:latitud', 'numeric', 'between:-180,180'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'fuente_url.required' => 'Incluye el enlace de la fuente que respalda la actividad.',
            'precio_fuente_url.required_with' => 'Un precio necesita el enlace de la tarifa publicada.',
            'precio_verificado_en.required_with' => 'Indica cuándo comprobaste la tarifa.',
            'precio_verificado_en.after_or_equal' => 'Revisa nuevamente la tarifa: la comprobación tiene más de siete días.',
            'atractivo_id.exists' => 'El atractivo debe estar aprobado y pertenecer al destino seleccionado.',
            'imagen_fuente_url.required_with' => 'Indica la página de origen de la fotografía.',
            'imagen_autor.required_with' => 'Indica el autor de la fotografía.',
            'imagen_licencia.required_with' => 'Indica la licencia o autorización de uso de la fotografía.',
        ];
    }
}
