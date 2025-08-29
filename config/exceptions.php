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

    'log' => [
        'enabled' => env('EXCEPTION_LOG_ENABLED', true),
        'channel' => env('EXCEPTION_LOG_CHANNEL', 'single'),
    ],

];
