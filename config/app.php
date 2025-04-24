<?php

return [

    // Application name
    'name' => env('APP_NAME'),

    // Application environment
    'env' => env('APP_ENV'),

    // Debug mode
    // When in debug mode, detailed error messages with stack traces are provided
    'debug' => (bool) env('APP_DEBUG'),

    // Application URL
    // Used to generate URLs when using the Artisan CLI
    'url' => env('APP_URL'),

    // Application timezone
    'timezone' => env('APP_TIMEZONE', 'UTC'),

    // Application locale
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    // Encryption cipher and key
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),

    // Maintenance mode driver
    // Determines how Laravel tracks "maintenance mode" status
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    ],

];
