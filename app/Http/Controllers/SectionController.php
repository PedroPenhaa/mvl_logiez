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
use App\Models\User;


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
        
        // Obter CPF do campo document_number da tabela users
        $cpf = $user->document_number ?? 'Não informado';
        
        // Obter dados do endereço dos campos separados
        $rua = $user->address ?? '';
        $numero = $user->address_number ?? '';
        $complemento = $user->address_complement ?? '';
        
        // Formatar os dados do usuário para exibição
        $userData = [
            'nome' => $user->name,
            'email' => $user->email,
            'cpf' => $cpf,
            'documento' => $cpf, // Para compatibilidade
            'profile_type' => $user->profile_type ?? 'individual',
            'telefone' => $user->phone ?? 'Não informado',
            'rua' => $rua,
            'numero' => $numero,
            'complemento' => $complemento,
            'cidade' => $user->city ?? 'Não informado',
            'estado' => $user->state ?? 'Não informado',
            'cep' => $user->postal_code ?? 'Não informado',
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
     * Formata o CEP mantendo apenas os 5 primeiros dígitos e completando com zeros
     *
     * @param string $cep
     * @return string
     */
    private function formatarCep($cep) 
    {
        // Remove qualquer caractere não numérico
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        // Pega os 5 primeiros dígitos e completa com zeros
        return substr($cep, 0, 5);
    }

    /**
     * Detectar país baseado no formato do CEP
     *
     * @param string $cep
     * @return string
     */
    private function detectarPais($cep) 
    {
        // Remove caracteres não alfanuméricos
        $cep = preg_replace('/[^A-Za-z0-9]/', '', $cep);
        
        // CEP brasileiro: 8 dígitos (formato: 00000-000)
        if (strlen($cep) === 8 && ctype_digit($cep)) {
            return 'BR';
        }
        
        // ZIP code americano: 9 dígitos (formato: 00000-0000)
        if (strlen($cep) === 9 && ctype_digit($cep)) {
            return 'US';
        }
        
        // Para 5 dígitos, usar heurística baseada no padrão
        if (strlen($cep) === 5 && ctype_digit($cep)) {
            // ZIP codes americanos típicos: 10001-99999
            // CEPs brasileiros típicos: 01000-99999
            // Vamos assumir que se começa com 0, é mais provável ser Brasil
            if (substr($cep, 0, 1) === '0') {
                return 'BR';
            }
            // Se começa com 1-9, é mais provável ser EUA
            return 'US';
        }
        
        // CEP brasileiro com menos de 5 dígitos (assumir Brasil)
        if (strlen($cep) < 5 && ctype_digit($cep)) {
            return 'BR';
        }
        
        // Por padrão, assumir Brasil se não conseguir detectar
        return 'BR';
    }

    /**
     * Calcula a porcentagem baseada no valor conforme as faixas definidas
     *
     * @param float $valor
     * @return float
     */
    private function calcularPorcentagem($valor)
    {
        if ($valor <= 350.00) {
            return 0.30; // 30%
        } elseif ($valor <= 700.00) {
            return 0.27; // 27%
        } elseif ($valor <= 1200.00) {
            return 0.24; // 24%
        } elseif ($valor <= 2000.00) {
            return 0.21; // 21%
        } elseif ($valor <= 3000.00) {
            return 0.19; // 19%
        } else {
            return 0.16; // 16%
        }
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

            // Detectar países baseado nos CEPs
            $paisOrigem = $this->detectarPais($request->origem);
            $paisDestino = $this->detectarPais($request->destino);
            
            // Formatar os CEPs de origem e destino
            $request->merge([
                'origem' => $this->formatarCep($request->origem),
                'destino' => $this->formatarCep($request->destino)
            ]);

            // Obter cotação do dólar atual
            $cotacaoDolar = $this->obterCotacaoDolar();

            $valorDolar = $cotacaoDolar['cotacao'] ?? 5.71; // Valor padrão caso a API falhe

            // Obter cotação real da FedEx
            $resultado = $this->fedexService->calcularCotacao(
                [
                    'postalCode' => $request->origem,
                    'countryCode' => $paisOrigem
                ],
                [
                    'postalCode' => $request->destino,
                    'countryCode' => $paisDestino
                ],
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
                if ($cotacao['moeda'] === 'BRL' || $cotacao['moeda'] === 'USD') {
                    $valorUSD = $cotacao['valorTotal'] / $valorDolar;
                    $cotacaoProcessada['valorTotal'] = number_format($valorUSD, 2, '.', '');
                    $cotacaoProcessada['moeda'] = 'USD';
                    
                    // Aplicar porcentagem ao valor em BRL
                    $porcentagem = $this->calcularPorcentagem($cotacao['valorTotal']);
                    $valorComPorcentagem = $cotacao['valorTotal'] * (1 + $porcentagem);
                    $cotacaoProcessada['valorTotalBRL'] = number_format($valorComPorcentagem, 2, ',', '.');
                } 
                // Se a moeda for USD, adicionar valor em BRL
               /* else if ($cotacao['moeda'] === 'USD') {
                    $valorBRL = $cotacao['valorTotal'] * $valorDolar;
                    $cotacaoProcessada['valorTotalBRL'] = number_format($valorBRL, 2, ',', '.');
                }
                // Se a moeda for EUR, converter para USD e adicionar valor em BRL
                else if ($cotacao['moeda'] === 'EUR') {
                    $valorUSD = $cotacao['valorTotal'] / 0.85; // Supondo taxa de conversão de 0.85
                    $cotacaoProcessada['valorTotal'] = number_format($valorUSD, 2, '.', '');
                    $cotacaoProcessada['moeda'] = 'USD';
                    $valorBRL = $valorUSD * $valorDolar;
                    $cotacaoProcessada['valorTotalBRL'] = number_format($valorBRL, 2, ',', '.');
                }*/
                $cotacoesProcessadas[] = $cotacaoProcessada;
            }
            
            $resultado['cotacoesFedEx'] = $cotacoesProcessadas;
            $resultado['cotacaoDolar'] = $valorDolar;
            
            // Salvar dados na sessão para uso no PDF
            session([
                'cotacao_origem' => $request->origem,
                'cotacao_destino' => $request->destino,
                'cotacao_pais_origem' => $paisOrigem,
                'cotacao_pais_destino' => $paisDestino,
                'cotacao_altura' => $request->altura,
                'cotacao_largura' => $request->largura,
                'cotacao_comprimento' => $request->comprimento,
                'cotacao_peso' => $request->peso,
                'cotacoes_fedex' => $cotacoesProcessadas,
                'peso_cubico' => $resultado['pesoCubico'] ?? 0,
                'peso_real' => $request->peso,
                'peso_utilizado' => $resultado['pesoUtilizado'] ?? $request->peso,
                'cotacao_dolar' => $valorDolar,
                'data_consulta' => date('Y-m-d H:i:s')
            ]);
            
            // Gerar hash para o PDF
            $hash = $this->saveCotacaoToCache($request, $resultado);
            $resultado['hash'] = $hash;
            
            // Salvar cotação na tabela quotes
            try {
                $userId = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null;
                
                // Salvar apenas um registro com a primeira cotação
                if (!empty($cotacoesProcessadas)) {
                    $cotacao = $cotacoesProcessadas[0]; // Pega a primeira cotação
                    
                    \App\Models\Quote::create([
                        'user_id' => $userId,
                        'origin_postal_code' => $request->origem,
                        'origin_country' => $paisOrigem,
                        'destination_postal_code' => $request->destino,
                        'destination_country' => $paisDestino,
                        'package_height' => $request->altura,
                        'package_width' => $request->largura,
                        'package_length' => $request->comprimento,
                        'package_weight' => $request->peso,
                        'carrier' => 'FedEx',
                        'service_code' => $cotacao['codigoServico'] ?? '',
                        'service_name' => $cotacao['nomeServico'] ?? '',
                        'delivery_time_min' => $cotacao['prazoMinimo'] ?? 0,
                        'delivery_time_max' => $cotacao['prazoMaximo'] ?? 0,
                        'total_price' => $cotacao['valorTotal'] ?? 0,
                        'currency' => $cotacao['moeda'] ?? 'USD',
                        'exchange_rate' => $valorDolar,
                        'total_price_brl' => str_replace(['.', ','], ['', '.'], $cotacao['valorTotalBRL'] ?? 0),
                        'request_data' => $request->all(),
                        'response_data' => $resultado,
                        'is_simulation' => $resultado['simulado'] ?? false,
                        'quote_reference' => uniqid('QT'),
                        'expires_at' => now()->addDays(7),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                }
            } catch (\Exception $e) {
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            
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
     * Salva uma cotação no cache e retorna o hash para recuperá-la.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $resultado
     * @return string
     */
    private function saveCotacaoToCache(Request $request, array $resultado)
    {
        // Criar identificador baseado nos parâmetros da cotação (determinístico)
        $paisOrigem = $this->detectarPais($request->origem);
        $paisDestino = $this->detectarPais($request->destino);
        
        $paramsKey = $request->origem . 
                    $request->destino . 
                    $paisOrigem .
                    $paisDestino .
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
            
            return $hash;
        }
        
        // Armazenar dados da cotação no banco de dados
        $paisOrigem = $this->detectarPais($request->origem);
        $paisDestino = $this->detectarPais($request->destino);
        
        $dados = [
            'origem_cep' => $request->origem,
            'origem_pais' => $paisOrigem,
            'destino_cep' => $request->destino,
            'destino_pais' => $paisDestino,
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
                
            } else {
            }
        } catch (\Exception $e) {
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
        // Log para debug - verificar todos os dados recebidos
        Log::info('Dados recebidos em processarEnvio:', [
            'all_data' => $request->all(),
            'freight_value' => $request->freight_value,
            'servico_entrega' => $request->servico_entrega
        ]);
        
        // Validar os dados de entrada
        try {
            $request->validate([
                'produtos_json' => 'required|string',
                'valor_total' => 'required|numeric',
                'peso_total' => 'required|numeric',
                'tipo_operacao' => 'required|string|in:enviar,receber',
                'tipo_pessoa' => 'required|string|in:pf,pj',
                'tipo_envio' => 'required|string',
                
                'origem_nome' => 'required|string',
                'origem_endereco' => 'nullable|string',
                'origem_cidade' => 'nullable|string',
                'origem_estado' => 'nullable|string',
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
                'freight_value' => 'nullable|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação dos dados',
                'errors' => $e->errors()
            ], 422);
        }
        
        // Decodificar os produtos do JSON
        $produtos = json_decode($request->produtos_json, true);
        
        // Verificar se há produtos
        if (empty($produtos)) {
            return response()->json([
                'success' => false,
                'message' => 'É necessário adicionar pelo menos um produto para o envio.'
            ], 422);
        }
        
        // Calcular peso total (produtos + caixa)
        $pesoTotal = $request->peso_total + $request->peso_caixa;
        
        try {
            // Preparar dados para a API da FedEx
            $dadosRemetente = [
                'nome' => $request->origem_nome,
                'endereco' => $request->origem_endereco,
                'complemento' => $request->origem_complemento ?? '',
                'cidade' => $request->origem_cidade,
                'estado' => $request->origem_estado,
                'cep' => $this->validarCodigoPostal($request->origem_cep, $request->origem_pais),
                'pais' => $request->origem_pais,
                'telefone' => $this->limparTelefone($request->origem_telefone),
                'email' => $request->origem_email
            ];
            
            $dadosDestinatario = [
                'nome' => $request->destino_nome,
                'endereco' => $request->destino_endereco,
                'complemento' => $request->destino_complemento ?? '',
                'cidade' => $request->destino_cidade,
                'estado' => $request->destino_estado,
                'cep' => $this->validarCodigoPostal($request->destino_cep, $request->destino_pais),
                'pais' => $request->destino_pais,
                'telefone' => $this->limparTelefone($request->destino_telefone),
                'email' => $request->destino_email
            ];
            
            $dadosPacote = [
                'altura' => (float) $request->altura,
                'largura' => (float) $request->largura,
                'comprimento' => (float) $request->comprimento,
                'peso' => (float) $pesoTotal
            ];
            
            // Formatar produtos para o formato esperado pela API da FedEx
            $dadosProdutos = [];
            foreach ($produtos as $produto) {
                $dadosProdutos[] = [
                    'descricao' => $produto['descricao'] ?? $produto['nome'],
                    'peso' => (float) $produto['peso'],
                    'quantidade' => (int) $produto['quantidade'],
                    'valor_unitario' => (float) ($produto['valor_unitario'] ?? $produto['valor'] ?? 0),
                    'pais_origem' => $produto['pais_origem'] ?? 'BR',
                    'ncm' => $produto['ncm'] ?? $produto['codigo'] ?? '000000',
                    'unidade' => $produto['unidade'] ?? 'UNI' // Incluir unidade do produto
                ];
            }
            
            // Obter o valor do frete da requisição ou da sessão
            $freightValue = $request->freight_value ?? 0;
            if (!$freightValue) {
                $servicoInfo = session('servico_selecionado');
                $freightValue = $servicoInfo['valorTotalBRL'] ?? 0;
            }
            
            // Log para debug
            Log::info('Valor do frete obtido:', [
                'freight_value_request' => $request->freight_value,
                'freight_value_session' => $servicoInfo['valorTotalBRL'] ?? 'não encontrado',
                'freight_value_final' => $freightValue
            ]);
            
            // Criar o envio usando a API da FedEx
            $resultado = $this->criarEnvioFedEx($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $request->servico_entrega, $request->tipo_operacao, $request->tipo_pessoa, $request->tipo_envio, $freightValue);
            
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
            
            return response()->json([
                'success' => true,
                'trackingNumber' => $resultado['trackingNumber'],
                'shipmentId' => $resultado['shipmentId'],
                'labelUrl' => $resultado['labelUrl'],
                'servicoContratado' => $resultado['servicoContratado'],
                'dataCriacao' => $resultado['dataCriacao'],
                'simulado' => $resultado['simulado'],
                'message' => $resultado['message'],
                'hash' => $hash,
                'nextStep' => 'etiqueta'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o envio: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Cria o envio usando a API da FedEx
     */
    private function criarEnvioFedEx($dadosRemetente, $dadosDestinatario, $dadosPacote, $dadosProdutos, $servicoEntrega, $tipoOperacao, $tipoPessoa, $tipoEnvio, $freightValue = 0)
    {
        // Log para debug - verificar se o valor está chegando
        Log::info('Valor do frete recebido em criarEnvioFedEx:', [
            'freightValue' => $freightValue,
            'freightValue_type' => gettype($freightValue)
        ]);
        // Usar as credenciais de produção configuradas no sistema
        $apiUrl = config('services.fedex.api_url');
        $clientId = config('services.fedex.client_id');
        $clientSecret = config('services.fedex.client_secret');
        $shipperAccount = config('services.fedex.shipper_account');
        
        // Obter token de autenticação
        $authUrl = $apiUrl . '/oauth/token';
        
        $tokenPayload = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];
        
        Log::info('🔐 Iniciando autenticação FedEx:', [
            'auth_url' => $authUrl,
            'client_id' => $clientId,
            'grant_type' => 'client_credentials'
        ]);
        
        $authCurl = curl_init();
        curl_setopt_array($authCurl, [
            CURLOPT_URL => $authUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($tokenPayload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded",
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $authResponse = curl_exec($authCurl);
        $authHttpCode = curl_getinfo($authCurl, CURLINFO_HTTP_CODE);
        $authErr = curl_error($authCurl);
        
        curl_close($authCurl);
        
        Log::info('🔐 Resposta da autenticação FedEx:', [
            'http_code' => $authHttpCode,
            'response' => $authResponse,
            'error' => $authErr
        ]);
        
        if ($authErr) {
            throw new \Exception('Erro na autenticação: ' . $authErr);
        }
        
        if ($authHttpCode != 200) {
            throw new \Exception('Falha na autenticação. Código HTTP: ' . $authHttpCode);
        }
        
        $authData = json_decode($authResponse, true);
        $accessToken = $authData['access_token'] ?? null;
        
        Log::info('✅ Token obtido com sucesso:', [
            'token_length' => strlen($accessToken),
            'token_type' => $authData['token_type'] ?? 'unknown'
        ]);
        
        if (!$accessToken) {
            throw new \Exception('Não foi possível obter o token de acesso.');
        }

        // Preparar requisição de criação de envio
        $shipUrl = $apiUrl . '/ship/v1/shipments';
        $transactionId = uniqid('logiez_ship_');
        $shipDate = date('Y-m-d');
        
        // Montar commodities para a alfândega
        $commodities = [];
        foreach ($dadosProdutos as $produto) {
            $commodities[] = [
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
                'harmonizedCode' => $produto['ncm']
            ];
        }
        
        // Construir payload da requisição
        $shipRequest = [
            'labelResponseOptions' => 'URL_ONLY',
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
                    ],
                    'tins' => [
                        [
                            'number' => '12345678901',
                            'tinType' => 'BUSINESS_NATIONAL',
                            'usage' => 'EXPORT'
                        ]
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
                        ],
                        'tins' => [
                            [
                                'number' => '123456789',
                                'tinType' => 'BUSINESS_NATIONAL',
                                'usage' => 'IMPORT'
                            ]
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
                                'value' => $shipperAccount
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
                                    'value' => $shipperAccount
                                ]
                            ]
                        ]
                    ],
                    'commodities' => $commodities,
                    'commercialInvoice' => [
                        'purpose' => 'GIFT'
                    ]
                ]
            ],
            'accountNumber' => [
                'value' => $shipperAccount
            ]
        ];

        Log::info('📦 Payload da requisição de envio FedEx:', [
            'ship_url' => $shipUrl,
            'transaction_id' => $transactionId,
            'ship_date' => $shipDate,
            'service_type' => $servicoEntrega,
            'payload_json' => json_encode($shipRequest, JSON_PRETTY_PRINT)
        ]);

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
                "X-locale: en_US",
                "x-customer-transaction-id: " . $transactionId
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $shipResponse = curl_exec($shipCurl);
        $shipHttpCode = curl_getinfo($shipCurl, CURLINFO_HTTP_CODE);
        $shipErr = curl_error($shipCurl);
        
        curl_close($shipCurl);
        
        Log::info('📦 Resposta da API de envio FedEx:', [
            'http_code' => $shipHttpCode,
            'response' => $shipResponse,
            'error' => $shipErr,
            'response_length' => strlen($shipResponse)
        ]);
        
        // Se houver erro, disparar exceção
        if ($shipErr) {
            throw new \Exception('Erro na requisição de envio: ' . $shipErr);
        }
        
        if ($shipHttpCode != 200) {
            $errorDetails = json_decode($shipResponse, true);
            $errorMessage = 'Resposta da API: ' . substr($shipResponse, 0, 500);
            throw new \Exception('Falha no envio. Código HTTP: ' . $shipHttpCode . "\n" . $errorMessage);
        }
        
        // Processar resposta
        $shipData = json_decode($shipResponse, true);
        
        Log::info('✅ Resultado processado da API FedEx:', [
            'tracking_number' => $shipData['output']['transactionShipments'][0]['masterTrackingNumber'] ?? null,
            'shipment_id' => $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['shipmentId'] ?? null,
            'label_url' => $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['url'] ?? null,
            'response_structure' => array_keys($shipData)
        ]);
        
        // Extrair informações relevantes
        $trackingNumber = $shipData['output']['transactionShipments'][0]['masterTrackingNumber'] ?? null;
        $shipmentId = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['shipmentId'] ?? null;
        $labelUrl = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['url'] ?? null;
        
        // Salvar dados do envio no banco de dados
        try {
            $userId = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null;
            
            // Log para debug antes de criar o shipment
            Log::info('Criando shipment com freight_value:', [
                'freight_value' => $freightValue,
                'tracking_value_type' => gettype($freightValue),
                'tracking_number' => $trackingNumber,
                'user_id' => $userId
            ]);
            
            // Calcular pesos líquido e bruto
            $netWeightLbs = $dadosPacote['peso']; // Peso líquido é o peso do produto
            $grossWeightLbs = $dadosPacote['peso']; // Peso bruto é o mesmo para envios simples
            
            // Criar o registro principal do shipment
            $shipment = \App\Models\Shipment::create([
                'user_id' => $userId,
                'tracking_number' => $trackingNumber,
                'shipment_id' => $shipmentId,
                'carrier' => 'FEDEX',
                'service_code' => $servicoEntrega,
                'service_name' => $servicoEntrega,
                'label_url' => $labelUrl,
                'label_format' => 'PDF',
                'status' => 'created',
                'status_description' => 'Envio criado com sucesso',
                'last_status_update' => now(),
                'package_height' => $dadosPacote['altura'],
                'package_width' => $dadosPacote['largura'],
                'package_length' => $dadosPacote['comprimento'],
                'package_weight' => $dadosPacote['peso'],
                'net_weight_lbs' => $netWeightLbs,
                'gross_weight_lbs' => $grossWeightLbs,
                'volumes' => 1, // Número de volumes/embalagens
                'total_price' => 0, // Será calculado se necessário
                'currency' => 'USD',
                'total_price_brl' => 0, // Será calculado se necessário
                'freight_value' => $freightValue, // Valor do frete escolhido pelo usuário
                'ship_date' => $shipDate,
                'is_simulation' => false,
                'was_delivered' => false,
                'has_issues' => false,
                'tipo_envio' => $tipoEnvio,
                'tipo_pessoa' => $tipoPessoa,
                'tipo_operacao' => $tipoOperacao
            ]);
            
            // Criar endereço do remetente
            \App\Models\SenderAddress::create([
                'shipment_id' => $shipment->id,
                'name' => $dadosRemetente['nome'],
                'phone' => $dadosRemetente['telefone'],
                'email' => $dadosRemetente['email'],
                'address' => $dadosRemetente['endereco'],
                'address_complement' => $dadosRemetente['complemento'] ?? null,
                'city' => $dadosRemetente['cidade'],
                'state' => $dadosRemetente['estado'],
                'postal_code' => $dadosRemetente['cep'],
                'country' => $dadosRemetente['pais'],
                'is_residential' => false
            ]);
            
            // Criar endereço do destinatário
            \App\Models\RecipientAddress::create([
                'shipment_id' => $shipment->id,
                'name' => $dadosDestinatario['nome'],
                'phone' => $dadosDestinatario['telefone'],
                'email' => $dadosDestinatario['email'],
                'address' => $dadosDestinatario['endereco'],
                'address_complement' => $dadosDestinatario['complemento'] ?? null,
                'city' => $dadosDestinatario['cidade'],
                'state' => $dadosDestinatario['estado'],
                'postal_code' => $dadosDestinatario['cep'],
                'country' => $dadosDestinatario['pais'],
                'is_residential' => false
            ]);
            
            // Criar itens do envio
            foreach ($dadosProdutos as $produto) {
                \App\Models\ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'description' => $produto['descricao'],
                    'weight' => $produto['peso'],
                    'quantity' => $produto['quantidade'],
                    'unit_price' => $produto['valor_unitario'],
                    'total_price' => $produto['valor_unitario'] * $produto['quantidade'],
                    'currency' => 'USD',
                    'country_of_origin' => $produto['pais_origem'],
                    'harmonized_code' => $produto['ncm'],
                    'unit_type' => $produto['unidade'] ?? 'UNI' // Unidade do produto (UNI, PAR, etc.)
                ]);
            }
            
            Log::info('✅ Dados do envio salvos no banco com sucesso:', [
                'shipment_id' => $shipment->id,
                'tracking_number' => $trackingNumber
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao salvar dados do envio no banco:', [
                'error' => $e->getMessage(),
                'tracking_number' => $trackingNumber
            ]);
            // Não interromper o fluxo se houver erro no salvamento
        }
        
        $resultado = [
            'success' => true,
            'trackingNumber' => $trackingNumber,
            'shipmentId' => $shipmentId,
            'labelUrl' => $labelUrl,
            'servicoContratado' => $servicoEntrega,
            'dataCriacao' => date('Y-m-d H:i:s'),
            'simulado' => false,
            'message' => 'Envio processado com sucesso! Você pode imprimir a etiqueta e rastrear seu pacote.'
        ];
        
        return $resultado;
    }

    /**
     * Valida e formata códigos postais
     */
    private function validarCodigoPostal($postalCode, $countryCode)
    {
        // Remover caracteres não alfanuméricos
        $postalCode = preg_replace('/[^A-Za-z0-9]/', '', $postalCode);
        
        // Converter para maiúsculas
        $postalCode = strtoupper($postalCode);
        
        switch ($countryCode) {
            case 'BR':
                // CEP brasileiro: sempre usar apenas 4 dígitos
                if (strlen($postalCode) >= 4) {
                    // Pegar os 4 primeiros dígitos e completar com zeros
                    $cep4digitos = substr($postalCode, 0, 4);
                    return $cep4digitos . '0000'; // Sempre retorna 8 dígitos com zeros
                }
                // Se não tiver pelo menos 4 dígitos, retornar um CEP válido de exemplo
                return '01310000'; // CEP válido de São Paulo
                
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
     * Limpa e formata números de telefone
     */
    private function limparTelefone($telefone)
    {
        // Remover todos os caracteres não numéricos
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        // Se o telefone começar com código do país, manter
        if (strlen($telefone) >= 10) {
            return $telefone;
        }
        
        // Se for brasileiro e não tiver código do país, adicionar
        if (strlen($telefone) === 11 && substr($telefone, 0, 2) === '11') {
            return '55' . $telefone;
        }
        
        return $telefone;
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
        return app()->make(\App\Http\Controllers\PaymentController::class)->processar($request);
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
        
        if ($request->codigo_rastreamento === '794616896420') {
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
            'tipo_documento' => 'required|in:individual,business',
            'documento' => 'required|string|max:20',
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
            // 1. ATUALIZAR TABELA USERS
            if ($user instanceof \App\Models\User) {
                $user->name = $request->nome;
                $user->email = $request->email;
                $user->profile_type = $request->tipo_documento; // individual ou business
                $user->document_number = $request->documento; // CPF ou CNPJ
                $user->phone = $request->telefone; // Telefone
                $user->address = $request->rua; // Rua
                $user->address_number = $request->numero; // Número
                $user->address_complement = $request->complemento; // Complemento
                $user->city = $request->cidade; // Cidade
                $user->state = $request->estado; // Estado
                $user->postal_code = $request->cep; // CEP
                $user->country = 'BR'; // País
                
                // Log para debug
                \Illuminate\Support\Facades\Log::info('Salvando dados na tabela users:', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_type' => $user->profile_type,
                    'document_number' => $user->document_number,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'address_number' => $user->address_number,
                    'address_complement' => $user->address_complement,
                    'city' => $user->city,
                    'state' => $user->state,
                    'postal_code' => $user->postal_code
                ]);
                
                $user->save();
                
                // Log após salvar
                \Illuminate\Support\Facades\Log::info('Dados salvos na tabela users com sucesso');
            }
            
            // 2. ATUALIZAR TABELA USER_PROFILES
            // Verificar se o usuário tem um perfil, se não tiver, criar um
            $userProfile = $user->profile;
            if (!$userProfile) {
                $userProfile = new \App\Models\UserProfile();
                $userProfile->user_id = $user->id;
            }
            
            // Construir endereço completo para user_profiles
            $endereco = $request->rua;
            if ($request->numero) {
                $endereco .= ', ' . $request->numero;
            }
            if ($request->complemento) {
                $endereco .= ' - ' . $request->complemento;
            }
            
            // Atualizar o perfil com as informações
            $userProfile->phone = $request->telefone;
            $userProfile->address = $endereco;
            $userProfile->city = $request->cidade;
            $userProfile->state = $request->estado;
            $userProfile->zip_code = $request->cep;
            $userProfile->country = 'BR';
            $userProfile->company_name = 'CPF:' . $request->cpf; // Manter compatibilidade
            
            // Log para debug
            \Illuminate\Support\Facades\Log::info('Salvando dados na tabela user_profiles:', [
                'user_id' => $userProfile->user_id,
                'phone' => $userProfile->phone,
                'address' => $userProfile->address,
                'city' => $userProfile->city,
                'state' => $userProfile->state,
                'zip_code' => $userProfile->zip_code,
                'company_name' => $userProfile->company_name
            ]);
            
            $userProfile->save();
            
            // Log após salvar
            \Illuminate\Support\Facades\Log::info('Dados salvos na tabela user_profiles com sucesso');
            
            // Formatar dados para retorno
            $userData = [
                'nome' => $user->name,
                'email' => $user->email,
                'cpf' => $request->documento, // Para compatibilidade
                'documento' => $request->documento,
                'profile_type' => $request->tipo_documento,
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
            // Log silencioso do erro
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
        $page = $request->get('page', 1);
        $perPage = 6; // 6 etiquetas por página
        
        $query = \App\Models\Shipment::where('user_id', $userId)
            ->whereNotNull('tracking_number')
            ->orderByDesc('created_at');
            
        $total = $query->count();
        $etiquetas = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();
        
        // Retornar apenas os campos necessários para a tabela
        $dados = $etiquetas->map(function($etiqueta) {
            return [
                'id' => $etiqueta->id,
                'tracking_number' => $etiqueta->tracking_number,
                'ship_date' => $etiqueta->created_at->format('Y-m-d H:i:s'),
                'recipient_name' => optional($etiqueta->recipientAddress)->name ?? '',
                'recipient_city' => optional($etiqueta->recipientAddress)->city ?? '',
                'recipient_country' => optional($etiqueta->recipientAddress)->country ?? '',
                'status' => $etiqueta->status,
                'label_url' => $etiqueta->label_url,
                'service_name' => $etiqueta->service_name,
            ];
        });
        
        $totalPages = ceil($total / $perPage);
        
        return response()->json([
            'success' => true, 
            'etiquetas' => $dados,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Gera o array do invoice para um shipment específico
     */
    public function apiInvoiceByShipment($shipment_id)
    {
        $shipment = \App\Models\Shipment::with(['recipientAddress', 'senderAddress', 'items', 'user.userProfile'])->find($shipment_id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Envio não encontrado.'], 404);
        }
        
        $recipient = $shipment->recipientAddress;
        $sender = $shipment->senderAddress;
        $items = $shipment->items;
        $userProfile = $shipment->user->userProfile ?? null;
        
        $cartoons = [];
        $total_qty = 0;
        $total_amount = 0;
        
        foreach ($items as $item) {
            $cartoons[] = [
                'goods' => $item->description ?? 'Produto',
                'ncm' => $item->ncm ?? $item->harmonized_code ?? '',
                'qty_utd' => $item->quantity ?? 0,
                'qty_unidade' => $item->unit_type ?? 'PAR',
                'unit_price_usd' => $item->unit_price_usd ?? $item->unit_price ?? 0,
                'amount_usd' => $item->total_price_usd ?? $item->total_price ?? 0,
            ];
            $total_qty += $item->quantity ?? 0;
            $total_amount += $item->total_price_usd ?? $item->total_price ?? 0;
        }
        
        // Calcular peso em libras se não estiver definido
        $net_weight_lbs = $shipment->net_weight_lbs ?? ($shipment->package_weight * 2.20462);
        $gross_weight_lbs = $shipment->gross_weight_lbs ?? ($shipment->package_weight * 2.20462);
        
        // Montar endereço completo do remetente
        $senderAddress = '';
        if ($sender) {
            $senderAddress = $sender->address;
            if ($sender->address_complement) {
                $senderAddress .= ', ' . $sender->address_complement;
            }
            $senderAddress .= ', ' . $sender->city . ' - ' . $sender->state . ' - ' . $sender->country;
        }
        
        // Montar endereço completo do destinatário
        $recipientAddress = '';
        if ($recipient) {
            $recipientAddress = $recipient->address;
            if ($recipient->address_complement) {
                $recipientAddress .= ', ' . $recipient->address_complement;
            }
            $recipientAddress .= ', ' . $recipient->city . ' - ' . $recipient->state . ' - ' . $recipient->country;
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
            'pages' => 1,
            'cartoons' => $cartoons,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount,
            'freight' => $shipment->freight_value ?? 98,
            'volumes' => $shipment->volumes ?? 1,
            'net_weight' => number_format($net_weight_lbs, 2),
            'gross_weight' => number_format($gross_weight_lbs, 2),
            'sender' => [
                'name' => $sender->name ?? ($userProfile->company_name ?? 'Remetente'),
                'address' => $senderAddress,
                'contact' => $sender->phone ?? ($userProfile->default_sender_phone ?? ''),
            ],
            'recipient' => [
                'name' => $recipient->name ?? 'Destinatário',
                'address' => $recipientAddress,
                'city' => $recipient->city ?? '',
                'state' => $recipient->state ?? '',
                'country' => $recipient->country ?? '',
                'contact' => $recipient->phone ?? '',
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
        $shipment = \App\Models\Shipment::with(['recipientAddress', 'senderAddress', 'items', 'user.userProfile'])->find($shipment_id);
        if (!$shipment) {
            abort(404, 'Envio não encontrado.');
        }
        
        $recipient = $shipment->recipientAddress;
        $sender = $shipment->senderAddress;
        $items = $shipment->items;
        $userProfile = $shipment->user->userProfile ?? null;
        
        $cartoons = [];
        $total_qty = 0;
        $total_amount = 0;
        
        foreach ($items as $item) {
            $cartoons[] = [
                'goods' => $item->description ?? 'Produto',
                'ncm' => $item->ncm ?? $item->harmonized_code ?? '',
                'qty_utd' => $item->quantity ?? 0,
                'qty_unidade' => $item->unit_type ?? 'PAR',
                'unit_price_usd' => $item->unit_price_usd ?? $item->unit_price ?? 0,
                'amount_usd' => $item->total_price_usd ?? $item->total_price ?? 0,
            ];
            $total_qty += $item->quantity ?? 0;
            $total_amount += $item->total_price_usd ?? $item->total_price ?? 0;
        }
        
        // Calcular peso em libras se não estiver definido
        $net_weight_lbs = $shipment->net_weight_lbs ?? ($shipment->package_weight * 2.20462);
        $gross_weight_lbs = $shipment->gross_weight_lbs ?? ($shipment->package_weight * 2.20462);
        
        // Montar endereço completo do remetente
        $senderAddress = '';
        if ($sender) {
            $senderAddress = $sender->address;
            if ($sender->address_complement) {
                $senderAddress .= ', ' . $sender->address_complement;
            }
            $senderAddress .= ', ' . $sender->city . ' - ' . $sender->state . ' - ' . $sender->country;
        }
        
        // Montar endereço completo do destinatário
        $recipientAddress = '';
        if ($recipient) {
            $recipientAddress = $recipient->address;
            if ($recipient->address_complement) {
                $recipientAddress .= ', ' . $recipient->address_complement;
            }
            $recipientAddress .= ', ' . $recipient->city . ' - ' . $recipient->state . ' - ' . $recipient->country;
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
            'pages' => 1,
            'cartoons' => $cartoons,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount,
            'freight' => $shipment->freight_value ?? 98,
            'volumes' => $shipment->volumes ?? 1,
            'net_weight' => number_format($net_weight_lbs, 2),
            'gross_weight' => number_format($gross_weight_lbs, 2),
            'sender' => [
                'name' => $sender->name ?? ($userProfile->company_name ?? 'Remetente'),
                'address' => $senderAddress,
                'contact' => $sender->phone ?? ($userProfile->default_sender_phone ?? ''),
            ],
            'recipient' => [
                'name' => $recipient->name ?? 'Destinatário',
                'address' => $recipientAddress,
                'city' => $recipient->city ?? '',
                'state' => $recipient->state ?? '',
                'country' => $recipient->country ?? '',
                'contact' => $recipient->phone ?? '',
            ],
        ];
        
        // PERFORMANCE: opções DomPDF
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
        ]);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('invoice_' . $shipment->id . '.pdf');
    }
} 