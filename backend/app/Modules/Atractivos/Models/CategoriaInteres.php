<?php

namespace App\Modules\Atractivos\Models;

use App\Modules\Establecimientos\Models\Establecimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaInteres extends Model
{
    /**
     * Tabla definida por feature/destinos o módulo de catálogo base.
     * Este modelo se declara aquí para que las relaciones de Atractivo
     * y Establecimiento puedan resolverse sin errores de autoloading.
     *
     * IMPORTANTE: La migración de esta tabla NO vive en esta rama.
     * Solo se define el modelo con sus relaciones inversas.
     */
    protected $table = 'categorias_interes';

    // Sin timestamps en el schema original.
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // ─── Relaciones inversas ─────────────────────────────────────────────────

    /**
     * Las categorías pueden pertenecer a muchos atractivos.
     */
    public function atractivos(): BelongsToMany
    {
        return $this->belongsToMany(
            Atractivo::class,
            'atractivo_categoria',
            'categoria_id',
            'atractivo_id'
        );
    }

    /**
     * Las categorías pueden pertenecer a muchos establecimientos.
     */
    public function establecimientos(): BelongsToMany
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_categoria',
            'categoria_id',
            'establecimiento_id'
        );
    }
}
