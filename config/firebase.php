<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            'credentials' => [
                'file' => storage_path('app/public/firebase-adminsdk.json'),
            ],
            'database' => [
                'url' => env('FIREBASE_DATABASE_URL'),
            ],
        ],
    ],
];
