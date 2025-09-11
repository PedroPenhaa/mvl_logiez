<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exception Renderer
    |--------------------------------------------------------------------------
    |
    | This option controls whether to use the default Laravel exception renderer
    | or a custom one. Setting this to false will disable the custom renderer
    | and use the default Laravel exception handling.
    |
    */

    'use_custom_renderer' => false,

    /*
    |--------------------------------------------------------------------------
    | Exception Handler
    |--------------------------------------------------------------------------
    |
    | This option controls the exception handler class to use.
    |
    */

    'handler' => \Illuminate\Foundation\Exceptions\Handler::class,
];
