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
     * @param bool $forcarNovoToken Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    public function getAuthToken($forceRefresh = false) {
        if (!$forceRefresh && Cache::has('fedex_token')) {
            return Cache::get('fedex_token');
        }
    
        $authUrl = $this->apiUrl . '/oauth/token';
        
        $response = Http::asForm()->post($authUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]);
    
        if ($response->failed()) {
            throw new \Exception('Falha na autenticação: ' . $response->body());
        }
    
        $data = $response->json();
        $token = $data['access_token'] ?? null;
    
        if (!$token) {
            throw new \Exception('Token não recebido');
        }
    
        Cache::put('fedex_token', $token, now()->addSeconds($data['expires_in'] - 60));
    
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
     * Simula cotação de frete (usado enquanto resolve problemas de permissão na API)
     * 
     * @param string|array $origem CEP ou array com dados de origem
     * @param string|array $destino CEP ou array com dados de destino
     * @param float $altura Altura em cm
     * @param float $largura Largura em cm
     * @param float $comprimento Comprimento em cm
     * @param float $peso Peso em kg
     * @return array
     */
    public function simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso)
    {
        // Cálculo do peso cúbico (dimensional)
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);
        
        // Dados de países para personalizar a simulação
        $countryCodeOrigem = 'BR';
        $countryCodeDestino = 'US';
        
        if (is_array($origem) && isset($origem['countryCode'])) {
            $countryCodeOrigem = $origem['countryCode'];
        }
        
        if (is_array($destino) && isset($destino['countryCode'])) {
            $countryCodeDestino = $destino['countryCode'];
        }
        
        // Fator de ajuste baseado na combinação de países
        $fatorPais = 1.0;
        $prazoExtra = 0;
        
        // Ajuste para envios internacionais específicos
        if ($countryCodeOrigem != $countryCodeDestino) {
            // Europa
            if (in_array($countryCodeDestino, ['DE', 'FR', 'ES', 'IT', 'GB', 'PT'])) {
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
        ];
    }
} 