<?php

return [

    // Default queue connection
    // We don't use any queuing, so this is just a stub
    'default' => env('QUEUE_CONNECTION', 'sync'),

    // Only sync driver is used (no actual queuing)
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
    ],

];
