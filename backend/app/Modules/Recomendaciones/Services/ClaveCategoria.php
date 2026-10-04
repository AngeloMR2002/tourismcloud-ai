<?php

namespace App\Modules\Recomendaciones\Services;

/**
 * Clave estable para comparar nombres de categoría:
 * ignora mayúsculas, espacios repetidos y tildes de las vocales.
 * "Naturaleza y Aventura" y "naturaleza  y aventura" dan la misma clave.
 */
final class ClaveCategoria
{
    public static function de(string $nombre): string
    {
        $nombre = trim((string) preg_replace('/\s+/u', ' ', $nombre));
        $nombre = mb_strtolower($nombre, 'UTF-8');

        return strtr($nombre, [
            'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        ]);
    }
}
