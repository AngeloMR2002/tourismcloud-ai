<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerario extends Model
{
    protected $table = 'itinerarios';
    
    protected $fillable = [
        'turista_id',
        'destino_id',
        'preferencia_id',
        'nombre',
        'descripcion',
        'presupuesto_total',
        'dias',
        'estado'
    ];

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }
}