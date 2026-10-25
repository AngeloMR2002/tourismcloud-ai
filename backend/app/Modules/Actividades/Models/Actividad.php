<?php

namespace App\Modules\Actividades\Models;

use App\Modules\Destinos\Models\Destino;
use App\Modules\Atractivos\Models\CategoriaInteres;
use Database\Factories\ActividadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Actividad extends Model
{
    /** @use HasFactory<ActividadFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'destino_id',
        'nombre',
        'descripcion',
        'costo',
        'duracion_estimada_min',
        'latitud',
        'longitud',
        'imagen_portada',
        'estado',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'costo' => 'decimal:2',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Destino, $this>
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    /**
     * @return BelongsToMany<CategoriaInteres, $this>
     */
    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaInteres::class,
            'actividad_categoria',
            'actividad_id',
            'categoria_id',
        );
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDeDestino(Builder $query, int $destinoId): Builder
    {
        return $query->where('destino_id', $destinoId);
    }

    public function getPortadaUrlAttribute(): ?string
    {
        $ruta = $this->rutaPortadaSegura();

        if ($ruta === null) {
            return null;
        }

        try {
            $disco = Storage::disk('public');

            return $disco->exists($ruta) ? $disco->url($ruta) : null;
        } catch (Throwable $throwable) {
            report($throwable);

            return null;
        }
    }

    public function getUbicacionUrlAttribute(): ?string
    {
        if (! is_numeric($this->latitud) || ! is_numeric($this->longitud)) {
            return null;
        }

        $latitud = (float) $this->latitud;
        $longitud = (float) $this->longitud;

        if ($latitud < -90 || $latitud > 90 || $longitud < -180 || $longitud > 180) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode("{$this->latitud},{$this->longitud}");
    }

    protected static function newFactory(): ActividadFactory
    {
        return ActividadFactory::new();
    }

    private function rutaPortadaSegura(): ?string
    {
        if (! is_string($this->imagen_portada)) {
            return null;
        }

        $ruta = $this->imagen_portada;

        if (
            $ruta === ''
            || str_starts_with($ruta, '/')
            || str_contains($ruta, '..')
            || str_contains($ruta, '\\')
            || ! (
                str_starts_with($ruta, 'actividades/portadas/')
                || str_starts_with($ruta, 'actividades-rutas-demo/')
            )
        ) {
            return null;
        }

        return $ruta;
    }
}
