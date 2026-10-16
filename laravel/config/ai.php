<?php

return [
    'provider' => env('AI_PROVIDER', 'gemini'),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'models' => [
            env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
            env('GEMINI_FALLBACK_MODEL', 'gemini-2.5-flash'),
        ],
    ],
];