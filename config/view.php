<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | View Debug Mode
    |--------------------------------------------------------------------------
    |
    | When view debug mode is enabled, the compiled view will be checked
    | for modifications. This is useful during development as it allows
    | you to see changes to your views without recompiling them.
    |
    */

    'debug' => env('VIEW_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | View Namespaces
    |--------------------------------------------------------------------------
    |
    | Here you may define the namespaces for your views.
    |
    */

    'namespaces' => [
        'laravel-exceptions-renderer' => resource_path('views/errors'),
    ],

];
