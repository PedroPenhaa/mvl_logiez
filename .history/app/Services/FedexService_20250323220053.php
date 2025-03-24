<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

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
     */
    public function getAuthToken()
    {
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
                "Content-Type: application/x-www-form-urlencoded"
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
     */
    public function simularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso)
    {
        // Cálculo do peso cúbico (dimensional)
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);
        
        // Cotações simuladas realistas
        $cotacoes = [
            [
                'servico' => 'FedEx International Priority',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY',
                'valorTotal' => number_format(130 + ($pesoUtilizado * 15), 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => '3-5 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+4 days'))
            ],
            [
                'servico' => 'FedEx International Economy',
                'servicoTipo' => 'INTERNATIONAL_ECONOMY',
                'valorTotal' => number_format(100 + ($pesoUtilizado * 12), 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => '5-7 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+6 days'))
            ],
            [
                'servico' => 'FedEx International First',
                'servicoTipo' => 'INTERNATIONAL_FIRST',
                'valorTotal' => number_format(180 + ($pesoUtilizado * 22), 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => '1-3 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+3 days'))
            ]
        ];
        
        // Adicionar valor promocional se o peso for baixo
        if ($pesoUtilizado < 5) {
            $cotacoes[] = [
                'servico' => 'FedEx International Priority (Promocional)',
                'servicoTipo' => 'INTERNATIONAL_PRIORITY_PROMO',
                'valorTotal' => number_format(100 + ($pesoUtilizado * 12), 2, '.', ''),
                'moeda' => 'USD',
                'tempoEntrega' => '3-5 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+4 days'))
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