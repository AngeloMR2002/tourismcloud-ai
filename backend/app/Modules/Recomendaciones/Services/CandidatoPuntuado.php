<?php

namespace App\Modules\Recomendaciones\Services;

class CandidatoPuntuado
{
    public function __construct(
        public readonly CandidatoItinerario $candidato,
        public readonly float $puntaje,
    ) {
    }
}