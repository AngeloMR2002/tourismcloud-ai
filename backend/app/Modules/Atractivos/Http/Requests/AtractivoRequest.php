<?php

namespace App\Modules\Atractivos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtractivoRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'costo_entrada' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'duracion_estimada_min' => [
                'nullable',
                'integer',
                'min:1',
                'max:10080',
            ],

            'latitud' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitud' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'imagen_portada' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],

            // Categorías: array de IDs enteros, opcionales.
            'categorias' => [
                'nullable',
                'array',
            ],

            'categorias.*' => [
                'integer',
                'min:1',
            ],

            // Galería de imágenes adicional.
            'galeria' => [
                'nullable',
                'array',
                'max:10',
            ],

            'galeria.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            // ─── Horarios ───────────────────────────────────────────────
            //
            // Los horarios ahora se almacenan en la tabla "horarios".
            // Cada elemento representa un intervalo de atención.
            //
            // Ejemplo:
            // [
            //     [
            //         'dia' => 'lunes',
            //         'hora_inicio' => '06:00',
            //         'hora_fin' => '17:30'
            //     ],
            //     ...
            // ]
            //
            'horarios' => [
                'nullable',
                'array',
            ],

            'horarios.*' => [
                'required',
                'array',
            ],

            'horarios.*.dia' => [
                'required',
                'string',
                Rule::in([
                    'lunes',
                    'martes',
                    'miercoles',
                    'jueves',
                    'viernes',
                    'sabado',
                    'domingo',
                ]),
            ],

            'horarios.*.hora_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'horarios.*.hora_fin' => [
                'required',
                'date_format:H:i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' => 'Debes seleccionar un destino.',

            'nombre.required' => 'El nombre del atractivo es obligatorio.',
            'nombre.max' => 'El nombre no puede superar 150 caracteres.',

            'costo_entrada.required' =>
                'El costo de entrada es obligatorio (usa 0 si es gratuito).',

            'costo_entrada.min' =>
                'El costo no puede ser negativo.',

            'estado.in' =>
                'El estado debe ser "activo" o "inactivo".',

            'imagen_portada.image' =>
                'La portada debe ser una imagen (jpg, png o webp).',

            'imagen_portada.max' =>
                'La imagen de portada no puede superar 4 MB.',

            'galeria.max' =>
                'Puedes subir máximo 10 imágenes en la galería.',

            'latitud.between' =>
                'La latitud debe estar entre -90 y 90.',

            'longitud.between' =>
                'La longitud debe estar entre -180 y 180.',

            'horarios.array' =>
                'Los horarios deben enviarse como una lista de intervalos.',

            'horarios.*.array' =>
                'Cada horario debe ser un objeto con día, hora de inicio y hora de fin.',

            'horarios.*.dia.required' =>
                'Debes indicar el día del horario.',

            'horarios.*.dia.in' =>
                'El día indicado no es válido.',

            'horarios.*.hora_inicio.required' =>
                'Debes indicar la hora de inicio.',

            'horarios.*.hora_inicio.date_format' =>
                'La hora de inicio debe tener el formato HH:mm (ej: 09:00).',

            'horarios.*.hora_fin.required' =>
                'Debes indicar la hora de fin.',

            'horarios.*.hora_fin.date_format' =>
                'La hora de fin debe tener el formato HH:mm (ej: 18:00).',
        ];
    }

    /**
     * Prepara los datos antes de la validación.
     *
     * Si horarios llega como JSON string desde fetch/axios,
     * se convierte a array para que pueda ser validado.
     */
    protected function prepareForValidation(): void
    {
        $horarios = $this->input('horarios');

        if (is_string($horarios)) {
            $horariosJson = json_decode($horarios, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $horarios = $horariosJson;
            }
        }

        if (!is_array($horarios)) {
            return;
        }

        $dias = [
            'lunes',
            'martes',
            'miercoles',
            'jueves',
            'viernes',
            'sabado',
            'domingo',
        ];

        foreach ($horarios as $clave => &$horario) {
            if (
                is_array($horario)
                && !isset($horario['dia'])
                && is_string($clave)
                && in_array($clave, $dias, true)
            ) {
                $horario['dia'] = $clave;
            }
        }
        unset($horario);

        $this->merge(['horarios' => $horarios]);
    }

    /**
     * Validación adicional de horarios.
     *
     * Verifica que la hora de fin sea posterior a la hora de inicio.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horarios = $this->input('horarios', []);

            if (!is_array($horarios)) {
                return;
            }

            foreach ($horarios as $indice => $horario) {
                if (!is_array($horario)) {
                    continue;
                }

                $horaInicio = $horario['hora_inicio'] ?? null;
                $horaFin = $horario['hora_fin'] ?? null;

                if (!$horaInicio || !$horaFin) {
                    continue;
                }

                if ($horaFin <= $horaInicio) {
                    $validator->errors()->add(
                        "horarios.{$indice}.hora_fin",
                        'La hora de fin debe ser posterior a la hora de inicio.'
                    );
                }
            }
        });
    }
}
