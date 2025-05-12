<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FedexService
{
    protected $clientId;
    protected $clientSecret;
    protected $apiUrl;
    protected $shipperAccount;
    // Variável estática para rastrear o código de rastreamento especial
    private static $trackingSpecialCode = null;
    
    public function __construct()
    {
        $this->apiUrl = config('services.fedex.api_url', "https://apis-sandbox.fedex.com");
        $this->clientId = config('services.fedex.client_id', "l7517499d73dc1470c8f56fe055c45113c");
        $this->clientSecret = config('services.fedex.client_secret', "41d8172c88c345cca8f47695bc97a5cd");
        $this->shipperAccount = config('services.fedex.shipper_account', "740561073");

        // Registrar ambiente em uso para diagnóstico
        Log::info('FedexService inicializado', [
            'ambiente' => config('services.fedex.use_production', false) ? 'Produção' : 'Homologação',
            'apiUrl' => $this->apiUrl
        ]);
    }
    
    /**
     * Obter token de autenticação da API FedEx
     * 
     * @param bool $forceRefresh Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    public function getAuthToken($forceRefresh = false) {
        if (!$forceRefresh && Cache::has('fedex_token')) {
            $token = Cache::get('fedex_token');
            
            // Se estiver processando um código especial, fazer log
            if (self::$trackingSpecialCode) {
                Log::info('======= TOKEN FEDEX DO CACHE =======', [
                    'Token' => substr($token, 0, 10) . '...' . substr($token, -10),
                    'Usado_Para' => 'Rastreamento do código ' . self::$trackingSpecialCode
                ]);
            }
            
            return $token;
        }
    
        $authUrl = $this->apiUrl . '/oauth/token';
        
        // Preparar payload para a solicitação de token
        $tokenPayload = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];
        
        // Se estiver processando um código especial, fazer log
        if (self::$trackingSpecialCode) {
            Log::info('======= SOLICITAÇÃO DE TOKEN FEDEX =======', [
                'URL' => $authUrl,
                'Payload' => $tokenPayload,
                'Client_ID' => $this->clientId,
                'Client_Secret' => substr($this->clientSecret, 0, 5) . '...' . substr($this->clientSecret, -5),
                'Usado_Para' => 'Rastreamento do código ' . self::$trackingSpecialCode
            ]);
        }
        
        $response = Http::asForm()->post($authUrl, $tokenPayload);
    
        if ($response->failed()) {
            throw new \Exception('Falha na autenticação: ' . $response->body());
        }
    
        $data = $response->json();
        $token = $data['access_token'] ?? null;
    
        if (!$token) {
            throw new \Exception('Token não recebido');
        }
        
        // Extrair tempo de expiração (geralmente 3600 segundos = 1 hora)
        $expiresIn = $data['expires_in'] ?? 3600;
        
        // Armazenar no cache por um pouco menos que o tempo de expiração
        $cacheMinutes = floor($expiresIn / 60) - 5; // 5 minutos de margem
        Cache::put('fedex_token', $token, now()->addMinutes($cacheMinutes));
        
        // Armazenar detalhes adicionais para diagnóstico
        Cache::put('fedex_token_details', [
            'expires_in' => $expiresIn,
            'obtained_at' => now()->toDateTimeString(),
            'expires_at' => now()->addSeconds($expiresIn)->toDateTimeString()
        ], now()->addMinutes($cacheMinutes));
        
        // Se estiver processando um código especial, fazer log
        if (self::$trackingSpecialCode) {
            Log::info('======= NOVO TOKEN FEDEX OBTIDO =======', [
                'Token' => substr($token, 0, 10) . '...' . substr($token, -10),
                'Expira_Em' => $expiresIn . ' segundos',
                'Usado_Para' => 'Rastreamento do código ' . self::$trackingSpecialCode,
                'Resposta_Completa' => $data
            ]);
        }
    
        return $token;
    }
    
    /**
     * Calcular cotação de frete
     * 
     * @param string|array $origem CEP ou array com dados de origem
     * @param string|array $destino CEP ou array com dados de destino
     * @param float $altura Altura em cm
     * @param float $largura Largura em cm
     * @param float $comprimento Comprimento em cm
     * @param float $peso Peso em kg
     * @param bool $forcarSimulacao Se true, força o uso da simulação em vez da API real
     * @return array
     */
    public function calcularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso, $forcarSimulacao = false)
    {
        // Se forçar simulação, usa o método de simulação
        if ($forcarSimulacao) {
            return $this->simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso);
        }
    
        try {
            // Obter token de autenticação
            $accessToken = $this->getAuthToken(true); // Forçar novo token
    
            // Cálculo do peso cúbico
            $pesoCubico = ($altura * $largura * $comprimento) / 5000;
            $pesoUtilizado = max($pesoCubico, $peso);
    
            // Preparar requisição de cotação
            $rateUrl = $this->apiUrl . '/rate/v1/rates/quotes';
            $transactionId = uniqid('logiez_rate_');
            $shipDate = date('Y-m-d');
    
            // Extrair códigos postais
            $postalCodeOrigem = is_array($origem) ? ($origem['postalCode'] ?? $origem[0] ?? '') : $origem;
            $postalCodeDestino = is_array($destino) ? ($destino['postalCode'] ?? $destino[0] ?? '') : $destino;
            $countryCodeOrigem = is_array($origem) ? ($origem['countryCode'] ?? 'BR') : 'BR';
            
            $countryCodeDestino = is_array($destino) ? ($destino['countryCode'] ?? 'US') : 'US';
    
            $rateRequest = [
                'accountNumber' => [
                    'value' => $this->shipperAccount
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
                                        'value' => $this->shipperAccount
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
            ]);
    
            $rateResponse = curl_exec($rateCurl);
            $rateHttpCode = curl_getinfo($rateCurl, CURLINFO_HTTP_CODE);
            $rateErr = curl_error($rateCurl);
            
            curl_close($rateCurl);
    
            if ($rateErr) {
                throw new \Exception('Erro na requisição de cotação: ' . $rateErr);
            }
    
            if ($rateHttpCode != 200) {
                throw new \Exception('Falha na cotação. Código HTTP: ' . $rateHttpCode);
            }
    
            $rateData = json_decode($rateResponse, true);
    
            // Extrair cotações da resposta - mesma lógica do command
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
    
            return [
                'success' => true,
                'pesoCubico' => round($pesoCubico, 2),
                'pesoReal' => $peso,
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'cotacoesFedEx' => $cotacoes,
                'simulado' => false,
                'dataConsulta' => date('Y-m-d H:i:s'),
                'respostaOriginal' => $rateData // Opcional - para debug
            ];
    
        } catch (\Exception $e) {
            Log::error('Erro ao calcular cotação FedEx', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            // Em caso de erro, retorna simulação
            $resultado = $this->simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso);
            $resultado['mensagem'] = 'Cotação simulada devido a erro na API: ' . $e->getMessage();
            return $resultado;
        }
    }

    /**
     * Simula uma cotação de envio FedEx
     * 
     * @param string|array $origem CEP de origem ou array com ['postalCode' => '...', 'countryCode' => '...']
     * @param string|array $destino CEP de destino ou array com ['postalCode' => '...', 'countryCode' => '...']
     * @param float $altura Altura da embalagem em cm
     * @param float $largura Largura da embalagem em cm
     * @param float $comprimento Comprimento da embalagem em cm
     * @param float $peso Peso em kg
     * @return array
     */
    public function simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso)
    {
        // Cálculo do peso cúbico (peso volumétrico)
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);
        
        // Extrair códigos postais
        $postalCodeOrigem = is_array($origem) ? ($origem['postalCode'] ?? $origem[0] ?? '') : $origem;
        $postalCodeDestino = is_array($destino) ? ($destino['postalCode'] ?? $destino[0] ?? '') : $destino;
        $countryCodeOrigem = is_array($origem) ? ($origem['countryCode'] ?? 'BR') : 'BR';
        $countryCodeDestino = is_array($destino) ? ($destino['countryCode'] ?? 'US') : 'US';
        
        // Obter a cotação atual do dólar
        $cotacaoDolar = $this->obterCotacaoDolar();
        $valorDolar = $cotacaoDolar['cotacao'] ?? 5.71; // Valor padrão caso a API falhe
        
        // Fatores de ajuste de preço e prazo com base no país de destino
        $fatorPais = 1.0; // Fator padrão para Estados Unidos
        $prazoExtra = 0;  // Dias extras para entrega
        
        // Ajustar fator de país e prazo adicional com base no país de destino
        if ($countryCodeOrigem != $countryCodeDestino) {
            // Europa
            if (in_array($countryCodeDestino, ['GB', 'DE', 'FR', 'IT', 'ES', 'PT'])) {
                $fatorPais = 1.2; // Europa é mais cara que EUA
                $prazoExtra = 1;  // +1 dia para Europa
            }
            // Ásia
            else if (in_array($countryCodeDestino, ['CN', 'JP', 'KR', 'IN', 'SG'])) {
                $fatorPais = 1.4; // Ásia é mais cara que Europa
                $prazoExtra = 2;  // +2 dias para Ásia
            }
            // América Latina
            else if (in_array($countryCodeDestino, ['MX', 'AR', 'CL', 'CO', 'PE'])) {
                $fatorPais = 0.9; // América Latina é mais barata que EUA
                $prazoExtra = 1;  // +1 dia para América Latina
            }
            // Austrália/Oceania
            else if (in_array($countryCodeDestino, ['AU', 'NZ'])) {
                $fatorPais = 1.5; // Austrália é mais cara
                $prazoExtra = 3;  // +3 dias para Oceania
            }
        } else {
            // Envio doméstico (mesmo país) - mais barato
            $fatorPais = 0.6;
            $prazoExtra = -2; // -2 dias para doméstico
        }
        
        // Cotações simuladas realistas
        $cotacoes = [
            [
                'servico' => 'FedEx International Priority',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY',
                'valorTotal' => number_format((130 + ($pesoUtilizado * 15)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (3 + $prazoExtra) . '-' . (5 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (4 + $prazoExtra) . ' days'))
            ],
            [
                'servico' => 'FedEx International Economy',
                'servicoTipo' => 'INTERNATIONAL_ECONOMY',
                'valorTotal' => number_format((100 + ($pesoUtilizado * 12)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (5 + $prazoExtra) . '-' . (7 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (6 + $prazoExtra) . ' days'))
            ]
        ];
        
        // Adicionar FedEx International First para destinos que não sejam América Latina
        if (!in_array($countryCodeDestino, ['MX', 'AR', 'CL', 'CO', 'PE', 'BR'])) {
            $cotacoes[] = [
                'servico' => 'FedEx International First',
                'servicoTipo' => 'INTERNATIONAL_FIRST',
                'valorTotal' => number_format((180 + ($pesoUtilizado * 22)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (1 + $prazoExtra) . '-' . (3 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (2 + $prazoExtra) . ' days'))
            ];
        }
        
        // Adicionar valor promocional se o peso for baixo (menos de 5kg)
        if ($pesoUtilizado < 5) {
            $cotacoes[] = [
                'servico' => 'FedEx International Priority (Promocional)',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY_PROMO',
                'valorTotal' => number_format((100 + ($pesoUtilizado * 12)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (3 + $prazoExtra) . '-' . (5 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (4 + $prazoExtra) . ' days'))
            ];
        }
        
        // Adicionar opção expressa para envios urgentes (peso acima de 20kg)
        if ($pesoUtilizado > 20) {
            $cotacoes[] = [
                'servico' => 'FedEx International Priority Direct',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY_EXPRESS',
                'valorTotal' => number_format((250 + ($pesoUtilizado * 25)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (1 + $prazoExtra) . '-' . (2 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (1 + $prazoExtra) . ' days'))
            ];
        }
        
        // Adicionar opção econômica para envios grandes não urgentes
        if ($pesoUtilizado > 30) {
            $cotacoes[] = [
                'servico' => 'FedEx International Economy Freight',
                'servicoTipo' => 'INTERNATIONAL_ECONOMY_FREIGHT',
                'valorTotal' => number_format((80 + ($pesoUtilizado * 8)) * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (7 + $prazoExtra) . '-' . (10 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (8 + $prazoExtra) . ' days'))
            ];
        }
        
        // Verificação de segurança: se não retornou nenhuma cotação, adicione uma opção padrão
        if (empty($cotacoes)) {
            $cotacoes[] = [
                'servico' => 'FedEx International Priority (Padrão)',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY',
                'valorTotal' => number_format(150 * $fatorPais, 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => (3 + $prazoExtra) . '-' . (5 + $prazoExtra) . ' dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+' . (4 + $prazoExtra) . ' days'))
            ];
        }
        
        // Converter valores USD para BRL
        foreach ($cotacoes as $key => $cotacao) {
            $valorUSD = floatval(str_replace(',', '', $cotacao['valorTotal']));
            $valorBRL = $valorUSD * $valorDolar;
            $cotacoes[$key]['valorTotalBRL'] = number_format($valorBRL, 2, ',', '.');
        }
        
        // Log dos resultados da cotação
        Log::info('Resultado da simulação de cotação', [
            'cotacoes_count' => count($cotacoes),
            'cotacoes' => $cotacoes,
            'cotacao_dolar' => $valorDolar
        ]);
        
        // Adicionar informações de simulação
        return [
            'success' => true,
            'pesoCubico' => round($pesoCubico, 2),
            'pesoReal' => $peso,
            'pesoUtilizado' => round($pesoUtilizado, 2),
            'cotacoesFedEx' => $cotacoes,
            'dataConsulta' => date('Y-m-d H:i:s'),
            'simulado' => true, // Indicar que é uma simulação
            'mensagem' => 'Cotação simulada devido a acesso limitado à API FedEx. Valores aproximados.',
            'cotacaoDolar' => $valorDolar // Adiciona a cotação do dólar à resposta
        ];
    }

    /**
     * Obtém a cotação atual do dólar usando a API AwesomeAPI
     * 
     * @return array Array com informações da cotação
     */
    private function obterCotacaoDolar()
    {
        try {
            // URL da API AwesomeAPI para cotações
            $response = Http::get("https://economia.awesomeapi.com.br/json/daily/USD-BRL/1");
            
            if ($response->successful()) {
                $cotacao = $response->json();
                
                if (!empty($cotacao) && isset($cotacao[0])) {
                    $dadosCotacao = $cotacao[0];
                    
                    return [
                        'success' => true,
                        'data' => date('d/m/Y', strtotime($dadosCotacao['create_date'])),
                        'cotacao' => floatval($dadosCotacao['ask'])
                    ];
                }
            }
            
            // Valor padrão em caso de falha
            Log::warning('Falha ao obter cotação do dólar. Usando valor padrão.');
            return [
                'success' => false,
                'data' => date('d/m/Y'),
                'cotacao' => 5.71
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao consultar cotação do dólar: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => date('d/m/Y'),
                'cotacao' => 5.71
            ];
        }
    }

    /**
     * Rastreia um número de rastreamento FedEx
     * 
     * @param string $trackingNumber Número de rastreamento a ser consultado
     * @param bool $includeDetailedScans Se true, inclui detalhes completos de todos os eventos
     * @param bool $forcarSimulacao Se true, força o uso da simulação em vez da API real
     * @return array
     * @throws \Exception em caso de erro na API
     */
    public function rastrearEnvio($trackingNumber, $includeDetailedScans = true, $forcarSimulacao = false)
    {
        // Definir a variável estática para o token de autenticação saber que estamos processando um código especial
        self::$trackingSpecialCode = $trackingNumber === '794616896420' ? $trackingNumber : null;
        
        // Verificar se é um código de rastreio especial que precisa de credenciais específicas
        $specialTrackingConfig = config('services.fedex.special_tracking.' . $trackingNumber);
        if (!empty($specialTrackingConfig)) {
            // Substituição para código específico - use as credenciais de configuração
            $this->clientId = $specialTrackingConfig['client_id'];
            $this->clientSecret = $specialTrackingConfig['client_secret'];
            $this->apiUrl = $specialTrackingConfig['api_url'];
            
            Log::info('======= RASTREAMENTO FEDEX - CÓDIGO ESPECIAL =======', [
                'Data/Hora' => now()->format('Y-m-d H:i:s'),
                'Tracking Number' => $trackingNumber,
                'Client_ID' => $this->clientId,
                'Client_Secret' => substr($this->clientSecret, 0, 5) . '...' . substr($this->clientSecret, -5),
                'API_URL' => $this->apiUrl,
                'Shipper_Account' => $this->shipperAccount,
                'Ambiente' => "Teste específico para o código " . $trackingNumber
            ]);
        }

        // Se forçar simulação, usa o método de simulação
        if ($forcarSimulacao) {
            return $this->simularRastreamento($trackingNumber);
        }
    
        try {
            // Obter token de autenticação - forçar renovação para códigos especiais
            $accessToken = $this->getAuthToken(!empty($specialTrackingConfig));
    
            // Preparar requisição de rastreamento
            $trackUrl = $this->apiUrl . '/track/v1/trackingnumbers';
            $transactionId = uniqid('logiez_track_');
    
            $trackRequest = [
                'includeDetailedScans' => $includeDetailedScans,
                'trackingInfo' => [
                    [
                        'trackingNumberInfo' => [
                            'trackingNumber' => $trackingNumber
                        ]
                    ]
                ]
            ];
            
            // Log especial para o payload se for o código específico
            if (self::$trackingSpecialCode) {
                Log::info('======= PAYLOAD DE REQUISIÇÃO FEDEX TRACKING =======', [
                    'URL' => $trackUrl,
                    'Transaction_ID' => $transactionId,
                    'Payload' => json_encode($trackRequest, JSON_PRETTY_PRINT),
                    'Headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . substr($accessToken, 0, 10) . '...',
                        'X-locale' => 'pt_BR',
                        'x-customer-transaction-id' => $transactionId
                    ]
                ]);
            }
    
            // Fazer a requisição
            $trackCurl = curl_init();
            curl_setopt_array($trackCurl, [
                CURLOPT_URL => $trackUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($trackRequest),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $accessToken,
                    "X-locale: pt_BR",
                    "x-customer-transaction-id: " . $transactionId
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
    
            $trackResponse = curl_exec($trackCurl);
            $trackHttpCode = curl_getinfo($trackCurl, CURLINFO_HTTP_CODE);
            $trackErr = curl_error($trackCurl);
            
            curl_close($trackCurl);
    
            if (self::$trackingSpecialCode) {
                Log::info('======= RESPOSTA DA REQUISIÇÃO FEDEX TRACKING =======', [
                    'HTTP_Code' => $trackHttpCode,
                    'Response' => $trackResponse ? substr($trackResponse, 0, 1000) . '...' : 'Vazia',
                    'Erro' => $trackErr ?: 'Nenhum'
                ]);
            }

            if ($trackErr) {
                throw new \Exception('Erro na requisição de rastreamento: ' . $trackErr);
            }
    
            if ($trackHttpCode != 200) {
                throw new \Exception('Falha no rastreamento. Código HTTP: ' . $trackHttpCode);
            }
    
            $trackData = json_decode($trackResponse, true);
            
            // Para código especial 794616896420, verificar se temos resposta virtual
            $isVirtualResponse = false;
            if ($trackingNumber === '794616896420' && isset($trackData['output']['alerts'])) {
                foreach ($trackData['output']['alerts'] as $alert) {
                    if (isset($alert['code']) && $alert['code'] === 'VIRTUAL.RESPONSE') {
                        $isVirtualResponse = true;
                        Log::info('======= RESPOSTA VIRTUAL DETECTADA NA API FEDEX =======', [
                            'Tracking_Number' => $trackingNumber,
                            'Alert' => $alert
                        ]);
                        // Continuar normalmente pois existem dados de rastreamento válidos
                    }
                }
            }
    
            // Processar os dados de rastreamento
            $result = $this->processarDadosRastreamento($trackData, $trackingNumber);
            $result['simulado'] = false;
            $result['respostaOriginal'] = $trackData; // Opcional - para debug
            
            // Se foi detectada uma resposta virtual e o processamento falhou, ativar simulação
            if ($isVirtualResponse && !$result['success']) {
                Log::info('======= ATIVANDO SIMULAÇÃO PARA RESPOSTA VIRTUAL =======', [
                    'Tracking_Number' => $trackingNumber
                ]);
                return $this->simularRastreamento($trackingNumber);
            }
            
            return $result;
    
        } catch (\Exception $e) {
            // Registrar o erro no log
            Log::error('Erro ao rastrear envio FedEx', [
                'error' => $e->getMessage(),
                'trackingNumber' => $trackingNumber,
                'trace' => $e->getTraceAsString()
            ]);
    
            // Propagar a exceção para ser tratada pelo controller
            throw $e;
        }
    }

    /**
     * Processa os dados de resposta da API de rastreamento
     * 
     * @param array $trackData Dados brutos da resposta da API
     * @param string $trackingNumber Número de rastreamento consultado
     * @return array
     */
    private function processarDadosRastreamento($trackData, $trackingNumber)
    {
        $resultado = [
            'success' => true,
            'trackingNumber' => $trackingNumber,
            'eventos' => [],
            'statusAtual' => '',
            'ultimaAtualizacao' => '',
            'origem' => '',
            'destino' => '',
            'dataPostagem' => '',
            'dataEntregaPrevista' => '',
            'servicoDescricao' => '',
            'temAtraso' => false,
            'detalhesAtraso' => '',
            'entregue' => false,
            'dataEntrega' => ''
        ];

        // Se for um código especial, fazer log dos dados recebidos para processamento
        if (self::$trackingSpecialCode) {
            Log::info('======= PROCESSANDO DADOS DE RASTREAMENTO FEDEX =======', [
                'Tracking_Number' => $trackingNumber,
                'Dados_API' => json_encode($trackData, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR)
            ]);
        }

        // Verificar se temos uma resposta virtual
        $isVirtualResponse = false;
        if (!empty($trackData['output']['alerts'])) {
            foreach ($trackData['output']['alerts'] as $alert) {
                if (isset($alert['code']) && $alert['code'] === 'VIRTUAL.RESPONSE') {
                    $isVirtualResponse = true;
                    // É uma resposta virtual, não consideramos como falha
                    Log::info('======= RESPOSTA VIRTUAL DETECTADA =======', [
                        'Tracking_Number' => $trackingNumber,
                        'alert' => $alert
                    ]);
                    // Continue processando normalmente, pois há dados válidos
                }
            }
        }
        
        // Se houver alertas na resposta e não for uma resposta virtual, consideramos falha
        if (!empty($trackData['output']['alerts']) && !$isVirtualResponse) {
            $resultado['success'] = false;
            $resultado['mensagem'] = $trackData['output']['alerts'];
            return $resultado;
        }

        // Verificar se há resultados de rastreamento
        if (!isset($trackData['output']['completeTrackResults'][0]['trackResults'][0])) {
            $resultado['success'] = false;
            $resultado['mensagem'] = 'Nenhum dado de rastreamento encontrado';
            return $resultado;
        }

        $trackResult = $trackData['output']['completeTrackResults'][0]['trackResults'][0];

        // Extrair dados básicos do envio
        if (isset($trackResult['serviceDetail'])) {
            $resultado['servicoDescricao'] = $trackResult['serviceDetail']['description'] ?? 'Serviço FedEx';
        }

        // Verificar se o pacote foi entregue
        if (isset($trackResult['latestStatusDetail']['code']) && $trackResult['latestStatusDetail']['code'] === 'DL') {
            $resultado['entregue'] = true;
            if (isset($trackResult['latestStatusDetail']['statusByLocale'])) {
                $resultado['statusAtual'] = $trackResult['latestStatusDetail']['statusByLocale'];
            }
            if (isset($trackResult['deliveryDetails']['deliveryDate'])) {
                $resultado['dataEntrega'] = $trackResult['deliveryDetails']['deliveryDate'];
                $resultado['ultimaAtualizacao'] = $trackResult['deliveryDetails']['deliveryDate'];
            }
        } else {
            // Status atual se não entregue
            if (isset($trackResult['latestStatusDetail']['statusByLocale'])) {
                $resultado['statusAtual'] = $trackResult['latestStatusDetail']['statusByLocale'];
            } elseif (isset($trackResult['latestStatusDetail']['description'])) {
                $resultado['statusAtual'] = $trackResult['latestStatusDetail']['description'];
            }
            
            if (isset($trackResult['latestStatusDetail']['scanDate'])) {
                $resultado['ultimaAtualizacao'] = $trackResult['latestStatusDetail']['scanDate'];
            }
        }

        // Dados de origem e destino
        if (isset($trackResult['shipperInformation']['address'])) {
            $shipper = $trackResult['shipperInformation']['address'];
            $result_origem = [];
            if (isset($shipper['city'])) $result_origem[] = $shipper['city'];
            if (isset($shipper['stateOrProvinceCode'])) $result_origem[] = $shipper['stateOrProvinceCode'];
            if (isset($shipper['countryName'])) $result_origem[] = $shipper['countryName'];
            $resultado['origem'] = implode(', ', $result_origem);
        }

        if (isset($trackResult['recipientInformation']['address'])) {
            $recipient = $trackResult['recipientInformation']['address'];
            $result_destino = [];
            if (isset($recipient['city'])) $result_destino[] = $recipient['city'];
            if (isset($recipient['stateOrProvinceCode'])) $result_destino[] = $recipient['stateOrProvinceCode'];
            if (isset($recipient['countryName'])) $result_destino[] = $recipient['countryName'];
            $resultado['destino'] = implode(', ', $result_destino);
        }

        // Data de postagem e previsão de entrega
        if (isset($trackResult['shipDates']['shipDate'])) {
            $resultado['dataPostagem'] = $trackResult['shipDates']['shipDate'];
        }

        if (isset($trackResult['dateAndTimes'])) {
            foreach ($trackResult['dateAndTimes'] as $dateDetail) {
                if ($dateDetail['type'] === 'ESTIMATED_DELIVERY') {
                    $resultado['dataEntregaPrevista'] = $dateDetail['dateTime'];
                    break;
                }
            }
        }

        // Verificar se há atraso
        if (isset($trackResult['scanEvents'][0]['delayDetail']['status']) && $trackResult['scanEvents'][0]['delayDetail']['status'] === 'DELAYED') {
            $resultado['temAtraso'] = true;
            if (isset($trackResult['scanEvents'][0]['delayDetail']['type'])) {
                $resultado['detalhesAtraso'] = $trackResult['scanEvents'][0]['delayDetail']['type'];
                if (isset($trackResult['scanEvents'][0]['delayDetail']['subType'])) {
                    $resultado['detalhesAtraso'] .= ' - ' . $trackResult['scanEvents'][0]['delayDetail']['subType'];
                }
            }
        }

        // Processar eventos de rastreamento
        if (isset($trackResult['scanEvents'])) {
            foreach ($trackResult['scanEvents'] as $event) {
                $eventoRastreamento = [
                    'data' => $event['date'] ?? '',
                    'hora' => isset($event['time']) ? $event['time'] : '',
                    'status' => $event['eventDescription'] ?? '',
                    'descricao' => $event['scanDetails'] ?? '',
                    'codigo' => $event['eventType'] ?? '',
                    'local' => ''
                ];

                // Formatar local do evento
                if (isset($event['scanLocation'])) {
                    $location = $event['scanLocation'];
                    $local_parts = [];
                    if (isset($location['city'])) $local_parts[] = $location['city'];
                    if (isset($location['stateOrProvinceCode'])) $local_parts[] = $location['stateOrProvinceCode'];
                    if (isset($location['countryName'])) $local_parts[] = $location['countryName'];
                    
                    $eventoRastreamento['local'] = implode(', ', $local_parts);
                }

                $resultado['eventos'][] = $eventoRastreamento;
            }
        }

        return $resultado;
    }

    /**
     * Simula rastreamento de envio (usado quando há problemas com a API)
     * 
     * @param string $trackingNumber Número de rastreamento
     * @return array
     */
    public function simularRastreamento($trackingNumber)
    {
        // Definir data de envio simulada (entre 1 e 15 dias atrás)
        $diasEnvio = rand(1, 15);
        $dataEnvio = date('Y-m-d', strtotime("-{$diasEnvio} days"));
        $horaEnvio = sprintf('%02d:%02d:%02d', rand(8, 19), rand(0, 59), rand(0, 59));
        
        // Definir local de origem e destino simulados
        $origensSimuladas = [
            ['cidade' => 'São Paulo', 'estado' => 'SP', 'pais' => 'Brasil'],
            ['cidade' => 'Rio de Janeiro', 'estado' => 'RJ', 'pais' => 'Brasil'],
            ['cidade' => 'Belo Horizonte', 'estado' => 'MG', 'pais' => 'Brasil'],
            ['cidade' => 'Curitiba', 'estado' => 'PR', 'pais' => 'Brasil'],
            ['cidade' => 'Porto Alegre', 'estado' => 'RS', 'pais' => 'Brasil']
        ];
        
        $destinosSimulados = [
            ['cidade' => 'Miami', 'estado' => 'FL', 'pais' => 'Estados Unidos'],
            ['cidade' => 'Nova York', 'estado' => 'NY', 'pais' => 'Estados Unidos'],
            ['cidade' => 'Los Angeles', 'estado' => 'CA', 'pais' => 'Estados Unidos'],
            ['cidade' => 'Londres', 'estado' => '', 'pais' => 'Reino Unido'],
            ['cidade' => 'Paris', 'estado' => '', 'pais' => 'França'],
            ['cidade' => 'Tóquio', 'estado' => '', 'pais' => 'Japão']
        ];
        
        // Selecionar origem e destino aleatórios
        $origem = $origensSimuladas[array_rand($origensSimuladas)];
        $destino = $destinosSimulados[array_rand($destinosSimulados)];
        
        $origemStr = $origem['cidade'] . ', ' . $origem['estado'] . ', ' . $origem['pais'];
        $destinoStr = $destino['cidade'] . ', ' . $destino['estado'] . ', ' . $destino['pais'];
        
        // Criar simulação de eventos com base no número do dia atual
        $day = date('d');
        $eventosSemEntrega = ($day % 7 === 0); // A cada 7 dias simula um envio sem entrega
        $temAtraso = ($day % 5 === 0); // A cada 5 dias simula um envio com atraso
        
        // Determinar quantos eventos de rastreamento serão simulados
        $totalEventos = $eventosSemEntrega ? rand(2, 5) : rand(5, 9);
        
        // Gerar eventos simulados
        $eventos = [];
        $statusAtual = '';
        $ultimaAtualizacao = '';
        $dataEntrega = null;
        $dataEntregaPrevista = date('Y-m-d', strtotime($dataEnvio . " +5 days"));
        
        // Evento inicial: Registrado/Coletado
        $eventos[] = [
            'data' => $dataEnvio,
            'hora' => $horaEnvio,
            'status' => 'Envio registrado',
            'descricao' => 'Pacote recebido pela FedEx',
            'codigo' => 'PU',
            'local' => $origemStr
        ];
        
        // Adicionar eventos intermediários
        $diasDecorridos = 1;
        for ($i = 1; $i < $totalEventos - 1; $i++) {
            $dataEvento = date('Y-m-d', strtotime($dataEnvio . " +{$diasDecorridos} days"));
            $horaEvento = sprintf('%02d:%02d:%02d', rand(0, 23), rand(0, 59), rand(0, 59));
            
            // O local depende do estágio do envio
            $progressao = $i / ($totalEventos - 1);
            $localEvento = $progressao < 0.5 ? $origemStr : $destinoStr;
            
            // Determinar o tipo de evento com base na progressão
            $tipoEvento = '';
            $descricaoEvento = '';
            
            if ($progressao < 0.3) {
                $tipoEvento = 'Em processamento';
                $descricaoEvento = 'Pacote em processamento no centro de distribuição';
            } else if ($progressao < 0.5) {
                $tipoEvento = 'Em trânsito';
                $descricaoEvento = 'Pacote saiu do centro de distribuição';
            } else if ($progressao < 0.7) {
                $tipoEvento = 'Em trânsito internacional';
                $descricaoEvento = 'Pacote em trânsito para o país de destino';
            } else if ($progressao < 0.9) {
                $tipoEvento = 'Chegada ao destino';
                $descricaoEvento = 'Pacote chegou ao país de destino';
            } else {
                $tipoEvento = 'Em rota de entrega';
                $descricaoEvento = 'Pacote saiu para entrega ao destinatário';
            }
            
            // Se tiver atraso e estiver no meio do processo, adicionar evento de atraso
            if ($temAtraso && $progressao > 0.4 && $progressao < 0.6) {
                $tipoEvento = 'Atraso identificado';
                $descricaoEvento = 'Há um atraso no processamento do pacote';
            }
            
            $eventos[] = [
                'data' => $dataEvento,
                'hora' => $horaEvento,
                'status' => $tipoEvento,
                'descricao' => $descricaoEvento,
                'codigo' => 'XX',
                'local' => $localEvento
            ];
            
            $ultimaAtualizacao = $dataEvento . ' ' . $horaEvento;
            $statusAtual = $tipoEvento;
            
            $diasDecorridos += rand(1, 2);
        }
        
        // Adicionar evento final
        $entregue = !$eventosSemEntrega;
        if ($entregue) {
            $dataEntrega = date('Y-m-d', strtotime($dataEnvio . " +{$diasDecorridos} days"));
            $horaEntrega = sprintf('%02d:%02d:%02d', rand(9, 19), rand(0, 59), rand(0, 59));
            
            $eventos[] = [
                'data' => $dataEntrega,
                'hora' => $horaEntrega,
                'status' => 'Entregue',
                'descricao' => 'Pacote entregue ao destinatário',
                'codigo' => 'DL',
                'local' => $destinoStr
            ];
            
            $ultimaAtualizacao = $dataEntrega . ' ' . $horaEntrega;
            $statusAtual = 'Entregue';
        } else {
            // Se não foi entregue, o último evento é baseado no dia do mês
            if ($day % 3 === 0) {
                $statusAtual = 'Liberação alfandegária pendente';
            } else if ($day % 3 === 1) {
                $statusAtual = 'Em trânsito';
            } else {
                $statusAtual = 'Aguardando retirada';
            }
        }
        
        // Organizar eventos por data/hora (mais recentes primeiro)
        usort($eventos, function($a, $b) {
            $dateA = strtotime($a['data'] . ' ' . $a['hora']);
            $dateB = strtotime($b['data'] . ' ' . $b['hora']);
            return $dateB - $dateA;
        });
        
        return [
            'success' => true,
            'trackingNumber' => $trackingNumber,
            'eventos' => $eventos,
            'statusAtual' => $statusAtual,
            'ultimaAtualizacao' => $ultimaAtualizacao,
            'origem' => $origemStr,
            'destino' => $destinoStr,
            'dataPostagem' => $dataEnvio,
            'dataEntregaPrevista' => $dataEntregaPrevista,
            'servicoDescricao' => 'FedEx International Priority',
            'temAtraso' => $temAtraso,
            'detalhesAtraso' => $temAtraso ? 'Atraso devido a condições climáticas' : '',
            'entregue' => $entregue,
            'dataEntrega' => $dataEntrega,
            'simulado' => true,
            'mensagem' => 'Rastreamento simulado para demonstração'
        ];
    }

    /**
     * Solicita comprovante de entrega assinado (SPOD)
     * 
     * @param string $trackingNumber Número de rastreamento
     * @param string $format Formato do documento (PDF ou PNG)
     * @return array|null Retorna array com o documento codificado em base64 ou null em caso de erro
     */
    public function solicitarComprovanteEntrega($trackingNumber, $format = 'PDF')
    {
        try {
            // Obter token de autenticação
            $accessToken = $this->getAuthToken();
    
            // Preparar requisição do documento
            $documentUrl = $this->apiUrl . '/track/v1/trackingdocuments';
            $transactionId = uniqid('logiez_spod_');
    
            $documentRequest = [
                'trackDocumentDetail' => [
                    'documentType' => 'SIGNATURE_PROOF_OF_DELIVERY',
                    'documentFormat' => strtoupper($format) // PDF ou PNG
                ],
                'trackDocumentSpecification' => [
                    [
                        'trackingNumberInfo' => [
                            'trackingNumber' => $trackingNumber
                        ]
                    ]
                ]
            ];
    
            // Fazer a requisição
            $documentCurl = curl_init();
            curl_setopt_array($documentCurl, [
                CURLOPT_URL => $documentUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($documentRequest),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $accessToken,
                    "X-locale: pt_BR",
                    "x-customer-transaction-id: " . $transactionId
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
    
            $documentResponse = curl_exec($documentCurl);
            $documentHttpCode = curl_getinfo($documentCurl, CURLINFO_HTTP_CODE);
            $documentErr = curl_error($documentCurl);
            
            curl_close($documentCurl);
    
            if ($documentErr) {
                throw new \Exception('Erro na requisição do comprovante de entrega: ' . $documentErr);
            }
    
            if ($documentHttpCode != 200) {
                throw new \Exception('Falha ao obter comprovante. Código HTTP: ' . $documentHttpCode);
            }
    
            $documentData = json_decode($documentResponse, true);
    
            // Verificar se tem documento na resposta
            if (!isset($documentData['output']['document'])) {
                throw new \Exception('Documento de comprovante de entrega não disponível');
            }
    
            return [
                'success' => true,
                'trackingNumber' => $trackingNumber,
                'documentType' => $documentData['output']['documentType'] ?? 'SIGNATURE_PROOF_OF_DELIVERY',
                'documentFormat' => $documentData['output']['documentFormat'] ?? strtoupper($format),
                'document' => $documentData['output']['document'],
                'simulado' => false
            ];
    
        } catch (\Exception $e) {
            Log::error('Erro ao solicitar comprovante de entrega FedEx', [
                'error' => $e->getMessage(),
                'trackingNumber' => $trackingNumber,
                'trace' => $e->getTraceAsString()
            ]);
    
            // Em caso de erro, informa o erro e retorna nulo
            return [
                'success' => false,
                'trackingNumber' => $trackingNumber,
                'mensagem' => 'Erro ao solicitar comprovante de entrega: ' . $e->getMessage(),
                'simulado' => false
            ];
        }
    }

    /**
     * Cria um envio na FedEx e retorna os dados da remessa
     * 
     * @param array $dadosRemetente Dados do remetente
     * @param array $dadosDestinatario Dados do destinatário
     * @param array $dadosPacote Dados do pacote (dimensões, peso, etc)
     * @param array $dadosProdutos Dados dos produtos para alfândega
     * @param string $servicoEntrega Código do serviço de entrega (ex: FEDEX_INTERNATIONAL_PRIORITY)
     * @param bool $forcarSimulacao Se true, força o uso da simulação em vez da API real
     * @return array
     */
    public function criarEnvio($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $servicoEntrega = 'FEDEX_INTERNATIONAL_PRIORITY', $forcarSimulacao = false)
    {
        try {
            // Log de dados que estão sendo enviados
            Log::info('Dados de envio FedEx', [
                'remetente' => $dadosRemetente,
                'destinatario' => $dadosDestinatario,
                'pacote' => $dadosPacote,
                'produtos' => $dadosProdutos,
                'servico' => $servicoEntrega
            ]);
            
            // Obter token de autenticação
            $accessToken = $this->getAuthToken();
            
            // Preparar requisição de criação de envio
            $shipUrl = $this->apiUrl . '/ship/v1/shipments';
            $transactionId = uniqid('logiez_ship_');
            $shipDate = date('Y-m-d');
            
            // Construir conteúdo da requisição
            $shipRequest = [
                'requestedShipment' => [
                    'shipper' => [
                        'contact' => [
                            'personName' => $dadosRemetente['nome'],
                            'phoneNumber' => $dadosRemetente['telefone'],
                            'emailAddress' => $dadosRemetente['email']
                        ],
                        'address' => [
                            'streetLines' => [
                                $dadosRemetente['endereco'],
                                $dadosRemetente['complemento'] ?? ''
                            ],
                            'city' => $dadosRemetente['cidade'],
                            'stateOrProvinceCode' => $dadosRemetente['estado'],
                            'postalCode' => $dadosRemetente['cep'],
                            'countryCode' => $dadosRemetente['pais'],
                            'residential' => false
                        ]
                    ],
                    'recipients' => [
                        [
                            'contact' => [
                                'personName' => $dadosDestinatario['nome'],
                                'phoneNumber' => $dadosDestinatario['telefone'],
                                'emailAddress' => $dadosDestinatario['email']
                            ],
                            'address' => [
                                'streetLines' => [
                                    $dadosDestinatario['endereco'],
                                    $dadosDestinatario['complemento'] ?? ''
                                ],
                                'city' => $dadosDestinatario['cidade'],
                                'stateOrProvinceCode' => $dadosDestinatario['estado'],
                                'postalCode' => $dadosDestinatario['cep'],
                                'countryCode' => $dadosDestinatario['pais'],
                                'residential' => false
                            ]
                        ]
                    ],
                    'shipDatestamp' => $shipDate,
                    'serviceType' => $servicoEntrega,
                    'packagingType' => 'YOUR_PACKAGING',
                    'pickupType' => 'USE_SCHEDULED_PICKUP',
                    'blockInsightVisibility' => false,
                    'shippingChargesPayment' => [
                        'paymentType' => 'SENDER',
                        'payor' => [
                            'responsibleParty' => [
                                'accountNumber' => [
                                    'value' => $this->shipperAccount
                                ]
                            ]
                        ]
                    ],
                    'labelSpecification' => [
                        'labelFormatType' => 'COMMON2D',
                        'imageType' => 'PDF',
                        'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL'
                    ],
                    'rateRequestType' => ['ACCOUNT', 'LIST'],
                    'preferredCurrency' => 'USD',
                    'totalPackageCount' => 1,
                    'requestedPackageLineItems' => [
                        [
                            'weight' => [
                                'units' => 'KG',
                                'value' => $dadosPacote['peso']
                            ],
                            'dimensions' => [
                                'length' => $dadosPacote['comprimento'],
                                'width' => $dadosPacote['largura'],
                                'height' => $dadosPacote['altura'],
                                'units' => 'CM'
                            ],
                            'groupPackageCount' => 1
                        ]
                    ],
                    'customsClearanceDetail' => [
                        'dutiesPayment' => [
                            'paymentType' => 'SENDER',
                            'payor' => [
                                'responsibleParty' => [
                                    'accountNumber' => [
                                        'value' => $this->shipperAccount
                                    ]
                                ]
                            ]
                        ],
                        'commodities' => []
                    ]
                ],
                'accountNumber' => [
                    'value' => $this->shipperAccount
                ],
                'labelResponseOptions' => 'URL_ONLY'
            ];
            
            // Adicionar produtos para alfândega
            foreach ($dadosProdutos as $produto) {
                $shipRequest['requestedShipment']['customsClearanceDetail']['commodities'][] = [
                    'description' => $produto['descricao'],
                    'weight' => [
                        'units' => 'KG',
                        'value' => $produto['peso']
                    ],
                    'quantity' => $produto['quantidade'],
                    'quantityUnits' => 'PCS',
                    'unitPrice' => [
                        'amount' => $produto['valor_unitario'],
                        'currency' => 'USD'
                    ],
                    'customsValue' => [
                        'amount' => $produto['valor_unitario'] * $produto['quantidade'],
                        'currency' => 'USD'
                    ],
                    'countryOfManufacture' => $produto['pais_origem'],
                    'harmonizedCode' => $produto['ncm'] ?? '000000' // NCM ou código harmonizado
                ];
            }
            
            // Fazer a requisição
            $shipCurl = curl_init();
            curl_setopt_array($shipCurl, [
                CURLOPT_URL => $shipUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($shipRequest),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $accessToken,
                    "X-locale: pt_BR",
                    "x-customer-transaction-id: " . $transactionId
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            
            $shipResponse = curl_exec($shipCurl);
            $shipHttpCode = curl_getinfo($shipCurl, CURLINFO_HTTP_CODE);
            $shipErr = curl_error($shipCurl);
            
            curl_close($shipCurl);
            
            // Fazer log da resposta em ambiente de desenvolvimento
            if (config('app.debug')) {
                Log::info('Resposta da API FedEx (Criação de Envio)', [
                    'HTTP_Code' => $shipHttpCode,
                    'Response' => $shipResponse ? substr($shipResponse, 0, 1000) . '...' : 'Vazia',
                    'Erro' => $shipErr ?: 'Nenhum'
                ]);
            }
            
            if ($shipErr) {
                throw new \Exception('Erro na requisição de envio: ' . $shipErr);
            }
            
            if ($shipHttpCode != 200) {
                throw new \Exception('Falha no envio. Código HTTP: ' . $shipHttpCode . '. Resposta: ' . $shipResponse);
            }
            
            // Processar resposta
            $shipData = json_decode($shipResponse, true);
            
            // Extrair informações relevantes
            $trackingNumber = $shipData['output']['transactionShipments'][0]['masterTrackingNumber'] ?? null;
            $shipmentId = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['shipmentId'] ?? null;
            $labelUrl = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['url'] ?? null;
            
            // Estruturar resposta
            $resultado = [
                'success' => true,
                'trackingNumber' => $trackingNumber,
                'shipmentId' => $shipmentId,
                'labelUrl' => $labelUrl,
                'servicoContratado' => $servicoEntrega,
                'dataCriacao' => date('Y-m-d H:i:s'),
                'simulado' => false,
                'detalhes' => $shipData
            ];
            
            // Cache do resultado por 24 horas
            if ($trackingNumber) {
                Cache::put('fedex_envio_' . $trackingNumber, $resultado, now()->addDay());
            }
            
            return $resultado;
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar envio FedEx', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Se forçar simulação está explicitamente definido como true, use simulação, 
            // caso contrário, deixe o erro propagar
            if ($forcarSimulacao === true) {
                // Em caso de erro, retornar simulação com mensagem
                $resultado = $this->simularCriacaoEnvio($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $servicoEntrega);
                $resultado['mensagem'] = $e->getMessage();
                
                return $resultado;
            } else {
                // Se não estiver usando forçar simulação, propagar o erro
                throw $e;
            }
        }
    }
    
    /**
     * Simula a criação de um envio para testes
     */
    private function simularCriacaoEnvio($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $servicoEntrega)
    {
        // Gerar número de rastreamento simulado
        $prefixos = ['FEDX', '9205', '9612', '7781'];
        $prefixo = $prefixos[array_rand($prefixos)];
        $trackingNumber = $prefixo . rand(10000000, 99999999) . rand(10, 99);
        
        // Calcular peso total dos produtos
        $pesoTotal = 0;
        $valorTotal = 0;
        
        foreach ($dadosProdutos as $produto) {
            $pesoTotal += $produto['peso'] * $produto['quantidade'];
            $valorTotal += $produto['valor_unitario'] * $produto['quantidade'];
        }
        
        // Simular URL da etiqueta (usando serviço público para gerar QR code com o número de rastreamento)
        $labelUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($trackingNumber);
        
        // Estruturar resposta simulada
        $resultado = [
            'success' => true,
            'trackingNumber' => $trackingNumber,
            'shipmentId' => 'SIM' . rand(1000000, 9999999),
            'labelUrl' => $labelUrl,
            'servicoContratado' => $servicoEntrega,
            'dataCriacao' => date('Y-m-d H:i:s'),
            'simulado' => true,
            'detalhes' => [
                'remetente' => $dadosRemetente,
                'destinatario' => $dadosDestinatario,
                'pacote' => $dadosPacote,
                'produtos' => $dadosProdutos,
                'valorDeclarado' => $valorTotal,
                'pesoTotal' => $pesoTotal
            ]
        ];
        
        // Cache do resultado simulado por 24 horas
        Cache::put('fedex_envio_' . $trackingNumber, $resultado, now()->addDay());
        
        return $resultado;
    }
} 