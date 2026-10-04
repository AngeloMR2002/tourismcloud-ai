<?php

namespace App\Modules\Recomendaciones\Services;

use App\Modules\Recomendaciones\Contracts\GeneradorRecomendacionesInterface;

class RecomendacionOrchestratorService implements GeneradorRecomendacionesInterface
{
    public function __construct(
        private ComprensionService $comprensionService,
        private ObtenerPreferenciasTuristaService $preferenciasService,
        private ValidarSolicitudService $validarSolicitudService,
        private NormalizadorRestriccionesService $normalizadorService,
        private OptimizadorItinerarioService $optimizadorService,
        private VerificarFactibilidadService $factibilidadService,
        private ValidadorItinerarioService $validadorService,
        private PresentacionService $presentacionService,
    ) {}

    public function generar(
        int $turistaId,
        int $destinoId,
        string $solicitud
    ): array {
        /*
         * 1. Comprender la solicitud mediante Gemini.
         */
        $comprension = $this->comprensionService->comprender(
            $solicitud
        );

        if (
            ($comprension['estado'] ?? null) === 'invalido'
        ) {
            return [
                'estado' => 'REQUIERE_VALIDACION',
                'errores' => [],
                'observaciones' =>
                    $comprension['observaciones'] ?? [],
                'itinerario' => null,
            ];
        }

        /*
         * 2. Verificar solicitudes adicionales
         *    que podrían no estar soportadas.
         */
        $validacionSolicitud =
            $this->validarSolicitudService->validar(
                $comprension['solicitudes_adicionales'] ?? []
            );

        if (!$validacionSolicitud['valido']) {
            return [
                'estado' => 'REQUIERE_VALIDACION',
                'errores' =>
                    $validacionSolicitud['no_disponibles'],
                'observaciones' => [],
                'itinerario' => null,
            ];
        }

        /*
         * 3. Obtener preferencias almacenadas.
         */
        $preferencias =
            $this->preferenciasService->obtener(
                $turistaId
            );

        /*
         * 4. Combinar preferencias + solicitud.
         */
        $restricciones =
            $this->normalizadorService->normalizar(
                $comprension['criterios'],
                $preferencias
            );

        /*
         * 5. Resolver actividades por día.
         *
         * Temporalmente se utiliza la configuración
         * definida para el ritmo.
         */
        $restricciones['actividades_por_dia'] =
            $this->obtenerActividadesPorDia(
                $restricciones['ritmo'] ?? 'moderado'
            );

        /*
         * 6. Obtener candidatos del destino.
         */
        $candidatos =
            $this->optimizadorService->obtenerCandidatos(
                $destinoId
            );

        /*
         * 7. Aplicar filtros.
         */
        $candidatosFiltrados =
            $this->optimizadorService->filtrarCandidatos(
                $candidatos,
                $restricciones
            );

        /*
         * 8. Verificar factibilidad antes de optimizar.
         */
        $factibilidad =
            $this->factibilidadService->verificar(
                $candidatosFiltrados,
                $restricciones
            );

        if (!$factibilidad['factible']) {
            return [
                'estado' => 'NO_FACTIBLE',
                'restricciones' => $restricciones,
                'errores' => $factibilidad['problemas'],
                'observaciones' => [],
                'itinerario' => null,
            ];
        }

        /*
         * 9. Puntuar candidatos.
         */
        $candidatosPuntuados =
            $this->optimizadorService->puntuarCandidatos(
                $candidatosFiltrados,
                $restricciones
            );

        /*
         * 10. Construir itinerario.
         */
        $diasItinerario =
            $this->optimizadorService->construirDias(
                $candidatosPuntuados,
                $restricciones
            );

        /*
         * 11. Validar el itinerario generado.
         */
        $validacion =
            $this->validadorService->validar(
                $diasItinerario,
                $restricciones
            );

        if (!$validacion['valido']) {
            return [
                'estado' => 'NO_FACTIBLE',
                'restricciones' => $restricciones,
                'errores' => $validacion['errores'],
                'observaciones' =>
                    $validacion['observaciones'],
                'itinerario' => null,
            ];
        }

        /*
         * 12. Conservar solo los días con actividades.
         *     El validador ya informó en observaciones cuántos
         *     días se cubrieron frente a los solicitados.
         */
        $diasConActividades = array_values(array_filter(
            $diasItinerario,
            fn (DiaItinerario $dia) => $dia->cantidadActividades() > 0
        ));

        if ($diasConActividades === []) {
            return [
                'estado' => 'NO_FACTIBLE',
                'restricciones' => $restricciones,
                'errores' => [[
                    'criterio' => 'actividades',
                    'mensaje' =>
                        'No se pudo programar ninguna actividad '
                        . 'con las restricciones indicadas.',
                ]],
                'observaciones' => [],
                'itinerario' => null,
            ];
        }

        /*
         * 13. Resultado exitoso.
         */
        $presentacion = $this->presentacionService->presentar(
            $diasConActividades,
            $restricciones
        );

        return [
            'estado' => 'OK',
            'restricciones' => $restricciones,
            'errores' => [],
            'observaciones' => $validacion['observaciones'],
            'itinerario' => $presentacion,
        ];
    }

    private function obtenerActividadesPorDia(
        string $ritmo
    ): int {
        return match ($ritmo) {
            'relajado' => 2,
            'intenso' => 4,
            default => 3,
        };
    }
}