<?php

namespace App\Modules\Actividades\Services;

use App\Modules\Actividades\Models\Actividad;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ActividadService
{
    /**
     * Lista actividades con filtros opcionales para turistas o el panel de operador.
     */
    public function listarConFiltros(array $filtros = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Actividad::with(['destino', 'atractivo', 'operador']);

        if (empty($filtros['incluir_inactivos'])) {
            $query->activos()->aprobadas()->whereHas('destino', fn ($q) => $q->activos());
        }

        // Filtro por estado (por defecto solo activas en catálogo público)
        if (! empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        } elseif (! isset($filtros['incluir_inactivos']) || ! $filtros['incluir_inactivos']) {
            $query->activos();
        }

        // Búsqueda por texto (nombre o descripción)
        if (! empty($filtros['buscar'])) {
            $termino = '%'.trim($filtros['buscar']).'%';
            $query->where(function ($q) use ($termino) {
                $q->whereLike('nombre', $termino)
                    ->orWhereLike('descripcion', $termino)
                    ->orWhereLike('punto_encuentro', $termino);
            });
        }

        // Filtro por destino
        if (! empty($filtros['destino_id'])) {
            $query->deDestino((int) $filtros['destino_id']);
        }

        // Filtro por atractivo
        if (! empty($filtros['atractivo_id'])) {
            $query->deAtractivo((int) $filtros['atractivo_id']);
        }

        // Filtro por operador (panel operador)
        if (! empty($filtros['operador_id'])) {
            $query->where('operador_id', (int) $filtros['operador_id']);
        }

        // Filtro por categoría
        if (! empty($filtros['categoria'])) {
            $query->categoria($filtros['categoria']);
        }

        // Filtro por dificultad
        if (! empty($filtros['nivel_dificultad'])) {
            $query->dificultad($filtros['nivel_dificultad']);
        }

        // Filtro por precio máximo (restricción presupuestaria)
        if (isset($filtros['precio_max']) && is_numeric($filtros['precio_max'])) {
            $query->precioMaximo((float) $filtros['precio_max']);
        }

        // Filtro por duración máxima en minutos (restricción de tiempo)
        if (isset($filtros['duracion_max']) && is_numeric($filtros['duracion_max'])) {
            $query->duracionMaxima((int) $filtros['duracion_max']);
        }

        // Ordenamiento
        $orden = $filtros['orden'] ?? 'recientes';
        match ($orden) {
            'precio_asc' => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            'duracion_asc' => $query->orderBy('duracion_min', 'asc'),
            'nombre_asc' => $query->orderBy('nombre', 'asc'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene una actividad por su ID con sus relaciones cargadas.
     */
    public function obtenerPorId(int $id, bool $soloActiva = false): Actividad
    {
        $query = Actividad::with(['destino', 'atractivo', 'operador']);

        if ($soloActiva) {
            $query->activos()->aprobadas()->whereHas('destino', fn ($q) => $q->activos());
        }

        return $query->findOrFail($id);
    }

    /**
     * Registra una nueva actividad turística en el sistema.
     */
    public function crear(array $datos): Actividad
    {
        return Actividad::create($datos);
    }

    /**
     * Actualiza los datos de una actividad existente.
     */
    public function actualizar(Actividad $actividad, array $datos): Actividad
    {
        $actividad->update($datos);

        return $actividad->fresh(['destino', 'atractivo']);
    }

    /**
     * Elimina lógicamente (soft delete) una actividad.
     */
    public function eliminar(Actividad $actividad): bool
    {
        return (bool) $actividad->delete();
    }

    /**
     * Alterna el estado activo / inactivo de una actividad.
     */
    public function toggleEstado(Actividad $actividad): Actividad
    {
        $nuevoEstado = $actividad->estado === 'activo' ? 'inactivo' : 'activo';
        $actividad->update(['estado' => $nuevoEstado]);

        return $actividad;
    }

    /**
     * Consulta estructurada para el motor de recomendación y optimización de IA (P4 / P5).
     * Devuelve actividades activas compatibles con las restricciones dadas.
     */
    public function obtenerParaOptimizadorIA(
        int $destinoId,
        ?float $presupuestoMax = null,
        ?int $tiempoMaxMin = null,
        array $categorias = []
    ): Collection {
        $query = Actividad::with(['destino', 'atractivo'])
            ->activos()
            ->aprobadas()
            ->deDestino($destinoId);

        if ($presupuestoMax !== null) {
            $query->precioMaximo($presupuestoMax);
        }

        if ($tiempoMaxMin !== null) {
            $query->duracionMaxima($tiempoMaxMin);
        }

        if (! empty($categorias)) {
            $query->whereIn('categoria', $categorias);
        }

        return $query->whereHas('destino', fn ($q) => $q->activos())->orderBy('id')->limit(20)->get();
    }
}
