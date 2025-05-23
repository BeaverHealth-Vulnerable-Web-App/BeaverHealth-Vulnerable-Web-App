<?php

return [

    // Default authentication guard
    'defaults' => [
        'guard' => 'web',
    ],

    // Authentication guards define how users are authenticated per request
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    // User providers define how user records are fetched from storage
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    // Rate limiting for login page accesses
    'login_page_access_rate_limit' => [
        // Whether to enable rate limiting for login page accesses locally
        'enable_locally' => env('LOGIN_PAGE_ACCESS_RATE_ENABLE', false),

        // The number of accesses before a lockout occurs
        'max_attempts' => env('LOGIN_PAGE_ACCESS_RATE_MAX', 15),

        // How long before a recorded access expires and no longer counts toward the limit
        'decay_seconds' => env('LOGIN_PAGE_ACCESS_RATE_DECAY', 60),
    ],

    // Rate limiting for login attempts
    'login_attempts_rate_limit' => [
        // Whether to enable rate limiting for login attempts locally
        'enable_locally' => env('LOGIN_ATTEMPTS_RATE_ENABLE', false),

        // The number of login attempts before a lockout occurs
        'max_attempts' => env('LOGIN_ATTEMPTS_RATE_MAX', 5),

        // How long before a failed login attempt expires and no longer counts toward the limit
        'decay_seconds' => env('LOGIN_ATTEMPTS_RATE_DECAY', 60),
    ]

];
