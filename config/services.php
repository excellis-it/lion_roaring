<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google' => [
        'maps_key' => env('GOOGLE_MAPS_API_KEY'),

        'translate' => [
            'key' => env('GOOGLE_TRANSLATE_API_KEY'),
            'enabled' => env('TRANSLATE_ENABLED', true),
            // Hard stop: characters sent to Google per calendar month ($20 / 1M chars)
            'monthly_char_limit' => (int) env('TRANSLATE_MONTHLY_CHAR_LIMIT', 2000000),
            // Longest single string we will pay to translate
            'max_chars_per_string' => (int) env('TRANSLATE_MAX_CHARS_PER_STRING', 5000),
            // Per-visitor characters per day (abuse guard on the public endpoint)
            'session_daily_char_limit' => (int) env('TRANSLATE_SESSION_DAILY_CHAR_LIMIT', 200000),
            // Bump to invalidate every visitor's localStorage translation cache
            'cache_version' => env('TRANSLATE_CACHE_VERSION', 'v1'),
        ],
    ],

    'metalpriceapi' => [
        'key' => env('METALPRICEAPI_KEY', '95735e9850d7d454adfac60f2a6c6984'),
    ],

    'goldapi' => [
        'base' => env('GOLD_API_BASE', 'https://api.gold-api.com'),
    ],

    'market_rates' => [
        'primary' => env('MARKET_RATE_PRIMARY_API', 'metalpriceapi'),
    ],

];
