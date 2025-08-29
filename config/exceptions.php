<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exception Handler Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the exception handler for Laravel 12
    |
    */

    'renderer' => [
        'hint_path' => resource_path('views/errors'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Logging
    |--------------------------------------------------------------------------
    |
    | Configure exception logging
    |
    */

    'log' => [
        'enabled' => env('EXCEPTION_LOG_ENABLED', true),
        'channel' => env('EXCEPTION_LOG_CHANNEL', 'single'),
    ],

];
