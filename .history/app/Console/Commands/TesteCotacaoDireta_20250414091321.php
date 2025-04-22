<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TesteCotacaoDireta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:cotacao-direta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de cotação da FedEx diretamente sem usar cache';

    protected $apiUrl;
    protected $clientId;
    protected $clientSecret;
    protected $shipperAccount;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Obter credenciais diretamente das configurações
        $useProduction = env('FEDEX_USE_PRODUCTION', false);
        $this->apiUrl = $useProduction
            ? env('FEDEX_PROD_API_URL', 'https://apis.fedex.com')
            : env('FEDEX_HOM_API_URL', 'https://apis-sandbox.fedex.com');
            
        $this->clientId = $useProduction
            ? env('FEDEX_PROD_CLIENT_ID')
            : env('FEDEX_HOM_CLIENT_ID');
            
        $this->clientSecret = $useProduction
            ? env('FEDEX_PROD_CLIENT_SECRET')
            : env('FEDEX_HOM_CLIENT_SECRET');
            
        $this->shipperAccount = $useProduction
            ? env('FEDEX_PROD_SHIPPER_ACCOUNT')
            : env('FEDEX_HOM_SHIPPER_ACCOUNT');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando teste de cotação direta FedEx...');
        $this->info('Ambiente: ' . (env('FEDEX_USE_PRODUCTION', false) ? 'Produção' : 'Homologação'));
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
        
        // Obter token
        $this->info('Obtendo token de autenticação...');
        try {
            $authUrl = $this->apiUrl . '/oauth/token';
            
            // Preparar payload para a solicitação de token
            $tokenPayload = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ];
            
            $this->info('Fazendo requisição para: ' . $authUrl);
            
            $response = Http::asForm()->post($authUrl, $tokenPayload);
            
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                
                if (!$token) {
                    $this->error('Token não encontrado na resposta');
                    return Command::FAILURE;
                }
                
                $this->info('Token obtido com sucesso: ' . substr($token, 0, 10) . '...' . substr($token, -10));
                
                // Agora fazer a requisição de cotação
                $this->info('Realizando consulta de cotação...');
                
                // Cálculo do peso cúbico
                $pesoCubico = ($altura * $largura * $comprimento) / 5000;
                $pesoUtilizado = max($pesoCubico, $peso);
                $this->info('Peso cúbico: ' . $pesoCubico . ' kg');
                $this->info('Peso utilizado: ' . $pesoUtilizado . ' kg');
                
                // Preparar requisição de cotação
                $rateUrl = $this->apiUrl . '/rate/v1/rates/quotes';
                $shipDate = date('Y-m-d');
                
                $rateRequest = [
                    'accountNumber' => [
                        'value' => $this->shipperAccount
                    ],
                    'requestedShipment' => [
                        'shipper' => [
                            'address' => [
                                'postalCode' => $origem,
                                'countryCode' => 'BR',
                                'residential' => false
                            ]
                        ],
                        'recipient' => [
                            'address' => [
                                'postalCode' => $destino,
                                'countryCode' => 'US',
                                'residential' => false
                            ]
                        ],
                        'preferredCurrency' => 'USD',
                        'rateRequestType' => ['LIST', 'ACCOUNT'],
                        'shipDateStamp' => $shipDate,
                        'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                        'packagingType' => 'YOUR_PACKAGING',
                        'requestedPackageLineItems' => [
                            [
                                'weight' => [
                                    'units' => 'KG',
                                    'value' => $peso
                                ],
                                'dimensions' => [
                                    'length' => $comprimento,
                                    'width' => $largura,
                                    'height' => $altura,
                                    'units' => 'CM'
                                ],
                                'groupPackageCount' => 1
                            ]
                        ],
                        'totalPackageCount' => 1
                    ],
                    'carrierCodes' => ['FDXE']
                ];
                
                $this->info('Fazendo requisição para: ' . $rateUrl);
                
                $rateResponse = Http::withToken($token)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-locale' => 'en_US'
                    ])
                    ->post($rateUrl, $rateRequest);
                
                if ($rateResponse->successful()) {
                    $rateData = $rateResponse->json();
                    $this->info('Resposta de cotação obtida com sucesso!');
                    $this->info('Resposta completa:');
                    $this->info(json_encode($rateData, JSON_PRETTY_PRINT));
                    
                    // Verificar se existem opções de cotação
                    if (isset($rateData['output']['rateReplyDetails']) && !empty($rateData['output']['rateReplyDetails'])) {
                        $this->info('Serviços disponíveis:');
                        foreach ($rateData['output']['rateReplyDetails'] as $rateDetail) {
                            $serviceType = $rateDetail['serviceType'] ?? 'N/A';
                            $serviceName = $rateDetail['serviceName'] ?? 'Serviço não especificado';
                            
                            $amount = 'N/A';
                            $currency = 'N/A';
                            
                            if (isset($rateDetail['ratedShipmentDetails'][0]['totalNetCharge'])) {
                                $amount = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['amount'] ?? 'N/A';
                                $currency = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['currency'] ?? 'N/A';
                            }
                            
                            $this->info("Serviço: {$serviceName} ({$serviceType}) - Valor: {$amount} {$currency}");
                        }
                    } else {
                        $this->warn('Nenhuma opção de envio encontrada para os parâmetros fornecidos.');
                        
                        // Verificar se há algum erro ou notificação na resposta
                        if (isset($rateData['output']['notifications'])) {
                            $this->info('Notificações da API:');
                            foreach ($rateData['output']['notifications'] as $notification) {
                                $code = $notification['code'] ?? 'N/A';
                                $message = $notification['message'] ?? 'N/A';
                                $this->warn("Código: {$code} - Mensagem: {$message}");
                            }
                        }
                    }
                } else {
                    $this->error('Erro na requisição de cotação: ' . $rateResponse->status());
                    $this->error('Resposta: ' . $rateResponse->body());
                }
            } else {
                $this->error('Erro na requisição do token: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Erro durante a execução: ' . $e->getMessage());
            Log::error('Erro no teste de cotação direta: ' . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
} 