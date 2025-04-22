<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TesteCotacaoFedex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:cotacao-fedex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de cotação da FedEx e exibe os detalhes da resposta';

    protected $fedexService;
    protected $apiUrl;
    protected $clientId;
    protected $clientSecret;
    protected $shipperAccount;

    /**
     * Create a new command instance.
     */
    public function __construct(FedexService $fedexService)
    {
        parent::__construct();
        $this->fedexService = $fedexService;
        
        // Obter credenciais diretamente das configurações
        $this->apiUrl = config('services.fedex.api_url', "https://apis-sandbox.fedex.com");
        $this->clientId = config('services.fedex.client_id');
        $this->clientSecret = config('services.fedex.client_secret');
        $this->shipperAccount = config('services.fedex.shipper_account');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando teste de cotação FedEx...');
        $this->info('API URL: ' . $this->apiUrl);
        $this->info('Client ID: ' . $this->clientId);
        $this->info('Shipper Account: ' . $this->shipperAccount);
        
        // Parâmetros de teste
        $origem = '01310-100'; // São Paulo
        $destino = '10001'; // New York
        $altura = 10; // cm
        $largura = 20; // cm
        $comprimento = 30; // cm
        $peso = 1; // kg
        
        // Testar simulação
        $this->info('Testando com simulação forçada:');
        try {
            $simulado = $this->fedexService->calcularCotacao(
                $origem,
                $destino,
                $altura,
                $largura,
                $comprimento,
                $peso,
                true // Forçar simulação
            );
            
            $this->info('Resposta da simulação:');
            $this->info(json_encode($simulado, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error('Erro na simulação: ' . $e->getMessage());
            Log::error('Erro no teste de simulação: ' . $e->getMessage());
        }
        
        // Testar obtenção do token diretamente
        $this->info('Testando obtenção do token diretamente:');
        try {
            $authUrl = $this->apiUrl . '/oauth/token';
            
            // Preparar payload para a solicitação de token
            $tokenPayload = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ];
            
            $this->info('Fazendo requisição para: ' . $authUrl);
            $this->info('Payload: ' . json_encode($tokenPayload));
            
            $response = Http::asForm()->post($authUrl, $tokenPayload);
            
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? 'N/A';
                
                $this->info('Token obtido com sucesso: ' . substr($token, 0, 10) . '...' . substr($token, -10));
                $this->info('Resposta completa: ' . json_encode($data));
                
                // Agora vamos fazer a requisição de cotação diretamente
                $rateUrl = $this->apiUrl . '/rate/v1/rates/quotes';
                $this->info('Fazendo requisição de cotação para: ' . $rateUrl);
                
                // [Código da requisição omitido para não ficar muito extenso]
                $this->info('Requisição de cotação precisa ser implementada diretamente. Por enquanto, use a simulação.');
            } else {
                $this->error('Erro na requisição do token: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Erro ao obter token: ' . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
} 