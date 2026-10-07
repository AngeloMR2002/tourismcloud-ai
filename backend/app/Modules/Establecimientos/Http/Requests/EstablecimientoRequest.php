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
            'destino_id'   => ['required', 'integer', 'min:1'],
            'tipo'         => ['required', Rule::in(['hotel', 'restaurante', 'transporte', 'agencia', 'otro'])],
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:5000'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'latitud'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'     => ['nullable', 'numeric', 'between:-180,180'],
            'rango_precio' => ['nullable', Rule::in(['bajo', 'medio', 'alto', 'lujo'])],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:5120'],
            'estado'       => ['required', Rule::in(['activo', 'inactivo'])],

            // Categorías: array de IDs enteros, opcionales.
            'categorias'   => ['nullable', 'array'],
            'categorias.*' => ['integer', 'min:1'],

            // Galería de imágenes adicional.
            'galeria'      => ['nullable', 'array', 'max:10'],
            'galeria.*'    => ['file', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif', 'max:5120'],

            // ─── Horarios ───────────────────────────────────────────────────
            'horarios'           => ['nullable', 'array'],
            'horarios.lunes'     => ['nullable', 'array'],
            'horarios.martes'    => ['nullable', 'array'],
            'horarios.miercoles' => ['nullable', 'array'],
            'horarios.jueves'    => ['nullable', 'array'],
            'horarios.viernes'   => ['nullable', 'array'],
            'horarios.sabado'    => ['nullable', 'array'],
            'horarios.domingo'   => ['nullable', 'array'],
            'horarios.*.abre'    => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horarios.*.cierra'  => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'destino_id.required'     => 'Debes seleccionar un destino.',
            'tipo.required'           => 'El tipo de establecimiento es obligatorio.',
            'tipo.in'                 => 'El tipo debe ser: hotel, restaurante, transporte, agencia u otro.',
            'nombre.required'         => 'El nombre del establecimiento es obligatorio.',
            'nombre.max'              => 'El nombre no puede superar 150 caracteres.',
            'estado.required'         => 'El estado es obligatorio.',
            'estado.in'               => 'El estado debe ser "activo" o "inactivo".',
            'rango_precio.in'         => 'El rango de precio debe ser: bajo, medio, alto o lujo.',
            'imagen_portada.image'    => 'La portada debe ser una imagen válida (JPG, PNG, WebP o JFIF).',
            'imagen_portada.mimes'    => 'La imagen de portada debe estar en formato JPG, JPEG, PNG, WEBP o JFIF.',
            'imagen_portada.max'      => 'La imagen de portada no puede superar 5 MB.',
            'galeria.max'             => 'Puedes subir máximo 10 imágenes en la galería.',
            'galeria.*.file'          => 'Cada archivo de la galería debe ser un archivo válido.',
            'galeria.*.image'         => 'Cada archivo de la galería debe ser una imagen válida (JPG, PNG, WebP o JFIF).',
            'galeria.*.mimes'         => 'Las imágenes de la galería deben estar en formato JPG, JPEG, PNG, WEBP o JFIF.',
            'galeria.*.max'           => 'Cada imagen de la galería no puede superar 5 MB.',
            'latitud.between'         => 'La latitud debe estar entre -90 y 90.',
            'longitud.between'        => 'La longitud debe estar entre -180 y 180.',
            'horarios.*.abre.regex'   => 'El horario de apertura debe estar en formato HH:mm (ej: 09:00).',
            'horarios.*.cierra.regex' => 'El horario de cierre debe estar en formato HH:mm (ej: 18:00).',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->horarios && is_string($this->horarios)) {
            $this->merge(['horarios' => json_decode($this->horarios, true)]);
        }
    }

    /**
     * Validación adicional: días abiertos deben tener abre + cierra, y cierra > abre.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horarios = $this->input('horarios', []);

            if (!is_array($horarios)) {
                return;
            }

            $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

            foreach ($dias as $dia) {
                $horario = $horarios[$dia] ?? null;

                if ($horario === null) {
                    continue;
                }

                if (!is_array($horario)) {
                    $validator->errors()->add("horarios.{$dia}", "El horario del {$dia} debe ser null (cerrado) o un objeto con 'abre' y 'cierra'.");
                    continue;
                }

                $abre   = $horario['abre']   ?? null;
                $cierra = $horario['cierra'] ?? null;

                if (!$abre || !$cierra) {
                    $validator->errors()->add("horarios.{$dia}", "Si el {$dia} está abierto, debes indicar tanto la hora de apertura como la de cierre.");
                    continue;
                }

                if ($cierra <= $abre) {
                    $validator->errors()->add("horarios.{$dia}", "La hora de cierre del {$dia} debe ser posterior a la hora de apertura.");
                }
            }
        });
    }
}
