<?php

namespace App\Modules\Establecimientos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstablecimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id' => ['required', 'integer', 'min:1'],
            'tipo' => ['required', Rule::in(['hotel', 'restaurante', 'transporte', 'agencia', 'otro'])],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'rango_precio' => ['nullable', Rule::in(['Bajo', 'Medio', 'Alto', 'Lujoso'])],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:5120'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],

            // Categorías: array de IDs enteros, opcionales.
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'min:1'],

            // Galería de imágenes adicional.
            'galeria' => ['nullable', 'array', 'max:10'],
            'galeria.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:5120'],

            // Imágenes marcadas para eliminación
            'eliminar_imagenes' => ['nullable', 'array'],
            'eliminar_imagenes.*' => ['integer'],

            // ─── Horarios ───────────────────────────────────────────────────
            'horarios' => ['nullable', 'array'],
            'horarios.lunes' => ['nullable', 'array'],
            'horarios.martes' => ['nullable', 'array'],
            'horarios.miercoles' => ['nullable', 'array'],
            'horarios.jueves' => ['nullable', 'array'],
            'horarios.viernes' => ['nullable', 'array'],
            'horarios.sabado' => ['nullable', 'array'],
            'horarios.domingo' => ['nullable', 'array'],
            'horarios.*.abre' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horarios.*.cierra' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' => __('provider_places.validation.destination_required'),
            'tipo.required' => __('provider_places.validation.type_required'),
            'tipo.in' => __('provider_places.validation.type_in'),
            'nombre.required' => __('provider_places.validation.name_required'),
            'nombre.max' => __('provider_places.validation.name_max'),
            'estado.required' => __('provider_places.validation.status_required'),
            'estado.in' => __('provider_places.validation.status_in'),
            'rango_precio.in' => __('provider_places.validation.price_range_in'),
            'imagen_portada.image' => __('provider_places.validation.cover_image'),
            'imagen_portada.mimes' => __('provider_places.validation.cover_mimes'),
            'imagen_portada.max' => __('provider_places.validation.cover_max'),
            'galeria.max' => __('provider_places.validation.gallery_max'),
            'galeria.*.file' => __('provider_places.validation.gallery_file'),
            'galeria.*.image' => __('provider_places.validation.gallery_image'),
            'galeria.*.mimes' => __('provider_places.validation.gallery_mimes'),
            'galeria.*.max' => __('provider_places.validation.gallery_image_max'),
            'latitud.between' => __('provider_places.validation.latitude_between'),
            'longitud.between' => __('provider_places.validation.longitude_between'),
            'horarios.*.abre.regex' => __('provider_places.validation.opening_format'),
            'horarios.*.cierra.regex' => __('provider_places.validation.closing_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->horarios && is_string($this->horarios)) {
            $this->merge(['horarios' => json_decode($this->horarios, true)]);
        }
    }

    /**
     * Validación adicional: para cada día con datos, exige abre y cierra juntos, y cierra > abre.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horarios = $this->input('horarios', []);

            if (! is_array($horarios)) {
                return;
            }

            foreach ($horarios as $dia => $horario) {
                if (! is_array($horario)) {
                    continue;
                }

                $abreRaw = $horario['abre'] ?? null;
                $cierraRaw = $horario['cierra'] ?? null;

                $abre = is_string($abreRaw) ? trim($abreRaw) : $abreRaw;
                $cierra = is_string($cierraRaw) ? trim($cierraRaw) : $cierraRaw;

                $tieneAbre = ! empty($abre);
                $tieneCierra = ! empty($cierra);

                // Si no tiene ninguno de los dos, se considera día cerrado
                if (! $tieneAbre && ! $tieneCierra) {
                    continue;
                }

                // (a) Exigir que abre y cierra vengan juntos
                if ($tieneAbre && ! $tieneCierra) {
                    $validator->errors()->add(
                        "horarios.{$dia}.cierra",
                        __('provider_places.validation.closing_required', ['day' => __('provider_places.day_'.$dia)])
                    );

                    continue;
                }

                if (! $tieneAbre && $tieneCierra) {
                    $validator->errors()->add(
                        "horarios.{$dia}.abre",
                        __('provider_places.validation.opening_required', ['day' => __('provider_places.day_'.$dia)])
                    );

                    continue;
                }

                // (b) Exigir que cierra > abre
                if ($cierra <= $abre) {
                    $validator->errors()->add(
                        "horarios.{$dia}.cierra",
                        __('provider_places.validation.closing_after_opening', ['day' => __('provider_places.day_'.$dia)])
                    );
                }
            }
        });
    }
}
