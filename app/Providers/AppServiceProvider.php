<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FedexService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FedexService::class, function ($app) {
            return new FedexService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
