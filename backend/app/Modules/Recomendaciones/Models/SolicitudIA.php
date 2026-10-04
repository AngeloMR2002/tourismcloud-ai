<?php

namespace App\Modules\Recomendaciones\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudIA extends Model
{
    // La tabla solo tiene created_at.
    public const UPDATED_AT = null;

    protected $table = 'solicitudes_ia';

    protected $fillable = [
        'itinerario_id',
        'turista_id',
        'prompt_usuario',
        'restricciones_json',
        'respuesta_ia_json',
        'modelo_usado',
        'tiempo_respuesta_ms',
    ];

    protected $casts = [
        'restricciones_json' => 'array',
        'respuesta_ia_json' => 'array',
        'tiempo_respuesta_ms' => 'integer',
    ];
}
