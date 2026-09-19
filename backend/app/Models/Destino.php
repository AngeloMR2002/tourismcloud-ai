<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stub del modelo Destino.
 *
 * IMPORTANTE: Este modelo es un placeholder temporal para que las relaciones
 * Atractivo::destino() y Establecimiento::destino() puedan resolverse
 * mientras feature/destinos llega a develop.
 *
 * NO modificar este archivo — la versión definitiva la trae feature/destinos.
 * Eliminar este archivo cuando se haga merge de esa rama.
 */
class Destino extends Model
{
    protected $table = 'destinos';

    protected $fillable = [
        'organizacion_id',
        'operador_id',
        'nombre',
        'descripcion',
        'pais',
        'region',
        'ciudad',
        'latitud',
        'longitud',
        'estado',
    ];

    public function atractivos(): HasMany
    {
        return $this->hasMany(Atractivo::class, 'destino_id');
    }

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'destino_id');
    }
}
