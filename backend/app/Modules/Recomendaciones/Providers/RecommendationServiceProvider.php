<?php

namespace App\Modules\Recomendaciones\Providers;

use App\Modules\Recomendaciones\Contracts\GeneradorRecomendacionesInterface;
use App\Modules\Recomendaciones\Contracts\LLMClientInterface;
use App\Modules\Recomendaciones\Services\CalculadoraTraslados;
use App\Modules\Recomendaciones\Services\RecomendacionOrchestratorService;
use App\Modules\Recomendaciones\Services\AI\AnthropicClientService;
use App\Modules\Recomendaciones\Services\AI\GeminiClientService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class RecommendationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LLMClientInterface::class,
            function () {
                $provider = config('recommendations.llm.provider');

                return match ($provider) {
                    'gemini' => app(GeminiClientService::class),
                    'anthropic' => app(AnthropicClientService::class),

                    default => throw new RuntimeException(
                        "Proveedor LLM no soportado: {$provider}"
                    ),
                };
            }
        );

        $this->app->singleton(
            CalculadoraTraslados::class,
            fn () => new CalculadoraTraslados(
                factorRodeo: (float) config('recommendations.traslados.factor_rodeo', 1.3),
                umbralCaminataKm: (float) config('recommendations.traslados.umbral_caminata_km', 1.0),
                velocidadCaminataKmh: (float) config('recommendations.traslados.velocidad_caminata_kmh', 5.0),
                velocidadVehiculoKmh: (float) config('recommendations.traslados.velocidad_vehiculo_kmh', 25.0),
                esperaVehiculoMin: (int) config('recommendations.traslados.espera_vehiculo_min', 5),
            )
        );

        // Frontera del módulo: hoy implementación local; al separar el
        // microservicio, aquí se enlaza el cliente HTTP.
        $this->app->bind(
            GeneradorRecomendacionesInterface::class,
            RecomendacionOrchestratorService::class
        );
    }

    public function boot(): void
    {

        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        RateLimiter::for('recomendaciones', function (Request $request) {
            return Limit::perMinute(
                config('recommendations.limits.rate_limit_per_minute', 5)
            )->by($request->user()?->id ?: $request->ip());
        });
    }
}
