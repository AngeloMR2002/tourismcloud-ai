<?php

namespace App\Modules\Recomendaciones\Services;

use App\Models\Actividad;

class OptimizadorItinerarioService
{
    public function __construct(
        private CalculadoraTraslados $traslados = new CalculadoraTraslados()
    ) {
    }

    public function obtenerCandidatos(int $destinoId): array
    {
        $actividades = Actividad::query()
            ->where('destino_id', $destinoId)
            ->where('estado', 'activo')
            ->with([
                'atractivo.categorias',
                'atractivo.resenas',
                'establecimiento.resenas',
                'establecimiento',
                'categoria',
            ])
            ->get();

        return $actividades
            ->map(
                fn (Actividad $actividad) =>
                    $this->convertirACandidato($actividad)
            )
            ->values()
            ->all();
    }

    public function filtrarCandidatos(
        array $candidatos,
        array $restricciones
    ): array {
        return array_values(
            array_filter(
                $candidatos,
                function (CandidatoItinerario $candidato) use ($restricciones) {
                    return $this->cumpleCosto(
                        $candidato,
                        $restricciones
                    )
                    && $this->cumpleDuracion(
                        $candidato,
                        $restricciones
                    )
                    && $this->cumpleCategorias(
                        $candidato,
                        $restricciones
                    );
                }
            )
        );
    }

    private function cumpleCosto(
        CandidatoItinerario $candidato,
        array $restricciones
    ): bool {
        $costoMin = $restricciones['costo_min_actividad'] ?? null;
        $costoMax = $restricciones['costo_max_actividad'] ?? null;

        if (
            $costoMin !== null &&
            $candidato->costo < $costoMin
        ) {
            return false;
        }

        if (
            $costoMax !== null &&
            $candidato->costo > $costoMax
        ) {
            return false;
        }

        return true;
    }

    private function cumpleDuracion(
        CandidatoItinerario $candidato,
        array $restricciones
    ): bool {
        $duracionMin = $restricciones['duracion_min_actividad'] ?? null;
        $duracionMax = $restricciones['duracion_max_actividad'] ?? null;

        if (
            $duracionMin !== null &&
            $candidato->duracionMin < $duracionMin
        ) {
            return false;
        }

        if (
            $duracionMax !== null &&
            $candidato->duracionMin > $duracionMax
        ) {
            return false;
        }

        return true;
    }

    private function cumpleCategorias(
        CandidatoItinerario $candidato,
        array $restricciones
    ): bool {
        return true;
    }


    private function calcularDistanciaEntreAtractivos(
        ?\App\Models\Atractivo $origen,
        ?\App\Models\Atractivo $destino
    ): ?float {
        if (
            $origen === null ||
            $destino === null ||
            $origen->latitud === null ||
            $origen->longitud === null ||
            $destino->latitud === null ||
            $destino->longitud === null
        ) {
            return null;
        }

        $latitudOrigen = deg2rad((float) $origen->latitud);
        $longitudOrigen = deg2rad((float) $origen->longitud);

        $latitudDestino = deg2rad((float) $destino->latitud);
        $longitudDestino = deg2rad((float) $destino->longitud);

        $diferenciaLatitud = $latitudDestino - $latitudOrigen;
        $diferenciaLongitud = $longitudDestino - $longitudOrigen;

        $a = sin($diferenciaLatitud / 2) ** 2
            + cos($latitudOrigen)
            * cos($latitudDestino)
            * sin($diferenciaLongitud / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return round(6371 * $c, 2);
    }


    private function convertirACandidato(
        Actividad $actividad
    ): CandidatoItinerario {
        $categorias = [];

        if ($actividad->categoria) {
            $categorias[] = $actividad->categoria->nombre;
        }

        if ($actividad->atractivo) {
            foreach ($actividad->atractivo->categorias as $categoria) {
                $categorias[] = $categoria->nombre;
            }
        }

        $calificacion = null;

        if ($actividad->atractivo) {
            $calificacion = $actividad->atractivo
                ->resenas
                ->avg('calificacion');
        } elseif ($actividad->establecimiento) {
            $calificacion = $actividad->establecimiento
                ->resenas
                ->avg('calificacion');
        }

        return new CandidatoItinerario(
            actividadId: $actividad->id,
            atractivoId: $actividad->atractivo_id,
            establecimientoId: $actividad->establecimiento_id,
            nombre: $actividad->nombre,
            categorias: array_values(array_unique($categorias)),
            costo: (float) $actividad->costo,
            duracionMin: (int) $actividad->duracion_min,
            horaApertura: $actividad->atractivo?->horario_apertura,
            horaCierre: $actividad->atractivo?->horario_cierre,
            calificacion: $calificacion !== null
                ? (float) $calificacion
                : null,
            distanciaKm: null,
            tiempoViajeMin: null,
            latitud: $actividad->atractivo?->latitud !== null
                ? (float) $actividad->atractivo->latitud
                : null,
            longitud: $actividad->atractivo?->longitud !== null
                ? (float) $actividad->atractivo->longitud
                : null,
        );
    }


    public function puntuarCandidatos(
        array $candidatos,
        array $restricciones
    ): array {
        $candidatosPuntuados = [];

        foreach ($candidatos as $candidato) {
            $puntaje = 0;

            $puntaje += $this->puntuarCategorias(
                $candidato,
                $restricciones
            );

            $puntaje += $this->puntuarDistancia(
                $candidato,
                $restricciones
            );

            $puntaje += $this->puntuarCalificacion(
                $candidato
            );

            $candidatosPuntuados[] = new CandidatoPuntuado(
                candidato: $candidato,
                puntaje: $puntaje
            );
        }

        usort(
            $candidatosPuntuados,
            fn (
                CandidatoPuntuado $a,
                CandidatoPuntuado $b
            ) => $b->puntaje <=> $a->puntaje
        );

        return $candidatosPuntuados;
    }


    private function puntuarCategorias(
        CandidatoItinerario $candidato,
        array $restricciones
    ): float {
        $categoriasPreferidas = $restricciones['categorias'] ?? [];

        if (empty($categoriasPreferidas)) {
            return 0;
        }

        $coincidencias = count(
            array_intersect(
                $candidato->categorias,
                $categoriasPreferidas
            )
        );

        return $coincidencias * 10;
    }


    private function puntuarDistancia(
        CandidatoItinerario $candidato,
        array $restricciones
    ): float {
        if (
            !($restricciones['preferir_menor_distancia'] ?? false)
            || $candidato->distanciaKm === null
        ) {
            return 0;
        }

        return max(
            0,
            10 - $candidato->distanciaKm
        );
    }

    private function puntuarCalificacion(
        CandidatoItinerario $candidato
    ): float {
        if ($candidato->calificacion === null) {
            return 0;
        }

        return $candidato->calificacion;
    }


    public function construirDias(
        array $candidatosPuntuados,
        array $restricciones
    ): array {
        $dias = $restricciones['dias'] ?? null;

        if ($dias === null || $dias <= 0) {
            return [];
        }

        $actividadesPorDia = $this->obtenerActividadesPorDia(
            $restricciones
        );

        $diasItinerario = [];

        $horaInicio = $restricciones['hora_inicio'] ?? null;
        $horaFin = $restricciones['hora_fin'] ?? null;

        for ($i = 1; $i <= $dias; $i++) {
            $diasItinerario[] = new DiaItinerario(
                numero: $i,
                horaInicio: $horaInicio,
                horaFin: $horaFin
            );
        }

        $costoMaximo = $restricciones['presupuesto_max'] ?? null;

        $costoTotal = 0;

        while ($this->existeCapacidadDisponible(
            $diasItinerario,
            $actividadesPorDia
        )) {
            $candidatoPuntuado = $this->seleccionarSiguienteCandidato(
                $candidatosPuntuados,
                $diasItinerario,
                $restricciones,
                $costoTotal,
                $costoMaximo
            );

            if ($candidatoPuntuado === null) {
                break;
            }

            $candidato = $candidatoPuntuado->candidato;

            $dia = $this->buscarDiaDisponible(
                $diasItinerario,
                $candidato,
                $actividadesPorDia,
                $costoTotal,
                $costoMaximo
            );

            if ($dia === null) {
                break;
            }

            $traslado = $this->calcularTraslado($dia, $candidato);

            $dia->agregarActividad(
                $candidato,
                $traslado['distancia_km'] ?? 0,
                $traslado['tiempo_min']
            );
            $costoTotal += $candidato->costo;

            $candidatosPuntuados = array_values(
                array_filter(
                    $candidatosPuntuados,
                    fn (CandidatoPuntuado $item) =>
                        $item->candidato->actividadId
                        !== $candidato->actividadId
                )
            );
        }

        return $diasItinerario;
    }

    private function obtenerActividadesPorDia(
        array $restricciones
    ): int {
        if (
            isset($restricciones['actividades_por_dia'])
            && $restricciones['actividades_por_dia'] > 0
        ) {
            return (int) $restricciones['actividades_por_dia'];
        }

        return match ($restricciones['ritmo'] ?? 'moderado') {
            'relajado' => 2,
            'moderado' => 3,
            'intenso' => 4,
            default => 3,
        };
    }


    private function buscarDiaDisponible(
        array $dias,
        CandidatoItinerario $candidato,
        int $actividadesPorDia,
        float $costoTotal,
        ?float $costoMaximo
    ): ?DiaItinerario {
        foreach ($dias as $dia) {
            if (
                $dia->cantidadActividades()
                >= $actividadesPorDia
            ) {
                continue;
            }

            if (
                $costoMaximo !== null
                && $costoTotal + $candidato->costo
                    > $costoMaximo
            ) {
                continue;
            }

            $traslado = $this->calcularTraslado($dia, $candidato);

            if (!$dia->puedeAgregarActividad(
                $candidato,
                $traslado['tiempo_min']
            )) {
                continue;
            }

            return $dia;
        }

        return null;
    }

    private function existeCapacidadDisponible(
        array $dias,
        int $actividadesPorDia
    ): bool {
        foreach ($dias as $dia) {
            if ($dia->cantidadActividades() < $actividadesPorDia) {
                return true;
            }
        }

        return false;
    }

    private function seleccionarSiguienteCandidato(
        array $candidatosPuntuados,
        array $diasItinerario,
        array $restricciones,
        float $costoTotal,
        ?float $costoMaximo
    ): ?CandidatoPuntuado {

        $candidatosDisponibles = [];

        foreach ($candidatosPuntuados as $candidatoPuntuado) {
            $candidato = $candidatoPuntuado->candidato;

            if (
                $costoMaximo !== null &&
                $costoTotal + $candidato->costo > $costoMaximo
            ) {
                continue;
            }

            foreach ($diasItinerario as $dia) {
                if (
                    $dia->cantidadActividades()
                    >= $this->obtenerActividadesPorDia($restricciones)
                ) {
                    continue;
                }

                $traslado = $this->calcularTraslado($dia, $candidato);

                if (!$dia->puedeAgregarActividad(
                    $candidato,
                    $traslado['tiempo_min']
                )) {
                    continue;
                }

                $distancia = $traslado['distancia_km'];

                $puntaje = $candidatoPuntuado->puntaje;

                if (
                    ($restricciones['preferir_menor_distancia'] ?? false)
                    && $distancia !== null
                ) {
                    $puntaje += max(0, 10 - $distancia);
                }

                $candidatosDisponibles[] = [
                    'candidato' => $candidatoPuntuado,
                    'distancia' => $distancia,
                    'puntaje' => $puntaje,
                ];

                break;
            }
        }

        if (empty($candidatosDisponibles)) {
            return null;
        }

        usort(
            $candidatosDisponibles,
            fn (array $a, array $b): int =>
                $b['puntaje'] <=> $a['puntaje']
        );

        return $candidatosDisponibles[0]['candidato'];
    }

    /**
     * Distancia en línea recta y tiempo estimado de traslado desde la
     * última actividad del día.
     *
     * La primera actividad del día no tiene traslado (se desconoce el
     * punto de partida). Si a alguna de las dos actividades le faltan
     * coordenadas, el tiempo no se puede estimar y se considera 0.
     *
     * @return array{distancia_km: ?float, tiempo_min: int}
     */
    private function calcularTraslado(
        DiaItinerario $dia,
        CandidatoItinerario $candidato
    ): array {
        $distancia = $this->obtenerDistanciaDesdeUltimaActividad(
            $dia,
            $candidato
        );

        return [
            'distancia_km' => $distancia,
            'tiempo_min' => $this->traslados->tiempoMin($distancia),
        ];
    }

    private function obtenerDistanciaDesdeUltimaActividad(
        DiaItinerario $dia,
        CandidatoItinerario $candidato
    ): ?float {
        $actividades = $dia->actividades();

        if (empty($actividades)) {
            return null;
        }

        $ultimaActividad = end($actividades);
        $candidatoAnterior = $ultimaActividad->candidato;

        if (
            $candidatoAnterior->latitud === null ||
            $candidatoAnterior->longitud === null ||
            $candidato->latitud === null ||
            $candidato->longitud === null
        ) {
            return null;
        }

        return $this->calcularDistanciaEntreCoordenadas(
            $candidatoAnterior->latitud,
            $candidatoAnterior->longitud,
            $candidato->latitud,
            $candidato->longitud
        );
    }

    private function calcularDistanciaEntreCoordenadas(
        float $latitudOrigen,
        float $longitudOrigen,
        float $latitudDestino,
        float $longitudDestino
    ): float {
        $latitudOrigen = deg2rad($latitudOrigen);
        $longitudOrigen = deg2rad($longitudOrigen);

        $latitudDestino = deg2rad($latitudDestino);
        $longitudDestino = deg2rad($longitudDestino);

        $diferenciaLatitud = $latitudDestino - $latitudOrigen;
        $diferenciaLongitud = $longitudDestino - $longitudOrigen;

        $a = sin($diferenciaLatitud / 2) ** 2
            + cos($latitudOrigen)
            * cos($latitudDestino)
            * sin($diferenciaLongitud / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return round(6371 * $c, 2);
    }
}