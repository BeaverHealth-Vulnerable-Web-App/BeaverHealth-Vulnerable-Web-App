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

            'unix_socket' => env('DB_SOCKET', ''), // Optional: for local MySQL via socket; leave empty for TCP/IP

            'charset' => env('DB_CHARSET', 'utf8mb4'),                // Use full Unicode
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'), // Case-insensitive string sorting

            'prefix' => '',           // We don't use table prefixes - keep this blank
            'prefix_indexes' => true, // Safe default - allows index names to include prefix if added later
            'strict' => true,         // Enforce strict SQL mode - prevents silent data loss or type coercion
            'engine' => null,         // Let MySQL choose storage engine

            // Extra PDO options (SSL, etc.)
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],

    // Migrations table keeps track of which migrations have run
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
