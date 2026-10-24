<?php

namespace App\Modules\Valoraciones\Traits;

use App\Modules\Valoraciones\Models\Valoracion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Collection<int, Valoracion> $valoraciones
 * @property-read float $promedio_puntuacion
 * @property-read int $total_resenas
 * @property-read array $distribucion_estrellas
 * @property-read Valoracion|null $mi_valoracion
 *
 * @mixin Model
 */
trait HasValoraciones
{
    /**
     * Relación polimórfica hacia las valoraciones asociadas al modelo.
     */
    public function valoraciones(): MorphMany
    {
        return $this->morphMany(Valoracion::class, 'valorable')->orderByDesc('created_at');
    }

    /**
     * Accessor: Promedio de puntuación redondeado a 1 decimal (ej. 4.8 o 0.0).
     */
    public function getPromedioPuntuacionAttribute(): float
    {
        if (array_key_exists('valoraciones_avg_puntuacion', $this->attributes)) {
            return round((float) $this->attributes['valoraciones_avg_puntuacion'], 1);
        }
        $avg = $this->relationLoaded('valoraciones')
            ? $this->valoraciones->avg('puntuacion')
            : $this->valoraciones()->avg('puntuacion');

        return $avg ? (float) round((float) $avg, 1) : 0.0;
    }

    /**
     * Accessor: Número total de reseñas registradas.
     */
    public function getTotalResenasAttribute(): int
    {
        if (array_key_exists('valoraciones_count', $this->attributes)) {
            return (int) $this->attributes['valoraciones_count'];
        }

        return $this->relationLoaded('valoraciones')
            ? $this->valoraciones->count()
            : $this->valoraciones()->count();
    }

    /**
     * Accessor: Distribución porcentual y conteo de estrellas (del 5 al 1).
     *
     * Retorna un arreglo asociativo:
     * [
     *    5 => ['estrella' => 5, 'cantidad' => X, 'porcentaje' => Y],
     *    ...
     *    1 => ['estrella' => 1, 'cantidad' => X, 'porcentaje' => Y],
     * ]
     */
    public function getDistribucionEstrellasAttribute(): array
    {
        $total = $this->total_resenas;

        if ($this->relationLoaded('valoraciones')) {
            $conteos = $this->valoraciones->groupBy('puntuacion')->map->count()->all();
        } else {
            $conteos = $this->valoraciones()
                ->reorder()
                ->selectRaw('puntuacion, count(*) as cantidad')
                ->groupBy('puntuacion')
                ->pluck('cantidad', 'puntuacion')
                ->all();
        }

        $distribucion = [];
        foreach ([5, 4, 3, 2, 1] as $estrella) {
            $cantidad = (int) ($conteos[$estrella] ?? 0);
            $porcentaje = $total > 0 ? (float) round(($cantidad / $total) * 100, 1) : 0.0;

            $distribucion[$estrella] = [
                'estrella' => $estrella,
                'cantidad' => $cantidad,
                'porcentaje' => $porcentaje,
            ];
        }

        return $distribucion;
    }

    /**
     * Accessor: Retorna la valoración realizada por el usuario actualmente autenticado (si existe).
     */
    public function getMiValoracionAttribute(): ?Valoracion
    {
        if (! Auth::check()) {
            return null;
        }

        return $this->relationLoaded('valoraciones')
            ? $this->valoraciones->firstWhere('usuario_id', Auth::id())
            : $this->valoraciones()->where('usuario_id', Auth::id())->first();
    }
}
