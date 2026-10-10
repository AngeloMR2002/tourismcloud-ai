<?php

namespace App\Models;

use App\Modules\Establecimientos\Models\Establecimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stub del modelo Usuario.
 *
 * IMPORTANTE: Este modelo es un placeholder temporal para que la relación
 * Establecimiento::proveedor() pueda resolverse mientras feature/auth
 * llega a develop.
 *
 * NO modificar este archivo — la versión definitiva la trae feature/auth.
 * Eliminar este archivo cuando se haga merge de esa rama.
 * La tabla real en el schema SQL se llama 'usuarios' (no 'users').
 */
class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'email',
        'password',
        'rol',
        'telefono',
        'estado',
    ];

    protected $hidden = ['password'];

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'proveedor_id');
    }
}
