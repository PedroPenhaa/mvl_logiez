<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\FedexService;

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
        return view('sections.pagamento');
    }
    
    public function etiqueta()
    {
        return view('sections.etiqueta');
    }
    
    public function rastreamento()
    {
        return view('sections.rastreamento');
    }
    
    public function perfil()
    {
        // Simulação de dados do usuário para o exemplo
        $user = [
            'nome' => 'João Silva',
            'email' => 'joao.silva@exemplo.com',
            'cpf' => '123.456.789-00',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310-100',
            'rua' => 'Avenida Paulista',
            'numero' => '1000',
            'complemento' => 'Apto 123',
            'telefone' => '(11) 98765-4321'
        ];
        
        return view('sections.perfil', ['usuario' => $user]);
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
            // Usar exatamente o mesmo código do command que está funcionando
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

            // Se forçar simulação, usar o método do FedexService
            if ($request->forcarSimulacao == 'true') {
                $resultado = $this->fedexService->simularCotacao(
                    $request->origem,
                    $request->destino,
                    $request->altura,
                    $request->largura,
                    $request->comprimento,
                    $request->peso
                );
                
                // Gerar um hash para recuperar a cotação posteriormente (para o PDF)
                $hash = md5(uniqid('fedex_quote_') . time());
                
                // Armazenar dados da cotação no cache para recuperação futura
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
                
                Cache::put('cotacao_' . $hash, [
                    'dados' => $dados,
                    'resultado' => $resultado
                ], now()->addDays(7)); // Armazenar por 7 dias
                
                // Construir resposta
                return response()->json([
                    'success' => true,
                    'pesoCubico' => $resultado['pesoCubico'],
                    'pesoReal' => $resultado['pesoReal'],
                    'pesoUtilizado' => $resultado['pesoUtilizado'],
                    'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                    'mensagem' => $resultado['mensagem'] ?? null,
                    'simulado' => $resultado['simulado'] ?? false,
                    'dataConsulta' => $resultado['dataConsulta'],
                    'hash' => $hash
                ]);
            }

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

            // Formatar resultados para serem compatíveis com o frontend
            $resultado = [
                'success' => $rateHttpCode == 200,
                'pesoCubico' => round($pesoCubico, 2),
                'pesoReal' => $peso,
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'cotacoesFedEx' => $cotacoes,
                'simulado' => false,
                'dataConsulta' => date('Y-m-d H:i:s')
            ];

            // Tratar caso em que não há cotações disponíveis
            if (empty($cotacoes) && $rateHttpCode == 200) {
                // Usar simulação neste caso
                $resultadoSimulado = $this->fedexService->simularCotacao(
                    $request->origem,
                    $request->destino,
                    $request->altura,
                    $request->largura,
                    $request->comprimento,
                    $request->peso
                );
                
                $resultado = [
                    'success' => true,
                    'pesoCubico' => round($pesoCubico, 2),
                    'pesoReal' => $peso,
                    'pesoUtilizado' => round($pesoUtilizado, 2),
                    'cotacoesFedEx' => $resultadoSimulado['cotacoesFedEx'],
                    'simulado' => true,
                    'mensagem' => 'Cotação simulada devido a erro na API: Falha na cotação. Código HTTP: ' . $rateHttpCode,
                    'dataConsulta' => date('Y-m-d H:i:s')
                ];
            }
            
            // Gerar um hash para recuperar a cotação posteriormente (para o PDF)
            $hash = md5(uniqid('fedex_quote_') . time());
            
            // Armazenar dados da cotação no cache para recuperação futura
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
            
            Cache::put('cotacao_' . $hash, [
                'dados' => $dados,
                'resultado' => $resultado
            ], now()->addDays(7)); // Armazenar por 7 dias
            
            // Construir resposta
            return response()->json([
                'success' => true,
                'pesoCubico' => $resultado['pesoCubico'],
                'pesoReal' => $resultado['pesoReal'],
                'pesoUtilizado' => $resultado['pesoUtilizado'],
                'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                'mensagem' => $resultado['mensagem'] ?? null,
                'simulado' => $resultado['simulado'] ?? false,
                'dataConsulta' => $resultado['dataConsulta'],
                'hash' => $hash
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao calcular cotação FedEx', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Em caso de erro, usar a simulação automaticamente sem mostrar o modal
            $resultado = $this->fedexService->simularCotacao(
                $request->origem,
                $request->destino,
                $request->altura,
                $request->largura,
                $request->comprimento,
                $request->peso
            );
            
            // Gerar um hash para recuperar a cotação posteriormente (para o PDF)
            $hash = md5(uniqid('fedex_quote_') . time());
            
            // Armazenar dados da cotação no cache para recuperação futura
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
            
            Cache::put('cotacao_' . $hash, [
                'dados' => $dados,
                'resultado' => $resultado
            ], now()->addDays(7)); // Armazenar por 7 dias
            
            // Configurar mensagem de erro
            $resultado['mensagem'] = 'Cotação simulada devido a erro na API: ' . $e->getMessage();
            
            // Retornar simulação em caso de erro
            return response()->json([
                'success' => true,
                'pesoCubico' => $resultado['pesoCubico'],
                'pesoReal' => $resultado['pesoReal'],
                'pesoUtilizado' => $resultado['pesoUtilizado'],
                'cotacoesFedEx' => $resultado['cotacoesFedEx'],
                'mensagem' => $resultado['mensagem'],
                'simulado' => true,
                'dataConsulta' => $resultado['dataConsulta'],
                'hash' => $hash
            ]);
        }
    }
    
    /**
     * Processa os dados de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarEnvio(Request $request)
    {
        // Validar os dados de entrada
        $request->validate([
            'remetente_nome' => 'required|string',
            'remetente_email' => 'required|email',
            'remetente_telefone' => 'required|string',
            'remetente_tipo' => 'required|in:fisica,juridica',
            'remetente_documento' => 'required|string',
            
            'destinatario_nome' => 'required|string',
            'destinatario_email' => 'required|email',
            'destinatario_telefone' => 'required|string',
            'destinatario_endereco' => 'required|string',
            'destinatario_cidade' => 'required|string',
            'destinatario_pais' => 'required|string',
            'destinatario_cep' => 'required|string',
            
            'mercadoria_tipo' => 'required|string',
            'mercadoria_valor' => 'required|numeric',
            'mercadoria_descricao' => 'required|string',
            'mercadoria_altura' => 'required|numeric',
            'mercadoria_largura' => 'required|numeric',
            'mercadoria_comprimento' => 'required|numeric',
            'mercadoria_peso' => 'required|numeric',
            'mercadoria_liquido' => 'required|boolean',
        ]);
        
        // Gerar código de envio (simulação)
        $codigoEnvio = 'DHL' . rand(100000000, 999999999);
        
        // Em produção, aqui seria feita a gravação no banco de dados
        // e integração com a API da DHL para iniciar o processo de envio
        
        return response()->json([
            'success' => true,
            'codigoEnvio' => $codigoEnvio,
            'message' => 'Dados de envio processados com sucesso.',
            'nextStep' => 'pagamento'
        ]);
    }
    
    /**
     * Processa o pagamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarPagamento(Request $request)
    {
        // Validação depende do método de pagamento
        $request->validate([
            'metodo' => 'required|in:cartao,boleto,pix',
            'valorTotal' => 'required|numeric',
            'codigoEnvio' => 'required|string',
        ]);
        
        // Validação específica para cartão de crédito
        if ($request->metodo === 'cartao') {
            $request->validate([
                'cartao_numero' => 'required|string',
                'cartao_nome' => 'required|string',
                'cartao_validade' => 'required|string',
                'cartao_cvv' => 'required|string',
                'cartao_parcelas' => 'required|integer|min:1|max:12',
            ]);
        }
        
        // Simulação de processamento de pagamento
        // Em produção, aqui seria feita a integração com gateway de pagamento
        
        // Gerar código de transação
        $codigoTransacao = 'TRX' . rand(10000000, 99999999);
        
        return response()->json([
            'success' => true,
            'codigoTransacao' => $codigoTransacao,
            'codigoEnvio' => $request->codigoEnvio,
            'valorPago' => $request->valorTotal,
            'metodoPagamento' => $request->metodo,
            'message' => 'Pagamento processado com sucesso.',
            'nextStep' => 'etiqueta'
        ]);
    }
    
    /**
     * Busca informações de rastreamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarRastreamento(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
        ]);
        
        // Simulação de rastreamento
        // Em produção, aqui seria feita a integração com a API da DHL
        
        // Gerar histórico de eventos de forma aleatória
        $eventos = $this->gerarEventosRastreamento($request->codigo);
        
        return response()->json([
            'success' => true,
            'codigo' => $request->codigo,
            'origem' => 'São Paulo, Brasil',
            'destino' => 'Miami, Estados Unidos',
            'dataPostagem' => '2023-10-15',
            'status' => $eventos[0]['status'],
            'eventos' => $eventos
        ]);
    }
    
    /**
     * Gera eventos simulados de rastreamento.
     *
     * @param  string  $codigo
     * @return array
     */
    private function gerarEventosRastreamento($codigo)
    {
        $eventos = [];
        $statusOptions = [
            'Objeto postado',
            'Em trânsito',
            'Saiu para entrega',
            'Entregue ao destinatário',
            'Aguardando retirada',
            'Em processo de desembaraço',
            'Pagamento de taxa necessário'
        ];
        
        $locaisOptions = [
            'São Paulo, Brasil',
            'Rio de Janeiro, Brasil',
            'Miami, Estados Unidos',
            'Nova York, Estados Unidos',
            'Frankfurt, Alemanha',
            'Londres, Reino Unido',
            'Tóquio, Japão'
        ];
        
        // Data base (hoje menos alguns dias)
        $dataBase = strtotime('-10 days');
        
        // Número aleatório de eventos (entre 3 e 7)
        $numEventos = rand(3, 7);
        
        for ($i = 0; $i < $numEventos; $i++) {
            // Data do evento (incrementa alguns dias a cada evento)
            $dataEvento = date('Y-m-d H:i:s', $dataBase + ($i * rand(8, 24) * 3600));
            
            // Status para esse evento (último evento tem 50% de chance de ser entrega)
            $status = ($i === $numEventos - 1 && rand(0, 1) === 1) 
                ? 'Entregue ao destinatário' 
                : $statusOptions[array_rand($statusOptions)];
            
            // Local do evento
            $local = $locaisOptions[array_rand($locaisOptions)];
            
            // Adicionar evento ao array
            $eventos[] = [
                'data' => $dataEvento,
                'status' => $status,
                'local' => $local,
                'detalhe' => $this->gerarDetalheEvento($status)
            ];
        }
        
        // Ordenar eventos por data (mais recente primeiro)
        usort($eventos, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        
        return $eventos;
    }
    
    /**
     * Gera detalhes para o evento de rastreamento.
     *
     * @param  string  $status
     * @return string
     */
    private function gerarDetalheEvento($status)
    {
        $detalhes = [
            'Objeto postado' => 'Objeto postado pelo remetente',
            'Em trânsito' => 'Objeto em trânsito para o destino',
            'Saiu para entrega' => 'Objeto saiu para entrega ao destinatário',
            'Entregue ao destinatário' => 'Objeto entregue ao destinatário',
            'Aguardando retirada' => 'Objeto disponível para retirada em unidade',
            'Em processo de desembaraço' => 'Objeto em processo de desembaraço alfandegário',
            'Pagamento de taxa necessário' => 'Pagamento de taxa alfandegária necessário para liberação'
        ];
        
        return $detalhes[$status] ?? 'Evento registrado no sistema';
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
        
        // Em produção, aqui seria feita a atualização no banco de dados
        
        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'usuario' => $request->all()
        ]);
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
} 