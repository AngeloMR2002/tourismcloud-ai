<?php

namespace App\Modules\Recomendaciones\Services;

class CandidatoItinerario
{
    public function __construct(
        public readonly ?int $actividadId,
        public readonly ?int $atractivoId,
        public readonly ?int $establecimientoId,

        public readonly string $nombre,

        public readonly array $categorias,

        public readonly float $costo,
        public readonly int $duracionMin,

        public readonly ?string $horaApertura,
        public readonly ?string $horaCierre,

        public readonly ?float $calificacion,

        public readonly ?float $distanciaKm,
        public readonly ?int $tiempoViajeMin,

        public readonly ?float $latitud,
        public readonly ?float $longitud,
    ) {
    }
}