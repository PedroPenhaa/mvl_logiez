<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FedexService
{
    protected $clientId;
    protected $clientSecret;
    protected $apiUrl;
    protected $shipperAccount;
    
    public function __construct()
    {
        $this->clientId = config('services.fedex.client_id');
        $this->clientSecret = config('services.fedex.client_secret');
        $this->apiUrl = config('services.fedex.api_url');
        $this->shipperAccount = config('services.fedex.shipper_account');
    }
    
    /**
     * Obter token de autenticação da API FedEx
     * 
     * @param bool $forcarNovoToken Se true, ignora cache e solicita novo token
     * @return string Token de acesso
     */
    public function getAuthToken($forcarNovoToken = false)
    {
        // Verificar se já temos um token em cache
        if (!$forcarNovoToken && Cache::has('fedex_token')) {
            return Cache::get('fedex_token');
        }
        
        $authUrl = $this->apiUrl . '/oauth/token';
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $authUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=" . $this->clientId . "&client_secret=" . $this->clientSecret,
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
            Log::error('Erro na autenticação FedEx', ['erro' => $err]);
            throw new \Exception('Erro na requisição de autenticação: ' . $err);
        }
        
        if ($httpCode != 200) {
            Log::error('Erro na autenticação FedEx', [
                'httpCode' => $httpCode,
                'resposta' => $response
            ]);
            throw new \Exception('Falha na autenticação. Código HTTP: ' . $httpCode);
        }
        
        $authData = json_decode($response, true);
        $accessToken = $authData['access_token'] ?? null;
        
        if (!$accessToken) {
            throw new \Exception('Token de acesso não encontrado na resposta');
        }
        
        // Guardar token em cache (expira 1 hora antes do tempo real de expiração)
        $expiresIn = ($authData['expires_in'] ?? 3600) - 3600;
        if ($expiresIn < 0) {
            $expiresIn = 1800; // Se o tempo for negativo, define para 30 minutos
        }
        
        Cache::put('fedex_token', $accessToken, now()->addSeconds($expiresIn));
        
        return $accessToken;
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
        // Se forçar simulação, pula a chamada da API
        if ($forcarSimulacao) {
            $resultado = $this->simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso);
            $resultado['mensagem'] = 'Cotação simulada solicitada pelo sistema';
            return $resultado;
        }

        // Obter token de autenticação
        try {
            $accessToken = $this->getAuthToken();
        } catch (\Exception $e) {
            Log::warning('Falha na autenticação FedEx, usando simulação', ['erro' => $e->getMessage()]);
            $resultado = $this->simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso);
            $resultado['mensagem'] = 'Cotação simulada devido a falha na autenticação: ' . $e->getMessage();
            return $resultado;
        }

        // Cálculo do peso cúbico (dimensional)
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);
        
        // Extrair códigos postais e códigos de país
        $postalCodeOrigem = $origem;
        $countryCodeOrigem = 'BR';
        $postalCodeDestino = $destino;
        $countryCodeDestino = 'US';
        
        if (is_array($origem)) {
            $postalCodeOrigem = $origem['postalCode'] ?? $origem[0] ?? '';
            $countryCodeOrigem = $origem['countryCode'] ?? $origem[1] ?? 'BR';
        }
        
        if (is_array($destino)) {
            $postalCodeDestino = $destino['postalCode'] ?? $destino[0] ?? '';
            $countryCodeDestino = $destino['countryCode'] ?? $destino[1] ?? 'US';
        }
        
        // Preparar requisição de cotação
        $rateUrl = $this->apiUrl . '/rate/v1/rates/quotes';
        
        $rateRequest = [
            'accountNumber' => [
                'value' => $this->shipperAccount
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
                'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                'rateRequestType' => ['ACCOUNT', 'LIST'],
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
                ]
            ]
        ];
        
        // Enviar requisição de cotação
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
                "X-locale: en_US"
            ],
        ]);
        
        $rateResponse = curl_exec($rateCurl);
        $rateHttpCode = curl_getinfo($rateCurl, CURLINFO_HTTP_CODE);
        $rateErr = curl_error($rateCurl);
        
        curl_close($rateCurl);
        
        // Log para debug
        Log::info('Resposta de cotação FedEx', [
            'httpCode' => $rateHttpCode,
            'erro' => $rateErr,
            'resposta' => json_decode($rateResponse, true)
        ]);
        
        // Verificar erros - Se der erro 403 ou qualquer outro, usamos simulação
        if ($rateErr || $rateHttpCode != 200) {
            Log::warning('Erro na API FedEx, usando simulação', [
                'httpCode' => $rateHttpCode,
                'erro' => $rateErr
            ]);
            
            $resultado = $this->simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso);
            
            if ($rateErr) {
                $resultado['mensagem'] = 'Cotação simulada devido a erro na requisição: ' . $rateErr;
            } else {
                $resultado['mensagem'] = 'Cotação simulada devido ao código HTTP: ' . $rateHttpCode;
            }
            
            $resultado['apiAttempt'] = [
                'httpCode' => $rateHttpCode,
                'erro' => $rateErr
            ];
            
            return $resultado;
        }
        
        // Processar resposta
        $rateData = json_decode($rateResponse, true);
        
        // Extrair cotações da resposta
        $cotacoes = [];
        if (isset($rateData['output']['rateReplyDetails'])) {
            foreach ($rateData['output']['rateReplyDetails'] as $rateDetail) {
                $serviceName = $rateDetail['serviceName'] ?? 'Serviço Desconhecido';
                $serviceType = $rateDetail['serviceType'] ?? '';
                $amount = 0;
                $currency = 'USD';
                
                if (isset($rateDetail['ratedShipmentDetails'][0]['totalNetCharge'])) {
                    $amount = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['amount'];
                    $currency = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['currency'];
                }
                
                $deliveryDate = null;
                $deliveryTime = null;
                
                if (isset($rateDetail['commit']['dateDetail'])) {
                    if (isset($rateDetail['commit']['dateDetail']['dayFormat'])) {
                        $deliveryTime = $rateDetail['commit']['dateDetail']['dayFormat'];
                    }
                    
                    // Tentar obter data estimada se disponível
                    if (isset($rateDetail['commit']['dateDetail']['date'])) {
                        $deliveryDate = $rateDetail['commit']['dateDetail']['date'];
                    }
                }
                
                $cotacoes[] = [
                    'servico' => $serviceName,
                    'servicoTipo' => $serviceType,
                    'valorTotal' => $amount,
                    'moeda' => $currency,
                    'tempoEntrega' => $deliveryTime,
                    'dataEntrega' => $deliveryDate
                ];
            }
        }
        
        // Retornar resultado
        return [
            'success' => true,
            'pesoCubico' => round($pesoCubico, 2),
            'pesoReal' => $peso,
            'pesoUtilizado' => round($pesoUtilizado, 2),
            'cotacoesFedEx' => $cotacoes,
            'simulado' => false,
            'dataConsulta' => date('Y-m-d H:i:s')
        ];
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