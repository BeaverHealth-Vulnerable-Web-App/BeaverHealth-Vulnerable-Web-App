<?php

use Illuminate\Support\Str;

return [

    // Session storage driver
    'driver' => env('SESSION_DRIVER', 'file'),

    // Minutes before session expires
    'lifetime' => env('SESSION_LIFETIME', 120),

    // Whether to expire session on browser close
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    // Whether to encrypt session data on disk
    'encrypt' => env('SESSION_ENCRYPT', false),

    // Location for 'file' session driver
    'files' => storage_path('framework/sessions'),

    // Not used with 'file' driver, but required keys
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),

    // Chance of old sessions being cleaned up for a given request (2%)
    'lottery' => [2, 100],

    // Session cookie name
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME'), '_') . '_session'
    ),

    // Limits where cookie is sent to on the domain
    // '/' means the cookie is sent to all routes
    'path' => env('SESSION_PATH', '/'),

    // Defines domains/subdomains the cookie is available to
    // If null, defaults to the domain the request came from
    // We don't need this since we don't use subdomains
    'domain' => env('SESSION_DOMAIN'),

    // If true, cookie is sent only over HTTPS
    // Should be true in production to prevent session hijacking
    'secure' => env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'prod'),

    // If true, the cookie cannot be accessed by JavaScript
    // Prevents XSS from reading session cookie
    'http_only' => env('SESSION_HTTP_ONLY', true),

    // Controls same-site cookie behavior
    // 'lax' means cookies are sent with top-level navigation (e.g., link clicks)
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    // If true, marks the cookie as a partitioned third-party cookie
    // Not needed, since we don't embed the app in other sites
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
