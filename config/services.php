<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY', 're_4ZWQHtqY_BiqLj9e1krUdnUtshCVTnDpj'),
        'from' => env('RESEND_FROM', 'AgriCo Support <onboarding@resend.dev>'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', (env('APP_URL', 'http://localhost:8000') . '/auth/facebook/callback')),
    ],

    'soil_ml' => [
        'endpoint'       => env('SOIL_ML_API_URL'),
        'python_binary'  => env('PYTHON_BINARY'),
        'scan_root'      => env('SCAN_SOIL_ROOT', storage_path('app/Scan_Soil_Image')),
        'munsell_csv'    => env('MUNSELL_REFERENCE_CSV', base_path('python/munsell_reference_full.csv')),
        'stage2_mode'    => env('STAGE2_MODE', 'classical'),
    ],

    'turnstile' => [
        'key'    => env('TURNSTILE_SITE_KEY', '0x4AAAAAAE7UD3OjLmMRWxlW'),
        'secret' => env('TURNSTILE_SECRET'),
    ],

];
