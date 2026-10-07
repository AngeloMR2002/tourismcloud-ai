<?php

namespace App\Modules\Rutas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiltroRutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:activo,inactivo'],
            'destino_id' => ['nullable', 'integer'],
            'nivel_dificultad' => ['nullable', 'string', 'in:facil,moderada,dificil,experto'],
            'transporte_recomendado' => ['nullable', 'string', 'in:a_pie,bicicleta,automovil,autobus,mixto'],
            'tipo_ruta' => ['nullable', 'string', 'in:circuito,lineal,tematica,senderismo'],
            'duracion_max_horas' => ['nullable', 'numeric', 'min:0'],
            'orden' => ['nullable', 'string', 'in:recientes,duracion_asc,distancia_asc,nombre_asc'],
        ];
    }
}
