<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class TestFedexCotacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedex:test-cotacao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de cotação da FedEx';

    /**
     * Execute the console command.
     */
    public function handle(FedexService $fedexService)
    {
        $this->info('🚚 Testando cotação de envio FedEx');
        $this->info('----------------------------------');

        // Valores fixos para o teste
        $origem = '01310100';    // CEP de São Paulo (Av. Paulista)
        $destino = '33131';      // CEP de Miami, FL
        $altura = 10;            // 10 cm
        $largura = 20;           // 20 cm
        $comprimento = 30;       // 30 cm
        $peso = 10;              // 10 kg

        $this->info("📦 Dados do pacote:");
        $this->info("Origem: $origem");
        $this->info("Destino: $destino");
        $this->info("Dimensões: {$altura}cm x {$largura}cm x {$comprimento}cm");
        $this->info("Peso: {$peso}kg");
        $this->info("Usando API real sem fallback para simulação");

        $this->newLine();
        $this->info('⏳ Enviando requisição para calcular cotação...');
        
        try {
            // Substituir a chamada original pelo código interno da função calcularCotacao
            // mas sem a parte que faz fallback para a simulação
            
            // Obter token de autenticação
            $accessToken = $fedexService->getAuthToken(true); // Forçar novo token
    
            // Cálculo do peso cúbico
            $pesoCubico = ($altura * $largura * $comprimento) / 5000;
            $pesoUtilizado = max($pesoCubico, $peso);
    
            // Preparar requisição de cotação
            $apiUrl = config('services.fedex.api_url', "https://apis-sandbox.fedex.com");
            $rateUrl = $apiUrl . '/rate/v1/rates/quotes';
            $transactionId = uniqid('logiez_rate_');
            $shipDate = date('Y-m-d');
            $shipperAccount = config('services.fedex.shipper_account', "740561073");
    
            // Extrair códigos postais
            $postalCodeOrigem = $origem;
            $postalCodeDestino = $destino;
            $countryCodeOrigem = 'BR';
            $countryCodeDestino = 'US';
    
            $rateRequest = [
                'accountNumber' => [
                    'value' => $shipperAccount
                ],
                'rateRequestControlParameters' => [
                    'returnTransitTimes' => true,
                    'servicesNeededOnRateFailure' => true,
                    'variableOptions' => 'FREIGHT_GUARANTEE',
                    'rateSortOrder' => 'SERVICENAMETRADITIONAL'
                ],
                'requestedShipment' => [
                    'shipper' => [
                        'address' => [
                            'postalCode' => substr($postalCodeOrigem, 0, 10),
                            'countryCode' => $countryCodeOrigem,
                            'residential' => false
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'postalCode' => substr($postalCodeDestino, 0, 10),
                            'countryCode' => $countryCodeDestino,
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
                    'totalPackageCount' => 1,
                    'documentShipment' => false,
                    'customsClearanceDetail' => [
                        'dutiesPayment' => [
                            'paymentType' => 'SENDER',
                            'payor' => [
                                'responsibleParty' => [
                                    'accountNumber' => [
                                        'value' => $shipperAccount
                                    ]
                                ]
                            ]
                        ],
                        'commodities' => [
                            [
                                'description' => 'Test Product',
                                'weight' => [
                                    'units' => 'KG',
                                    'value' => $peso
                                ],
                                'quantity' => 1,
                                'quantityUnits' => 'PCS',
                                'unitPrice' => [
                                    'amount' => 100,
                                    'currency' => 'USD'
                                ],
                                'customsValue' => [
                                    'amount' => 100,
                                    'currency' => 'USD'
                                ],
                                'countryOfManufacture' => $countryCodeOrigem,
                                'harmonizedCode' => '123456'
                            ]
                        ],
                        'commercialInvoice' => [
                            'purpose' => 'SAMPLE'
                        ]
                    ]
                ],
                'carrierCodes' => ['FDXE']
            ];
    
            // Fazer a requisição
            $rateCurl = curl_init();
            curl_setopt_array($rateCurl, [
                CURLOPT_URL => $rateUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($rateRequest),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $accessToken,
                    "X-locale: en_US",
                    "x-customer-transaction-id: " . $transactionId
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => true
            ]);
    
            $rateResponse = curl_exec($rateCurl);
            $rateHttpCode = curl_getinfo($rateCurl, CURLINFO_HTTP_CODE);
            $rateErr = curl_error($rateCurl);
            
            curl_close($rateCurl);

            // Log da resposta detalhada para debug
            $this->info('Resposta da API - Status HTTP: ' . $rateHttpCode);
            if ($rateErr) {
                $this->error('Erro cURL: ' . $rateErr);
            }
    
            // Se houver erro, simplesmente disparar exceção
            if ($rateErr) {
                throw new \Exception('Erro na requisição de cotação: ' . $rateErr);
            }
    
            if ($rateHttpCode != 200) {
                $errorDetails = json_decode($rateResponse, true);
                $errorMessage = 'Resposta da API: ' . substr($rateResponse, 0, 500);
                throw new \Exception('Falha na cotação. Código HTTP: ' . $rateHttpCode . "\n" . $errorMessage);
            }
    
            $rateData = json_decode($rateResponse, true);
    
            // Extrair cotações da resposta
            $cotacoes = [];
            if (isset($rateData['output']['rateReplyDetails'])) {
                foreach ($rateData['output']['rateReplyDetails'] as $rateDetail) {
                    $serviceName = $rateDetail['serviceName'] ?? 'Serviço Desconhecido';
                    $serviceType = $rateDetail['serviceType'] ?? '';
                    
                    // Pegar o primeiro ratedShipmentDetails (ACCOUNT)
                    $ratedShipment = $rateDetail['ratedShipmentDetails'][0] ?? null;
                    
                    if ($ratedShipment) {
                        $amount = $ratedShipment['totalNetCharge'] ?? 0;
                        $currency = $ratedShipment['currency'] ?? 'USD';
                        
                        // Extrair informações de entrega
                        $deliveryInfo = '';
                        $deliveryDate = 'N/A';
                        
                        if (isset($rateDetail['commit'])) {
                            if (isset($rateDetail['commit']['commitMessageDetails'])) {
                                $deliveryInfo = $rateDetail['commit']['commitMessageDetails'];
                            } elseif (isset($rateDetail['commit']['deliveryMessages'][0])) {
                                $deliveryInfo = $rateDetail['commit']['deliveryMessages'][0];
                            }
                            
                            if (isset($rateDetail['commit']['dateDetail']['dayFormat'])) {
                                $deliveryDate = $rateDetail['commit']['dateDetail']['dayFormat'];
                            } elseif (isset($rateDetail['commit']['dateDetail']['date'])) {
                                $deliveryDate = $rateDetail['commit']['dateDetail']['date'];
                            }
                            
                            if ($deliveryDate === 'N/A' && isset($rateDetail['commit']['derivedDeliveryDate'])) {
                                $deliveryDate = $rateDetail['commit']['derivedDeliveryDate'];
                            }
                        }
                        
                        $cotacoes[] = [
                            'servico' => $serviceName,
                            'servicoTipo' => $serviceType,
                            'valorTotal' => number_format($amount, 2, '.', ''),
                            'moeda' => $currency,
                            'tempoEntrega' => $deliveryInfo,
                            'dataEntrega' => $deliveryDate
                        ];
                    }
                }
            }
    
            $resultado = [
                'success' => true,
                'pesoCubico' => round($pesoCubico, 2),
                'pesoReal' => $peso,
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'cotacoesFedEx' => $cotacoes,
                'simulado' => false,
                'dataConsulta' => date('Y-m-d H:i:s')
            ];

            // Imprimir informações do peso
            $this->newLine();
            $this->info('✅ Cotação calculada com sucesso!');
            $this->info('----------------------------------');
            $this->info("Peso Cúbico: {$resultado['pesoCubico']} kg");
            $this->info("Peso Real: {$resultado['pesoReal']} kg");
            $this->info("Peso Utilizado: {$resultado['pesoUtilizado']} kg");
            $this->info("Simulado: Não");
            
            // Imprimir resultados das cotações
            $this->newLine();
            $this->info('📋 Opções de envio encontradas: ' . count($resultado['cotacoesFedEx']));
            
            if (count($resultado['cotacoesFedEx']) > 0) {
                $this->table(
                    ['Serviço', 'Valor', 'Moeda', 'Tempo de Entrega', 'Data Estimada'],
                    array_map(function($cotacao) {
                        return [
                            $cotacao['servico'],
                            $cotacao['valorTotal'],
                            $cotacao['moeda'],
                            $cotacao['tempoEntrega'] ?? 'N/A',
                            $cotacao['dataEntrega'] ?? 'N/A'
                        ];
                    }, $resultado['cotacoesFedEx'])
                );
            } else {
                $this->error('Nenhuma opção de envio encontrada!');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao calcular cotação: ' . $e->getMessage());
            
            Log::error('Erro no command TestFedexCotacao', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
} 