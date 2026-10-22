<?php

namespace App\Modules\Usuarios\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $fillable = ['usuario_id', 'tipo', 'recurso_id'];
}
