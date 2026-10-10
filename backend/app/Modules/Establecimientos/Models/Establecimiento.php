<?php

namespace App\Modules\Establecimientos\Models;

use App\Models\CategoriaInteres;
use App\Models\Destino;
use App\Models\Imagen;
use App\Models\Usuario;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Establecimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'establecimientos';

    protected $fillable = [
        'destino_id',
        'proveedor_id',
        'tipo',
        'nombre',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'rango_precio',
        'imagen_portada',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'latitud'    => 'decimal:7',
            'longitud'   => 'decimal:7',
            'deleted_at' => 'datetime',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'proveedor_id');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'establecimiento_categoria',
            'establecimiento_id',
            'categoria_id'
        );
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'establecimiento_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'entidad_id')
            ->where('entidad_tipo', 'establecimiento')
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

    public function scopeDelProveedor($query, int $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    public function scopeDeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDeRango($query, string $rango)
    {
        return $query->where('rango_precio', $rango);
    }
}