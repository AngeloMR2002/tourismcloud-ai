<?php

namespace App\Modules\Recomendaciones\Services;

class PresentacionService
{
    /**
     * Prepara los itinerarios para su presentación en el frontend.
     *
     * @param array $diasItinerario
     * @param array $restricciones
     * @return array
     */
    public function presentar(
        array $diasItinerario,
        array $restricciones = []
    ): array {
        return [
            'tipo' => 'recomendaciones',
            'itinerarios' => [
                $this->presentarItinerario(
                    $diasItinerario,
                    $restricciones,
                    1
                ),
            ],
        ];
    }

    /**
     * Prepara la información general del itinerario.
     */
    private function presentarItinerario(
        array $diasItinerario,
        array $restricciones,
        int $numero
    ): array {
        $costoTotal = 0;
        $cantidadActividades = 0;
        $tiempoViajeTotal = 0;
        $distanciaTotal = 0;

        foreach ($diasItinerario as $dia) {
            $costoTotal += $dia->costoTotal();
            $cantidadActividades += $dia->cantidadActividades();
            $tiempoViajeTotal += $dia->tiempoViajeTotalMin();
            $distanciaTotal += $dia->distanciaTotalKm();
        }

        return [
            'id' => $numero,
            'titulo' => "Itinerario {$numero}",

            'resumen' => [
                'dias' => count($diasItinerario),
                'dias_solicitados' => $restricciones['dias'] ?? null,
                'costo_total' => $costoTotal,
                'cantidad_actividades' => $cantidadActividades,
                'distancia_km' => round($distanciaTotal, 2),
                'tiempo_viaje_total_min' => $tiempoViajeTotal,
                'ritmo' => $restricciones['ritmo'] ?? null,
                'categorias' => $restricciones['categorias'] ?? [],
            ],

            'dias' => array_map(
                fn (DiaItinerario $dia) =>
                    $this->presentarDia($dia),
                $diasItinerario
            ),
        ];
    }

    /**
     * Prepara la información correspondiente a un día.
     */
    private function presentarDia(
        DiaItinerario $dia
    ): array {
        return [
            'numero' => $dia->numero,
            'hora_inicio' => FormatoHora::normalizar($dia->horaInicio),
            'hora_fin' => FormatoHora::normalizar($dia->horaFin),

            'costo_total' => $dia->costoTotal(),
            'cantidad_actividades' => $dia->cantidadActividades(),
            'duracion_total_min' => $dia->duracionTotalMin(),

            'tiempo_viaje_total_min' =>
                $dia->tiempoViajeTotalMin(),

            'tiempo_espera_total_min' =>
                $dia->tiempoEsperaTotalMin(),

            'distancia_total_km' =>
                $dia->distanciaTotalKm(),

            'actividades' => array_map(
                fn (ActividadProgramada $actividad) =>
                    $this->presentarActividad($actividad),
                $dia->actividades()
            ),
        ];
    }

    /**
     * Prepara la información correspondiente a una actividad.
     */
    private function presentarActividad(
        ActividadProgramada $actividad
    ): array {
        $candidato = $actividad->candidato;

        return [
            'actividad_id' => $candidato->actividadId,
            'atractivo_id' => $candidato->atractivoId,
            'establecimiento_id' =>
                $candidato->establecimientoId,

            'nombre' => $candidato->nombre,

            'hora_inicio' => $actividad->horaInicio,
            'hora_fin' => $actividad->horaFin,

            'duracion_min' => $candidato->duracionMin,
            'costo' => $candidato->costo,
            'categorias' => $candidato->categorias,

            'hora_apertura' =>
                FormatoHora::normalizar($candidato->horaApertura),
            'hora_cierre' =>
                FormatoHora::normalizar($candidato->horaCierre),

            'calificacion' => $candidato->calificacion,

            'distancia_antes_km' =>
                $actividad->distanciaAntesKm,

            'tiempo_viaje_antes_min' =>
                $actividad->tiempoViajeAntesMin,

            'tiempo_espera_min' =>
                $actividad->tiempoEsperaMin,
        ];
    }
}
