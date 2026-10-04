<?php

namespace App\Modules\Recomendaciones\Services;

class NormalizadorRestriccionesService
{
    /**
     * Horario del día que se usa cuando el turista no lo indicó
     * ni en el prompt ni en sus preferencias guardadas.
     */
    private const HORA_INICIO_POR_DEFECTO = '08:00';
    private const HORA_FIN_POR_DEFECTO = '20:00';

    /**
     * Combina las preferencias almacenadas con los criterios
     * explícitos detectados en la solicitud del turista.
     *
     * Precedencia:
     * prompt explícito > preferencia almacenada > valor por defecto.
     */
    public function normalizar(
        array $criterios,
        array $preferencias = []
    ): array {
        $restricciones = $this->valoresPorDefecto();

        foreach ($restricciones as $criterio => $valorPorDefecto) {
            $restricciones[$criterio] = $this->resolverCriterio(
                $criterios[$criterio] ?? null,
                $preferencias[$criterio] ?? null,
                $valorPorDefecto
            );
        }

        foreach (['hora_inicio', 'hora_fin'] as $criterio) {
            $restricciones[$criterio] = FormatoHora::normalizar(
                $restricciones[$criterio]
            );
        }

        $restricciones['categorias'] = $this->resolverCategorias(
            $criterios['categorias'] ?? null,
            $preferencias['categorias'] ?? []
        );

        return $restricciones;
    }

    /**
     * Valores utilizados cuando el criterio no aparece
     * ni en el prompt ni en las preferencias almacenadas.
     */
    private function valoresPorDefecto(): array
    {
        return [
            'dias' => null,
            'presupuesto_min' => null,
            'presupuesto_max' => null,
            'ritmo' => 'moderado',
            'hora_inicio' => self::HORA_INICIO_POR_DEFECTO,
            'hora_fin' => self::HORA_FIN_POR_DEFECTO,
            'categorias' => [],
            'costo_max_actividad' => null,
            'costo_min_actividad' => null,
            'duracion_min_actividad' => null,
            'duracion_max_actividad' => null,
            'preferir_menor_distancia' => false,
        ];
    }

    /**
     * Resuelve un criterio aplicando la precedencia:
     *
     * 1. Prompt explícito.
     * 2. Preferencia almacenada.
     * 3. Valor por defecto.
     */
    private function resolverCriterio(
        ?array $criterioPrompt,
        mixed $preferencia,
        mixed $valorPorDefecto
    ): mixed {
        if (
            is_array($criterioPrompt) &&
            ($criterioPrompt['mencionado'] ?? false) === true
        ) {
            return $criterioPrompt['valor'] ?? $valorPorDefecto;
        }

        if ($preferencia !== null) {
            return $preferencia;
        }

        return $valorPorDefecto;
    }

    /**
     * Resuelve las categorías considerando las operaciones:
     *
     * reemplazar
     * agregar
     * eliminar
     */
    private function resolverCategorias(
        ?array $criterioPrompt,
        array $categoriasAlmacenadas
    ): array {
        $categorias = array_map(
            fn ($categoria) => $this->normalizarNombreCategoria($categoria),
            $categoriasAlmacenadas
        );

        $categorias = array_values(
            array_unique($categorias)
        );

        if (
            !is_array($criterioPrompt) ||
            ($criterioPrompt['mencionado'] ?? false) !== true
        ) {
            return $categorias;
        }

        $valor = $criterioPrompt['valor'] ?? [];
        $operacion = $criterioPrompt['operacion'] ?? 'reemplazar';

        if (!is_array($valor)) {
            return $categorias;
        }

        $valor = array_map(
            fn ($categoria) => $this->normalizarNombreCategoria($categoria),
            $valor
        );

        switch ($operacion) {
            case 'reemplazar':
                return array_values(array_unique($valor));

            case 'agregar':
                return array_values(
                    array_unique(
                        array_merge($categorias, $valor)
                    )
                );

            case 'eliminar':
                return array_values(
                    array_diff($categorias, $valor)
                );

            default:
                return $categorias;
        }
    }

    private function normalizarNombreCategoria(string $categoria): string
    {
        return mb_convert_case(
            trim($categoria),
            MB_CASE_TITLE,
            'UTF-8'
        );
    }
}