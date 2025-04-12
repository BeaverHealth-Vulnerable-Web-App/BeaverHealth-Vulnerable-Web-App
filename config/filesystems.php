<?php

return [

    // Default disk used for file storage
    'default' => env('FILESYSTEM_DISK', 'local'),

    // Available file system disks
    'disks' => [

        // Private storage
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ],

        // Disk for patient record uploads
        'patient_records' => [
            'driver' => 'local',
            'root' => storage_path('app/patient_records'),
            'throw' => false,
        ]

    ],

];
