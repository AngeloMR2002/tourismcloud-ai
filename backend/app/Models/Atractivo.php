<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atractivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'atractivos';

    protected $fillable = [
        'destino_id',
        'nombre',
        'descripcion',
        'horarios',
        'costo_entrada',
        'duracion_estimada_min',
        'latitud',
        'longitud',
        'imagen_portada',
        'estado',
    ];

    /**
     * Los campos horarios se castean automáticamente a/desde array PHP.
     * Esto garantiza que $atractivo->horarios devuelva un array,
     * no un string JSON, sin necesidad de json_decode() manual.
     */
    protected function casts(): array
    {
        return [
            'horarios'      => 'array',
            'costo_entrada' => 'decimal:2',
            'latitud'       => 'decimal:7',
            'longitud'      => 'decimal:7',
            'deleted_at'    => 'datetime',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    /**
     * El atractivo pertenece a un destino.
     * Destino es gestionado por feature/destinos; el modelo se asume existente.
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    /**
     * El atractivo puede pertenecer a múltiples categorías de interés.
     */
    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'atractivo_categoria',
            'atractivo_id',
            'categoria_id'
        );
    }

    /**
     * Imágenes adicionales del atractivo (galería).
     * Usa la relación polimórfica manual via columna entidad_tipo.
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'entidad_id')
                    ->where('entidad_tipo', 'atractivo')
                    ->orderBy('orden');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /** Filtra solo atractivos activos. */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /** Filtra por destino. */
    public function scopeDeDestino($query, int $destinoId)
    {
        return $query->where('destino_id', $destinoId);
    }

    /** Filtra por rango de costo de entrada. */
    public function scopeCostoEntre($query, float $min, float $max)
    {
        return $query->whereBetween('costo_entrada', [$min, $max]);
    }
}
