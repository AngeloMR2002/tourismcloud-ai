<?php

namespace App\Modules\Actividades\Services;

use App\Modules\Actividades\Models\Actividad;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ActividadService
{
    /**
     * @param  array{buscar?: string, destino_id?: int, costo_max?: float|int, duracion_max?: int}  $filtros
     */
    public function listarCatalogo(array $filtros = [], int $porPagina = 12): LengthAwarePaginator
    {
        $consulta = Actividad::query()
            ->with('destino')
            ->activas()
            ->whereHas('destino', function (Builder $consulta): void {
                $consulta->where('estado', 'activo');
            });

        $this->aplicarFiltros($consulta, $filtros);

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
        $consulta = Actividad::query()
            ->with('destino')
            ->whereHas('destino', function (Builder $consulta) use ($operadorId): void {
                $consulta->where('operador_id', $operadorId);
            });

        $this->aplicarFiltros($consulta, $filtros);

        if (! empty($filtros['estado'])) {
            $consulta->where('estado', $filtros['estado']);
        }

        return $consulta
            ->latest('updated_at')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos, ?UploadedFile $portada = null): Actividad
    {
        $rutaNueva = null;

        try {
            /** @var Actividad $actividad */
            $actividad = DB::transaction(function () use ($datos, $portada, &$rutaNueva): Actividad {
                if ($portada !== null) {
                    $rutaNueva = $this->guardarPortada($portada);
                    $datos['imagen_portada'] = $rutaNueva;
                }

                return Actividad::query()->create($datos);
            });
        } catch (Throwable $throwable) {
            $this->eliminarArchivoSeguro($rutaNueva);

            throw $throwable;
        }

        return $actividad->fresh('destino');
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(
        Actividad $actividad,
        array $datos,
        ?UploadedFile $portada = null,
        bool $quitarPortada = false,
    ): Actividad {
        $rutaAnterior = is_string($actividad->imagen_portada) ? $actividad->imagen_portada : null;
        $rutaNueva = null;

        try {
            /** @var Actividad $actividadActualizada */
            $actividadActualizada = DB::transaction(function () use ($actividad, $datos, $portada, $quitarPortada, &$rutaNueva): Actividad {
                if ($portada !== null) {
                    $rutaNueva = $this->guardarPortada($portada);
                    $datos['imagen_portada'] = $rutaNueva;
                } elseif ($quitarPortada) {
                    $datos['imagen_portada'] = null;
                }

                $actividad->update($datos);

                return $actividad->fresh('destino');
            });
        } catch (Throwable $throwable) {
            $this->eliminarArchivoSeguro($rutaNueva);

            throw $throwable;
        }

        if (($portada !== null || $quitarPortada) && $rutaAnterior !== $rutaNueva) {
            $this->eliminarArchivoSeguro($rutaAnterior);
        }

        return $actividadActualizada;
    }

    public function eliminar(Actividad $actividad): void
    {
        DB::transaction(function () use ($actividad): void {
            $actividad->delete();
        });
    }

    public function toggleEstado(Actividad $actividad): Actividad
    {
        return DB::transaction(function () use ($actividad): Actividad {
            $actividad->estado = $actividad->estado === 'activo' ? 'inactivo' : 'activo';
            $actividad->save();

            return $actividad->fresh('destino');
        });
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    private function aplicarFiltros(Builder $consulta, array $filtros): void
    {
        if (! empty($filtros['destino_id'])) {
            $consulta->deDestino((int) $filtros['destino_id']);
        }

        if (isset($filtros['costo_max']) && $filtros['costo_max'] !== '') {
            $consulta->where('costo', '<=', $filtros['costo_max']);
        }

        if (isset($filtros['duracion_max']) && $filtros['duracion_max'] !== '') {
            $consulta->where('duracion_estimada_min', '<=', $filtros['duracion_max']);
        }

        if (! empty($filtros['buscar'])) {
            $termino = '%'.$filtros['buscar'].'%';

            $consulta->where(function (Builder $subconsulta) use ($termino): void {
                $subconsulta
                    ->where('nombre', 'ilike', $termino)
                    ->orWhere('descripcion', 'ilike', $termino);
            });
        }
    }

    private function eliminarArchivoSeguro(?string $ruta): void
    {
        if (! $this->esRutaPortadaValida($ruta)) {
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
        $ruta = $portada->store('actividades/portadas', 'public');

        if (! is_string($ruta) || $ruta === '') {
            throw new RuntimeException('No fue posible guardar la imagen de portada.');
        }

        return $ruta;
    }

    private function esRutaPortadaValida(?string $ruta): bool
    {
        return is_string($ruta)
            && str_starts_with($ruta, 'actividades/portadas/')
            && ! str_contains($ruta, '..')
            && ! str_contains($ruta, '\\');
    }
}
