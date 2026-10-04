<?php

namespace App\Modules\Recomendaciones\Services\AI;

use App\Modules\Recomendaciones\Contracts\LLMClientInterface;
use App\Modules\Recomendaciones\Exceptions\LLMNoDisponibleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicClientService implements LLMClientInterface
{
    private string $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function enviar(
        string $systemPrompt,
        string $userPrompt,
        array $outputFormat,
    ): array {
        $apiKey = config('recommendations.anthropic.api_key');
        $model = config('recommendations.anthropic.model');
        $maxTokens = config('recommendations.anthropic.max_tokens');

        if (empty($apiKey)) {
            throw new LLMNoDisponibleException(
                'No se ha configurado ANTHROPIC_API_KEY.'
            );
        }

        try {
            $response = Http::connectTimeout(5)
                ->timeout(config('recommendations.anthropic.timeout', 30))
                ->retry(
                    2,
                    500,
                    fn ($e) => $e instanceof ConnectionException
                        || ($e instanceof RequestException
                            && in_array($e->response->status(), [429, 500, 502, 503, 529], true)),
                    throw: false
                )
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => $model,
                    'max_tokens' => $maxTokens,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'output_config' => [
                        'format' => $outputFormat,
                    ],
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Anthropic: fallo de conexión', ['error' => $e->getMessage()]);

            throw new LLMNoDisponibleException('Anthropic no respondió.', 0, $e);
        }

        if ($response->failed()) {
            Log::warning('Anthropic: respuesta con error', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            throw new LLMNoDisponibleException(
                "Anthropic respondió con estado {$response->status()}."
            );
        }

        return $this->extraerJsonRespuesta($response->json() ?? []);
    }

    /**
     * La API devuelve un sobre {content:[{type:'text',text:'...'}], stop_reason...}.
     * El resto del módulo espera directamente el JSON del modelo.
     */
    private function extraerJsonRespuesta(array $respuesta): array
    {
        $stop = $respuesta['stop_reason'] ?? null;

        if ($stop === 'max_tokens' || $stop === 'refusal') {
            throw new LLMNoDisponibleException(
                "Anthropic no completó la respuesta ({$stop})."
            );
        }

        $texto = collect($respuesta['content'] ?? [])
            ->firstWhere('type', 'text')['text'] ?? null;

        if (!is_string($texto) || trim($texto) === '') {
            throw new LLMNoDisponibleException(
                'Anthropic no devolvió contenido JSON.'
            );
        }

        $resultado = json_decode($texto, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($resultado)) {
            throw new LLMNoDisponibleException(
                'Anthropic devolvió un JSON inválido: ' . json_last_error_msg()
            );
        }

        return $resultado;
    }
}
