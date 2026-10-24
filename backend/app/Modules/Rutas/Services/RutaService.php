<?php

namespace App\Modules\Rutas\Services;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Rutas\Models\Ruta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class RutaService
{
    /**
     * @param  array{buscar?: string, destino_id?: int, duracion_max?: int, distancia_max?: float|int}  $filtros
     */
    public function listarCatalogo(array $filtros = [], int $porPagina = 12): LengthAwarePaginator
    {
        $consulta = Ruta::query()
            ->with(['destino', 'paradas.atractivo'])
            ->withCount('paradas');

        $this->aplicarVisibilidadPublica($consulta);
        $this->aplicarFiltrosCatalogo($consulta, $filtros);

        return $consulta
            ->orderBy('nombre')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * @param  array{buscar?: string, destino_id?: int, estado?: string}  $filtros
     */
    public function listarDelOperador(int $operadorId, array $filtros = [], int $porPagina = 15): LengthAwarePaginator
    {
        $consulta = Ruta::query()
            ->with(['destino', 'paradas.atractivo'])
            ->withCount('paradas')
            ->whereHas('destino', function (Builder $consulta) use ($operadorId): void {
                $consulta->where('operador_id', $operadorId);
            });

        if (! empty($filtros['destino_id'])) {
            $consulta->where('rutas.destino_id', (int) $filtros['destino_id']);
        }

        if (! empty($filtros['estado'])) {
            $consulta->where('rutas.estado', $filtros['estado']);
        }

        $this->aplicarBusqueda($consulta, $filtros['buscar'] ?? null);

        return $consulta
            ->latest('rutas.updated_at')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function obtenerPublica(Ruta $ruta): ?Ruta
    {
        $consulta = Ruta::query()
            ->whereKey($ruta->getKey())
            ->with(['destino', 'paradas.atractivo']);

        $this->aplicarVisibilidadPublica($consulta);

        return $consulta->first();
    }

    /**
     * @param  array<string, mixed>  $datos
     * @param  array<int, mixed>  $paradas
     */
    public function crear(array $datos, array $paradas, ?UploadedFile $portada = null): Ruta
    {
        $rutaNueva = null;

        try {
            /** @var Ruta $ruta */
            $ruta = DB::transaction(function () use ($datos, $paradas, $portada, &$rutaNueva): Ruta {
                unset($datos['imagen_portada']);

                $paradasValidas = $this->bloquearParadasValidas($paradas, (int) $datos['destino_id']);

                if ($portada !== null) {
                    $rutaNueva = $this->guardarPortada($portada);
                    $datos['imagen_portada'] = $rutaNueva;
                }

                $ruta = Ruta::query()->create($datos);
                $this->reemplazarParadas($ruta, $paradasValidas);

                return $ruta->fresh(['destino', 'paradas.atractivo']);
            });
        } catch (Throwable $throwable) {
            $this->eliminarArchivoPropio($rutaNueva);

            throw $throwable;
        }

        return $ruta;
    }

    /**
     * @param  array<string, mixed>  $datos
     * @param  array<int, mixed>  $paradas
     */
    public function actualizar(
        Ruta $ruta,
        array $datos,
        array $paradas,
        ?UploadedFile $portada = null,
        bool $quitarPortada = false,
    ): Ruta {
        $rutaAnterior = $this->rutaPortadaPropia($ruta->imagen_portada);
        $rutaNueva = null;

        try {
            /** @var Ruta $rutaActualizada */
            $rutaActualizada = DB::transaction(function () use ($ruta, $datos, $paradas, $portada, $quitarPortada, &$rutaNueva): Ruta {
                /** @var Ruta $rutaBloqueada */
                $rutaBloqueada = Ruta::query()->lockForUpdate()->findOrFail($ruta->getKey());
                unset($datos['imagen_portada']);

                $paradasValidas = $this->bloquearParadasValidas($paradas, (int) $datos['destino_id']);

                if ($portada !== null) {
                    $rutaNueva = $this->guardarPortada($portada);
                    $datos['imagen_portada'] = $rutaNueva;
                } elseif ($quitarPortada) {
                    $datos['imagen_portada'] = null;
                }

                $rutaBloqueada->update($datos);
                $this->reemplazarParadas($rutaBloqueada, $paradasValidas);

                return $rutaBloqueada->fresh(['destino', 'paradas.atractivo']);
            });
        } catch (Throwable $throwable) {
            $this->eliminarArchivoPropio($rutaNueva);

            throw $throwable;
        }

        if (($portada !== null || $quitarPortada) && $rutaAnterior !== $rutaNueva) {
            $this->eliminarArchivoPropio($rutaAnterior);
        }

        return $rutaActualizada;
    }

    public function eliminar(Ruta $ruta): void
    {
        $rutaPortada = DB::transaction(function () use ($ruta): ?string {
            /** @var Ruta $rutaBloqueada */
            $rutaBloqueada = Ruta::query()->lockForUpdate()->findOrFail($ruta->getKey());
            $rutaPortada = $this->rutaPortadaPropia($rutaBloqueada->imagen_portada);

            $rutaBloqueada->delete();

            return $rutaPortada;
        });

        $this->eliminarArchivoPropio($rutaPortada);
    }

    public function toggleEstado(Ruta $ruta): Ruta
    {
        return DB::transaction(function () use ($ruta): Ruta {
            /** @var Ruta $rutaBloqueada */
            $rutaBloqueada = Ruta::query()->lockForUpdate()->findOrFail($ruta->getKey());
            $rutaBloqueada->estado = $rutaBloqueada->estado === 'activo' ? 'inactivo' : 'activo';
            $rutaBloqueada->save();

            return $rutaBloqueada->fresh(['destino', 'paradas.atractivo']);
        });
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    private function aplicarFiltrosCatalogo(Builder $consulta, array $filtros): void
    {
        if (! empty($filtros['destino_id'])) {
            $consulta->where('rutas.destino_id', (int) $filtros['destino_id']);
        }

        if (isset($filtros['duracion_min']) && $filtros['duracion_min'] !== '') {
            $consulta->where('rutas.duracion_estimada_min', '>=', $filtros['duracion_min']);
        }
        foreach (['costo_min' => '>=', 'costo_max' => '<='] as $campo => $operador) {
            if (isset($filtros[$campo]) && $filtros[$campo] !== '') {
                $consulta->whereRaw(
                    "(SELECT COALESCE(SUM(a.costo_entrada), 0) FROM ruta_paradas p JOIN atractivos a ON a.id = p.atractivo_id WHERE p.ruta_id = rutas.id AND a.deleted_at IS NULL) {$operador} ?",
                    [$filtros[$campo]]
                );
            }
        }
        if (isset($filtros['duracion_max']) && $filtros['duracion_max'] !== '') {
            $consulta->where('rutas.duracion_estimada_min', '<=', $filtros['duracion_max']);
        }

        if (isset($filtros['distancia_max']) && $filtros['distancia_max'] !== '') {
            $consulta->where('rutas.distancia_km', '<=', $filtros['distancia_max']);
        }

        $this->aplicarBusqueda($consulta, $filtros['buscar'] ?? null);
    }

    private function aplicarBusqueda(Builder $consulta, mixed $buscar): void
    {
        if (! is_string($buscar) || trim($buscar) === '') {
            return;
        }

        $termino = '%'.trim($buscar).'%';

        $consulta->where(function (Builder $subconsulta) use ($termino): void {
            $subconsulta
                ->where('rutas.nombre', 'ilike', $termino)
                ->orWhere('rutas.descripcion', 'ilike', $termino);
        });
    }

    public function aplicarVisibilidadPublica(Builder $consulta): void
    {
        $conteoAtractivosValidosDistintos = <<<'SQL'
SELECT COUNT(DISTINCT atractivo.id)
FROM ruta_paradas AS parada
INNER JOIN atractivos AS atractivo ON atractivo.id = parada.atractivo_id
WHERE parada.ruta_id = rutas.id
  AND atractivo.estado = ?
  AND atractivo.deleted_at IS NULL
  AND atractivo.destino_id = rutas.destino_id
SQL;

        $conteoParadasTotales = <<<'SQL'
SELECT COUNT(*)
FROM ruta_paradas AS parada
WHERE parada.ruta_id = rutas.id
SQL;

        $consulta
            ->where('rutas.estado', 'activo')
            ->whereHas('destino', function (Builder $consulta): void {
                $consulta->where('estado', 'activo');
            })
            ->whereRaw("({$conteoAtractivosValidosDistintos}) >= 2", ['activo'])
            ->whereRaw("({$conteoParadasTotales}) = ({$conteoAtractivosValidosDistintos})", ['activo']);
    }

    /**
     * @param  array<int, mixed>  $paradas
     * @return array<int, int>
     */
    private function bloquearParadasValidas(array $paradas, int $destinoId): array
    {
        $ids = [];

        foreach ($paradas as $paradaId) {
            if (! is_int($paradaId) && ! (is_string($paradaId) && ctype_digit($paradaId))) {
                throw ValidationException::withMessages([
                    'paradas' => __('operator_routes.stops_valid'),
                ]);
            }

            $ids[] = (int) $paradaId;
        }

        $ids = array_values(array_unique($ids));

        if (count($ids) < 2 || count($ids) !== count($paradas)) {
            throw ValidationException::withMessages([
                'paradas' => __('operator_routes.stops_minimum_distinct'),
            ]);
        }

        $atractivosBloqueados = Atractivo::query()
            ->whereIn('id', $ids)
            ->where('destino_id', $destinoId)
            ->where('estado', 'activo')
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id']);

        $cantidadValidas = $atractivosBloqueados->count();

        if ($cantidadValidas !== count($ids)) {
            throw ValidationException::withMessages([
                'paradas' => __('operator_routes.stops_valid'),
            ]);
        }

        return $ids;
    }

    /**
     * @param  array<int, int>  $paradas
     */
    private function reemplazarParadas(Ruta $ruta, array $paradas): void
    {
        $ruta->paradas()->delete();

        foreach ($paradas as $indice => $atractivoId) {
            $ruta->paradas()->create([
                'atractivo_id' => $atractivoId,
                'orden' => $indice + 1,
            ]);
        }
    }

    private function eliminarArchivoPropio(?string $ruta): void
    {
        $ruta = $this->rutaPortadaPropia($ruta);

        if ($ruta === null) {
            return;
        }

        try {
            $disco = Storage::disk('public');

            if ($disco->exists($ruta)) {
                $disco->delete($ruta);
            }
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function guardarPortada(UploadedFile $portada): string
    {
        $ruta = $portada->store('rutas/portadas', 'public');
        if ($ruta === false) {
            throw new RuntimeException('No se pudo guardar la portada de la ruta.');
        }

        return $ruta;
    }

    private function rutaPortadaPropia(mixed $ruta): ?string
    {
        if (! is_string($ruta)) {
            return null;
        }

        $ruta = ltrim($ruta, '/');

        if (
            $ruta === ''
            || str_contains($ruta, '..')
            || str_contains($ruta, '\\')
            || preg_match('/^[a-z][a-z0-9+.-]*:/i', $ruta) === 1
            || ! str_starts_with($ruta, 'rutas/portadas/')
        ) {
            return null;
        }

        return $ruta;
    }
}
