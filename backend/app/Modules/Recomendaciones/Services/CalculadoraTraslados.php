<?php

namespace App\Modules\Recomendaciones\Services;

final class CalculadoraTraslados
{
    public function __construct(
        private float $factorRodeo = 1.3,
        private float $umbralCaminataKm = 1.0,
        private float $velocidadCaminataKmh = 5.0,
        private float $velocidadVehiculoKmh = 25.0,
        private int $esperaVehiculoMin = 5,
    ) {
    }

    /**
     * @param float|null $distanciaRectaKm Distancia en línea recta.
     *        null significa que no se conoce y no se puede estimar.
     * @return int Minutos, redondeados hacia arriba (0 si es 0 o null).
     */
    public function tiempoMin(?float $distanciaRectaKm): int
    {
        if ($distanciaRectaKm === null || $distanciaRectaKm <= 0) {
            return 0;
        }

        $recorridoKm = $distanciaRectaKm * $this->factorRodeo;

        if ($distanciaRectaKm <= $this->umbralCaminataKm) {
            $minutos = $recorridoKm / $this->velocidadCaminataKmh * 60;
        } else {
            $minutos = $recorridoKm / $this->velocidadVehiculoKmh * 60
                + $this->esperaVehiculoMin;
        }

        return (int) ceil($minutos);
    }
}
