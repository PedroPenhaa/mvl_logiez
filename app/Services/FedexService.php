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
        $this->apiUrl = config('services.fedex.api_url', "https://apis.fedex.com");
        $this->clientId = config('services.fedex.client_id');
        $this->clientSecret = config('services.fedex.client_secret');
        $this->shipperAccount = config('services.fedex.shipper_account');

    }
    
    /**
     * Obter token de autenticação da API FedEx para cotação e envio
     * 
     * @param bool $forceRefresh Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    public function getAuthToken($forceRefresh = false) {
        return $this->getAuthTokenForOperation('shipping', $forceRefresh);
    }
    
    /**
     * Obter token de autenticação da API FedEx para rastreamento
     * 
     * @param bool $forceRefresh Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    public function getTrackingAuthToken($forceRefresh = false) {
        return $this->getAuthTokenForOperation('tracking', $forceRefresh);
    }
    
    /**
     * Obter token de autenticação da API FedEx para uma operação específica
     * 
     * @param string $operation Tipo de operação ('shipping' ou 'tracking')
     * @param bool $forceRefresh Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    private function getAuthTokenForOperation($operation = 'shipping', $forceRefresh = false) {
        $cacheKey = 'fedex_token_' . $operation;
        
        if (!$forceRefresh && Cache::has($cacheKey)) {
            $token = Cache::get($cacheKey);
            return $token;
        }
    
        $authUrl = $this->apiUrl . '/oauth/token';
        
        // Usar credenciais específicas para cada operação
        $clientId = $operation === 'tracking' 
            ? config('services.fedex.tracking_client_id', $this->clientId)
            : $this->clientId;
        $clientSecret = $operation === 'tracking'
            ? config('services.fedex.tracking_client_secret', $this->clientSecret)
            : $this->clientSecret;
        
        // Preparar payload para a solicitação de token
        $tokenPayload = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];
        
        // Se estiver processando um código especial, fazer log
        if (self::$trackingSpecialCode) {
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
        Cache::put($cacheKey, $token, now()->addMinutes($cacheMinutes));
        
        // Armazenar detalhes adicionais para diagnóstico
        Cache::put($cacheKey . '_details', [
            'expires_in' => $expiresIn,
            'obtained_at' => now()->toDateTimeString(),
            'expires_at' => now()->addSeconds($expiresIn)->toDateTimeString(),
            'operation' => $operation
        ], now()->addMinutes($cacheMinutes));
        
        // Se estiver processando um código especial, fazer log
        if (self::$trackingSpecialCode) {
        }
    
        return $token;
    }
    
    /**
     * Valida e formata código postal de acordo com o país
     */
    private function validarEFormatarCodigoPostal($postalCode, $countryCode)
    {
        // Log da entrada
        Log::info('🔍 Validando código postal:', [
            'postalCode_original' => $postalCode,
            'countryCode' => $countryCode
        ]);

        // Remover caracteres não alfanuméricos
        $postalCode = preg_replace('/[^A-Za-z0-9]/', '', $postalCode);
        
        // Converter para maiúsculas
        $postalCode = strtoupper($postalCode);
        
        switch ($countryCode) {
            case 'BR':
                // CEP brasileiro: SEMPRE usar apenas os primeiros 5 dígitos para FedEx
                if (strlen($postalCode) >= 5 && ctype_digit($postalCode)) {
                    $cepFormatado = substr($postalCode, 0, 5);
                    Log::info('✅ CEP brasileiro formatado:', [
                        'cep_original' => $postalCode,
                        'cep_formatado' => $cepFormatado
                    ]);
                    return $cepFormatado;
                }
                // Se não tiver pelo menos 5 dígitos, retornar um CEP válido de exemplo
                Log::warning('⚠️ CEP brasileiro inválido, usando padrão:', [
                    'cep_original' => $postalCode,
                    'cep_padrao' => '01310'
                ]);
                return '01310'; // CEP válido de São Paulo (apenas 5 dígitos)
                
            case 'US':
                // ZIP code americano: 5 dígitos ou 5+4 dígitos
                if (strlen($postalCode) === 5 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                if (strlen($postalCode) === 9 && ctype_digit($postalCode)) {
                    return substr($postalCode, 0, 5) . '-' . substr($postalCode, 5);
                }
                // Se não estiver no formato correto, retornar um ZIP válido de exemplo
                return '10001'; // ZIP válido de Nova York
                
            case 'CA':
                // Código postal canadense: A1A 1A1 (letra, dígito, letra, espaço, dígito, letra, dígito)
                if (preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/', $postalCode)) {
                    return str_replace(' ', '', $postalCode);
                }
                return 'M5V3A8'; // Código postal válido de Toronto
                
            case 'MX':
                // Código postal mexicano: 5 dígitos
                if (strlen($postalCode) === 5 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                return '06000'; // Código postal válido da Cidade do México
                
            case 'AR':
                // Código postal argentino: 4 dígitos
                if (strlen($postalCode) === 4 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                return '1001'; // Código postal válido de Buenos Aires
                
            default:
                // Para outros países, retornar o código limpo ou um valor padrão
                if (strlen($postalCode) >= 3 && strlen($postalCode) <= 10) {
                    return $postalCode;
                }
                return '00000';
        }
    }

    /**
     * Validar código postal usando a API de validação da FedEx
     */
    private function validarCodigoPostalFedEx($postalCode, $countryCode, $stateCode = null)
    {
        try {
            $accessToken = $this->getAuthToken(true);
            $validateUrl = $this->apiUrl . '/country/v1/postal/validate';
            $transactionId = uniqid('logiez_validate_');
            
            $validateRequest = [
                'carrierCode' => 'FDXE',
                'countryCode' => $countryCode,
                'stateOrProvinceCode' => $stateCode,
                'postalCode' => $postalCode,
                'shipDate' => date('Y-m-d'),
                'checkForMismatch' => true
            ];
            
          /*  Log::info('🔍 Validando código postal na FedEx:', [
                'postalCode' => $postalCode,
                'countryCode' => $countryCode,
                'stateCode' => $stateCode,
                'url' => $validateUrl
            ]);
            */
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-locale' => 'en_US',
                'x-customer-transaction-id' => $transactionId
            ])->post($validateUrl, $validateRequest);
            
           /* Log::info('📥 Resposta da validação de código postal:', [
                'http_code' => $response->status(),
                'response_body' => $response->body()
            ]);
            */
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['output']['cleanedPostalCode'])) {
                    return [
                        'valid' => true,
                        'cleanedPostalCode' => $data['output']['cleanedPostalCode'],
                        'city' => $data['output']['cityFirstInitials'] ?? null,
                        'stateOrProvinceCode' => $data['output']['stateOrProvinceCode'] ?? $stateCode
                    ];
                }
            }
            
            return ['valid' => false, 'error' => 'Código postal inválido'];
            
        } catch (\Exception $e) {
           /* Log::warning('⚠️ Erro na validação de código postal:', [
                'error' => $e->getMessage(),
                'postalCode' => $postalCode,
                'countryCode' => $countryCode
            ]);*/
            return ['valid' => false, 'error' => $e->getMessage()];
        }
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
        try {
            // Log dos dados recebidos
          /*  Log::info('🚀 Iniciando cotação FedEx:', [
                'origem_original' => $origem,
                'destino_original' => $destino,
                'altura' => $altura,
                'largura' => $largura,
                'comprimento' => $comprimento,
                'peso' => $peso
            ]);
*/
            // Obter token de autenticação
            $accessToken = $this->getAuthToken(true); // Forçar novo token
    
            // Cálculo do peso cúbico
            $pesoCubico = ($altura * $largura * $comprimento) / 5000;
            $pesoUtilizado = max($pesoCubico, $peso);
    
            // Preparar requisição de cotação
            $rateUrl = $this->apiUrl . config('services.fedex.rate_endpoint', '/rate/v1/rates/quotes');
            $transactionId = uniqid('logiez_rate_');
            $shipDate = date('Y-m-d');
    
            // Extrair códigos postais
            $postalCodeOrigem = is_array($origem) ? ($origem['postalCode'] ?? $origem[0] ?? '') : $origem;
            $postalCodeDestino = is_array($destino) ? ($destino['postalCode'] ?? $destino[0] ?? '') : $destino;
            $countryCodeOrigem = is_array($origem) ? ($origem['countryCode'] ?? 'BR') : 'BR';
            $countryCodeDestino = is_array($destino) ? ($destino['countryCode'] ?? 'US') : 'US';
    
            // Log antes da formatação
           /* Log::info('📮 Códigos postais antes da formatação:', [
                'postalCodeOrigem' => $postalCodeOrigem,
                'postalCodeDestino' => $postalCodeDestino,
                'countryCodeOrigem' => $countryCodeOrigem,
                'countryCodeDestino' => $countryCodeDestino
            ]);*/
    
            // Validar e formatar códigos postais de acordo com o país
            $postalCodeOrigem = $this->validarEFormatarCodigoPostal($postalCodeOrigem, $countryCodeOrigem);
            $postalCodeDestino = $this->validarEFormatarCodigoPostal($postalCodeDestino, $countryCodeDestino);
            
            // Log após a formatação
          /*      Log::info('📮 Códigos postais após formatação:', [
                'postalCodeOrigem_formatado' => $postalCodeOrigem,
                'postalCodeDestino_formatado' => $postalCodeDestino
            ]);*/
            
            // VALIDAÇÃO DE CÓDIGO POSTAL ANTES DA COTAÇÃO
            $validacaoOrigem = $this->validarCodigoPostalFedEx($postalCodeOrigem, $countryCodeOrigem, 'SP');
            if (!$validacaoOrigem['valid']) {
                return [
                    'success' => false,
                    'mensagem' => 'CEP de origem inválido: ' . $postalCodeOrigem . '. Por favor, insira um CEP válido.',
                    'error_code' => 'invalid_origin_postal_code'
                ];
            }
            
            $validacaoDestino = $this->validarCodigoPostalFedEx($postalCodeDestino, $countryCodeDestino, 'FL');
            if (!$validacaoDestino['valid']) {
                return [
                    'success' => false,
                    'mensagem' => 'CEP de destino inválido: ' . $postalCodeDestino . '. Por favor, insira um CEP válido.',
                    'error_code' => 'invalid_destination_postal_code'
                ];
            }
            
            // Usar códigos postais validados
            $postalCodeOrigem = $validacaoOrigem['cleanedPostalCode'];
            $postalCodeDestino = $validacaoDestino['cleanedPostalCode'];
            
           /* Log::info('✅ Códigos postais validados:', [
                'postalCodeOrigem_validado' => $postalCodeOrigem,
                'postalCodeDestino_validado' => $postalCodeDestino
            ]);*/
    
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
                            'streetLines' => ['Rua Teste, 123'],
                            'city' => 'São Paulo',
                            'stateOrProvinceCode' => 'SP',
                            'postalCode' => substr($postalCodeOrigem, 0, 10),
                            'countryCode' => $countryCodeOrigem,
                            'residential' => false
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'streetLines' => ['Test Street, 456'],
                            'city' => 'Orlando',
                            'stateOrProvinceCode' => 'FL',
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
                                'description' => 'Sample Product',
                                'weight' => [
                                    'units' => 'KG',
                                    'value' => $peso
                                ],
                                'quantity' => 1,
                                'customsValue' => [
                                    'amount' => '100',
                                    'currency' => 'USD'
                                ],
                                'unitPrice' => [
                                    'amount' => '100',
                                    'currency' => 'USD'
                                ],
                                'numberOfPieces' => 1,
                                'countryOfManufacture' => 'BR',
                                'quantityUnits' => 'PCS',
                                'name' => 'Sample Product'
                            ]
                        ]
                    ],
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
                'carrierCodes' => ['FDXE', 'FDXG']
            ];
    
            // Log da requisição completa
           /* Log::info('📤 Enviando requisição para FedEx API:', [
                'url' => $rateUrl,
                'transaction_id' => $transactionId,
                'payload' => json_encode($rateRequest, JSON_PRETTY_PRINT)
            ]);*/

            // Fazer a requisição
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-locale' => 'en_US',
                'x-customer-transaction-id' => $transactionId
            ])->post($rateUrl, $rateRequest);

            // Log da resposta
           /* Log::info('📥 Resposta da FedEx API:', [
                'http_code' => $response->status(),
                'response_body' => $response->body(),
                'success' => $response->successful()
            ]);*/

            if ($response->failed()) {
                $errorMessage = 'Falha na cotação. Código HTTP: ' . $response->status() . "\n" . $response->body();
               /* Log::error('❌ Erro na requisição FedEx:', [
                    'error_message' => $errorMessage,
                    'http_code' => $response->status(),
                    'response_body' => $response->body()
                ]);*/
                throw new \Exception($errorMessage);
            }
    
            $rateData = $response->json();

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
                'dataConsulta' => date('Y-m-d H:i:s')
            ];
    
            // Log do resultado final
           /* Log::info('✅ Cotação calculada com sucesso:', [
                'cotacoes_encontradas' => count($cotacoes),
                'peso_cubico' => $resultado['pesoCubico'],
                'peso_real' => $resultado['pesoReal'],
                'peso_utilizado' => $resultado['pesoUtilizado']
            ]);*/
    
            return $resultado;
    
        } catch (\Exception $e) {
            // Log do erro
           /* Log::error('❌ Erro ao calcular cotação FedEx:', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'origem' => $origem,
                'destino' => $destino
            ]);*/   
            
            // Em caso de erro, retornar erro real em vez de simulação
            return [
                'success' => false,
                'mensagem' => 'Erro ao obter cotação da FedEx: ' . $e->getMessage(),
                'error_code' => 'fedex_api_error'
            ];
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
            
            return [
                'success' => false,
                'data' => date('d/m/Y'),
                'cotacao' => 5.71
            ];
        } catch (\Exception $e) {
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
            
        }

        // Se forçar simulação, usa o método de simulação
        if ($forcarSimulacao) {
            return $this->simularRastreamento($trackingNumber);
        }
    
        try {
            // Obter token de autenticação - forçar renovação para códigos especiais
            $accessToken = $this->getTrackingAuthToken(!empty($specialTrackingConfig));
    
            // Preparar requisição de rastreamento
            $trackUrl = $this->apiUrl . config('services.fedex.track_endpoint', '/track/v1/trackingnumbers');
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
                    }
                }
            }
    
            // Processar os dados de rastreamento
            $result = $this->processarDadosRastreamento($trackData, $trackingNumber);
            $result['simulado'] = false;
            $result['respostaOriginal'] = $trackData; // Opcional - para debug
            
            // Se foi detectada uma resposta virtual e o processamento falhou, ativar simulação
            if ($isVirtualResponse && !$result['success']) {
                return $this->simularRastreamento($trackingNumber);
            }
            
            return $result;
    
        } catch (\Exception $e) {
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
        }

        // Verificar se temos uma resposta virtual
        $isVirtualResponse = false;
        if (!empty($trackData['output']['alerts'])) {
            foreach ($trackData['output']['alerts'] as $alert) {
                if (isset($alert['code']) && $alert['code'] === 'VIRTUAL.RESPONSE') {
                    $isVirtualResponse = true;
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
        
        // Verificar campos obrigatórios
        $camposObrigatoriosRemetente = ['name', 'phone', 'email', 'address', 'city', 'state', 'postalCode', 'country'];
        $camposObrigatoriosDestinatario = ['name', 'phone', 'email', 'address', 'city', 'state', 'postalCode', 'country'];
        $camposObrigatoriosPacote = ['height', 'width', 'length', 'weight'];
        
        // Verificar campos do remetente
        foreach ($camposObrigatoriosRemetente as $campo) {
            if (!isset($dadosRemetente[$campo]) || empty($dadosRemetente[$campo])) {
                $mensagemErro = "Campo obrigatório não encontrado no remetente: {$campo}";
                throw new \Exception($mensagemErro);
            }
        }
        
        // Verificar campos do destinatário
        foreach ($camposObrigatoriosDestinatario as $campo) {
            if (!isset($dadosDestinatario[$campo]) || empty($dadosDestinatario[$campo])) {
                $mensagemErro = "Campo obrigatório não encontrado no destinatário: {$campo}";
                throw new \Exception($mensagemErro);
            }
        }
        
        // Verificar campos do pacote
        foreach ($camposObrigatoriosPacote as $campo) {
            if (!isset($dadosPacote[$campo]) || empty($dadosPacote[$campo])) {
                $mensagemErro = "Campo obrigatório não encontrado no pacote: {$campo}";
                throw new \Exception($mensagemErro);
            }
        }
        
        // Verificar se temos produtos
        if (empty($dadosProdutos)) {
            $mensagemErro = "Lista de produtos vazia";
            throw new \Exception($mensagemErro);
        }
        
        // Verificar campos de cada produto
        foreach ($dadosProdutos as $index => $produto) {
            $camposObrigatoriosProduto = ['description', 'quantity', 'unitPrice', 'weight', 'countryOfOrigin'];
            foreach ($camposObrigatoriosProduto as $campo) {
                if (!isset($produto[$campo])) {
                    $mensagemErro = "Campo obrigatório não encontrado no produto {$index}: {$campo}";
                    throw new \Exception($mensagemErro);
                }
            }
        }
        
        // Verificar se devemos usar a simulação
        if ($forcarSimulacao || config('app.env') !== 'production') {
            return $this->simularCriacaoEnvio($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $servicoEntrega);
        }
        
        try {
            // Obter token de autenticação
            $accessToken = $this->getAuthToken();
            
            // Preparar requisição de criação de envio
            $shipUrl = $this->apiUrl . config('services.fedex.ship_endpoint', '/ship/v1/shipments');
            $transactionId = uniqid('logiez_ship_');
            $shipDate = date('Y-m-d');
            
            // Construir conteúdo da requisição
            $shipRequest = [
                'requestedShipment' => [
                    'shipper' => [
                        'contact' => [
                            'personName' => $dadosRemetente['name'],
                            'phoneNumber' => $dadosRemetente['phone'],
                            'emailAddress' => $dadosRemetente['email']
                        ],
                        'address' => [
                            'streetLines' => [
                                $dadosRemetente['address'],
                                $dadosRemetente['complement'] ?? ''
                            ],
                            'city' => $dadosRemetente['city'],
                            'stateOrProvinceCode' => $dadosRemetente['state'],
                            'postalCode' => $dadosRemetente['postalCode'],
                            'countryCode' => $dadosRemetente['country'],
                            'residential' => false
                        ]
                    ],
                    'recipients' => [
                        [
                            'contact' => [
                                'personName' => $dadosDestinatario['name'],
                                'phoneNumber' => $dadosDestinatario['phone'],
                                'emailAddress' => $dadosDestinatario['email']
                            ],
                            'address' => [
                                'streetLines' => [
                                    $dadosDestinatario['address'],
                                    $dadosDestinatario['complement'] ?? ''
                                ],
                                'city' => $dadosDestinatario['city'],
                                'stateOrProvinceCode' => $dadosDestinatario['state'],
                                'postalCode' => $dadosDestinatario['postalCode'],
                                'countryCode' => $dadosDestinatario['country'],
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
                    'description' => $produto['description'],
                    'weight' => [
                        'units' => 'KG',
                        'value' => $produto['weight']
                    ],
                    'quantity' => $produto['quantity'],
                    'quantityUnits' => 'PCS',
                    'unitPrice' => [
                        'amount' => $produto['unitPrice'],
                        'currency' => 'USD'
                    ],
                    'customsValue' => [
                        'amount' => $produto['unitPrice'] * $produto['quantity'],
                        'currency' => 'USD'
                    ],
                    'countryOfManufacture' => $produto['countryOfOrigin'],
                    'harmonizedCode' => $produto['harmonizedCode'] ?? '000000' // NCM ou código harmonizado
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
            $pesoTotal += $produto['weight'] * $produto['quantity'];
            $valorTotal += $produto['unitPrice'] * $produto['quantity'];
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