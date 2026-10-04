<?php

namespace App\Modules\Recomendaciones\Services;

class VerificarFactibilidadService
{
    public function verificar(
        array $candidatos,
        array $restricciones
    ): array {
        $problemas = [];

        $this->verificarPresupuestoMaximo(
            $restricciones,
            $problemas
        );

        $this->verificarHorario(
            $restricciones,
            $problemas
        );

        $this->verificarDias(
            $candidatos,
            $restricciones,
            $problemas
        );

        return [
            'factible' => empty($problemas),
            'problemas' => $problemas,
        ];
    }

    private function verificarPresupuestoMaximo(
        array $restricciones,
        array &$problemas
    ): void {
        $minimo =
            $restricciones['presupuesto_min'] ?? null;

        $maximo =
            $restricciones['presupuesto_max'] ?? null;

        if (
            $minimo !== null
            && $maximo !== null
            && $minimo > $maximo
        ) {
            // No se intenta corregir ni descartar uno de los dos valores:
            // se guía al turista para que fije un límite coherente en su
            // sección de preferencias. El campo "accion" permite al front
            // mostrar el enlace correspondiente.
            $problemas[] = [
                'criterio' => 'presupuesto',
                'accion' => 'actualizar_preferencias',
                'mensaje' =>
                    "El presupuesto mínimo (S/ {$minimo}) es mayor que "
                    . "el máximo (S/ {$maximo}). Actualiza tus "
                    . "preferencias para establecer un nuevo límite "
                    . "de presupuesto.",
                'presupuesto_min' => $minimo,
                'presupuesto_max' => $maximo,
            ];
        }
    }

    /**
     * El día debe terminar después de empezar. Con los horarios por
     * defecto esto puede ocurrir si el turista pide, por ejemplo,
     * empezar a las 21:00 sin indicar la hora de fin.
     */
    private function verificarHorario(
        array $restricciones,
        array &$problemas
    ): void {
        $horaInicio = $restricciones['hora_inicio'] ?? null;
        $horaFin = $restricciones['hora_fin'] ?? null;

        $inicio = $this->aMinutos($horaInicio);
        $fin = $this->aMinutos($horaFin);

        if ($inicio === null || $fin === null || $fin > $inicio) {
            return;
        }

        $inicioTexto = substr($horaInicio, 0, 5);
        $finTexto = substr($horaFin, 0, 5);

        $problemas[] = [
            'criterio' => 'horario',
            'mensaje' =>
                "La hora de fin del día ({$finTexto}) debe ser "
                . "posterior a la hora de inicio ({$inicioTexto}). "
                . 'Indica un horario válido.',
        ];
    }

    private function aMinutos(?string $hora): ?int
    {
        if (
            $hora === null
            || !preg_match('/^(\d{1,2}):(\d{2})/', $hora, $m)
        ) {
            return null;
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }

    private function verificarDias(
        array $candidatos,
        array $restricciones,
        array &$problemas
    ): void {
        $dias =
            $restricciones['dias'] ?? null;

        $actividadesPorDia =
            $restricciones['actividades_por_dia'] ?? null;

        if (
            $dias === null
            || $actividadesPorDia === null
        ) {
            return;
        }

        $capacidadMaxima =
            $dias * $actividadesPorDia;

        if (count($candidatos) > 0 && $capacidadMaxima <= 0) {
            $problemas[] = [
                'criterio' => 'dias',
                'mensaje' =>
                    'No existe capacidad para programar '
                    . 'actividades en el itinerario.',
            ];
        }
    }
}