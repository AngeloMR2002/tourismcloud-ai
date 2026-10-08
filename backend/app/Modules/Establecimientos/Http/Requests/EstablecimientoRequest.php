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
            'destino_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'proveedor_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'tipo' => [
                'required',
                Rule::in([
                    'hotel',
                    'restaurante',
                    'transporte',
                    'agencia',
                    'otro',
                ]),
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

            'direccion' => [
                'nullable',
                'string',
                'max:255',
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

            'rango_precio' => [
                'nullable',
                Rule::in([
                    'bajo',
                    'medio',
                    'alto',
                    'lujo',
                ]),
            ],

            'imagen_portada' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'estado' => [
                'required',
                Rule::in([
                    'activo',
                    'inactivo',
                ]),
            ],

            // Categorías
            'categorias' => [
                'nullable',
                'array',
            ],

            'categorias.*' => [
                'integer',
                'min:1',
            ],

            // Galería
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

            // ─── Horarios normalizados ────────────────────────────────
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
                'after:horarios.*.hora_inicio',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required' =>
                'Debes seleccionar un destino.',

            'proveedor_id.required' =>
                'Debes seleccionar un proveedor.',

            'tipo.required' =>
                'El tipo de establecimiento es obligatorio.',

            'tipo.in' =>
                'El tipo debe ser: hotel, restaurante, transporte, agencia u otro.',

            'nombre.required' =>
                'El nombre del establecimiento es obligatorio.',

            'nombre.max' =>
                'El nombre no puede superar 150 caracteres.',

            'estado.in' =>
                'El estado debe ser "activo" o "inactivo".',

            'rango_precio.in' =>
                'El rango de precio debe ser: bajo, medio, alto o lujo.',

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

            // Horarios
            'horarios.array' =>
                'Los horarios deben enviarse como una lista.',

            'horarios.*.array' =>
                'Cada horario debe tener una estructura válida.',

            'horarios.*.dia.required' =>
                'Cada horario debe indicar un día.',

            'horarios.*.dia.in' =>
                'El día indicado no es válido.',

            'horarios.*.hora_inicio.required' =>
                'Cada horario debe indicar una hora de inicio.',

            'horarios.*.hora_inicio.date_format' =>
                'La hora de inicio debe estar en formato HH:mm.',

            'horarios.*.hora_fin.required' =>
                'Cada horario debe indicar una hora de fin.',

            'horarios.*.hora_fin.date_format' =>
                'La hora de fin debe estar en formato HH:mm.',

            'horarios.*.hora_fin.after' =>
                'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $horarios = $this->input('horarios');

        if (is_string($horarios)) {
            $horariosJson = json_decode($horarios, true);
            $horarios = json_last_error() === JSON_ERROR_NONE
                && is_array($horariosJson)
                    ? $horariosJson
                    : $horarios;
        }

        if (is_array($horarios)) {
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
        } elseif (is_string($horarios)) {
            $this->merge(['horarios' => $horarios]);
        }
    }

    /**
     * Validación adicional de horarios.
     *
     * Verifica que cada intervalo tenga una hora de fin
     * posterior a la hora de inicio.
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