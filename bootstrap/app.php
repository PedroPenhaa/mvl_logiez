<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\SocialiteServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        SocialiteServiceProvider::class,
        Laravel\Socialite\SocialiteServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Desabilitar renderizador customizado para evitar erro do laravel-exceptions-renderer
        if (!config('exceptions.use_custom_renderer', false)) {
            // Usar apenas o handler padrão do Laravel
            return;
        }
    })
    ->create();
