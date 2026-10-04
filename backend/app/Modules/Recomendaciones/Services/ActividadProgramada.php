<?php

namespace App\Modules\Recomendaciones\Services;
use App\Modules\Recomendaciones\Services\CandidatoItinerario;

class ActividadProgramada
{
    public function __construct(
        public readonly CandidatoItinerario $candidato,
        public readonly string $horaInicio,
        public readonly string $horaFin,
        public readonly int $tiempoViajeAntesMin,
        public readonly int $tiempoEsperaMin,
        public readonly float $distanciaAntesKm
    ) {}
}