<?php

namespace App\Modules\Recomendaciones\Services;

use App\Modules\Recomendaciones\Models\SolicitudIA;
use Throwable;

class RegistroSolicitudIAService
{
    public function registrar(
        int $turistaId,
        string $prompt,
        array $restricciones,
        ?array $respuesta,
        int $tiempoMs,
    ): ?int {
        try {
            $proveedor = config('recommendations.llm.provider');

            return SolicitudIA::create([
                'turista_id' => $turistaId,
                'prompt_usuario' => $prompt,
                'restricciones_json' => $restricciones,
                'respuesta_ia_json' => $respuesta,
                'modelo_usado' => $proveedor . ':' . config("recommendations.{$proveedor}.model"),
                'tiempo_respuesta_ms' => $tiempoMs,
            ])->id;
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
