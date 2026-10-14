<?php

namespace App\Modules\Atractivos\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    /**
     * Tabla de imágenes polimórfica (entidad_tipo + entidad_id).
     * No usa Eloquent morphs() para respetar el esquema SQL original.
     * Las imágenes no se actualizan, solo se crean o eliminan.
     */
    protected $table = 'imagenes';

    // Solo created_at, sin updated_at (coincide con el schema SQL).
    const UPDATED_AT = null;

    protected $fillable = [
        'entidad_tipo',
        'entidad_id',
        'url',
        'orden',
        'alt_text',
    ];

    protected function casts(): array
    {
        return [
            'orden'      => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // ─── Scopes de filtro por entidad ────────────────────────────────────────

    public function scopeDeAtractivo($query, int $id)
    {
        return $query->where('entidad_tipo', 'atractivo')->where('entidad_id', $id);
    }

    public function scopeDeEstablecimiento($query, int $id)
    {
        return $query->where('entidad_tipo', 'establecimiento')->where('entidad_id', $id);
    }

    public function scopeDeDestino($query, int $id)
    {
        return $query->where('entidad_tipo', 'destino')->where('entidad_id', $id);
    }
}
