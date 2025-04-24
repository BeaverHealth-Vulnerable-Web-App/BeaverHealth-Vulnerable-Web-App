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

];
