<?php

namespace App\Modules\Atractivos\Models;

use App\Models\CategoriaInteres;
use App\Models\Destino;
use App\Models\Imagen;
use App\Models\Horario;
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
        'costo_entrada',
        'duracion_estimada_min',
        'latitud',
        'longitud',
        'imagen_portada',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo_entrada' => 'decimal:2',
            'latitud'       => 'decimal:7',
            'longitud'      => 'decimal:7',
            'deleted_at'    => 'datetime',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'atractivo_categoria',
            'atractivo_id',
            'categoria_id'
        );
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'atractivo_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'entidad_id')
            ->where('entidad_tipo', 'atractivo')
            ->orderBy('orden');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDeDestino($query, int $destinoId)
    {
        return $query->where('destino_id', $destinoId);
    }

    public function scopeCostoEntre($query, float $min, float $max)
    {
        return $query->whereBetween('costo_entrada', [$min, $max]);
    }
}