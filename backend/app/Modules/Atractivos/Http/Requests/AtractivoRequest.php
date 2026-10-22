<?php

namespace App\Modules\Atractivos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtractivoRequest extends FormRequest
{
    /**
     * Autorización: la verificación de pertenencia del destino al operador
     * se realiza en el controlador, no aquí, para mantener separación de capas.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id' => ['required', 'integer', 'min:1'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'costo_entrada' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duracion_estimada_min' => ['nullable', 'integer', 'min:1', 'max:14400'], // máximo 10 días en minutos
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
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
            // El campo horarios es un JSON object con 7 claves (días de la semana).
            // Cada valor es null (cerrado) o {"abre": "HH:mm", "cierra": "HH:mm"}.
            'horarios' => ['nullable', 'array'],
            'horarios.lunes' => ['nullable', 'array'],
            'horarios.martes' => ['nullable', 'array'],
            'horarios.miercoles' => ['nullable', 'array'],
            'horarios.jueves' => ['nullable', 'array'],
            'horarios.viernes' => ['nullable', 'array'],
            'horarios.sabado' => ['nullable', 'array'],
            'horarios.domingo' => ['nullable', 'array'],

            // Cuando el día NO es null, debe tener abre y cierra en formato HH:mm.
            'horarios.*.abre' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horarios.*.cierra' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' => __('operator_attractions.destination_required'),
            'nombre.required' => __('operator_attractions.name_required'),
            'nombre.max' => __('operator_attractions.name_max'),
            'costo_entrada.required' => __('operator_attractions.cost_required'),
            'costo_entrada.min' => __('operator_attractions.cost_nonnegative'),
            'estado.required' => __('operator_attractions.status_required'),
            'estado.in' => __('operator_attractions.status_valid'),
            'imagen_portada.image' => __('operator_attractions.cover_valid_image'),
            'imagen_portada.mimes' => __('operator_attractions.cover_valid_format'),
            'imagen_portada.max' => __('operator_attractions.cover_max_size'),
            'galeria.max' => __('operator_attractions.gallery_max'),
            'galeria.*.file' => __('operator_attractions.gallery_valid_file'),
            'galeria.*.image' => __('operator_attractions.gallery_valid_image'),
            'galeria.*.mimes' => __('operator_attractions.gallery_valid_format'),
            'galeria.*.max' => __('operator_attractions.gallery_max_size'),
            'latitud.between' => __('operator_attractions.latitude_range'),
            'longitud.between' => __('operator_attractions.longitude_range'),
            'horarios.*.abre.regex' => __('operator_attractions.opening_time_format'),
            'horarios.*.cierra.regex' => __('operator_attractions.closing_time_format'),
        ];
    }

    /**
     * Prepara los datos antes de la validación.
     * Convierte la estructura de horarios del formulario al formato JSONB esperado.
     */
    protected function prepareForValidation(): void
    {
        // Si horarios viene como string JSON (ej. desde fetch/axios), lo decodifica.
        if ($this->horarios && is_string($this->horarios)) {
            $this->merge(['horarios' => json_decode($this->horarios, true)]);
        }
    }

    /**
     * Validación adicional después de las reglas: verifica que los días con datos
     * exijan abre y cierra juntos, y que la hora de cierre sea posterior a la de apertura.
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
                        __('operator_attractions.closing_time_required', ['day' => __('operator_attractions.'.$this->dayTranslationKey($dia))])
                    );

                    continue;
                }

                if (! $tieneAbre && $tieneCierra) {
                    $validator->errors()->add(
                        "horarios.{$dia}.abre",
                        __('operator_attractions.opening_time_required', ['day' => __('operator_attractions.'.$this->dayTranslationKey($dia))])
                    );

                    continue;
                }

                // (b) Exigir que cierra > abre
                if ($cierra <= $abre) {
                    $validator->errors()->add(
                        "horarios.{$dia}.cierra",
                        __('operator_attractions.closing_after_opening', ['day' => __('operator_attractions.'.$this->dayTranslationKey($dia))])
                    );
                }
            }
        });
    }

    private function dayTranslationKey(string $day): string
    {
        return match ($day) {
            'lunes' => 'monday',
            'martes' => 'tuesday',
            'miercoles' => 'wednesday',
            'jueves' => 'thursday',
            'viernes' => 'friday',
            'sabado' => 'saturday',
            'domingo' => 'sunday',
            default => $day,
        };
    }
}
