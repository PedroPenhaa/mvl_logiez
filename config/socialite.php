<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configurações do Socialite
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações para os provedores de autenticação OAuth.
    | As credenciais são definidas no arquivo .env.
    |
    */

    'providers' => [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('APP_URL') . env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        ],

        'apple' => [
            'client_id' => env('APPLE_CLIENT_ID'),
            'client_secret' => env('APPLE_CLIENT_SECRET'),
            'redirect' => env('APP_URL') . env('APPLE_REDIRECT_URI', '/auth/apple/callback'),
        ],
    ],

]; 