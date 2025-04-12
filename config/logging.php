<?php

use Monolog\Handler\NullHandler;

return [

    // Default log channel
    'default' => env('LOG_CHANNEL', 'single'),

    // Channel for PHP/library deprecation warnings
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    // Available log channels
    'channels' => [

        // Standard application logs
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // Daily rotating log for user activity
        'user_activity' => [
            'driver' => 'daily',
            'path' => storage_path('logs/user_activity.log'),
            'level' => 'info',
            'days' => 30,
        ],

        // For deprecation warnings
        'deprecation' => [
            'driver' => 'single',
            'path' => storage_path('logs/deprecation.log'),
            'level' => 'notice',
            'replace_placeholders' => true,
        ],

        // Discard deprecation logs unless configured otherwise
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        // Fallback channel used when all other channels fail
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
