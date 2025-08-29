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
        // Configurar o renderer de exceções para Laravel 12
        $exceptions->renderable(function (\Throwable $e) {
            // Para erros 500, usar a view personalizada
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $statusCode = $e->getStatusCode();
                
                if ($statusCode === 500) {
                    return response()->view('errors.500', ['exception' => $e], 500);
                }
                
                if ($statusCode === 404) {
                    return response()->view('errors.404', ['exception' => $e], 404);
                }
            }
            
            // Para outros erros, usar a view genérica
            return response()->view('errors.error', ['exception' => $e], 500);
        });
    })->create();
