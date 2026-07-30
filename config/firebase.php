<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            'credentials' => [
                // Must stay off the public disk — storage/app/public is served
                // verbatim through the public/storage symlink.
                'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-adminsdk.json')),
            ],
            'database' => [
                'url' => env('FIREBASE_DATABASE_URL'),
            ],
        ],
    ],
];
