<?php

namespace App\Modules\Recomendaciones\Services;

use DateTimeImmutable;

class ValidadorItinerarioService
{
    public function validar(
        array $diasItinerario,
        array $restricciones
    ): array {
        $errores = [];
        $observaciones = [];

        $this->validarPresupuesto(
            $diasItinerario,
            $restricciones,
            $errores,
            $observaciones
        );

        $this->validarDias(
            $diasItinerario,
            $restricciones,
            $errores
        );

        $this->validarActividadesPorDia(
            $diasItinerario,
            $restricciones,
            $errores
        );

        $this->validarHorariosDiarios(
            $diasItinerario,
            $restricciones,
            $errores
        );

        $this->validarDuraciones(
            $diasItinerario,
            $restricciones,
            $errores
        );

        $this->validarSolapamientos(
            $diasItinerario,
            $errores
        );

        $this->observarCobertura(
            $diasItinerario,
            $restricciones,
            $observaciones
        );

        $this->observarCategorias(
            $diasItinerario,
            $restricciones,
            $observaciones
        );

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'observaciones' => $observaciones,
        ];
    }

    private function validarPresupuesto(
        array $diasItinerario,
        array $restricciones,
        array &$errores,
        array &$observaciones
    ): void {
        $costoTotal = 0;

        foreach ($diasItinerario as $dia) {
            $costoTotal += $dia->costoTotal();
        }

        $presupuestoMaximo =
            $restricciones['presupuesto_max'] ?? null;

        if (
            $presupuestoMaximo !== null
            && $costoTotal > $presupuestoMaximo
        ) {
            $errores[] = [
                'criterio' => 'presupuesto_max',
                'mensaje' =>
                    "El costo total de S/ {$costoTotal} "
                    . "supera el presupuesto máximo de "
                    . "S/ {$presupuestoMaximo}.",
            ];
        }

        // El mínimo orienta, pero no invalida: un itinerario que cuesta
        // menos de lo que el turista estaba dispuesto a gastar sigue
        // siendo válido. Se informa como observación.
        $presupuestoMinimo =
            $restricciones['presupuesto_min'] ?? null;

        if (
            $presupuestoMinimo !== null
            && $costoTotal < $presupuestoMinimo
        ) {
            $costoRedondeado = round($costoTotal, 2);

            $observaciones[] = [
                'criterio' => 'presupuesto_min',
                'tipo' => 'por_debajo_del_minimo',
                'mensaje' =>
                    "El itinerario cuesta S/ {$costoRedondeado}, "
                    . 'por debajo del presupuesto mínimo de '
                    . "S/ {$presupuestoMinimo} que indicaste.",
                'costo_total' => $costoRedondeado,
                'presupuesto_min' => $presupuestoMinimo,
            ];
        }
    }

    private function validarDias(
        array $diasItinerario,
        array $restricciones,
        array &$errores
    ): void {
        $diasSolicitados =
            $restricciones['dias'] ?? null;

        if ($diasSolicitados === null) {
            return;
        }

        if (count($diasItinerario) > $diasSolicitados) {
            $errores[] = [
                'criterio' => 'dias',
                'mensaje' =>
                    'El itinerario contiene más días '
                    . 'de los disponibles.',
            ];
        }
    }

    private function validarActividadesPorDia(
        array $diasItinerario,
        array $restricciones,
        array &$errores
    ): void {
        $maximo =
            $restricciones['actividades_por_dia'] ?? null;

        if ($maximo === null) {
            return;
        }

        foreach ($diasItinerario as $dia) {
            if ($dia->cantidadActividades() > $maximo) {
                $errores[] = [
                    'criterio' => 'actividades_por_dia',
                    'dia' => $dia->numero,
                    'mensaje' =>
                        "El día {$dia->numero} contiene "
                        . "{$dia->cantidadActividades()} actividades, "
                        . "superando el máximo de {$maximo}.",
                ];
            }
        }
    }

    private function validarHorariosDiarios(
        array $diasItinerario,
        array $restricciones,
        array &$errores
    ): void {
        $horaInicioPermitida =
            $restricciones['hora_inicio'] ?? null;

        $horaFinPermitida =
            $restricciones['hora_fin'] ?? null;

        if (
            $horaInicioPermitida === null
            || $horaFinPermitida === null
        ) {
            return;
        }

        $inicioPermitido =
            new DateTimeImmutable($horaInicioPermitida);

        $finPermitido =
            new DateTimeImmutable($horaFinPermitida);

        foreach ($diasItinerario as $dia) {
            foreach ($dia->actividades() as $actividad) {
                $inicioActividad =
                    new DateTimeImmutable($actividad->horaInicio);

                $finActividad =
                    new DateTimeImmutable($actividad->horaFin);

                if ($inicioActividad < $inicioPermitido) {
                    $errores[] = [
                        'criterio' => 'hora_inicio',
                        'dia' => $dia->numero,
                        'actividad' =>
                            $actividad->candidato->nombre,
                        'mensaje' =>
                            'La actividad comienza antes '
                            . 'del horario permitido.',
                    ];
                }

                if ($finActividad > $finPermitido) {
                    $errores[] = [
                        'criterio' => 'hora_fin',
                        'dia' => $dia->numero,
                        'actividad' =>
                            $actividad->candidato->nombre,
                        'mensaje' =>
                            'La actividad termina después '
                            . 'del horario permitido.',
                    ];
                }
            }
        }
    }

    private function validarDuraciones(
        array $diasItinerario,
        array $restricciones,
        array &$errores
    ): void {
        $duracionMinima =
            $restricciones['duracion_min_actividad'] ?? null;

        $duracionMaxima =
            $restricciones['duracion_max_actividad'] ?? null;

        if (
            $duracionMinima === null
            && $duracionMaxima === null
        ) {
            return;
        }

        foreach ($diasItinerario as $dia) {
            foreach ($dia->actividades() as $actividad) {
                $duracion =
                    $actividad->candidato->duracionMin;

                if (
                    $duracionMinima !== null
                    && $duracion < $duracionMinima
                ) {
                    $errores[] = [
                        'criterio' => 'duracion_min_actividad',
                        'dia' => $dia->numero,
                        'actividad' =>
                            $actividad->candidato->nombre,
                        'mensaje' =>
                            'La duración de la actividad '
                            . 'es inferior a la mínima permitida.',
                    ];
                }

                if (
                    $duracionMaxima !== null
                    && $duracion > $duracionMaxima
                ) {
                    $errores[] = [
                        'criterio' => 'duracion_max_actividad',
                        'dia' => $dia->numero,
                        'actividad' =>
                            $actividad->candidato->nombre,
                        'mensaje' =>
                            'La duración de la actividad '
                            . 'supera la máxima permitida.',
                    ];
                }
            }
        }
    }

    private function validarSolapamientos(
        array $diasItinerario,
        array &$errores
    ): void {
        foreach ($diasItinerario as $dia) {
            $actividades = $dia->actividades();

            for ($i = 0; $i < count($actividades) - 1; $i++) {
                $actual = $actividades[$i];
                $siguiente = $actividades[$i + 1];

                $finActual =
                    new DateTimeImmutable($actual->horaFin);

                $inicioSiguiente =
                    new DateTimeImmutable(
                        $siguiente->horaInicio
                    );

                if ($inicioSiguiente < $finActual) {
                    $errores[] = [
                        'criterio' => 'solapamiento',
                        'dia' => $dia->numero,
                        'mensaje' =>
                            "Existe un solapamiento entre "
                            . "las actividades "
                            . "'{$actual->candidato->nombre}' "
                            . "y "
                            . "'{$siguiente->candidato->nombre}'.",
                    ];
                }
            }
        }
    }

    /**
     * Avisa cuando el itinerario cubre menos días de los solicitados.
     * No es un error: el itinerario es válido, pero incompleto.
     */
    private function observarCobertura(
        array $diasItinerario,
        array $restricciones,
        array &$observaciones
    ): void {
        $diasSolicitados = $restricciones['dias'] ?? null;

        if ($diasSolicitados === null) {
            return;
        }

        $diasSolicitados = (int) $diasSolicitados;
        $diasConActividades = 0;
        $actividadesProgramadas = 0;

        foreach ($diasItinerario as $dia) {
            $cantidad = $dia->cantidadActividades();
            $actividadesProgramadas += $cantidad;

            if ($cantidad > 0) {
                $diasConActividades++;
            }
        }

        if ($diasConActividades >= $diasSolicitados) {
            return;
        }

        $actividades = $actividadesProgramadas === 1
            ? '1 actividad'
            : "{$actividadesProgramadas} actividades";

        $observaciones[] = [
            'criterio' => 'dias',
            'tipo' => 'cobertura_incompleta',
            'mensaje' =>
                "Se programaron {$actividades} en "
                . "{$diasConActividades} de los {$diasSolicitados} "
                . 'días solicitados. No hay más actividades '
                . 'disponibles que cumplan tus restricciones '
                . '(presupuesto, horarios y duración).',
            'dias_solicitados' => $diasSolicitados,
            'dias_con_actividades' => $diasConActividades,
            'actividades_programadas' => $actividadesProgramadas,
        ];
    }

    /**
     * Avisa de cada actividad incluida cuyas categorías no coinciden
     * con ninguna de las categorías pedidas. Las categorías funcionan
     * como preferencia (puntúan), no como filtro, así que esto puede
     * ocurrir; el turista debe saberlo.
     * Las actividades sin categoría asignada no se señalan.
     */
    private function observarCategorias(
        array $diasItinerario,
        array $restricciones,
        array &$observaciones
    ): void {
        $pedidas = array_values(array_filter(
            $restricciones['categorias'] ?? [],
            'is_string'
        ));

        if ($pedidas === []) {
            return;
        }

        $clavesPedidas = array_map(
            fn (string $categoria) => ClaveCategoria::de($categoria),
            $pedidas
        );

        $listaPedidas = implode(', ', $pedidas);

        foreach ($diasItinerario as $dia) {
            foreach ($dia->actividades() as $actividad) {
                $candidato = $actividad->candidato;

                $categoriasActividad = array_values(array_filter(
                    $candidato->categorias,
                    'is_string'
                ));

                if ($categoriasActividad === []) {
                    continue;
                }

                $clavesActividad = array_map(
                    fn (string $categoria) => ClaveCategoria::de($categoria),
                    $categoriasActividad
                );

                if (array_intersect($clavesActividad, $clavesPedidas) !== []) {
                    continue;
                }

                $etiqueta = count($categoriasActividad) > 1
                    ? 'Sus categorías'
                    : 'Su categoría';

                $observaciones[] = [
                    'criterio' => 'categorias',
                    'tipo' => 'fuera_de_categorias_pedidas',
                    'mensaje' =>
                        "«{$candidato->nombre}» (día {$dia->numero}) "
                        . 'no coincide con tus categorías de interés '
                        . "({$listaPedidas}). {$etiqueta}: "
                        . implode(', ', $categoriasActividad) . '.',
                    'actividad_id' => $candidato->actividadId,
                    'dia' => $dia->numero,
                    'categorias_actividad' => $categoriasActividad,
                ];
            }
        }
    }
}
