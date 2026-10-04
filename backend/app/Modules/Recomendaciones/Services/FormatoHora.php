<?php

namespace App\Modules\Recomendaciones\Services;

/**
 * Unifica las horas del módulo al formato HH:MM.
 * Las columnas TIME de PostgreSQL llegan como "08:30:00" y el LLM
 * puede devolver "8:30"; ambas quedan como "08:30".
 * Un valor que no reconoce se devuelve sin cambios.
 */
final class FormatoHora
{
    public static function normalizar(?string $hora): ?string
    {
        if ($hora === null) {
            return null;
        }

        if (!preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($hora), $m)) {
            return $hora;
        }

        return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
    }
}
