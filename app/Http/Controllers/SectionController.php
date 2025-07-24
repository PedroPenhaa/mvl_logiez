<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\FedexService;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\UserProfile;


class SectionController extends Controller
{
    protected $fedexService;
    
    public function __construct(FedexService $fedexService)
    {
        $this->fedexService = $fedexService;
    }
    
    public function dashboard()
    {
        return view('dashboard');
    }
    
    public function cotacao(Request $request)
    {
        // Verificar se há dados da última cotação FedEx na sessão
        $dados = session('dados_fedex', null);
        $resultado = session('resultado_fedex', null);
        
        // Se for uma requisição AJAX, retornar apenas a view da seção
        if ($request->ajax()) {
            return view('sections.cotacao', compact('dados', 'resultado'))->render();
        }
        
        // Se for acesso direto, retornar a view completa com o layout
        return view('sections.cotacao', compact('dados', 'resultado'));
    }
    
    public function envio(Request $request)
    {
        if ($request->ajax()) {
            return view('sections.envio')->render();
        }
        return view('sections.envio');
    }
    
    public function pagamento(Request $request)
    {
        $userId = Auth::id();
        
        // Verificar se o usuário está autenticado
        if (!\Illuminate\Support\Facades\Auth::check()) {
            $data = [
                'pendingPayments' => collect([]),
                'completedPayments' => collect([]),
                'cancelledPayments' => collect([])
            ];
            
            if ($request->ajax()) {
                return view('sections.pagamento', $data)->render();
            }
            return view('sections.pagamento', $data);
        }
        
        // Carregar os pagamentos do usuário
        $pendingPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('status', ['pending', 'PENDING'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $completedPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('status', ['completed', 'CONFIRMED'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $cancelledPayments = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('status', ['cancelled', 'CANCELLED'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = compact('pendingPayments', 'completedPayments', 'cancelledPayments');
        
        if ($request->ajax()) {
            return view('sections.pagamento', $data)->render();
        }
        return view('sections.pagamento', $data);
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
        if ($request->ajax()) {
            return view('sections.etiqueta')->render();
        }
        return view('sections.etiqueta');
    }
    
    public function rastreamento(Request $request)
    {
        if ($request->ajax()) {
            return view('sections.rastreamento')->render();
        }
        return view('sections.rastreamento');
    }
    
    public function perfil(Request $request)
    {
        // Verificar se o usuário está autenticado
        if (!\Illuminate\Support\Facades\Auth::check()) {
            $data = [
                'usuario' => [
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
                ],
                'shipments' => collect([])
            ];
            
            if ($request->ajax()) {
                return view('sections.perfil', $data)->render();
            }
            return view('sections.perfil', $data);
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
        
        $data = [
            'usuario' => $userData,
            'shipments' => $shipments
        ];
        
        if ($request->ajax()) {
            return view('sections.perfil', $data)->render();
        }
        return view('sections.perfil', $data);
    }

    /**
     * Processa o cálculo de cotação de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularCotacao(Request $request)
    {
        try {
            // Validar os dados de entrada
            $request->validate([
                'origem' => 'required|string',
                'destino' => 'required|string',
                'altura' => 'required|numeric|min:0',
                'largura' => 'required|numeric|min:0',
                'comprimento' => 'required|numeric|min:0',
                'peso' => 'required|numeric|min:0',
            ]);

            // Obter cotação do dólar atual
            $cotacaoDolar = $this->obterCotacaoDolar();
            $valorDolar = $cotacaoDolar['cotacao'] ?? 5.71; // Valor padrão caso a API falhe

            // Obter cotação real da FedEx
            $resultado = $this->fedexService->calcularCotacao(
                $request->origem,
                $request->destino,
                $request->altura,
                $request->largura,
                $request->comprimento,
                $request->peso
            );
            
            if (!$resultado['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $resultado['mensagem'] ?? 'Não foi possível obter a cotação.'
                ], 400);
            }

            // Processar cotações para adicionar conversão de moeda se necessário
            $cotacoesProcessadas = [];
            foreach ($resultado['cotacoesFedEx'] as $cotacao) {
                $cotacaoProcessada = $cotacao;
                
                // Se a moeda for BRL, converter para USD e adicionar valor em BRL
                if ($cotacao['moeda'] === 'BRL') {
                    $valorUSD = $cotacao['valorTotal'] / $valorDolar;
                    $cotacaoProcessada['valorTotal'] = number_format($valorUSD, 2, '.', '');
                    $cotacaoProcessada['moeda'] = 'USD';
                    $cotacaoProcessada['valorTotalBRL'] = number_format($cotacao['valorTotal'], 2, ',', '.');
                } 
                // Se a moeda for USD, adicionar valor em BRL
                else if ($cotacao['moeda'] === 'USD') {
                    $valorBRL = $cotacao['valorTotal'] * $valorDolar;
                    $cotacaoProcessada['valorTotalBRL'] = number_format($valorBRL, 2, ',', '.');
                }
                
                $cotacoesProcessadas[] = $cotacaoProcessada;
            }
            
            $resultado['cotacoesFedEx'] = $cotacoesProcessadas;
            $resultado['cotacaoDolar'] = $valorDolar;
            
            return response()->json([
                'status' => 'success',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao calcular cotação: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao calcular cotação: ' . $e->getMessage()
            ], 500);
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
            // Retornar a view padrão de cotação, igual ao acesso direto
            $dados = session('dados_fedex', null);
            $resultado = session('resultado_fedex', null);
            return view('sections.cotacao', compact('dados', 'resultado'))->render();
        }
        
        // Retorna a view da seção solicitada para outras seções
        return view('sections.' . $section)->render();
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

    /**
     * Retorna as etiquetas do usuário logado (tracking_number != null)
     */
    public function apiListarEtiquetasUsuario(Request $request)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Não autenticado.'], 401);
        }
        $userId = \Illuminate\Support\Facades\Auth::id();
        $etiquetas = \App\Models\Shipment::where('user_id', $userId)
            ->whereNotNull('tracking_number')
            ->orderByDesc('created_at')
            ->get();
        // Retornar apenas os campos necessários para a tabela
        $dados = $etiquetas->map(function($etiqueta) {
            return [
                'id' => $etiqueta->id,
                'tracking_number' => $etiqueta->tracking_number,
                'ship_date' => optional($etiqueta->ship_date)->format('Y-m-d'),
                'recipient_name' => optional($etiqueta->recipientAddress)->name ?? '',
                'recipient_city' => optional($etiqueta->recipientAddress)->city ?? '',
                'recipient_country' => optional($etiqueta->recipientAddress)->country ?? '',
                'status' => $etiqueta->status,
                'label_url' => $etiqueta->label_url,
                'service_name' => $etiqueta->service_name,
            ];
        });
        return response()->json(['success' => true, 'etiquetas' => $dados]);
    }

    /**
     * Gera o array do invoice para um shipment específico
     */
    public function apiInvoiceByShipment($shipment_id)
    {
        $shipment = \App\Models\Shipment::find($shipment_id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Envio não encontrado.'], 404);
        }
        $recipient = $shipment->recipientAddress;
        $items = $shipment->items;
        $cartoons = [];
        $total_qty = 0;
        $total_amount = 0;
        foreach ($items as $item) {
            $cartoons[] = [
                'goods' => $item->description ?? $item->name ?? 'Produto',
                'ncm' => $item->ncm ?? '',
                'qty_utd' => $item->quantity ?? 0,
                'qty_unidade' => $item->unit_type ?? 'PAR',
                'unit_price_usd' => $item->unit_price_usd ?? 0,
                'amount_usd' => $item->total_price_usd ?? 0,
            ];
            $total_qty += $item->quantity ?? 0;
            $total_amount += $item->total_price_usd ?? 0;
        }
        $invoice = [
            'invoice_number' => $shipment->id ? sprintf('#%05d', $shipment->id) : '#00000',
            'date' => $shipment->ship_date ? $shipment->ship_date->format('d/m/y') : now()->format('d/m/y'),
            'terms_of_payment' => 'INTERNACIONAL TRANSFER',
            'purchase_order' => $shipment->quote_id ?? '',
            'shipment' => 'FLIGHT',
            'marks' => 'N/A',
            'loading_airport' => 'VIRACOPOS (VCP)',
            'airport_of_discharge' => 'MIAMI AIRPORT (MIA)',
            'selling_conditions' => 'DAB',
            'pages' => 1,
            'cartoons' => $cartoons,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount,
            'freight' => $shipment->freight_usd ?? 98,
            'volumes' => $shipment->volumes ?? 4,
            'net_weight' => $shipment->net_weight_lbs ?? 37.0392,
            'gross_weight' => $shipment->gross_weight_lbs ?? 35.19,
            'container' => $shipment->container ?? 0,
            'sender' => [
                'name' => 'LS COMÉRCIO ATACADISTA E VAREJISTA LTDA',
                'address' => 'Rua 4, Pq Res. Dona Chiquinha, Cosmópolis - SP - Brazil',
                'contact' => '+55(19) 98116-6445 / envios@logiez.com.br',
                'cnpj' => '48.103.206/0001-73',
            ],
            'recipient' => [
                'name' => $recipient->name ?? 'Destinatário',
                'address' => $recipient->address ?? '',
                'city' => $recipient->city ?? '',
                'state' => $recipient->state ?? '',
                'country' => $recipient->country ?? '',
            ],
        ];
        return response()->json(['success' => true, 'invoice' => $invoice]);
    }

    /**
     * Gera o PDF do invoice para um shipment específico
     */
    public function apiInvoicePdfByShipment($shipment_id)
    {
        ini_set('memory_limit', '512M');
        $shipment = \App\Models\Shipment::find($shipment_id);
        if (!$shipment) {
            abort(404, 'Envio não encontrado.');
        }
        $recipient = $shipment->recipientAddress;
        $items = $shipment->items;
        $cartoons = [];
        $total_qty = 0;
        $total_amount = 0;
        foreach ($items as $item) {
            $cartoons[] = [
                'goods' => $item->description ?? $item->name ?? 'Produto',
                'ncm' => $item->ncm ?? '',
                'qty_utd' => $item->quantity ?? 0,
                'qty_unidade' => $item->unit_type ?? 'PAR',
                'unit_price_usd' => $item->unit_price_usd ?? 0,
                'amount_usd' => $item->total_price_usd ?? 0,
            ];
            $total_qty += $item->quantity ?? 0;
            $total_amount += $item->total_price_usd ?? 0;
        }
        $invoice = [
            'invoice_number' => $shipment->id ? sprintf('#%05d', $shipment->id) : '#00000',
            'date' => $shipment->ship_date ? $shipment->ship_date->format('d/m/y') : now()->format('d/m/y'),
            'terms_of_payment' => 'INTERNACIONAL TRANSFER',
            'purchase_order' => $shipment->quote_id ?? '',
            'shipment' => 'FLIGHT',
            'marks' => 'N/A',
            'loading_airport' => 'VIRACOPOS (VCP)',
            'airport_of_discharge' => 'MIAMI AIRPORT (MIA)',
            'selling_conditions' => 'DAB',
            'pages' => 1,
            'cartoons' => $cartoons,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount,
            'freight' => $shipment->freight_usd ?? 98,
            'volumes' => $shipment->volumes ?? 4,
            'net_weight' => $shipment->net_weight_lbs ?? 37.0392,
            'gross_weight' => $shipment->gross_weight_lbs ?? 35.19,
            'container' => $shipment->container ?? 0,
            'sender' => [
                'name' => 'LS COMÉRCIO ATACADISTA E VAREJISTA LTDA',
                'address' => 'Rua 4, Pq Res. Dona Chiquinha, Cosmópolis - SP - Brazil',
                'contact' => '+55(19) 98116-6445 / envios@logiez.com.br',
                'cnpj' => '48.103.206/0001-73',
            ],
            'recipient' => [
                'name' => $recipient->name ?? 'Destinatário',
                'address' => $recipient->address ?? '',
                'city' => $recipient->city ?? '',
                'state' => $recipient->state ?? '',
                'country' => $recipient->country ?? '',
            ],
        ];
        // PERFORMANCE: opções DomPDF
        $pdf = \PDF::loadView('pdf.invoice', ['invoice' => $invoice]);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
        ]);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('invoice_' . $shipment->id . '.pdf');
    }
} 