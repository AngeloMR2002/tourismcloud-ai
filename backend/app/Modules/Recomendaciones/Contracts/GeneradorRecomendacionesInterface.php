<?php

namespace App\Modules\Recomendaciones\Contracts;

/**
 * Frontera del módulo de IA. El resto de la aplicación depende solo de
 * esta interfaz; hoy la implementa el orquestador local y, al separar el
 * microservicio, la implementará un cliente HTTP.
 */
interface GeneradorRecomendacionesInterface
{
    /**
     * @return array{estado: string, errores: array, observaciones: array, itinerario: ?array, restricciones?: array}
     */
    public function generar(int $turistaId, int $destinoId, string $solicitud): array;
}
