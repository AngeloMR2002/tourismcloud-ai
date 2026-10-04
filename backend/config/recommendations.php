<?php

return [
    'llm' => [
        'provider' => env('LLM_PROVIDER', 'gemini'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 2048),
        'timeout' => (int) env('ANTHROPIC_TIMEOUT', 30),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 30),
    ],

    'limits' => [
        // Longitud máxima del texto libre que se envía al LLM.
        'max_solicitud_chars' => (int) env('RECOMMENDATIONS_MAX_CHARS', 1000),
        // Generaciones por minuto y por turista (cada una cuesta una llamada al LLM).
        'rate_limit_per_minute' => (int) env('RECOMMENDATIONS_RATE_LIMIT', 5),
    ],
];
