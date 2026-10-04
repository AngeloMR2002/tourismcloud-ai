<?php

namespace App\Modules\Recomendaciones\Services;

use App\Models\PreferenciaTurista;
use App\Modules\Recomendaciones\Exceptions\PreferenciaNoEncontradaException;

class ObtenerPreferenciasTuristaService
{
    public function obtener(int $turistaId): array
    {
        $preferencia = PreferenciaTurista::query()
            ->with('categoriasInteres')
            ->where('turista_id', $turistaId)
            ->where('vigente', true)
            ->first();

        if (!$preferencia) {
            throw new PreferenciaNoEncontradaException(
                "No se encontró una preferencia vigente para el turista {$turistaId}."
            );
        }

        return [
            'dias' => $preferencia->dias_disponibles,
            'presupuesto_min' => $preferencia->presupuesto_min !== null
                ? (float) $preferencia->presupuesto_min
                : null,
            'presupuesto_max' => $preferencia->presupuesto_max !== null
                ? (float) $preferencia->presupuesto_max
                : null,
            'ritmo' => $preferencia->ritmo,
            'hora_inicio' => $preferencia->hora_inicio_dia,
            'hora_fin' => $preferencia->hora_fin_dia,
            'categorias' => $preferencia->categoriasInteres
                ->pluck('nombre')
                ->values()
                ->toArray(),
        ];
    }
}