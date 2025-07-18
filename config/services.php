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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'fedex' => [
        'use_production' => env('FEDEX_USE_PRODUCTION', false),
        'client_id' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_CLIENT_ID') : env('FEDEX_HOM_CLIENT_ID'),
        'client_secret' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_CLIENT_SECRET') : env('FEDEX_HOM_CLIENT_SECRET'),
        'api_url' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_API_URL', 'https://apis.fedex.com') : env('FEDEX_HOM_API_URL', 'https://apis-sandbox.fedex.com'),
        'shipper_account' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_SHIPPER_ACCOUNT') : env('FEDEX_HOM_SHIPPER_ACCOUNT'),
        'auth_endpoint' => '/oauth/token',
        'rate_endpoint' => '/rate/v1/rates/quotes',
        'ship_endpoint' => '/ship/v1/shipments',
        'track_endpoint' => '/track/v1/trackingnumbers',
        // Credenciais específicas para rastreamento
        'tracking_client_id' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_TRACKING_CLIENT_ID') : env('FEDEX_HOM_CLIENT_ID'),
        'tracking_client_secret' => env('FEDEX_USE_PRODUCTION', false) ? env('FEDEX_PROD_TRACKING_CLIENT_SECRET') : env('FEDEX_HOM_CLIENT_SECRET'),
        'special_tracking' => [
            '794616896420' => [
                'client_id' => 'l76ba883d77f744aecb0a0d2d944f64e83',
                'client_secret' => '9715b01ba6004c74bf2774af3e51c336',
                'api_url' => 'https://apis-sandbox.fedex.com'
            ]
        ],
    ],

    // Configuração da API do Gemini
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI', '/auth/apple/callback'),
    ],

    /*
     * Configurações para integrações com APIs de serviços.
     */
    'asaas' => [
        'token' => env('ASAAS_API_TOKEN', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6Ojk5YWQxY2M2LTg1ODUtNDA2YS04ZWRlLTAzNTY5NDRmYmM2Mjo6JGFhY2hfYTI0ZmIzYjUtMWRiOS00MmJiLWI1MjItYjk1ZWRjNTQxYjI5'),
        'sandbox' => env('ASAAS_SANDBOX', true),
    ],

];
