<?php

namespace App\Modules\Establecimientos\Models;

use App\Modules\Atractivos\Models\CategoriaInteres;
use App\Modules\Atractivos\Models\Imagen;
use App\Modules\Destinos\Models\Destino;
use App\Modules\Usuarios\Models\Usuario;
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

    /**
     * Horarios semanales desde la tabla horarios.
     */
    public function getHorariosAttribute(): array
    {
        $rows = \Illuminate\Support\Facades\DB::table('horarios')
            ->where('establecimiento_id', $this->id)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[$row->dia] = [
                'abre'   => substr($row->hora_inicio, 0, 5),
                'cierra' => substr($row->hora_fin, 0, 5),
            ];
        }
        return $result;
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    /**
     * El establecimiento pertenece a un destino.
     * Destino es gestionado por feature/destinos.
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    /**
     * El establecimiento pertenece a un proveedor (usuario con rol proveedor).
     * Usuario/autenticación gestionado por feature/auth.
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'proveedor_id');
    }

    /**
     * El establecimiento puede pertenecer a múltiples categorías de interés.
     */
    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'establecimiento_categoria',
            'establecimiento_id',
            'categoria_id'
        );
    }

    /**
     * Imágenes adicionales del establecimiento (galería).
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'entidad_id')
                    ->where('entidad_tipo', 'establecimiento')
                    ->orderBy('orden');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /** Filtra solo establecimientos activos. */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /** Filtra por destino. */
    public function scopeDeDestino($query, int $destinoId)
    {
        return $query->where('destino_id', $destinoId);
    }

    /** Filtra por proveedor (dueño del registro). */
    public function scopeDelProveedor($query, int $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    /** Filtra por tipo de establecimiento. */
    public function scopeDeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /** Filtra por rango de precio. */
    public function scopeDeRango($query, string $rango)
    {
        return $query->where('rango_precio', $rango);
    }
}
