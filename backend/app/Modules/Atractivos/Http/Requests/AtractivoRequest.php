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
        $portadaRequerida = $this->isMethod('post');

        return [
            'destino_id'             => ['required', 'integer', 'min:1'],
            'nombre'                 => ['required', 'string', 'max:150'],
            'descripcion'            => ['required', 'string', 'max:5000'],
            'costo_entrada'          => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duracion_estimada_min'  => ['nullable', 'integer', 'min:1', 'max:10080'], // único campo opcional permitido
            'latitud'                => ['required', 'numeric', 'between:-90,90'],
            'longitud'               => ['required', 'numeric', 'between:-180,180'],
            'imagen_portada'         => array_merge(
                [$portadaRequerida ? 'required' : 'nullable'],
                ['image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:2000']
            ),
            'estado'                 => ['required', Rule::in(['activo', 'inactivo'])],

            // Categorías: obligatorio, al menos 1 categoría existente.
            'categorias'             => ['required', 'array', 'min:1'],
            'categorias.*'           => ['integer', 'min:1', 'exists:categorias_interes,id'],

            // Galería de imágenes adicional.
            'galeria'                => ['nullable', 'array', 'max:5'],
            'galeria.*'              => ['file', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:2000'],

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
            'destino_id.required'       => 'Debes seleccionar un destino.',
            'nombre.required'           => 'El nombre del atractivo es obligatorio.',
            'nombre.max'                => 'El nombre no puede superar 150 caracteres.',
            'descripcion.required'      => 'La descripción del atractivo es obligatoria.',
            'descripcion.max'           => 'La descripción no puede superar 5000 caracteres.',
            'costo_entrada.required'    => 'El costo de entrada es obligatorio (usa 0 si es gratuito).',
            'costo_entrada.min'         => 'El costo no puede ser negativo.',
            'latitud.required'          => 'Debes seleccionar la ubicación en el mapa (latitud).',
            'latitud.between'           => 'La latitud debe estar entre -90 y 90.',
            'longitud.required'         => 'Debes seleccionar la ubicación en el mapa (longitud).',
            'longitud.between'          => 'La longitud debe estar entre -180 y 180.',
            'imagen_portada.required'   => 'Debes seleccionar una imagen de portada.',
            'imagen_portada.image'      => 'La portada debe ser una imagen válida (JPG, PNG, WebP o JFIF).',
            'imagen_portada.mimes'      => 'La imagen de portada debe estar en formato JPG, JPEG, PNG, WEBP o JFIF.',
            'imagen_portada.max'        => 'La imagen de portada no puede superar 2 MB.',
            'imagen_portada.uploaded'   => 'No se pudo subir la imagen; verifica que no supere 2 MB.',
            'estado.required'           => 'El estado es obligatorio.',
            'estado.in'                 => 'El estado debe ser "activo" o "inactivo".',
            'categorias.required'       => 'Debes seleccionar al menos una categoría de interés.',
            'categorias.array'          => 'El formato de categorías no es válido.',
            'categorias.min'            => 'Debes seleccionar al menos una categoría de interés.',
            'categorias.*.exists'       => 'Una o más categorías seleccionadas no son válidas.',
            'galeria.max'               => 'Puedes subir máximo 5 imágenes en la galería.',
            'galeria.*.file'            => 'Cada archivo de la galería debe ser un archivo válido.',
            'galeria.*.image'           => 'Cada archivo de la galería debe ser una imagen válida (JPG, PNG, WebP o JFIF).',
            'galeria.*.mimes'           => 'Las imágenes de la galería deben estar en formato JPG, JPEG, PNG, WEBP o JFIF.',
            'galeria.*.max'             => 'Cada imagen de la galería no puede superar 2 MB.',
            'galeria.*.uploaded'        => 'No se pudo subir la imagen; verifica que no supere 2 MB.',
            'horarios.*.abre.regex'     => 'El horario de apertura debe estar en formato HH:mm (ej: 09:00).',
            'horarios.*.cierra.regex'   => 'El horario de cierre debe estar en formato HH:mm (ej: 18:00).',
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
