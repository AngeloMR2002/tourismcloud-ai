<?php

namespace App\Modules\Rutas\Models;

use App\Models\Atractivo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaPunto extends Model
{
    use HasFactory;

    protected $table = 'ruta_puntos';

    protected $fillable = [
        'ruta_id',
        'atractivo_id',
        'nombre_parada',
        'descripcion_parada',
        'orden',
        'tiempo_estadia_min',
        'distancia_desde_anterior_km',
        'tiempo_traslado_min',
        'tipo_transporte_tramo',
        'latitud',
        'longitud',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'tiempo_estadia_min' => 'integer',
            'distancia_desde_anterior_km' => 'decimal:2',
            'tiempo_traslado_min' => 'integer',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function atractivo(): BelongsTo
    {
        return $this->belongsTo(Atractivo::class, 'atractivo_id');
    }
}
