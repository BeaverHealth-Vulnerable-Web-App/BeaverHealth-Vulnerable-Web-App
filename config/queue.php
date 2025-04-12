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

    // Job batching not used, but defined in case it's needed later
    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    // Failed jobs config, unused but defined to satisfy Laravel internals
    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'null'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
