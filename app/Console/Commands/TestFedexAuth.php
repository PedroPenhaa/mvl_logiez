<?php

namespace App\Console\Commands;

use App\Services\FedexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFedexAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedex:test-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a autenticação com a API FedEx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando teste de autenticação FedEx...');
        
        // Obter as configurações atuais
        $this->info('Ambiente: ' . (config('services.fedex.use_production') ? 'Produção' : 'Homologação'));
        $this->info('API URL: ' . config('services.fedex.api_url'));
        $this->info('Client ID: ' . substr(config('services.fedex.client_id'), 0, 5) . '...' . substr(config('services.fedex.client_id'), -5));
        
        try {
            // Inicializar o serviço FedEx
            $fedexService = new FedexService();
            
            // Limpar cache de token para forçar nova autenticação
            $this->info('Forçando novo token de autenticação...');
            
            // Obter token (com força de novo token)
            $token = $fedexService->getAuthToken(true);
            
            $this->info('Autenticação bem-sucedida!');
            $this->info('Token obtido: ' . substr($token, 0, 10) . '...' . substr($token, -10));
            
           // dd($token);
            // Informações do cache
            $tokenDetails = \Illuminate\Support\Facades\Cache::get('fedex_token_details');
            if ($tokenDetails) {
                $this->info('Token expira em: ' . $tokenDetails['expires_in'] . ' segundos');
                $this->info('Token obtido em: ' . $tokenDetails['obtained_at']);
                $this->info('Token expira em: ' . $tokenDetails['expires_at']);
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro na autenticação: ' . $e->getMessage());
            Log::error('Teste de autenticação FedEx falhou', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
} 