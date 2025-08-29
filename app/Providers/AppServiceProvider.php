<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FedexService;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;
use Illuminate\Support\Arr;

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
        

        
        // Registrar o Socialite manualmente com a configuração correta
        $this->app->singleton(Factory::class, function ($app) {
            // Carregar configurações de config/services.php e config/socialite.php
            $config = $app['config']['services'];
            
            // Se existir o arquivo config/socialite.php, mesclar suas configurações
            if ($app['config']->has('socialite.providers')) {
                $providers = $app['config']['socialite.providers'];
                foreach ($providers as $provider => $providerConfig) {
                    $config[$provider] = $providerConfig;
                }
            }
            
            return new SocialiteManager($app, $config);
        });
        
        // Criar um alias para o Facade
        $this->app->alias(Factory::class, 'Laravel\Socialite\Facades\Socialite');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
