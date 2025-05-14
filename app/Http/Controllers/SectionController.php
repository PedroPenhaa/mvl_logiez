<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\FedexService;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    protected $fedexService;
    
    public function __construct(FedexService $fedexService)
    {
        $this->fedexService = $fedexService;
    }
    
    public function dashboard()
    {
        return view('sections.dashboard');
    }
    
    public function cotacao()
    {
        // Verificar se há dados da última cotação FedEx na sessão
        $dados = session('dados_fedex', null);
        $resultado = session('resultado_fedex', null);
        
        return view('sections.cotacao', compact('dados', 'resultado'));
    }
    
    public function envio()
    {
        return view('sections.envio');
    }
    
    public function pagamento()
    {
        // Verificar se o usuário está autenticado
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return view('sections.pagamento', [
                'pendingPayments' => collect([]),
                'completedPayments' => collect([]),
                'cancelledPayments' => collect([])
            ]);
        }

        // Usar o mesmo código do PaymentController index, mas só renderizar a view
        $pendingPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $completedPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $cancelledPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sections.pagamento', compact('pendingPayments', 'completedPayments', 'cancelledPayments'));
    }
    
    public function etiqueta(Request $request)
    {
        // Verificar se é uma solicitação para checar dados na sessão
        if ($request->has('check_session')) {
            $resultadoEnvio = session('resultado_envio');
            $dadosEnvio = session('dados_envio');
            
            // Se tiver dados de envio na sessão, retornar
            if ($resultadoEnvio) {
                // Adicionar os dados detalhados do envio ao resultado
                $resultadoEnvio['dados'] = $dadosEnvio;
                
                return response()->json([
                    'hasEnvio' => true,
                    'envio' => $resultadoEnvio
                ]);
            }
            
            // Caso não tenha dados na sessão
            return response()->json([
                'hasEnvio' => false
            ]);
        }
        
        // Renderização normal da view
        return view('sections.etiqueta');
    }
    
    public function rastreamento()
    {
        return view('sections.rastreamento');
    }
    
    public function perfil()
    {
        // Verificar se o usuário está autenticado
        if (!\Illuminate\Support\Facades\Auth::check()) {
            $userData = [
                'nome' => 'Usuário não autenticado',
                'email' => '',
                'cpf' => '',
                'telefone' => '',
                'rua' => '',
                'numero' => '',
                'complemento' => '',
                'cidade' => '',
                'estado' => '',
                'cep' => ''
            ];
            
            return view('sections.perfil', [
                'usuario' => $userData,
                'shipments' => collect([])
            ]);
        }
        
        // Obter o usuário autenticado
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Tentar carregar o perfil do usuário se existir
        $userProfile = $user->profile ?? null;
        
        // Extrair CPF do campo company_name temporariamente
        $cpf = 'Não informado';
        if ($userProfile && !empty($userProfile->company_name) && strpos($userProfile->company_name, 'CPF:') === 0) {
            $cpf = substr($userProfile->company_name, 4); // Remover o prefixo 'CPF:'
        }
        
        // Analisar o endereço se estiver em formato combinado
        $rua = '';
        $numero = '';
        $complemento = '';
        
        if ($userProfile && !empty($userProfile->address)) {
            // Tentar extrair número e complemento do endereço
            $endereco = $userProfile->address;
            $partes = explode(',', $endereco, 2);
            
            if (count($partes) > 1) {
                $rua = trim($partes[0]);
                $restante = trim($partes[1]);
                
                // Verificar se há complemento
                $partes_complemento = explode('-', $restante, 2);
                if (count($partes_complemento) > 1) {
                    $numero = trim($partes_complemento[0]);
                    $complemento = trim($partes_complemento[1]);
                } else {
                    $numero = $restante;
                }
            } else {
                $rua = $endereco;
            }
        }
        
        // Formatar os dados do usuário para exibição
        $userData = [
            'nome' => $user->name,
            'email' => $user->email,
            'cpf' => $cpf,
            'telefone' => $userProfile->phone ?? 'Não informado',
            'rua' => $rua,
            'numero' => $numero,
            'complemento' => $complemento,
            'cidade' => $userProfile->city ?? 'Não informado',
            'estado' => $userProfile->state ?? 'Não informado',
            'cep' => $userProfile->zip_code ?? 'Não informado',
            'data_cadastro' => $user->created_at ? $user->created_at->format('d/m/Y') : 'Não informado'
        ];
        
        // Verificar se a classe Shipment existe antes de consultar
        $shipments = collect([]);
        if (class_exists('\\App\\Models\\Shipment')) {
            $shipments = \App\Models\Shipment::where('user_id', $user->id)
                                           ->orderBy('created_at', 'desc')
                                           ->take(5)
                                           ->get();
        }
        
        return view('sections.perfil', [
            'usuario' => $userData,
            'shipments' => $shipments
        ]);
    }

    /**
     * Processa o cálculo de cotação de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularCotacao(Request $request)
    {
        // Validar os dados de entrada
        $request->validate([
            'origem' => 'required|string',
            'destino' => 'required|string',
            'altura' => 'required|numeric|min:0',
            'largura' => 'required|numeric|min:0',
            'comprimento' => 'required|numeric|min:0',
            'peso' => 'required|numeric|min:0',
        ]);
        
        try {
            // Verificar se é uma solicitação para forçar simulação
            if ($request->has('forcarSimulacao') && $request->forcarSimulacao) {
                $resultado = $this->fedexService->simularCotacao(
                    $request->origem, 
                    $request->destino,
                    $request->altura, 
                    $request->largura, 
                    $request->comprimento, 
                    $request->peso
                );
                
                // Salvar cotação no cache e obter hash para recuperação posterior
                $hash = $this->saveCotacaoToCache($request, $resultado);
                
                return response()->json([
                    'success' => true,
                    'pesoCubico' => $resultado['pesoCubico'],
                    'pesoReal' => $resultado['pesoReal'],
                    'pesoUtilizado' => $resultado['pesoUtilizado'],
                    'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                    'mensagem' => $resultado['mensagem'],
                    'simulado' => true,
                    'dataConsulta' => $resultado['dataConsulta'],
                    'cotacaoDolar' => $resultado['cotacaoDolar'] ?? null,
                    'hash' => $hash
                ]);
            }
                
            // Código para API real
            // Usar as credenciais de teste fornecidas pelo usuário
            $apiUrl = "https://apis-sandbox.fedex.com";
            $clientId = "l7517499d73dc1470c8f56fe055c45113c";
            $clientSecret = "41d8172c88c345cca8f47695bc97a5cd";
            $shipperAccount = "740561073";

            // Obter parâmetros do formulário
            $origem = $request->origem;
            $destino = $request->destino;
            $altura = $request->altura;
            $largura = $request->largura;
            $comprimento = $request->comprimento;
            $peso = $request->peso;

            // Cálculo do peso cúbico
            $pesoCubico = ($altura * $largura * $comprimento) / 5000;
            $pesoUtilizado = max($pesoCubico, $peso);
            
            // Obter cotação do dólar atual
            $cotacaoDolar = $this->obterCotacaoDolar();
            $valorDolar = $cotacaoDolar['cotacao'] ?? 5.71; // Valor padrão caso a API falhe

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

            // 2. Preparar requisição de cotação
            $rateUrl = $apiUrl . '/rate/v1/rates/quotes';

            // Data atual em formato YYYY-MM-DD
            $shipDate = date('Y-m-d');
            $transactionId = uniqid('logiez_rate_');

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

            // Headers da requisição
            $headers = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer " . $token,
                "X-locale: en_US",
                "x-customer-transaction-id: " . $transactionId
            ];

            // 5. Fazer a requisição real
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

            if ($rateErr) {
                throw new \Exception('Erro na requisição de cotação: ' . $rateErr);
            }

            // Tratar erro 503
            if ($rateHttpCode == 503) {
                throw new \Exception('Serviço da FedEx indisponível (503)');
            }

            // Processar resposta
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

            // Converter valores USD para BRL
            foreach ($cotacoes as $key => $cotacao) {
                $valorUSD = floatval(str_replace(',', '', $cotacao['valorTotal']));
                $valorBRL = $valorUSD * $valorDolar;
                $cotacoes[$key]['valorTotalBRL'] = number_format($valorBRL, 2, ',', '.');
            }
            
            // Formatar resultados para serem compatíveis com o frontend
            $resultado = [
                'success' => true,
                'pesoCubico' => round($pesoCubico, 2),
                'pesoReal' => $peso,
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'cotacoesFedEx' => $cotacoes,
                'simulado' => false,
                'dataConsulta' => date('Y-m-d H:i:s'),
                'cotacaoDolar' => $valorDolar
            ];
            
            // Salvar cotação no cache e obter hash para recuperação posterior
            $hash = $this->saveCotacaoToCache($request, $resultado);
                
            return response()->json([
                'success' => true,
                'pesoCubico' => $resultado['pesoCubico'],
                'pesoReal' => $resultado['pesoReal'],
                'pesoUtilizado' => $resultado['pesoUtilizado'],
                'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                'simulado' => false,
                'dataConsulta' => $resultado['dataConsulta'],
                'cotacaoDolar' => $valorDolar,
                'hash' => $hash
            ]);
        } catch (\Exception $e) {
            // Verificar se é um erro específico da API da FedEx
            $message = $e->getMessage();
            
            // Verificar se é o erro específico de serviço indisponível
            if (strpos($message, 'Service unavailable') !== false || 
                strpos($message, '503') !== false ||
                strpos($message, 'indisponível') !== false) {
                
                Log::warning('API FedEx indisponível. Redirecionando para simulação.', [
                    'error' => $message
                ]);
                
                // Retornar código de erro específico para que o frontend possa tratar
                return response()->json([
                    'success' => false,
                    'error_code' => 'fedex_unavailable',
                    'message' => 'Serviço da FedEx temporariamente indisponível. Você pode tentar novamente ou usar a simulação.'
                ]);
            }
            
            // Para outros erros, tentar simulação automaticamente
            try {
                Log::warning('Erro na API FedEx. Usando simulação como fallback.', [
                    'error' => $message,
                    'stacktrace' => $e->getTraceAsString()
                ]);
                
                // Usar simulação como fallback
                $resultado = $this->fedexService->simularCotacao(
                    $request->origem, 
                    $request->destino,
                    $request->altura, 
                    $request->largura, 
                    $request->comprimento, 
                    $request->peso
                );
                
                // Adicionar mensagem específica
                $resultado['mensagem'] = 'Cotação simulada devido a um erro na API FedEx: ' . $message;
                
                // Salvar cotação no cache e obter hash para recuperação posterior
                $hash = $this->saveCotacaoToCache($request, $resultado);
                
                return response()->json([
                    'success' => true,
                    'pesoCubico' => $resultado['pesoCubico'],
                    'pesoReal' => $resultado['pesoReal'],
                    'pesoUtilizado' => $resultado['pesoUtilizado'],
                    'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                    'mensagem' => $resultado['mensagem'],
                    'simulado' => true,
                    'dataConsulta' => $resultado['dataConsulta'],
                    'cotacaoDolar' => $resultado['cotacaoDolar'] ?? null,
                    'hash' => $hash
                ]);
            } catch (\Exception $simException) {
                // Se mesmo a simulação falhar, registre e retorne erro
                Log::error('Erro ao processar simulação de cotação: ' . $simException->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar sua cotação: ' . $message,
                    'error_details' => 'Falha também na simulação: ' . $simException->getMessage()
                ], 500);
            }
        }
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
            $response = \Illuminate\Support\Facades\Http::get("https://economia.awesomeapi.com.br/json/daily/USD-BRL/1");
            
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
     * Salva uma cotação no cache e retorna o hash para recuperá-la.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $resultado
     * @return string
     */
    private function saveCotacaoToCache(Request $request, array $resultado)
    {
        // Criar identificador baseado nos parâmetros da cotação (determinístico)
        $paramsKey = $request->origem . 
                    $request->destino . 
                    $request->altura . 
                    $request->largura . 
                    $request->comprimento . 
                    $request->peso;
        
        // Gerar um hash determinístico para a cotação
        $hash = md5($paramsKey . date('Y-m-d')); // Hash baseado nos parâmetros + data atual
        $cacheKey = 'cotacao_' . $hash;
        
        // Verificar se já existe uma cotação recente com os mesmos parâmetros
        $existingCotacao = DB::table('cache')
            ->where('key', $cacheKey)
            ->where('expiration', '>', time())
            ->first();
            
        if ($existingCotacao) {
            // Se já existe uma cotação com os mesmos parâmetros e ainda válida, retorna o hash existente
            Log::info('Cotação com mesmos parâmetros já existe no cache, reutilizando hash', [
                'hash' => $hash,
                'parâmetros' => [
                    'origem' => $request->origem,
                    'destino' => $request->destino,
                    'dimensões' => $request->altura . 'x' . $request->largura . 'x' . $request->comprimento,
                    'peso' => $request->peso
                ]
            ]);
            
            return $hash;
        }
        
        // Armazenar dados da cotação no banco de dados
        $dados = [
            'origem_cep' => $request->origem,
            'origem_pais' => 'BR',
            'destino_cep' => $request->destino,
            'destino_pais' => 'US',
            'altura' => $request->altura,
            'largura' => $request->largura,
            'comprimento' => $request->comprimento,
            'peso' => $request->peso
        ];
        
        // Armazenar diretamente na tabela cache
        $expiration = now()->addDays(7)->timestamp;
        $userId = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null;
        $value = serialize([
            'dados' => $dados,
            'resultado' => $resultado
        ]);
        
        try {
            // Verificar novamente se já existe (verificação de concorrência)
            $exists = DB::table('cache')->where('key', $cacheKey)->exists();
            
            if (!$exists) {
                DB::table('cache')->insert([
                    'key' => $cacheKey,
                    'value' => $value,
                    'expiration' => $expiration,
                    'user_id' => $userId,
                    'type' => 'cotacao',
                    'origem' => $request->origem,
                    'destino' => $request->destino,
                    'altura' => $request->altura,
                    'largura' => $request->largura,
                    'comprimento' => $request->comprimento,
                    'peso' => $request->peso,
                    'created_at' => now()
                ]);
                
                Log::info('Nova cotação salva no cache', [
                    'hash' => $hash,
                    'parâmetros' => [
                        'origem' => $request->origem,
                        'destino' => $request->destino,
                        'dimensões' => $request->altura . 'x' . $request->largura . 'x' . $request->comprimento,
                        'peso' => $request->peso
                    ]
                ]);
            } else {
                Log::info('Cotação já foi inserida por outra thread, ignorando inserção duplicada', [
                    'hash' => $hash
                ]);
            }
        } catch (\Exception $e) {
            // Log o erro mas não interrompa o fluxo
            Log::error('Erro ao salvar cotação no cache: ' . $e->getMessage());
        }
        
        return $hash;
    }
    
    /**
     * Processa os dados de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarEnvio(Request $request)
    {
        // Log para diagnosticar problemas
        Log::info('Dados recebidos no processarEnvio', [
            'all' => $request->all(),
            'produtos_json' => $request->produtos_json,
            'valor_total' => $request->valor_total,
            'peso_total' => $request->peso_total,
            'altura' => $request->altura,
            'largura' => $request->largura,
            'comprimento' => $request->comprimento,
            'peso_caixa' => $request->peso_caixa,
            'caixas_json' => $request->caixas_json,
            'servico_entrega' => $request->servico_entrega,
        ]);
        
        // Validar os dados de entrada
        $request->validate([
            'produtos_json' => 'required|string',
            'valor_total' => 'required|numeric',
            'peso_total' => 'required|numeric',
            
            'origem_nome' => 'required|string',
            'origem_endereco' => 'required|string',
            'origem_cidade' => 'required|string',
            'origem_estado' => 'required|string',
            'origem_cep' => 'required|string',
            'origem_pais' => 'required|string',
            'origem_telefone' => 'required|string',
            'origem_email' => 'required|email',
            
            'destino_nome' => 'required|string',
            'destino_endereco' => 'required|string',
            'destino_cidade' => 'required|string',
            'destino_estado' => 'required|string',
            'destino_cep' => 'required|string',
            'destino_pais' => 'required|string',
            'destino_telefone' => 'required|string',
            'destino_email' => 'required|email',
            
            'altura' => 'required|numeric',
            'largura' => 'required|numeric',
            'comprimento' => 'required|numeric',
            'peso_caixa' => 'required|numeric',
            'servico_entrega' => 'required|string',
        ]);
        
        // Decodificar os produtos do JSON
        $produtos = json_decode($request->produtos_json, true);
        
        // Log detalhado dos produtos recebidos
        Log::info('Produtos recebidos para processamento:', [
            'produtos_json' => $request->produtos_json,
            'produtos_decodificados' => $produtos
        ]);
        
        // Verificar se há produtos
        if (empty($produtos)) {
            return response()->json([
                'success' => false,
                'message' => 'É necessário adicionar pelo menos um produto para o envio.'
            ], 422);
        }
        
        // Calcular peso total (produtos + caixa)
        $pesoTotal = $request->peso_total + $request->peso_caixa;
        
        // Preparar dados para o FedexService
        $dadosRemetente = [
            'nome' => $request->origem_nome,
            'endereco' => $request->origem_endereco,
            'complemento' => $request->origem_complemento ?? '',
            'cidade' => $request->origem_cidade,
            'estado' => $request->origem_estado,
            'cep' => $request->origem_cep,
            'pais' => $request->origem_pais,
            'telefone' => $request->origem_telefone,
            'email' => $request->origem_email
        ];
        
        $dadosDestinatario = [
            'nome' => $request->destino_nome,
            'endereco' => $request->destino_endereco,
            'complemento' => $request->destino_complemento ?? '',
            'cidade' => $request->destino_cidade,
            'estado' => $request->destino_estado,
            'cep' => $request->destino_cep,
            'pais' => $request->destino_pais,
            'telefone' => $request->destino_telefone,
            'email' => $request->destino_email
        ];
        
        $dadosPacote = [
            'altura' => $request->altura,
            'largura' => $request->largura,
            'comprimento' => $request->comprimento,
            'peso' => $pesoTotal
        ];
        
        // Formatar produtos para o formato esperado pela FedexService
        $dadosProdutos = [];
        foreach ($produtos as $produto) {
            $dadosProdutos[] = [
                'descricao' => $produto['descricao'] ?? $produto['nome'],
                'peso' => $produto['peso'],
                'quantidade' => $produto['quantidade'],
                'valor_unitario' => $produto['valor_unitario'] ?? $produto['valor'] ?? 0,
                'pais_origem' => $produto['pais_origem'] ?? 'BR',
                'ncm' => $produto['ncm'] ?? $produto['codigo'] ?? '000000'
            ];
        }
        
        // Forçar simulação em ambiente de desenvolvimento
        $forcarSimulacao = config('app.env') !== 'production' || $request->has('forcarSimulacao');
        
        try {
            // Criar o envio usando o FedexService
            $resultado = $this->fedexService->criarEnvio(
                $dadosRemetente,
                $dadosDestinatario,
                $dadosPacote,
                $dadosProdutos,
                $request->servico_entrega,
                $forcarSimulacao
            );
            
            // Armazenar resultado na sessão para uso posterior
            session(['dados_envio' => [
                'remetente' => $dadosRemetente,
                'destinatario' => $dadosDestinatario,
                'pacote' => $dadosPacote,
                'produtos' => $dadosProdutos,
                'valorTotal' => $request->valor_total,
                'pesoTotal' => $pesoTotal
            ]]);
            
            session(['resultado_envio' => $resultado]);
            
            // Armazenar no cache por 24 horas
            $hash = md5(json_encode($request->all()) . time());
            Cache::put('envio_' . $hash, [
                'dados' => session('dados_envio'),
                'resultado' => $resultado
            ], now()->addDay());
            
            // Se o envio falhou mas temos simulação, adicionar mensagem
            if (isset($resultado['mensagem'])) {
                $mensagem = 'Simulação gerada devido a: ' . $resultado['mensagem'];
            } else {
                $mensagem = $resultado['simulado'] 
                    ? 'Simulação de envio gerada com sucesso. Em produção, este processo irá gerar uma etiqueta real da FedEx.' 
                    : 'Envio processado com sucesso! Você pode imprimir a etiqueta e rastrear seu pacote.';
            }
            
            return response()->json([
                'success' => true,
                'trackingNumber' => $resultado['trackingNumber'],
                'shipmentId' => $resultado['shipmentId'],
                'labelUrl' => $resultado['labelUrl'],
                'servicoContratado' => $resultado['servicoContratado'],
                'dataCriacao' => $resultado['dataCriacao'],
                'simulado' => $resultado['simulado'],
                'message' => $mensagem,
                'hash' => $hash,
                'nextStep' => 'etiqueta' // Direcionar para página de etiqueta
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar envio', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o envio: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ]
            ], 500);
        }
    }
    
    /**
     * Processa o pagamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarPagamento(Request $request)
    {
        // Redirecionar para o novo controlador de pagamentos
        return app()->make(\App\Http\Controllers\PaymentController::class)->process($request);
    }
    
    /**
     * Busca informações de rastreamento de um envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarRastreamento(Request $request)
    {
        $request->validate([
            'codigo_rastreamento' => 'required|string',
        ]);
        
        // Log especial para o código de rastreamento específico
        if ($request->codigo_rastreamento === '794616896420') {
            Log::info('====== CONTROLLER: RASTREAMENTO DE CÓDIGO ESPECIAL ======', [
                'codigo' => $request->codigo_rastreamento,
                'data_hora' => now()->format('Y-m-d H:i:s'),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'forcarSimulacao' => $request->has('forcarSimulacao') && $request->forcarSimulacao === 'true'
            ]);
        }
        
        try {
            // Verificar se estamos forçando simulação
            $forcarSimulacao = $request->has('forcarSimulacao') && $request->forcarSimulacao === 'true';
            
            // Usar o serviço FedEx para rastrear o envio
            $resultado = $this->fedexService->rastrearEnvio(
                $request->codigo_rastreamento,
                true, // incluir scans detalhados
                $forcarSimulacao 
            );
            
            // Log da resposta para o código especial
            if ($request->codigo_rastreamento === '794616896420') {
                Log::info('====== CONTROLLER: RESPOSTA DO RASTREAMENTO ======', [
                    'codigo' => $request->codigo_rastreamento,
                    'sucesso' => $resultado['success'],
                    'status' => $resultado['statusAtual'] ?? '',
                    'entregue' => $resultado['entregue'] ?? false,
                    'simulado' => $resultado['simulado'] ?? true,
                    'eventos_count' => count($resultado['eventos'] ?? []),
                    'mensagem' => $resultado['mensagem'] ?? ''
                ]);
                
                // Verificar se temos mensagem "This is a Virtual Response" no resultado original
                $isVirtualResponse = false;
                if (isset($resultado['respostaOriginal']['output']['alerts'])) {
                    foreach ($resultado['respostaOriginal']['output']['alerts'] as $alert) {
                        if (isset($alert['code']) && $alert['code'] === 'VIRTUAL.RESPONSE') {
                            $isVirtualResponse = true;
                            break;
                        }
                    }
                }
                
                if ($isVirtualResponse) {
                    Log::info('====== CÓDIGO 794616896420: RESPOSTA VIRTUAL DETECTADA ======');
                }
            }
            
            // Se o resultado não tem sucesso, podemos ter alguns alertas que precisamos tratar
            if (!$resultado['success']) {
                // Se for um problema específico com o código de rastreio especial
                if ($request->codigo_rastreamento === '794616896420') {
                    // Vamos verificar se é uma resposta virtual da FedEx
                    if (isset($resultado['respostaOriginal']['output']['alerts'])) {
                        foreach ($resultado['respostaOriginal']['output']['alerts'] as $alert) {
                            if (isset($alert['code']) && $alert['code'] === 'VIRTUAL.RESPONSE') {
                                // É uma resposta virtual, então vamos processar os dados de rastreamento
                                $resultado['success'] = true;
                                // Continuar com o processamento normal, pois a API está retornando dados virtuais válidos
                            }
                        }
                    }
                }
                
                // Se ainda não tiver sucesso, retorna o erro
                if (!$resultado['success']) {
                    return response()->json([
                        'success' => false,
                        'error_code' => 'fedex_api_error',
                        'message' => $resultado['mensagem'] ?? 'Não foi possível obter informações de rastreamento.',
                    ], 200);
                }
            }
            
            // Estruturar resultado para a view
            return response()->json([
                'success' => true,
                'codigo' => $resultado['trackingNumber'],
                'origem' => $resultado['origem'],
                'destino' => $resultado['destino'],
                'dataPostagem' => $resultado['dataPostagem'],
                'dataEntregaPrevista' => $resultado['dataEntregaPrevista'],
                'status' => $resultado['statusAtual'],
                'entregue' => $resultado['entregue'],
                'servicoDescricao' => $resultado['servicoDescricao'],
                'temAtraso' => $resultado['temAtraso'],
                'detalhesAtraso' => $resultado['detalhesAtraso'],
                'ultimaAtualizacao' => $resultado['ultimaAtualizacao'],
                'dataEntrega' => $resultado['dataEntrega'],
                'eventos' => $resultado['eventos'],
                'simulado' => $resultado['simulado'],
                'mensagem' => $resultado['mensagem'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar rastreamento', [
                'error' => $e->getMessage(),
                'codigo' => $request->codigo_rastreamento
            ]);
            
            // Em vez de retornar uma simulação automaticamente, retornamos um erro específico
            // que o front-end identificará para mostrar o modal de escolha
            if (strpos($e->getMessage(), '503') !== false) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'fedex_unavailable',
                    'message' => 'O serviço da FedEx está temporariamente indisponível. Deseja usar uma simulação de rastreamento?',
                    'error_details' => $e->getMessage()
                ], 200); // Retornamos 200 para que o AJAX processe normalmente
            }
            
            // Para outros erros da API
            if (strpos($e->getMessage(), 'Falha no rastreamento.') !== false || 
                strpos($e->getMessage(), 'API') !== false) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'fedex_api_error',
                    'message' => 'Não foi possível obter informações de rastreamento da FedEx. Deseja ver uma simulação?',
                    'error_details' => $e->getMessage()
                ], 200);
            }
            
            // Para erros genéricos
            return response()->json([
                'success' => false,
                'error_code' => 'general_error',
                'message' => 'Erro ao buscar informações de rastreamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Solicita o comprovante de entrega assinado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function solicitarComprovanteEntrega(Request $request)
    {
        $request->validate([
            'codigo_rastreamento' => 'required|string',
            'formato' => 'nullable|string|in:PDF,PNG'
        ]);
        
        $formato = $request->formato ?? 'PDF';
        
        try {
            // Usar o serviço FedEx para obter o comprovante de entrega
            $documento = $this->fedexService->solicitarComprovanteEntrega(
                $request->codigo_rastreamento,
                $formato
            );
            
            if (!$documento['success']) {
                return response()->json([
                    'success' => false,
                    'mensagem' => $documento['mensagem']
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'trackingNumber' => $documento['trackingNumber'],
                'documentType' => $documento['documentType'],
                'documentFormat' => $documento['documentFormat'],
                'document' => $documento['document'], // Este é o documento em Base64
                'simulado' => $documento['simulado']
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao solicitar comprovante de entrega', [
                'error' => $e->getMessage(),
                'codigo' => $request->codigo_rastreamento
            ]);
            
            return response()->json([
                'success' => false,
                'mensagem' => 'Erro ao solicitar comprovante de entrega: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza os dados do perfil do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function atualizarPerfil(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cpf' => 'required|string|max:14',
            'telefone' => 'required|string|max:20',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'cep' => 'required|string|max:10',
        ]);
        
        // Verificar se o usuário está autenticado
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado',
            ], 401);
        }
        
        // Obter o usuário autenticado
        $user = \Illuminate\Support\Facades\Auth::user();
        
        try {
            // Atualizar apenas os campos que existem na tabela users (name e email)
            $user->name = $request->nome;
            $user->email = $request->email;
            $user->save();
            
            // Verificar se o usuário tem um perfil, se não tiver, criar um
            $userProfile = $user->profile;
            if (!$userProfile) {
                $userProfile = new \App\Models\UserProfile();
                $userProfile->user_id = $user->id;
            }
            
            // Construir endereço completo
            $endereco = $request->rua;
            if ($request->numero) {
                $endereco .= ', ' . $request->numero;
            }
            if ($request->complemento) {
                $endereco .= ' - ' . $request->complemento;
            }
            
            // Como não temos uma coluna específica para CPF,
            // vamos armazenar temporariamente no campo company_name
            // Nota: Esta é uma solução temporária até que a estrutura do banco seja atualizada
            $userProfile->company_name = 'CPF:' . $request->cpf;
            
            // Atualizar o perfil com as informações usando os campos existentes na tabela
            $userProfile->phone = $request->telefone;
            $userProfile->address = $endereco;
            $userProfile->city = $request->cidade;
            $userProfile->state = $request->estado;
            $userProfile->zip_code = $request->cep;
            $userProfile->country = 'BR';
            $userProfile->save();
            
            // Formatar dados para retorno
            $userData = [
                'nome' => $user->name,
                'email' => $user->email,
                'cpf' => $request->cpf,
                'telefone' => $userProfile->phone,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'cidade' => $userProfile->city,
                'estado' => $userProfile->state,
                'cep' => $userProfile->zip_code,
                'data_cadastro' => $user->created_at ? $user->created_at->format('d/m/Y') : 'Não informado'
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso',
                'usuario' => $userData
            ]);
            
        } catch (\Exception $e) {
            // Registrar o erro no log
            \Illuminate\Support\Facades\Log::error('Erro ao atualizar perfil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o perfil. Por favor, tente novamente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSection($section)
    {
        // Verificamos quais seções são válidas
        $validSections = ['dashboard', 'cotacao', 'envio', 'rastreamento', 'usuario'];
        
        if (!in_array($section, $validSections)) {
            return response()->json(['error' => 'Seção inválida'], 404);
        }
        
        // Tratamento especial para a seção de cotação
        if ($section === 'cotacao') {
            // Renderizar a view sem usar o helper route() no template
            $cotacaoView = view('sections.cotacao_alt')->render();
            return $cotacaoView;
        }
        
        // Retorna a view da seção solicitada para outras seções
        return view('sections.' . $section);
    }

    /**
     * Recupera uma cotação do cache pelo hash.
     *
     * @param  string  $hash
     * @return array|null
     */
    public function getCotacaoFromCache($hash)
    {
        try {
            $key = 'cotacao_' . $hash;
            $cached = DB::table('cache')
                ->where('key', $key)
                ->where('expiration', '>', time())
                ->first();
                
            if ($cached) {
                return unserialize($cached->value);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao recuperar cotação do cache: ' . $e->getMessage());
        }
        
        return null;
    }
} 