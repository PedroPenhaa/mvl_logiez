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
            'client_id' => '1070057278923-p5telmjqd2hsdco126tjfc7d9kp7fm2o.apps.googleusercontent.com',
            'client_secret' => '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',
            'redirect' => 'http://localhost:8080/auth/google/callback',
        ],

        'apple' => [
            'client_id' => env('APPLE_CLIENT_ID'),
            'client_secret' => env('APPLE_CLIENT_SECRET'),
            'redirect' => env('APP_URL') . env('APPLE_REDIRECT_URI', '/auth/apple/callback'),
        ],
    ],

]; 