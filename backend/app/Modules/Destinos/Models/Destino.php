<?php

namespace App\Modules\Destinos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Usuarios\Models\Usuario;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Establecimientos\Models\Establecimiento;

class Destino extends Model
{
    use HasFactory;

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

    // --- RELACIONES ---

    // Un destino puede tener un operador turístico asignado
    public function operador()
    {
        return $this->belongsTo(Usuario::class, 'operador_id');
    }

    // Un destino tiene muchos atractivos
    public function atractivos()
    {
        return $this->hasMany(Atractivo::class, 'destino_id');
    }

    // Un destino tiene muchos establecimientos
    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class, 'destino_id');
    }
}
