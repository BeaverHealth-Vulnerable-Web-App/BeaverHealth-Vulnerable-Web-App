<?php

use Illuminate\Support\Str;

return [

    // Default cache store
    'default' => env('CACHE_STORE', 'database'),

    // Cache stores define where cached data is stored
    'stores' => [

        // In-memory cache (only lasts for current request)
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        // Database-backed cache (persists across requests)
        'database' => [
            'driver' => 'database',
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE', 'cache_locks'),
        ],

    ],

    // Prefix added to all cache keys to avoid collisions
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME'), '_') . '_cache_'),

];
