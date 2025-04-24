<?php

return [

    // Default database connection
    'default' => env('DB_CONNECTION', 'mysql'),

    // Available database connections
    'connections' => [

        'mysql' => [
            'driver' => 'mysql',

            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),

            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),

            'strict' => true,
        ],

    ],

    // Migrations table keeps track of which migrations have run
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
