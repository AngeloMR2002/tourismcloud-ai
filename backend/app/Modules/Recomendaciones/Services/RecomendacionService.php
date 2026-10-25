<?php

namespace App\Modules\Recomendaciones\Services;

use App\Modules\Actividades\Models\Actividad;
use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Preferencias\Models\PreferenciaTurista;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RecomendacionService
{
    /**
     * @return array{
     *     recomendacionesAtractivos: Collection<int, array{item: Atractivo, intereses: array<int, string>, dentroPresupuesto: bool}>,
     *     recomendacionesActividades: Collection<int, array{item: Actividad, intereses: array<int, string>, dentroPresupuesto: bool}>,
     *     tienePreferencias: bool,
     *     categoriasPreferidas: Collection<int, string>
     * }
     */
    public function paraTurista(int $turistaId): array
    {
        $preferencia = PreferenciaTurista::query()
            ->with('categorias')
            ->where('turista_id', $turistaId)
            ->first();

        $categoriasIds = $preferencia?->categorias->modelKeys() ?? [];
        $presupuesto = $preferencia?->presupuesto_max === null
            ? null
            : (float) $preferencia->presupuesto_max;
        $limite = $preferencia === null ? 3 : 6;

        $atractivos = Atractivo::query()
            ->activos()
            ->whereHas('destino', fn (Builder $destino) => $destino->where('estado', 'activo'));
        $actividades = Actividad::query()
            ->activas()
            ->whereHas('destino', fn (Builder $destino) => $destino->where('estado', 'activo'));

        return [
            'recomendacionesAtractivos' => $this->rankear($atractivos, 'costo_entrada', $categoriasIds, $presupuesto, $limite),
            'recomendacionesActividades' => $this->rankear($actividades, 'costo', $categoriasIds, $presupuesto, $limite),
            'tienePreferencias' => $preferencia !== null,
            'categoriasPreferidas' => $preferencia?->categorias->pluck('nombre')->values() ?? collect(),
        ];
    }

    /**
     * @param  array<int, int>  $categoriaIds
     * @return Collection<int, array{item: Atractivo|Actividad, intereses: array<int, string>, dentroPresupuesto: bool}>
     */
    private function rankear(
        Builder $consulta,
        string $columnaCosto,
        array $categoriaIds,
        ?float $presupuesto,
        int $limite,
    ): Collection {
        $consulta->with(['destino', 'categorias']);

        if ($categoriaIds !== []) {
            $consulta->withCount([
                'categorias as coincidencias_categoria_count' => fn (Builder $categorias) => $categorias
                    ->whereIn('categorias_interes.id', $categoriaIds),
            ])->orderByDesc('coincidencias_categoria_count');
        }

        if ($categoriaIds === [] && $presupuesto === null) {
            return collect();
        }

        $consulta->where(function (Builder $coincidencias) use ($categoriaIds, $presupuesto, $columnaCosto): void {
            if ($categoriaIds !== []) {
                $coincidencias->whereHas('categorias', fn (Builder $categorias) => $categorias
                    ->whereIn('categorias_interes.id', $categoriaIds));
            }

            if ($presupuesto !== null) {
                if ($categoriaIds !== []) {
                    $coincidencias->orWhere($columnaCosto, '<=', $presupuesto);
                } else {
                    $coincidencias->where($columnaCosto, '<=', $presupuesto);
                }
            }
        });

        if ($presupuesto !== null) {
            $consulta->orderByRaw("CASE WHEN {$columnaCosto} <= ? THEN 1 ELSE 0 END DESC", [$presupuesto]);
        }

        return $consulta
            ->orderBy('nombre')
            ->limit($limite)
            ->get()
            ->map(function (Atractivo|Actividad $item) use ($categoriaIds, $presupuesto, $columnaCosto): array {
                $costo = (float) $item->getAttribute($columnaCosto);

                return [
                    'item' => $item,
                    'intereses' => $item->categorias
                        ->filter(fn ($categoria): bool => in_array((int) $categoria->id, $categoriaIds, true))
                        ->pluck('nombre')
                        ->values()
                        ->all(),
                    'dentroPresupuesto' => $presupuesto !== null && $costo <= $presupuesto,
                ];
            })
            ->values();
    }
}
