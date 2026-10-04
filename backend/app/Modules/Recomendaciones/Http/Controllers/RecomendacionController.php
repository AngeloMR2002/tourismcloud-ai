<?php

namespace App\Modules\Recomendaciones\Http\Controllers;

use App\Modules\Recomendaciones\Contracts\GeneradorRecomendacionesInterface;
use App\Modules\Recomendaciones\Exceptions\LLMNoDisponibleException;
use App\Modules\Recomendaciones\Exceptions\PreferenciaNoEncontradaException;
use App\Modules\Recomendaciones\Http\Requests\GenerarRecomendacionRequest;
use App\Modules\Recomendaciones\Services\RegistroSolicitudIAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class RecomendacionController extends Controller
{
    public function __construct(
        private GeneradorRecomendacionesInterface $generador,
        private RegistroSolicitudIAService $registro,
    ) {
    }

    public function generar(GenerarRecomendacionRequest $request): JsonResponse
    {
        $turistaId = (int) $request->user()->id;
        $destinoId = (int) $request->validated('destino_id');
        $solicitud = $request->validated('solicitud');

        $inicio = hrtime(true);

        try {
            $resultado = $this->generador->generar(
                $turistaId,
                $destinoId,
                $solicitud
            );
        } catch (PreferenciaNoEncontradaException $e) {
            $this->registrar($turistaId, $solicitud, [], ['error' => 'sin_preferencias'], $inicio);

            return response()->json([
                'estado' => 'REQUIERE_PREFERENCIAS',
                'mensaje' => 'Completa tus preferencias de viaje antes de generar un itinerario.',
            ], 422);
        } catch (LLMNoDisponibleException $e) {
            report($e);
            $this->registrar($turistaId, $solicitud, [], ['error' => 'llm_no_disponible'], $inicio);

            return response()->json([
                'estado' => 'SERVICIO_NO_DISPONIBLE',
                'mensaje' => 'No pudimos interpretar tu solicitud en este momento. Inténtalo de nuevo en unos minutos.',
            ], 503);
        }

        $solicitudId = $this->registrar(
            $turistaId,
            $solicitud,
            $resultado['restricciones'] ?? [],
            $resultado,
            $inicio
        );

        $status = ($resultado['estado'] ?? null) === 'OK' ? 200 : 422;

        return response()->json(
            ['solicitud_id' => $solicitudId] + $resultado,
            $status
        );
    }

    private function registrar(
        int $turistaId,
        string $solicitud,
        array $restricciones,
        ?array $respuesta,
        int $inicio
    ): ?int {
        return $this->registro->registrar(
            $turistaId,
            $solicitud,
            $restricciones,
            $respuesta,
            (int) ((hrtime(true) - $inicio) / 1_000_000)
        );
    }
}
