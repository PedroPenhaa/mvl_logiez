<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar o handler customizado
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );
        
        // Desabilitar o renderizador customizado problemático
        $this->app->bind(
            \Illuminate\Foundation\Exceptions\Renderer\Renderer::class,
            function () {
                return null;
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
