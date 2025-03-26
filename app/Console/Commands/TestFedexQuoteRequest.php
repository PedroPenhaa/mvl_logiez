<?php

namespace App\Console\Commands;

use App\Services\FedexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFedexQuoteRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedex:teste';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a requisição para cálculo de cotação com a API FedEx e exibe detalhes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Usar as credenciais de teste fornecidas pelo usuário
        $apiUrl = "https://apis-sandbox.fedex.com";
        $clientId = "l7517499d73dc1470c8f56fe055c45113c";
        $clientSecret = "41d8172c88c345cca8f47695bc97a5cd";
        $shipperAccount = "740561073";

        $this->info("\n📌 AMBIENTE DE EXECUÇÃO");
        $this->line("• Ambiente: <fg=yellow>Teste (Sandbox)</>");
        $this->line("• API URL: <fg=yellow>$apiUrl</>");
        $this->line("• Client ID: <fg=yellow>$clientId</>");
        $this->line("• Shipper Account: <fg=yellow>$shipperAccount</>");

        // Obter parâmetros da linha de comando
        $origem = '01222001'; // Alterado para string para evitar problemas com zeros à esquerda
        $destino = '10001';
        $altura = 10;
        $largura = 20;
        $comprimento = 30;
        $peso = 5;
        $salvarLog = 'log';

        // Cálculo do peso cúbico
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);

        try {
            // 1. Autenticação - Fazer diretamente a requisição para obter o token
            $authUrl = $apiUrl . '/oauth/token';
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $authUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=" . $clientId . "&client_secret=" . $clientSecret,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/x-www-form-urlencoded",
                    "Accept: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                throw new \Exception('Erro na requisição de autenticação: ' . $err);
            }

            if ($httpCode != 200) {
                throw new \Exception('Falha na autenticação. Código HTTP: ' . $httpCode . ' - ' . $response);
            }

            $authData = json_decode($response, true);
            $token = $authData['access_token'] ?? null;

            if (!$token) {
                throw new \Exception('Token de acesso não encontrado na resposta');
            }

            $this->line("• Token obtido: <fg=blue>" . $token . "</>");
            $this->line("• Expira em: <fg=blue>" . ($authData['expires_in'] ?? 'N/A') . " segundos</>");

            // 2. Preparar requisição de cotação
            $this->info("\n📝 REQUISIÇÃO DE COTAÇÃO");

            // Preparar URL
            $rateUrl = $apiUrl . '/rate/v1/rates/quotes';
            $this->line("• Endpoint: <fg=magenta>$rateUrl</>");

            // Data atual em formato YYYY-MM-DD
            $shipDate = date('Y-m-d');
            $transactionId = uniqid('logiez_rate_test_');
            $this->line("• Transaction ID: <fg=magenta>$transactionId</>");
            $this->line("• Data de envio: <fg=magenta>$shipDate</>");

            // Construir o payload da requisição
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
                            'postalCode' => substr($origem, 0, 10),
                            'countryCode' => 'BR',
                            'residential' => false
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'postalCode' => substr($destino, 0, 10),
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
                                'countryOfManufacture' => 'BR',
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

            // 3. Headers da requisição
            $this->info("\n📋 HEADERS DA REQUISIÇÃO");
            $headers = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer " . $token,
                "X-locale: en_US",
                "x-customer-transaction-id: " . $transactionId
            ];

            foreach ($headers as $header) {
                $this->line("• <fg=cyan>$header</>");
            }

            // 4. Exibir payload formatado
            $this->info("\n📤 PAYLOAD DA REQUISIÇÃO (JSON)");
            $formattedPayload = json_encode($rateRequest, JSON_PRETTY_PRINT);
            $this->line("<fg=yellow>$formattedPayload</>");

            // 5. Fazer a requisição real
            $this->info("\n📨 EXECUTANDO REQUISIÇÃO");

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
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_VERBOSE => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HEADER => false
            ]);

            $rateResponse = curl_exec($rateCurl);
            $rateHttpCode = curl_getinfo($rateCurl, CURLINFO_HTTP_CODE);
            $rateErr = curl_error($rateCurl);

            curl_close($rateCurl);

            $this->line("• <fg=green>Requisição enviada para a API FedEx</>");
            $this->line("• HTTP Code: <fg=" . ($rateHttpCode == 200 ? 'green' : 'red') . ">$rateHttpCode</>");

            if ($rateErr) {
                throw new \Exception('Erro na requisição de cotação: ' . $rateErr);
            }

            // Tratamento específico para o código 503
            if ($rateHttpCode == 503) {
                $this->warn("\n⚠️ AVISO: API FedEx indisponível (503 Service Unavailable)");
                $this->line("O serviço da FedEx está temporariamente indisponível.");
                throw new \Exception('Serviço da FedEx indisponível (503)');
            }

            // Processar resposta
            $rateData = json_decode($rateResponse, true);

            //dump('-------------------------- TO AQUI --------------------------');
            //dd($rateResponse);

            // Registrar resposta para análise
            if ($salvarLog) {
                Log::info('Resposta completa da API FedEx', [
                    'httpCode' => $rateHttpCode,
                    'resposta' => $rateData
                ]);
            }

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
            
            // Formatar o valor com 2 casas decimais
            $valorFormatado = number_format($amount, 2, '.', '');
            
            // Extrair informações de entrega corretamente
            $deliveryInfo = '';
            $deliveryDate = 'N/A';
            
            if (isset($rateDetail['commit'])) {
                // Tratar mensagens de entrega
                if (isset($rateDetail['commit']['commitMessageDetails'])) {
                    $deliveryInfo = $rateDetail['commit']['commitMessageDetails'];
                } elseif (isset($rateDetail['commit']['deliveryMessages'][0])) {
                    $deliveryInfo = $rateDetail['commit']['deliveryMessages'][0];
                }
                
                // Tratar data de entrega
                if (isset($rateDetail['commit']['dateDetail']['dayFormat'])) {
                    $deliveryDate = $rateDetail['commit']['dateDetail']['dayFormat'];
                } elseif (isset($rateDetail['commit']['dateDetail']['date'])) {
                    $deliveryDate = $rateDetail['commit']['dateDetail']['date'];
                }
                
                // Se não houver data específica, usar o padrão da API
                if ($deliveryDate === 'N/A' && isset($rateDetail['commit']['derivedDeliveryDate'])) {
                    $deliveryDate = $rateDetail['commit']['derivedDeliveryDate'];
                }
            }
            
            // Limpar mensagem de entrega se for muito longa
            if (strlen($deliveryInfo) > 30) {
                $deliveryInfo = substr($deliveryInfo, 0, 27) . '...';
            }
            
            $cotacoes[] = [
                'servico' => $serviceName,
                'servicoTipo' => $serviceType,
                'valorTotal' => $valorFormatado,
                'moeda' => $currency,
                'tempoEntrega' => $deliveryInfo,
                'dataEntrega' => $deliveryDate
            ];
        }
    }
}


            $resultado = [
                'success' => $rateHttpCode == 200,
                'pesoCubico' => round($pesoCubico, 2),
                'pesoReal' => $peso,
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'cotacoesFedEx' => $cotacoes,
                'simulado' => false,
                'dataConsulta' => date('Y-m-d H:i:s'),
                'httpCode' => $rateHttpCode,
                'respostaOriginal' => $rateData
            ];

            // 6. Exibir resultado
            $this->info("\n📥 RESPOSTA RECEBIDA");
            $this->line("• HTTP Code: <fg=" . ($resultado['httpCode'] == 200 ? 'green' : 'red') . ">" . $resultado['httpCode'] . "</>");

            // 7. Exibir cotações retornadas
            if (isset($resultado['cotacoesFedEx']) && count($resultado['cotacoesFedEx']) > 0) {
                $this->info("\n💰 COTAÇÕES DISPONÍVEIS");

                $this->table(
                    ['Serviço', 'Tipo', 'Valor', 'Moeda', 'Tempo de Entrega', 'Data Estimada'],
                    array_map(function ($cotacao) {
                        return [
                            $cotacao['servico'],
                            $cotacao['servicoTipo'],
                            $cotacao['valorTotal'],
                            $cotacao['moeda'],
                            $cotacao['tempoEntrega'] ?? 'N/A',
                            $cotacao['dataEntrega'] ?? 'N/A'
                        ];
                    }, $resultado['cotacoesFedEx'])
                );
            } else {
                $this->line("<fg=red>Nenhuma cotação disponível</>");
                $this->line("<fg=yellow>Resposta completa:</>");
                $this->line(json_encode($rateData, JSON_PRETTY_PRINT));
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("\n❌ ERRO NO TESTE");
            $this->error("• Mensagem: " . $e->getMessage());

            Log::error('Erro ao testar API FedEx', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
