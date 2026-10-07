<?php

namespace App\Modules\Rutas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar-catalogo') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['tipo_precio' => $this->input('tipo_precio', 'variable'),
            'es_propuesta' => $this->boolean('es_propuesta')]);
        if ($this->input('tipo_precio') === 'variable') {
            $this->merge(['costo_estimado' => null, 'precio_fuente_url' => null, 'precio_verificado_en' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'destino_id' => ['required', 'integer', Rule::exists('destinos', 'id')->where('estado', 'activo')],
            'tipo_ruta' => ['required', Rule::in(['circuito', 'lineal', 'tematica', 'senderismo'])],
            'es_propuesta' => ['boolean'],
            'duracion_estimada_horas' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
            'distancia_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'nivel_dificultad' => ['nullable', Rule::in(['facil', 'moderada', 'dificil', 'experto'])],
            'transporte_recomendado' => ['required', Rule::in(['a_pie', 'bicicleta', 'automovil', 'autobus', 'mixto'])],
            'costo_estimado' => ['nullable', 'required_unless:tipo_precio,variable', 'numeric', 'min:0', 'max:99999999.99'],
            'tipo_precio' => ['required', Rule::in(['fijo', 'estimado', 'variable'])],
            'precio_fuente_url' => ['nullable', 'required_with:costo_estimado', 'url:http,https', 'max:2048'],
            'precio_verificado_en' => ['nullable', 'required_with:costo_estimado', 'date', 'before_or_equal:now', 'after_or_equal:'.now()->subDays(config('tourism.price_days'))->toDateString()],
            'temporada_recomendada' => ['nullable', 'string', 'max:100'],
            'recomendaciones' => ['nullable', 'string', 'max:5000'],
            'imagen_portada' => ['nullable', 'url:http,https', 'max:2048'],
            'imagen_fuente_url' => ['nullable', 'required_with:imagen_portada', 'url:http,https', 'max:2048'],
            'imagen_autor' => ['nullable', 'required_with:imagen_portada', 'string', 'max:150'],
            'imagen_licencia' => ['nullable', 'required_with:imagen_portada', 'string', 'max:100'],
            'sitio_web_oficial' => ['nullable', 'url:http,https', 'max:2048'],
            'url_reserva_oficial' => ['nullable', 'url:http,https', 'max:2048'],
            'fuente_url' => ['required', 'url:http,https', 'max:2048'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'puntos' => ['required', 'array', 'min:2', 'max:30'],
            'puntos.*.atractivo_id' => ['nullable', 'integer', Rule::exists('atractivos', 'id')->where(fn ($q) =>
                $q->where('destino_id', $this->integer('destino_id'))->where('estado_verificacion', 'aprobado')->whereNull('deleted_at'))],
            'puntos.*.nombre_parada' => ['required', 'string', 'max:150'],
            'puntos.*.descripcion_parada' => ['nullable', 'string', 'max:2000'],
            'puntos.*.tiempo_estadia_min' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'puntos.*.distancia_desde_anterior_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'puntos.*.tiempo_traslado_min' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'puntos.*.tipo_transporte_tramo' => ['nullable', Rule::in(['a_pie', 'bicicleta', 'automovil', 'autobus', 'mixto', 'vehiculo'])],
            'puntos.*.latitud' => ['nullable', 'required_with:puntos.*.longitud', 'numeric', 'between:-90,90'],
            'puntos.*.longitud' => ['nullable', 'required_with:puntos.*.latitud', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'fuente_url.required' => 'Incluye una fuente que respalde los lugares del recorrido.',
            'puntos.required' => 'La ruta necesita al menos dos paradas.',
            'puntos.min' => 'La ruta necesita al menos dos paradas.',
            'puntos.*.atractivo_id.exists' => 'Cada atractivo debe estar aprobado y pertenecer al destino seleccionado.',
            'precio_fuente_url.required_with' => 'Un costo necesita el enlace de la tarifa publicada.',
            'precio_verificado_en.required_with' => 'Indica cuándo comprobaste la tarifa.',
        ];
    }
}
