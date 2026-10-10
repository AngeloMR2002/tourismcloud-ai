<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'estado'
    ];

    public function itinerarios()
    {
        return $this->hasMany(Itinerario::class, 'destino_id');
    }
}