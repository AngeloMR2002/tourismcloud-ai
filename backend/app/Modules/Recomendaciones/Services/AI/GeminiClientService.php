<?php

namespace App\Modules\Recomendaciones\Services\AI;

use App\Modules\Recomendaciones\Contracts\LLMClientInterface;
use App\Modules\Recomendaciones\Exceptions\LLMNoDisponibleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiClientService implements LLMClientInterface
{
    private string $baseUrl =
        'https://generativelanguage.googleapis.com/v1beta/models';

    public function enviar(
        string $systemPrompt,
        string $userPrompt,
        array $outputFormat,
    ): array {
        $apiKey = config('recommendations.gemini.api_key');
        $model = config('recommendations.gemini.model');
        $timeout = config('recommendations.gemini.timeout', 30);

        if (empty($apiKey)) {
            throw new LLMNoDisponibleException(
                'No se ha configurado GEMINI_API_KEY.'
            );
        }

        $schema = $this->extraerSchema($outputFormat);

        try {
            $response = Http::connectTimeout(5)
                ->timeout($timeout)
                ->retry(
                    2,
                    500,
                    fn ($e) => $e instanceof ConnectionException
                        || ($e instanceof RequestException
                            && in_array($e->response->status(), [429, 500, 502, 503], true)),
                    throw: false
                )
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'content-type' => 'application/json',
                ])
                ->post(
                    "{$this->baseUrl}/{$model}:generateContent",
                    [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemPrompt]],
                        ],
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $userPrompt]],
                            ],
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'responseJsonSchema' => $schema,
                        ],
                    ]
                );
        } catch (ConnectionException $e) {
            Log::warning('Gemini: fallo de conexión', ['error' => $e->getMessage()]);

            throw new LLMNoDisponibleException('Gemini no respondió.', 0, $e);
        }

        if ($response->failed()) {
            // El detalle va al log, no al mensaje de la excepción.
            Log::warning('Gemini: respuesta con error', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            throw new LLMNoDisponibleException(
                "Gemini respondió con estado {$response->status()}."
            );
        }

        return $this->extraerJsonRespuesta($response->json() ?? []);
    }

    private function extraerJsonRespuesta(array $respuesta): array
    {
        $texto = $respuesta['candidates'][0]['content']['parts'][0]['text']
            ?? null;

        if (!is_string($texto) || trim($texto) === '') {
            throw new LLMNoDisponibleException(
                'Gemini no devolvió contenido JSON.'
            );
        }

        $resultado = json_decode($texto, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($resultado)) {
            throw new LLMNoDisponibleException(
                'Gemini devolvió un JSON inválido: ' . json_last_error_msg()
            );
        }

        return $resultado;
    }

    private function extraerSchema(array $outputFormat): array
    {
        if (
            isset($outputFormat['type']) &&
            $outputFormat['type'] === 'json_schema' &&
            isset($outputFormat['schema']) &&
            is_array($outputFormat['schema'])
        ) {
            return $outputFormat['schema'];
        }

        throw new \InvalidArgumentException(
            'Formato de salida JSON Schema no válido.'
        );
    }
}
