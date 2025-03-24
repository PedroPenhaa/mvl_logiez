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
     */
    public function calcularCotacao($origem, $destino, $altura, $largura, $comprimento, $peso)
    {
        // Obter token de autenticação
        try {
            $accessToken = $this->getAuthToken();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        
        

        dd($accessToken);
        // Cálculo do peso cúbico (dimensional)
        $pesoCubico = ($altura * $largura * $comprimento) / 5000;
        $pesoUtilizado = max($pesoCubico, $peso);
        
        // Preparar requisição de cotação
        $rateUrl = $this->apiUrl . '/rate/v1/rates/quotes';
        
        $rateRequest = [
            'accountNumber' => [
                'value' => $this->shipperAccount
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
                "Authorization: Bearer " . $accessToken,
                "X-locale: pt_BR"
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
        
        // Verificar erros
        if ($rateErr) {
            return [
                'success' => false,
                'message' => 'Erro na requisição de cotação: ' . $rateErr
            ];
        }
        
        if ($rateHttpCode != 200) {
            return [
                'success' => false,
                'message' => 'Erro na cotação FedEx: Código HTTP ' . $rateHttpCode,
                'resposta' => json_decode($rateResponse, true)
            ];
        }
        
        // Processar resposta
        $rateData = json_decode($rateResponse, true);
        
        // Extrair cotações da resposta
        $cotacoes = [];
        if (isset($rateData['output']['rateReplyDetails'])) {
            foreach ($rateData['output']['rateReplyDetails'] as $rateDetail) {
                $serviceName = $rateDetail['serviceName'] ?? 'Serviço Desconhecido';
                $amount = 0;
                $currency = 'USD';
                
                if (isset($rateDetail['ratedShipmentDetails'][0]['totalNetCharge'])) {
                    $amount = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['amount'];
                    $currency = $rateDetail['ratedShipmentDetails'][0]['totalNetCharge']['currency'];
                }
                
                $tempoEntrega = null;
                if (isset($rateDetail['commit']['dateDetail']['dayFormat'])) {
                    $tempoEntrega = $rateDetail['commit']['dateDetail']['dayFormat'];
                }
                
                $cotacoes[] = [
                    'servico' => $serviceName,
                    'valorTotal' => $amount,
                    'moeda' => $currency,
                    'tempoEntrega' => $tempoEntrega
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
            'responseOriginal' => $rateData
        ];
    }
} 