<?php

namespace App\Modules\Recomendaciones\Services;

use DateTimeImmutable;

class DiaItinerario
{
    private array $actividades = [];

    private float $costoTotal = 0;

    private int $duracionTotalMin = 0;

    private int $tiempoViajeTotalMin = 0;

    private int $tiempoEsperaTotalMin = 0;

    private float $distanciaTotalKm = 0;

    public function __construct(
        public readonly int $numero,
        public readonly ?string $horaInicio = null,
        public readonly ?string $horaFin = null
    ) {}

    /**
     * @param int $tiempoViajeMin Minutos de traslado desde la actividad
     *        anterior del día (0 para la primera del día).
     */
    public function agregarActividad(
        CandidatoItinerario $candidato,
        float $distanciaAntesKm = 0,
        int $tiempoViajeMin = 0
    ): bool {
        $programacion = $this->calcularProgramacion(
            $candidato,
            $distanciaAntesKm,
            $tiempoViajeMin
        );

        if ($programacion === null) {
            return false;
        }

        $this->actividades[] = $programacion;

        $this->costoTotal += $candidato->costo;
        $this->duracionTotalMin += $candidato->duracionMin;
        $this->tiempoViajeTotalMin += $tiempoViajeMin;
        $this->tiempoEsperaTotalMin += $programacion->tiempoEsperaMin;
        $this->distanciaTotalKm += $distanciaAntesKm;

        return true;
    }

    private function calcularProgramacion(
        CandidatoItinerario $candidato,
        float $distanciaAntesKm = 0,
        int $tiempoViajeMin = 0
    ): ?ActividadProgramada {

        if (
            $this->horaInicio === null
            || $this->horaFin === null
        ) {
            $horaInicio = '00:00';

            return new ActividadProgramada(
                candidato: $candidato,
                horaInicio: $horaInicio,
                horaFin: $this->calcularHoraFin(
                    $horaInicio,
                    $candidato->duracionMin
                ),
                tiempoViajeAntesMin:
                    $tiempoViajeMin,
                tiempoEsperaMin: 0,
                distanciaAntesKm: $distanciaAntesKm
            );
        }

        $inicioDia = new DateTimeImmutable(
            $this->horaInicio
        );

        $finDia = new DateTimeImmutable(
            $this->horaFin
        );

        // Tiempo ya utilizado por las actividades anteriores.
        // Incluye duración + viajes + esperas.
        $minutosUtilizados =
            $this->duracionTotalConViajesMin();

        // Momento en que se inicia el viaje de la actividad actual.
        $horaSalida = $inicioDia->modify(
            "+{$minutosUtilizados} minutes"
        );

        $tiempoViaje =
            $tiempoViajeMin;

        // Llegada al lugar de la actividad.
        $inicioProgramado = $horaSalida->modify(
            "+{$tiempoViaje} minutes"
        );

        // Ajustar la llegada según el horario de apertura.
        $inicioActividad = $this->ajustarPorHorarioApertura(
            $inicioProgramado,
            $candidato
        );

        if ($inicioActividad === null) {
            return null;
        }

        // Tiempo de espera generado por llegar antes de la apertura.
        $tiempoEsperaMin = (int) (
            (
                $inicioActividad->getTimestamp()
                - $inicioProgramado->getTimestamp()
            ) / 60
        );

        $finActividad = $inicioActividad->modify(
            "+{$candidato->duracionMin} minutes"
        );

        // La actividad debe terminar dentro del horario disponible del día.
        if ($finActividad > $finDia) {
            return null;
        }

        return new ActividadProgramada(
            candidato: $candidato,
            horaInicio: $inicioActividad->format('H\:i'),
            horaFin: $finActividad->format('H\:i'),
            tiempoViajeAntesMin: $tiempoViaje,
            tiempoEsperaMin: $tiempoEsperaMin,
            distanciaAntesKm: $distanciaAntesKm
        );
    }

    private function ajustarPorHorarioApertura(
        DateTimeImmutable $inicio,
        CandidatoItinerario $candidato
    ): ?DateTimeImmutable {
        if (
            $candidato->horaApertura === null
            || $candidato->horaCierre === null
        ) {
            return $inicio;
        }

        $apertura = new DateTimeImmutable(
            $candidato->horaApertura
        );

        $cierre = new DateTimeImmutable(
            $candidato->horaCierre
        );

        if ($inicio < $apertura) {
            $inicio = $apertura;
        }

        $finActividad = $inicio->modify(
            "+{$candidato->duracionMin} minutes"
        );

        if ($finActividad > $cierre) {
            return null;
        }

        return $inicio;
    }

    private function calcularHoraFin(
        string $horaInicio,
        int $duracionMin
    ): string {
        return (new DateTimeImmutable($horaInicio))
            ->modify("+{$duracionMin} minutes")
            ->format('H:i');
    }

    public function puedeAgregarActividad(
        CandidatoItinerario $candidato,
        int $tiempoViajeMin = 0
    ): bool {
        return $this->calcularProgramacion(
            $candidato,
            0,
            $tiempoViajeMin
        ) !== null;
    }

    public function cantidadActividades(): int
    {
        return count($this->actividades);
    }

    public function actividades(): array
    {
        return $this->actividades;
    }

    public function costoTotal(): float
    {
        return $this->costoTotal;
    }

    public function duracionTotalMin(): int
    {
        return $this->duracionTotalMin;
    }

    public function tiempoViajeTotalMin(): int
    {
        return $this->tiempoViajeTotalMin;
    }

    public function duracionTotalConViajesMin(): int
    {
        return $this->duracionTotalMin
            + $this->tiempoViajeTotalMin
            + $this->tiempoEsperaTotalMin;
    }

    public function tiempoEsperaTotalMin(): int
    {
        return $this->tiempoEsperaTotalMin;
    }

    public function distanciaTotalKm(): float
    {
        return round($this->distanciaTotalKm, 2);
    }
}