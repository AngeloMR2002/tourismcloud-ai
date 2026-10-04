<?php

namespace App\Modules\Recomendaciones\Contracts;

interface LLMClientInterface
{
    public function enviar(
        string $systemPrompt,
        string $userPrompt,
        array $outputFormat
    ): array;
}