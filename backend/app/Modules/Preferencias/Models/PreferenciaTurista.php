<?php

namespace App\Modules\Preferencias\Models;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PreferenciaTurista extends Model
{
    protected $table = 'preferencias_turista';

    protected $fillable = [
        'turista_id',
        'presupuesto_min',
        'presupuesto_max',
        'dias_disponibles',
        'ritmo',
        'hora_inicio_dia',
        'hora_fin_dia',
        'vigente',
    ];

    public function turista(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'turista_id'
        );
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'preferencia_categoria',
            'preferencia_id',
            'categoria_id'
        );
    }
}