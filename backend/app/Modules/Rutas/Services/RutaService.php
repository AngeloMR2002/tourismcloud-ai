<?php

namespace App\Modules\Rutas\Services;

use App\Modules\Rutas\Models\Ruta;
use App\Modules\Rutas\Models\RutaPunto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RutaService
{
    /**
     * Lista rutas y circuitos turísticos con filtros para catálogo o panel.
     */
    public function listarConFiltros(array $filtros = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Ruta::with(['destino', 'operador', 'puntos.atractivo']);

        if (isset($filtros['operador_id'])) {
            $query->where('operador_id', (int) $filtros['operador_id']);
        }

        if (empty($filtros['incluir_inactivos'])) {
            $query->activas()->aprobadas()->whereHas('destino', fn ($q) => $q->activos());
        }

        // Filtro por estado
        if (! empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        } elseif (! isset($filtros['incluir_inactivos']) || ! $filtros['incluir_inactivos']) {
            $query->activas();
        }

        // Búsqueda textual
        if (! empty($filtros['buscar'])) {
            $termino = '%'.trim($filtros['buscar']).'%';
            $query->where(function ($q) use ($termino) {
                $q->whereLike('nombre', $termino)
                    ->orWhereLike('descripcion', $termino)
                    ->orWhereLike('temporada_recomendada', $termino);
            });
        }

        // Filtro por destino
        if (! empty($filtros['destino_id'])) {
            $query->deDestino((int) $filtros['destino_id']);
        }

        // Filtro por dificultad
        if (! empty($filtros['nivel_dificultad'])) {
            $query->dificultad($filtros['nivel_dificultad']);
        }

        // Filtro por transporte recomendado
        if (! empty($filtros['transporte_recomendado'])) {
            $query->transporte($filtros['transporte_recomendado']);
        }

        // Filtro por tipo de circuito
        if (! empty($filtros['tipo_ruta'])) {
            $query->tipoRuta($filtros['tipo_ruta']);
        }

        // Filtro por duración máxima en horas
        if (isset($filtros['duracion_max_horas']) && is_numeric($filtros['duracion_max_horas'])) {
            $query->where('duracion_estimada_horas', '<=', (float) $filtros['duracion_max_horas']);
        }

        // Ordenamiento
        $orden = $filtros['orden'] ?? 'recientes';
        match ($orden) {
            'duracion_asc' => $query->orderBy('duracion_estimada_horas', 'asc'),
            'distancia_asc' => $query->orderBy('distancia_km', 'asc'),
            'nombre_asc' => $query->orderBy('nombre', 'asc'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene una ruta por su ID con sus paradas cargadas en orden secuencial.
     */
    public function obtenerPorId(int $id, bool $soloActiva = false): Ruta
    {
        $query = Ruta::with(['destino', 'operador', 'puntos.atractivo']);

        if ($soloActiva) {
            $query->activas()->aprobadas()->whereHas('destino', fn ($q) => $q->activos());
        }

        return $query->findOrFail($id);
    }

    /**
     * Registra una nueva ruta junto con sus puntos / paradas dentro de una transacción.
     */
    public function crearConPuntos(array $datosRuta, array $puntos = []): Ruta
    {
        return DB::transaction(function () use ($datosRuta, $puntos) {
            $ruta = Ruta::create($datosRuta);

            if (! empty($puntos)) {
                $this->sincronizarPuntos($ruta, $puntos);
            }

            return $ruta->fresh(['destino', 'puntos.atractivo']);
        });
    }

    /**
     * Actualiza los datos de la ruta y sincroniza sus puntos / paradas.
     */
    public function actualizarConPuntos(Ruta $ruta, array $datosRuta, ?array $puntos = null): Ruta
    {
        return DB::transaction(function () use ($ruta, $datosRuta, $puntos) {
            $ruta->update($datosRuta);

            if ($puntos !== null) {
                // Reemplazamos los puntos con la nueva secuencia ordenada
                $ruta->puntos()->delete();
                $this->sincronizarPuntos($ruta, $puntos);
            }

            return $ruta->fresh(['destino', 'puntos.atractivo']);
        });
    }

    /**
     * Inserta los puntos de parada asignando el orden secuencial y recalculando totales si aplica.
     */
    protected function sincronizarPuntos(Ruta $ruta, array $puntos): void
    {
        $orden = 1;
        $distanciaTotal = 0.00;
        $distanciaCompleta = true;
        $tiempoCompleto = true;
        $tiempoTotalMinutos = 0;

        foreach ($puntos as $puntoData) {
            if (empty($puntoData['nombre_parada'])) {
                continue;
            }

            $distanciaTramo = isset($puntoData['distancia_desde_anterior_km']) ? (float) $puntoData['distancia_desde_anterior_km'] : null;
            $tiempoTraslado = isset($puntoData['tiempo_traslado_min']) ? (int) $puntoData['tiempo_traslado_min'] : null;
            $tiempoEstadia = isset($puntoData['tiempo_estadia_min']) ? (int) $puntoData['tiempo_estadia_min'] : null;
            $distanciaCompleta = $distanciaCompleta && ($orden === 1 || $distanciaTramo !== null);
            $tiempoCompleto = $tiempoCompleto && $tiempoEstadia !== null && ($orden === 1 || $tiempoTraslado !== null);

            $distanciaTotal += $distanciaTramo;
            $tiempoTotalMinutos += ($tiempoTraslado + $tiempoEstadia);

            RutaPunto::create([
                'ruta_id' => $ruta->id,
                'atractivo_id' => ! empty($puntoData['atractivo_id']) ? (int) $puntoData['atractivo_id'] : null,
                'nombre_parada' => $puntoData['nombre_parada'],
                'descripcion_parada' => $puntoData['descripcion_parada'] ?? null,
                'orden' => $orden++,
                'tiempo_estadia_min' => $tiempoEstadia,
                'distancia_desde_anterior_km' => $distanciaTramo,
                'tiempo_traslado_min' => $tiempoTraslado,
                'tipo_transporte_tramo' => $puntoData['tipo_transporte_tramo'] ?? ($ruta->transporte_recomendado ?? 'a_pie'),
                'latitud' => isset($puntoData['latitud']) ? (float) $puntoData['latitud'] : null,
                'longitud' => isset($puntoData['longitud']) ? (float) $puntoData['longitud'] : null,
            ]);
        }

        // Si la ruta no tenía distancia o duración manual, la actualizamos automáticamente con los puntos
        $updates = [];
        if ($ruta->distancia_km === null && $distanciaCompleta && $distanciaTotal > 0) {
            $updates['distancia_km'] = round($distanciaTotal, 2);
        }
        if ($ruta->duracion_estimada_horas === null && $tiempoCompleto && $tiempoTotalMinutos > 0) {
            $updates['duracion_estimada_horas'] = round($tiempoTotalMinutos / 60, 1);
        }

        if (! empty($updates)) {
            $ruta->update($updates);
        }
    }

    /**
     * Elimina lógicamente una ruta turística.
     */
    public function eliminar(Ruta $ruta): bool
    {
        return (bool) $ruta->delete();
    }

    /**
     * Alterna el estado activo / inactivo de una ruta.
     */
    public function toggleEstado(Ruta $ruta): Ruta
    {
        $nuevoEstado = $ruta->estado === 'activo' ? 'inactivo' : 'activo';
        $ruta->update(['estado' => $nuevoEstado]);

        return $ruta;
    }
}
