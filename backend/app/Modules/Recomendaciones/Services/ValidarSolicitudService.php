<?php

namespace App\Modules\Recomendaciones\Services;

class ValidarSolicitudService
{
    /**
     * Valida las solicitudes adicionales identificadas
     * por el servicio de comprensión.
     *
     * @param array $solicitudesAdicionales
     * @return array
     */
    public function validar(array $solicitudesAdicionales): array
    {
        $soportadas = [];
        $noDisponibles = [];

        foreach ($solicitudesAdicionales as $solicitud) {
            $criterio = $solicitud['criterio'] ?? null;
            $valor = $solicitud['valor'] ?? null;

            if (!$criterio) {
                continue;
            }

            if ($this->esSoportado($criterio)) {
                $soportadas[] = [
                    'criterio' => $criterio,
                    'valor' => $valor,
                ];
            } else {
                $noDisponibles[] = [
                    'criterio' => $criterio,
                    'valor' => $valor,
                ];
            }
        }

        return [
            'valido' => empty($noDisponibles),
            'soportadas' => $soportadas,
            'no_disponibles' => $noDisponibles,
        ];
    }

    /**
     * Determina si un criterio adicional puede ser
     * representado por las capacidades del sistema.
     */
    private function esSoportado(string $criterio): bool
    {
        $criteriosSoportados = [
            'dias',
            'presupuesto_min',
            'presupuesto_max',
            'ritmo',
            'hora_inicio',
            'hora_fin',
            'categorias',
            'costo_max_actividad',
            'costo_min_actividad',
            'duracion_min_actividad',
            'duracion_max_actividad',
            'preferir_menor_distancia',
        ];

        return in_array(
            $criterio,
            $criteriosSoportados,
            true
        );
    }
}