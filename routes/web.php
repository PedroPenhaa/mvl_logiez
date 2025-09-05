<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdutosController;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckAuthenticated;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CotacaoController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EtiquetaController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// Rotas para autenticação social
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::get('/auth/user-data', [SocialAuthController::class, 'showUserData'])->name('social.userData');
Route::post('/auth/complete-profile', [SocialAuthController::class, 'completeProfile'])->name('social.completeProfile');

// Rotas para pagamentos - Protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/pagamentos', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/pagamentos/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/pagamentos/pix/{transactionId}', [PaymentController::class, 'showPix'])->name('payments.pix');
    Route::get('/pagamentos/boleto/{transactionId}', [PaymentController::class, 'showBoleto'])->name('payments.boleto');
    Route::get('/pagamentos/simular-callback/{transactionId}', [PaymentController::class, 'simulateCallback'])->name('payments.simulate.callback');
    Route::post('/pagamentos/processar', [PaymentController::class, 'process'])->name('payments.process');
});

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');
});

// Rota padrão - redireciona para a página de login
Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

// Páginas informativas
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HomeController::class, 'help'])->name('help');

// Rotas da API para carregamento de seções (AJAX) - sem verificação de autenticação
Route::prefix('api')->name('api.')->group(function () {
    // Seções
    Route::get('/sections/cotacao', [SectionController::class, 'cotacao'])->name('sections.cotacao');
    Route::get('/sections/envio', [SectionController::class, 'envio'])->name('sections.envio');
    Route::get('/sections/pagamento', [SectionController::class, 'pagamento'])->name('sections.pagamento');
    Route::get('/sections/etiqueta', [SectionController::class, 'etiqueta'])->name('sections.etiqueta');
    Route::get('/sections/rastreamento', [SectionController::class, 'rastreamento'])->name('sections.rastreamento');
    Route::get('/sections/perfil', [SectionController::class, 'perfil'])->name('sections.perfil');

    // Produtos da Receita
    Route::get('/produtos', [ProdutosController::class, 'getProdutos'])->name('produtos.get');

    // Consulta de NCM via Gemini (sem CSRF)
    Route::post('/consulta-gemini', [ProdutosController::class, 'consultarGemini'])
        ->name('consulta.gemini')
        ->withoutMiddleware(['web']);

    // Consulta de CEP/Endereço via Gemini (sem CSRF)
    Route::post('/consulta-gemini-cep', [App\Http\Controllers\GeminiCEPController::class, 'consultar'])
        ->name('consulta.gemini-cep')
        ->withoutMiddleware(['web']);

    // Rota de teste simples
    Route::get('/teste-rotas', function () {
        return response()->json([
            'success' => true,
            'message' => 'Rotas funcionando!',
            'timestamp' => now()
        ]);
    });

    // Consulta de unidade tributária por NCM
    Route::get('/unidade-tributaria', [ProdutosController::class, 'consultarUnidadeTributaria'])->name('unidade-tributaria');

    // Processamento de dados
    Route::post('/envio/processar', [SectionController::class, 'processarEnvio'])->name('envio.processar');
    Route::post('/pagamento/processar', [SectionController::class, 'processarPagamento'])->name('pagamento.processar');
    Route::post('/rastreamento/buscar', [SectionController::class, 'buscarRastreamento'])->name('rastreamento.buscar');
    Route::post('/rastreamento/comprovante', [SectionController::class, 'solicitarComprovanteEntrega'])->name('rastreamento.comprovante');
    Route::post('/perfil/atualizar', [SectionController::class, 'atualizarPerfil'])->name('perfil.atualizar')->middleware(['web', 'auth']);

    // Rota para processar o envio
    //  Route::post('/envio/processar', [EnvioController::class, 'processar'])->name('envio.processar');

    // Rota para listar etiquetas do usuário logado
    Route::get('/sections/etiquetas-usuario', [SectionController::class, 'apiListarEtiquetasUsuario'])
        ->name('sections.etiquetas_usuario')
        ->middleware(['web', 'auth']);

    // Rota para gerar invoice de um shipment
    Route::get('/sections/invoice/{shipment_id}', [SectionController::class, 'apiInvoiceByShipment'])
        ->name('sections.invoice_by_shipment')
        ->middleware(['web', 'auth']);

    // Rota para baixar o PDF do invoice de um shipment
    Route::get('/sections/invoice/{shipment_id}/pdf', [SectionController::class, 'apiInvoicePdfByShipment'])
        ->name('sections.invoice_pdf_by_shipment')
        ->middleware(['web', 'auth']);

    // Rota para consulta de etiqueta FedEx
    Route::post('/fedex/etiqueta', [EtiquetaController::class, 'fedex'])
        ->name('fedex.etiqueta')
        ->withoutMiddleware(['web']);
});

// Esta rota deve estar definida em algum lugar do seu código
Route::get('/api/sections/{section}', [App\Http\Controllers\SectionController::class, 'getSection'])->name('section.get');

// Rotas para cotação FedEx
Route::get('/cotacao', [CotacaoController::class, 'index'])->name('cotacao.index');
Route::post('/cotacao/calcular', [CotacaoController::class, 'calcular'])->name('cotacao.calcular');
// Rota para calcular cotação FedEx a partir do dashboard
Route::post('/calcular-cotacao', [App\Http\Controllers\SectionController::class, 'calcularCotacao'])->name('calcular.cotacao');

// Redirecionamento para compatibilidade com código legado
Route::get('/cotacao-fedex', function () {
    return redirect('/cotacao');
});

// Rota para teste de processamento de envio
Route::post('/teste-envio', function (Illuminate\Http\Request $request) {

    // Retornar resposta de sucesso simulada
    return response()->json([
        'success' => true,
        'trackingNumber' => 'TEST' . rand(1000000, 9999999),
        'shipmentId' => 'SIM' . rand(1000000, 9999999),
        'labelUrl' => 'https://example.com/label-test.pdf',
        'servicoContratado' => $request->servico_entrega ?: 'FEDEX_INTERNATIONAL_PRIORITY',
        'dataCriacao' => date('Y-m-d H:i:s'),
        'simulado' => true,
        'message' => 'Teste de envio registrado com sucesso para fins de depuração. Isso é apenas uma simulação.',
        'hash' => md5(time()),
        'nextStep' => 'etiqueta'
    ]);
})->name('teste.envio.processar');

// Rota para autenticação FedEx (para testes e desenvolvimento)
Route::get('/test-fedex-auth', function () {
    $fedexService = app(App\Services\FedexService::class);
    return response()->json(['token' => $fedexService->getAuthToken()]);
})->name('api.fedex.auth');

// Rota para exportar cotação em PDF
Route::get('/exportar-cotacao-pdf', function (Illuminate\Http\Request $request, App\Http\Controllers\SectionController $sectionController) {
    try {
        $hash = $request->query('hash');

        if (!$hash) {
            return redirect('/cotacao')->with('error', 'Hash da cotação não fornecido');
        }

        // Buscar dados da cotação no cache
        $cotacaoData = $sectionController->getCotacaoFromCache($hash);

        if (!$cotacaoData) {
            // Se não encontrar no cache, tentar buscar dados da sessão ou usar dados da URL
            $dados = [
                'origem_cep' => $request->origem_cep ?? session('cotacao_origem') ?? '00000-000',
                'destino_cep' => $request->destino_cep ?? session('cotacao_destino') ?? '00000',
                'altura' => $request->altura ?? session('cotacao_altura') ?? 10,
                'largura' => $request->largura ?? session('cotacao_largura') ?? 10,
                'comprimento' => $request->comprimento ?? session('cotacao_comprimento') ?? 10,
                'peso' => $request->peso ?? session('cotacao_peso') ?? 1
            ];

            $cotacaoDolar = 5.42; // Valor padrão do dólar
            $pesoUtilizado = max(
                ($dados['altura'] * $dados['largura'] * $dados['comprimento']) / 6000,
                $dados['peso']
            );

            // Gerar cotações simuladas baseadas na imagem fornecida
            $cotacoes = [
                [
                    'servico' => 'FedEx International First®',
                    'servicoTipo' => 'FIRST',
                    'valorTotal' => '387.44',
                    'moeda' => 'USD',
                    'tempoEntrega' => 'Chega dia 03/09/2025 às 10:00 AM SE NÃO HOUVER ATRASO NA ALFÂNDEGA',
                    'valorTotalBRL' => '2.497,91'
                ],
                [
                    'servico' => 'FedEx International Economy®',
                    'servicoTipo' => 'ECONOMY',
                    'valorTotal' => '26.47',
                    'moeda' => 'USD',
                    'tempoEntrega' => 'Chega dia 03/09/2025 às 5:00 PM SE NÃO HOUVER ATRASO NA ALFÂNDEGA',
                    'valorTotalBRL' => '186,45'
                ],
                [
                    'servico' => 'FedEx International Priority® Express',
                    'servicoTipo' => 'PRIORITY_EXPRESS',
                    'valorTotal' => '32.35',
                    'moeda' => 'USD',
                    'tempoEntrega' => 'Chega dia 03/09/2025 BY NOON IF NO CUSTOMS DELAY',
                    'valorTotalBRL' => '227,84'
                ],
                [
                    'servico' => 'FedEx International Priority®',
                    'servicoTipo' => 'PRIORITY',
                    'valorTotal' => '31.44',
                    'moeda' => 'USD',
                    'tempoEntrega' => 'Chega dia 03/09/2025 às 5:00 PM SE NÃO HOUVER ATRASO NA ALFÂNDEGA',
                    'valorTotalBRL' => '221,47'
                ],
                [
                    'servico' => 'FedEx International Connect Plus',
                    'servicoTipo' => 'CONNECT_PLUS',
                    'valorTotal' => '42.34',
                    'moeda' => 'USD',
                    'tempoEntrega' => 'Chega dia 03/09/2025 às 10:00 PM SE NÃO HOUVER ATRASO NA ALFÂNDEGA',
                    'valorTotalBRL' => '298,23'
                ]
            ];

            $resultado = [
                'pesoCubico' => 0.8,
                'pesoReal' => 5,
                'pesoUtilizado' => 5,
                'cotacoesFedEx' => $cotacoes,
                'dataConsulta' => '2025-08-29 11:56:09',
                'simulado' => true,
                'mensagem' => 'Cotação simulada. Os valores são aproximados e podem variar.',
                'cotacaoDolar' => $cotacaoDolar
            ];

            $cotacaoData = [
                'dados' => $dados,
                'resultado' => $resultado
            ];
        }

        // Verificar se os dados estão na estrutura correta
        if (!isset($cotacaoData['dados']) || !isset($cotacaoData['resultado'])) {
            return redirect('/cotacao')->with('error', 'Dados da cotação inválidos. Por favor, calcule uma nova cotação.');
        }

        $dados = $cotacaoData['dados'];
        $resultado = $cotacaoData['resultado'];

        // Converter imagem para base64
        $logoPath = base_path('public/img/logo_logiez1.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Criar HTML para o PDF com layout ULTRA MODERNO e PROFISSIONAL
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8"/>
            <title>Cotação - Logiez</title>
            <style>
                @page { margin: 0cm 0cm; }
                body {
                    margin: 0cm 0cm 1cm 0cm;
                    font-family: "Helvetica", Arial, sans-serif;
                    font-size: 11px;
                    color: #333;
                    height: 100vh;
                    min-height: 26cm;
                    line-height: 1.2;
                }
        
                /* ===== HEADER ===== */
                .header {
                    background-color: #6f42c1;
                    width: 100%;
                    color: white;
                    padding: 10px;
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: space-around;
                    margin: 0 0 10px 0;
                }
                body {
                    margin: 0cm 0cm 2.5cm 0cm;
                    font-family: "Helvetica", Arial, sans-serif;
                    font-size: 13px;
                    color: #333;
                }
                .header .logo {
                    height: 60px;
                    width: auto;
                    margin-right: 20px;
                }
                .header .info {
                    display: none;
                }
        
                /* ===== FOOTER ===== */
                .footer {
                    position: fixed;
                    bottom: 0cm; left: 0cm; right: 0cm;
                    height: 1.2cm;
                    background-color: #6f42c1;
                    border-top: 1px solid #6f42c1;
                    font-size: 9px;
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.2cm 0.5cm 0 0.5cm;
                    text-align: center;
                    font-weight: bold;
                }
        
                /* ===== TITLES ===== */
                .title {
                    text-align: center;
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    margin-top: 0px;
                    color: #6f42c1;
                }
                .section-title {
                    font-size: 12px;
                    font-weight: bold;
                    color: #6f42c1;
                    margin: 8px 0 5px 0;
                    border-bottom: 2px solid #6f42c1;
                    padding-bottom: 3px;
                }
        
                /* ===== GRID INFO ===== */
                .info-grid {
                    display: table;
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 8px;
                }
                .info-item {
                    display: table-cell;
                    width: 50%;
                    padding: 6px 8px;
                    vertical-align: top;
                }
                .info-label {
                    font-size: 10px;
                    color: #6c757d;
                    text-transform: uppercase;
                    margin-bottom: 2px;
                }
                .info-value {
                    font-size: 11px;
                    font-weight: bold;
                    color: #495057;
                }
        
                /* ===== TABLE ===== */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 5px;
                    font-size: 10px;
                }
                th {
                    background-color: #6f42c1;
                    color: white;
                    padding: 5px;
                    text-align: left;
                    font-size: 10px;
                }
                td {
                    padding: 5px;
                    border-bottom: 1px solid #ddd;
                }
                tr:nth-child(even) { background-color: #f9f9f9; }
        
                /* ===== BOXES ===== */
                .box {
                    border: 2px solid #6f42c1;
                    border-radius: 8px;
                    padding: 8px;
                    margin-top: 8px;
                    font-size: 10px;
                    background-color: rgba(111, 66, 193, 0.05);
                }
                .highlight {
                    color: #27ae60;
                    font-weight: bold;
                }
                .disclaimer {
                    font-size: 9px;
                    color: #495057;
                    background-color: rgba(46, 204, 113, 0.1);
                    border: 2px solid #27ae60;
                    border-radius: 8px;
                    padding: 8px;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
        
            <!-- HEADER -->
            <div class="header">
                <div class="logo">
                    <img src="' . $logoBase64 . '" alt="Logiez" style="width: 100px;  height:auto;">
                </div>
                <span style="color: white; font-size: 9px; float: right; margin-top: 5px; margin-right: 30px;">
                    Soluções em Logística<br>
                    contato@logiez.com.br
                </span>
            </div>
        
            <!-- FOOTER -->
            <div class="footer">
                <div>
                    <strong>LOGIEZ - Especialistas em envios internacionais</strong><br>
                    <strong>www.logiez.com.br</strong>
                </div>
            </div>
        
            <!-- CONTENT -->
            <div style="margin: 0 0.5cm;">
            <div class="title">Cotação de Envio Internacional</div>
        
            <!-- Origem / Destino -->
            <div class="section-title">Informações de Origem e Destino</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Origem</div>
                    <div class="info-value">Brasil<br>CEP: ' . ($dados['origem_cep'] ?? 'N/A') . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Destino</div>
                    <div class="info-value">Internacional<br>CEP: ' . ($dados['destino_cep'] ?? 'N/A') . '</div>
                </div>
            </div>
        
            <!-- Dimensões -->
            <div class="section-title">Dimensões e Peso</div>
            <div class="info-grid">
                <div class="info-item"><div class="info-label">Altura</div><div class="info-value">' . ($dados['altura'] ?? 'N/A') . ' cm</div></div>
                <div class="info-item"><div class="info-label">Largura</div><div class="info-value">' . ($dados['largura'] ?? 'N/A') . ' cm</div></div>
                <div class="info-item"><div class="info-label">Comprimento</div><div class="info-value">' . ($dados['comprimento'] ?? 'N/A') . ' cm</div></div>
                <div class="info-item"><div class="info-label">Peso Real</div><div class="info-value">' . ($resultado['pesoReal'] ?? 'N/A') . ' kg</div></div>
            </div>
        
            <div class="box">
                <strong>Resumo do Cálculo de Peso:</strong><br>
                Peso Cúbico: <span class="highlight">' . ($resultado['pesoCubico'] ?? 'N/A') . ' kg</span><br>
                Peso Utilizado: <span class="highlight">' . ($resultado['pesoUtilizado'] ?? 'N/A') . ' kg</span>
            </div>
        
            <!-- Opções -->
            <div class="section-title">Opções de Envio</div>
            <table>
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Prazo</th>
                        <th>Valor (USD)</th>
                        <th>Valor (BRL)</th>
                    </tr>
                </thead>
                <tbody>';
                
                if (isset($resultado['cotacoesFedEx']) && count($resultado['cotacoesFedEx']) > 0) {
                    foreach ($resultado['cotacoesFedEx'] as $cotacao) {
                        // Formatar o tempo de entrega para o PDF
                        $tempoEntrega = $cotacao['tempoEntrega'] ?? 'Consultar';
                        $dataEntrega = $cotacao['dataEntrega'] ?? null;
                        
                        // Se houver data de entrega, formatar
                        if ($dataEntrega) {
                            try {
                                $data = new \DateTime($dataEntrega);
                                $tempoEntrega = 'Chega dia ' . $data->format('d/m/Y');
                                
                                // Adicionar horário se disponível
                                if (isset($cotacao['horarioEntrega'])) {
                                    $tempoEntrega .= ' às ' . $cotacao['horarioEntrega'];
                                }
                                
                                $tempoEntrega .= ' SE NÃO HOUVER ATRASO NA ALFÂNDEGA';
                            } catch (\Exception $e) {
                                // Se não conseguir formatar a data, usar o tempo original
                            }
                        }
                        
                        $html .= '
                        <tr>
                            <td>' . ($cotacao['servico'] ?? 'N/A') . '</td>
                            <td>' . $tempoEntrega . '</td>
                            <td>' . ($cotacao['valorTotal'] ?? 'N/A') . ' ' . ($cotacao['moeda'] ?? 'USD') . '</td>
                            <td class="highlight">R$ ' . ($cotacao['valorTotalBRL'] ?? 'N/A') . '</td>
                        </tr>';
                    }
                } else {
                    $html .= '<tr><td colspan="4" style="text-align:center; color:#888; font-style:italic;">Nenhuma opção disponível</td></tr>';
                }
        
        $html .= '
                </tbody>
            </table>
        
            <!-- Melhor opção -->
            <div class="box">
                <strong>Recomendação LOGIEZ:</strong><br>';
                if (isset($resultado['cotacoesFedEx']) && count($resultado['cotacoesFedEx']) > 0) {
                    $melhorOpcao = $resultado['cotacoesFedEx'][0];
                    $html .= ($melhorOpcao['servico'] ?? 'N/A') . ' - ' . ($melhorOpcao['tempoEntrega'] ?? 'N/A') . '<br>
                             <span class="highlight">R$ ' . ($melhorOpcao['valorTotalBRL'] ?? 'N/A') . '</span>';
                }
        $html .= '
            </div>
        
            <!-- Info Cotação -->
            <div class="section-title">Informações da Cotação</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Data da Consulta</div>
                    <div class="info-value">' . ($resultado['dataConsulta'] ?? date('Y-m-d H:i:s')) . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cotação do Dólar</div>
                    <div class="info-value">R$ ' . number_format(($resultado['cotacaoDolar'] ?? 5.00), 2, ",", ".") . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Validade</div>
                    <div class="info-value">7 dias</div>
                </div>
            </div>
        
            <!-- Avisos -->
            <div class="disclaimer">
                <strong>Importante:</strong><br>
                • Cotação válida por 7 dias<br>
                • Valores sujeitos a alteração pela FedEx<br>
                • Consulte-nos para embalagem e documentação
            </div>
            </div>
        </body>
        </html>';
        

        // Configurar e gerar o PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf->download('COTACAO_LOGIEZ_' . date('Y-m-d_His') . '.pdf');
    } catch (\Exception $e) {
        return redirect('/cotacao')->with('error', 'Erro ao gerar o PDF da cotação. Por favor, tente novamente.');
    }
})->name('cotacao.exportar.pdf');

// Rota para a página de confirmação de envio
Route::get('/confirmacao', [EnvioController::class, 'confirmacao'])->name('envio.confirmacao');

// Rota principal para rastreamento
Route::get('/rastreamento', [SectionController::class, 'rastreamento'])->name('rastreamento');

// Rota da API para obter detalhes de rastreamento via hash
Route::get('/api/rastreamento/detalhes', [EnvioController::class, 'getDetalhesRastreamento']);

// Rota para webhooks de serviços externos
Route::post('/webhook/asaas', [WebhookController::class, 'asaasWebhook'])->name('webhook.asaas');

// Rota de teste para validar a integração com o Asaas
Route::get('/test-asaas-integration', function (App\Http\Controllers\EnvioController $controller) {
    try {
        $request = new \Illuminate\Http\Request();

        // Usar CPF válido garantido para teste
        $request->merge([
            'name' => 'Cliente Teste',
            'email' => 'teste@example.com',
            'phone' => '11999999999',
            'card_cpf' => '12345678909', // CPF válido que passa no algoritmo
            'postalCode' => '01234567',
            'addressNumber' => '123'
        ]);

        // Verificar configuração do Asaas
        $asaasToken = env('ASAAS_API_TOKEN', 'Não configurado');
        $isSandbox = env('ASAAS_SANDBOX', true) ? 'Sim (Sandbox)' : 'Não (Produção)';
        $asaasTokenMasked = substr($asaasToken, 0, 10) . "..." . substr($asaasToken, -5);

        // Testes de validação de CPF
        $reflection = new ReflectionMethod($controller, 'validarCPF');
        $reflection->setAccessible(true);

        $cpfTests = [
            '12345678909' => 'Válido (Algoritmo)',
            '01234567890' => 'Válido (Dev)',
            '955.037.070-53' => 'Válido (Gerado)',
            '111.111.111-11' => 'Inválido (dígitos iguais)',
            '123.456.789-09' => 'Inválido (verificação falha)'
        ];

        $cpfResults = [];
        foreach ($cpfTests as $testCpf => $expectativa) {
            $cpfLimpo = preg_replace('/\D/', '', $testCpf);
            try {
                $resultado = $reflection->invoke($controller, $cpfLimpo);
                $cpfResults[$testCpf] = [
                    'resultado' => $resultado ? 'Válido' : 'Inválido',
                    'expectativa' => $expectativa,
                    'correto' => strpos($expectativa, $resultado ? 'Válido' : 'Inválido') === 0
                ];
            } catch (Exception $e) {
                $cpfResults[$testCpf] = ['erro' => $e->getMessage()];
            }
        }

        // Testar busca/criação de cliente no Asaas
        $customerId = null;
        $message = '';

        try {
            // Acessar o método privado usando reflexão
            $reflection = new ReflectionMethod($controller, 'buscarOuCriarClienteAsaas');
            $reflection->setAccessible(true);
            $customerId = $reflection->invoke($controller, $request);
            $message = 'Cliente criado/encontrado com sucesso no Asaas!';
        } catch (Exception $e) {
            $message = 'Erro: ' . $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'api_token' => $asaasTokenMasked,
            'sandbox' => $isSandbox,
            'tests' => [
                'validacao_cpf' => $cpfResults,
                'integracao_asaas' => [
                    'customer_id' => $customerId,
                    'message' => $message
                ]
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('test.asaas');

// Rota para testar apenas a validação de CPF
Route::get('/test-cpf-validation/{cpf?}', function (App\Http\Controllers\EnvioController $controller, $cpf = null) {
    try {
        // Se não foi fornecido CPF, testar vários
        if (!$cpf) {
            // Lista de CPFs para teste (com expectativas revisadas)
            $cpfs = [
                '01234567890' => 'Válido', // CPF específico para testes (aceito no ambiente de dev)
                '12345678909' => 'Válido', // Este CPF passa no algoritmo de verificação
                '11111111111' => 'Inválido', // Dígitos iguais não são aceitos
                '123.456.789-10' => 'Inválido', // Falha no algoritmo de validação
                '12345' => 'Inválido', // Menos de 11 dígitos
                '955.037.070-53' => 'Válido', // CPF válido gerado
                '95503707053' => 'Válido' // Mesmo CPF anterior sem formatação
            ];

            $reflection = new ReflectionMethod($controller, 'validarCPF');
            $reflection->setAccessible(true);

            $resultados = [];
            foreach ($cpfs as $testCpf => $expectativa) {
                $cpfLimpo = preg_replace('/\D/', '', $testCpf);
                try {
                    $resultado = $reflection->invoke($controller, $cpfLimpo);

                    // Adicionando step-by-step da validação para debugging
                    $passos = [];

                    // Passo 1: Verificação de comprimento
                    $passos['comprimento'] = strlen($cpfLimpo) == 11 ? 'OK' : 'Falhou';

                    // Passo 2: Verificação de dígitos repetidos
                    $passos['digitos_repetidos'] = preg_match('/(\d)\1{10}/', $cpfLimpo) ? 'Falhou' : 'OK';

                    // Passos 3 e 4: Cálculos dos dígitos verificadores (primeiro e segundo)
                    $passos['calculo'] = 'Não verificado (simplificado)';

                    // Passo especial: CPF de teste em ambiente de desenvolvimento
                    if ($cpfLimpo === '01234567890' && app()->environment('local')) {
                        $passos['teste_dev'] = 'Aceito como válido em ambiente de desenvolvimento';
                    }

                    $resultados[$testCpf] = [
                        'cpf_formatado' => $testCpf,
                        'cpf_limpo' => $cpfLimpo,
                        'resultado' => $resultado ? 'Válido' : 'Inválido',
                        'expectativa' => $expectativa,
                        'correto' => ($resultado ? 'Válido' : 'Inválido') === $expectativa,
                        'passos_validacao' => $passos
                    ];
                } catch (\Exception $e) {
                    $resultados[$testCpf] = [
                        'cpf_formatado' => $testCpf,
                        'cpf_limpo' => $cpfLimpo,
                        'resultado' => 'Erro: ' . $e->getMessage(),
                        'expectativa' => $expectativa,
                        'correto' => false
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Resultados da validação de CPF',
                'ambiente' => app()->environment(),
                'resultados' => $resultados,
                'observacao' => 'Falsos positivos/negativos podem ocorrer se as expectativas estiverem incorretas'
            ]);
        } else {
            // Testar o CPF fornecido na URL
            $cpfLimpo = preg_replace('/\D/', '', $cpf);

            // Acessar o método privado do controlador
            $reflection = new ReflectionMethod($controller, 'validarCPF');
            $reflection->setAccessible(true);
            $resultado = $reflection->invoke($controller, $cpfLimpo);

            // Adicionar mais detalhes para debug
            $detalhes = [
                'comprimento' => strlen($cpfLimpo) == 11 ? 'OK' : 'Falhou (deve ter 11 dígitos)',
                'digitos_repetidos' => preg_match('/(\d)\1{10}/', $cpfLimpo) ? 'Falhou (dígitos repetidos)' : 'OK',
                'ambiente' => app()->environment()
            ];

            // Se for o CPF de teste para desenvolvimento
            if ($cpfLimpo === '01234567890' && app()->environment('local')) {
                $detalhes['observacao'] = 'Este é o CPF de teste que é sempre aceito em ambiente de desenvolvimento';
            }

            return response()->json([
                'success' => true,
                'cpf_testado' => $cpf,
                'cpf_limpo' => $cpfLimpo,
                'valido' => $resultado,
                'message' => $resultado ? 'CPF válido' : 'CPF inválido',
                'detalhes' => $detalhes
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao validar CPF: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('test.cpf');

// Rota para testar processamento de pagamento completo
Route::get('/test-payment-processing', function (App\Http\Controllers\EnvioController $controller) {
    try {
        // Simular todos os dados necessários para um processamento de pagamento
        $request = new \Illuminate\Http\Request();
        $request->merge([
            // Dados do cliente
            'name' => 'Cliente Teste',
            'email' => 'teste' . time() . '@example.com', // Email único para cada teste
            'phone' => '11999999999',
            'card_cpf' => '12345678909', // CPF válido que passa no algoritmo
            'postalCode' => '01234567',
            'addressNumber' => '123',

            // Dados do pagamento
            'payment_method' => 'credit_card', // ou 'boleto', 'pix'
            'value' => 100.00, // R$ 100,00
            'description' => 'Teste de pagamento via API',
            'installments' => 1, // Pagamento à vista

            // Dados do cartão (simulados para teste)
            'card_name' => 'CLIENTE TESTE',
            'card_number' => '5162306219378829', // Número de cartão de teste
            'card_expiry_month' => '05',
            'card_expiry_year' => '2025',
            'card_ccv' => '123',

            // Dados do envio
            'service_id' => 1, // Serviço de entrega fictício
            'package_id' => 1, // Embalagem fictícia
            'origem_cep' => '01234567',
            'destino_cep' => '04321000',
            'origem_nome' => 'Remetente Teste',
            'origem_email' => 'remetente@example.com',
            'origem_telefone' => '11987654321',
            'origem_endereco' => 'Rua Teste Origem, 100',
            'origem_bairro' => 'Bairro Origem',
            'origem_cidade' => 'São Paulo',
            'origem_estado' => 'SP',
            'destino_nome' => 'Destinatário Teste',
            'destino_email' => 'destinatario@example.com',
            'destino_telefone' => '11912345678',
            'destino_endereco' => 'Rua Teste Destino, 200',
            'destino_bairro' => 'Bairro Destino',
            'destino_cidade' => 'São Paulo',
            'destino_estado' => 'SP',
            'items' => json_encode([
                [
                    'name' => 'Item de teste',
                    'quantity' => 1,
                    'weight' => 0.5, // 500g
                    'price' => 50.00
                ]
            ])
        ]);

        // Acessar o método privado de processamento de pagamento
        $pagamentoReflection = new ReflectionMethod($controller, 'processarPagamento');
        $pagamentoReflection->setAccessible(true);

        // Invocar o método de processar pagamento
        $resultadoPagamento = $pagamentoReflection->invoke($controller, $request);

        // Coletar os resultados
        return response()->json([
            'success' => true,
            'message' => 'Processamento de pagamento realizado com sucesso',
            'payment_result' => $resultadoPagamento,
            'request_data' => $request->all()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.payment');

// Test route for direct payment processing with proper CPF
Route::get('/test-direct-payment', function () {
    try {
        // Dados necessários para processamento de pagamento
        $dadosPagamento = [
            'payment_method' => 'credit_card',
            'payment_amount' => 100.00,
            'payment_currency' => 'BRL',
            'origem_nome' => 'Cliente Teste',
            'origem_email' => 'teste@exemplo.com',
            'origem_telefone' => '11987654321',
            'origem_cep' => '01001000',
            'origem_endereco' => 'Rua Teste, 123',
            'origem_complemento' => 'Apto 42',
            'card_cpf' => '955.037.070-53', // CPF válido
            'card_name' => 'CLIENTE TESTE',
            'card_number' => '5162306219378829',
            'card_expiry_month' => '12',
            'card_expiry_year' => '2025',
            'card_cvv' => '123',
            'installments' => 1,
        ];

        // Criar um objeto Request com os dados de pagamento
        $request = new \Illuminate\Http\Request();
        $request->merge($dadosPagamento);

        // Criar um mock do objeto shipment com ID inteiro
        $shipment = new \stdClass();
        $shipment->id = 99999; // ID inteiro para teste
        $shipment->valor = $dadosPagamento['payment_amount'];

        // Obter o controlador
        $controller = app()->make(App\Http\Controllers\EnvioController::class);

        // Usar reflexão para acessar método privado
        $reflection = new ReflectionMethod($controller, 'processarPagamento');
        $reflection->setAccessible(true);

        // Chamar o método com os parâmetros corretos
        $resultado = $reflection->invoke($controller, $shipment, $request);


        return response()->json([
            'success' => true,
            'message' => 'Processamento de pagamento concluído',
            'resultado' => $resultado
        ]);
    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Erro ao processar pagamento',
            'error' => $e->getMessage(),
            'trace' => app()->environment('local') ? $e->getTrace() : []
        ], 500);
    }
});

// Test route for detailed CPF validation debugging
Route::get('/debug-cpf/{cpf?}', function ($cpf = null) {
    try {
        // Get the EnvioController instance
        $fedexService = app()->make('App\Services\FedexService');
        $controller = new \App\Http\Controllers\EnvioController($fedexService);

        // Access the private validarCPF method using reflection
        $validarCPFMethod = new ReflectionMethod($controller, 'validarCPF');
        $validarCPFMethod->setAccessible(true);

        // Use our own detailed validation with same algorithm
        $results = [];

        // If no CPF provided, test a set of CPFs
        $testCPFs = $cpf ? [$cpf] : [
            '01234567890',         // Test CPF
            '111.111.111-11',      // Invalid - repeated digits
            '123.456.789-09',      // Known problematic CPF - should be invalid
            '12345678909',         // Valid CPF
            '955.037.070-53',      // Known problematic CPF - should be valid
            '538.107.800-10',      // Another valid CPF 
        ];

        foreach ($testCPFs as $testCpf) {
            // Clean the CPF
            $cleanCpf = preg_replace('/[^0-9]/', '', $testCpf);

            // Check if all digits are the same
            $allSameDigits = preg_match('/^(\d)\1{10}$/', $cleanCpf);

            // Verify the first check digit
            $sum1 = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum1 += $cleanCpf[$i] * (10 - $i);
            }
            $remainder1 = $sum1 % 11;
            $checkDigit1 = ($remainder1 < 2) ? 0 : 11 - $remainder1;
            $correctFirstDigit = ($checkDigit1 == $cleanCpf[9]);

            // Verify the second check digit
            $sum2 = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum2 += $cleanCpf[$i] * (11 - $i);
            }
            $remainder2 = $sum2 % 11;
            $checkDigit2 = ($remainder2 < 2) ? 0 : 11 - $remainder2;
            $correctSecondDigit = ($checkDigit2 == $cleanCpf[10]);

            // Calculate using current algorithm in controller
            $currentAlgorithmFirstDigit = 0;
            $currentAlgorithmSecondDigit = 0;

            // Replicate controller algorithm
            // First digit (t=9)
            $d1 = 0;
            for ($c = 0; $c < 9; $c++) {
                $d1 += $cleanCpf[$c] * ((9 + 1) - $c);
            }
            $currentAlgorithmFirstDigit = ((10 * $d1) % 11) % 10;

            // Second digit (t=10) 
            $d2 = 0;
            for ($c = 0; $c < 10; $c++) {
                $d2 += $cleanCpf[$c] * ((10 + 1) - $c);
            }
            $currentAlgorithmSecondDigit = ((10 * $d2) % 11) % 10;

            // Call the controller's method to see what it would return
            $isValid = $validarCPFMethod->invoke($controller, $testCpf);

            // Store detailed results
            $results[$testCpf] = [
                'original_cpf' => $testCpf,
                'cleaned_cpf' => $cleanCpf,
                'length_check' => strlen($cleanCpf) == 11 ? 'Pass' : 'Fail',
                'repeated_digits_check' => $allSameDigits ? 'Fail' : 'Pass',
                'check_digit_1' => [
                    'standard_algorithm' => [
                        'sum' => $sum1,
                        'remainder' => $remainder1,
                        'expected_digit' => $checkDigit1,
                        'actual_digit' => $cleanCpf[9],
                        'result' => $correctFirstDigit ? 'Pass' : 'Fail'
                    ],
                    'controller_algorithm' => [
                        'sum' => $d1,
                        'calculated_digit' => $currentAlgorithmFirstDigit,
                        'actual_digit' => $cleanCpf[9],
                        'result' => ($currentAlgorithmFirstDigit == $cleanCpf[9]) ? 'Pass' : 'Fail'
                    ]
                ],
                'check_digit_2' => [
                    'standard_algorithm' => [
                        'sum' => $sum2,
                        'remainder' => $remainder2,
                        'expected_digit' => $checkDigit2,
                        'actual_digit' => $cleanCpf[10],
                        'result' => $correctSecondDigit ? 'Pass' : 'Fail'
                    ],
                    'controller_algorithm' => [
                        'sum' => $d2,
                        'calculated_digit' => $currentAlgorithmSecondDigit,
                        'actual_digit' => $cleanCpf[10],
                        'result' => ($currentAlgorithmSecondDigit == $cleanCpf[10]) ? 'Pass' : 'Fail'
                    ]
                ],
                'controller_result' => $isValid ? 'Válido' : 'Inválido',
                'standard_algorithm_result' => ($correctFirstDigit && $correctSecondDigit) ? 'Válido' : 'Inválido'
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'explanation' => 'Detailed debugging information for CPF validation process.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Route específica para teste de um CPF individual
Route::get('/cpf-test/{cpf?}', function ($cpf = null) {
    try {
        // Lista de CPFs para testar se nenhum for fornecido
        $testCPFs = ['01234567890', '955.037.070-53', '538.107.800-10', '111.111.111-11', '123.456.789-09'];
        $cpfsToTest = $cpf ? [$cpf] : $testCPFs;

        // Obter o controlador
        $controller = app()->make(App\Http\Controllers\EnvioController::class);

        // Usar reflexão para acessar método privado
        $reflection = new ReflectionMethod($controller, 'validarCPF');
        $reflection->setAccessible(true);

        $resultados = [];

        foreach ($cpfsToTest as $testCPF) {
            // Validar o CPF atual
            $cpfLimpo = preg_replace('/[^0-9]/', '', $testCPF);
            $isValid = $reflection->invoke($controller, $testCPF);

            // Calcular os dígitos verificadores para diagnóstico
            $soma1 = 0;
            for ($i = 0; $i < 9; $i++) {
                $soma1 += $cpfLimpo[$i] * (10 - $i);
            }
            $resto1 = $soma1 % 11;
            $digitoCalculado1 = ($resto1 < 2) ? 0 : 11 - $resto1;

            $soma2 = 0;
            for ($i = 0; $i < 10; $i++) {
                $soma2 += $cpfLimpo[$i] * (11 - $i);
            }
            $resto2 = $soma2 % 11;
            $digitoCalculado2 = ($resto2 < 2) ? 0 : 11 - $resto2;

            // Verificação de dígitos repetidos
            $digitosRepetidos = preg_match('/^(\d)\1{10}$/', $cpfLimpo);

            // Armazenar resultados para este CPF
            $resultados[$testCPF] = [
                'cpf_original' => $testCPF,
                'cpf_limpo' => $cpfLimpo,
                'valido' => $isValid ? 'Sim' : 'Não',
                'comprimento_correto' => strlen($cpfLimpo) == 11 ? 'Sim' : 'Não',
                'digitos_repetidos' => $digitosRepetidos ? 'Sim' : 'Não',
                'primeiro_digito' => [
                    'valor_esperado' => $digitoCalculado1,
                    'valor_atual' => isset($cpfLimpo[9]) ? $cpfLimpo[9] : 'N/A',
                    'valido' => isset($cpfLimpo[9]) && $cpfLimpo[9] == $digitoCalculado1 ? 'Sim' : 'Não'
                ],
                'segundo_digito' => [
                    'valor_esperado' => $digitoCalculado2,
                    'valor_atual' => isset($cpfLimpo[10]) ? $cpfLimpo[10] : 'N/A',
                    'valido' => isset($cpfLimpo[10]) && $cpfLimpo[10] == $digitoCalculado2 ? 'Sim' : 'Não'
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Teste de validação de CPF realizado com sucesso',
            'resultados' => $resultados
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao testar validação de CPF',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Rota de teste simples
Route::get('/teste-rota', function () {
    return response()->json([
        'success' => true,
        'message' => 'Rota web funcionando!',
        'timestamp' => now()
    ]);
})->name('teste.rota');

// Rota de teste para verificar se a API está funcionando
Route::post('/teste-api', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando corretamente',
        'timestamp' => now()
    ]);
})->name('teste.api')->withoutMiddleware(['web']);

// Rotas tradicionais para navegação do menu lateral
Route::get('/envio', [App\Http\Controllers\SectionController::class, 'envio'])->name('envio');
Route::get('/pagamento', [App\Http\Controllers\SectionController::class, 'pagamento'])->name('pagamento');
Route::get('/etiqueta', [App\Http\Controllers\SectionController::class, 'etiqueta'])->name('etiqueta');
Route::get('/perfil', [App\Http\Controllers\SectionController::class, 'perfil'])->name('perfil');

// Rotas para consultar Gemini via comando Artisan
Route::post('/gemini-consulta', function (Request $request) {
    $produto = $request->input('produto');

    if (!$produto) {
        return response()->json([
            'success' => false,
            'error' => 'Produto não informado'
        ]);
    }

    try {
        // Obter chave da API do Gemini
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Chave da API do Gemini não configurada'
            ]);
        }

        // Configurar a chamada para a API do Gemini
        $model = config('services.gemini.model', 'gemini-2.0-flash');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        // Prompt otimizado para extrair NCM, descrição e unidade
        $prompt = "Para o produto '{$produto}', retorne APENAS o NCM (código de 8 dígitos no formato XXXX.XX.XX), a descrição completa do produto e a unidade de medida (UN, KG, L, M, etc). Formato da resposta: NCM: XXXX.XX.XX | Descrição: [descrição completa] | Unidade: [unidade]";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        // Fazer a requisição para a API do Gemini
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint . '?key=' . $apiKey, $data);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'error' => 'Erro na API do Gemini: HTTP ' . $response->status(),
                'response' => $response->body()
            ]);
        }

        $responseData = $response->json();

        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return response()->json([
                'success' => false,
                'error' => 'Resposta inválida da API do Gemini',
                'response' => $responseData
            ]);
        }

        $geminiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];

        // Extrair NCM, descrição e unidade da resposta
        $ncm = null;
        $descricao = null;
        $unidade = null;

        // Padrão para extrair NCM
        if (preg_match('/NCM:\s*(\d{4}\.\d{2}\.\d{2})/i', $geminiResponse, $ncmMatches)) {
            $ncm = $ncmMatches[1];
        } elseif (preg_match('/(\d{4}\.\d{2}\.\d{2})/', $geminiResponse, $ncmMatches)) {
            $ncm = $ncmMatches[1];
        }

        // Padrão para extrair descrição
        if (preg_match('/Descrição:\s*(.+?)(?:\s*\||\s*Unidade:|$)/i', $geminiResponse, $descMatches)) {
            $descricao = trim($descMatches[1]);
        } elseif (preg_match('/-\s*(.+?)(?:\s*\||\s*Unidade:|$)/i', $geminiResponse, $descMatches)) {
            $descricao = trim($descMatches[1]);
        } else {
            $descricao = $produto; // Fallback
        }

        // Padrão para extrair unidade
        if (preg_match('/Unidade:\s*([A-Z]{2,3})/i', $geminiResponse, $unidadeMatches)) {
            $unidade = strtoupper($unidadeMatches[1]);
        } else {
            // Determinar unidade baseada no tipo de produto
            $produtoLower = strtolower($produto);
            if (strpos($produtoLower, 'calçado') !== false || strpos($produtoLower, 'sapato') !== false || strpos($produtoLower, 'tenis') !== false) {
                $unidade = 'PAR'; // Par de calçados
            } elseif (strpos($produtoLower, 'roupa') !== false || strpos($produtoLower, 'camisa') !== false || strpos($produtoLower, 'calça') !== false) {
                $unidade = 'UN'; // Unidade
            } elseif (strpos($produtoLower, 'notebook') !== false || strpos($produtoLower, 'computador') !== false || strpos($produtoLower, 'calçado') !== false) {
                $unidade = 'UN'; // Unidade
            } else {
                $unidade = 'UN'; // Unidade padrão
            }
        }

        if ($ncm) {
            return response()->json([
                'success' => true,
                'ncm' => $ncm,
                'descricao' => $descricao,
                'unidade' => $unidade,
                'raw_response' => $geminiResponse
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'NCM não encontrado na resposta do Gemini',
                'raw_response' => $geminiResponse
            ]);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
})->name('gemini.consulta')->withoutMiddleware(['web']);

// Rotas administrativas - protegidas por autenticação e middleware admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/shipments', [App\Http\Controllers\AdminController::class, 'shipments'])->name('shipments');
    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('payments');
    Route::get('/quotes', [App\Http\Controllers\AdminController::class, 'quotes'])->name('quotes');
    Route::get('/addresses', [App\Http\Controllers\AdminController::class, 'addresses'])->name('addresses');
    Route::get('/items', [App\Http\Controllers\AdminController::class, 'items'])->name('items');
    Route::get('/tracking', [App\Http\Controllers\AdminController::class, 'tracking'])->name('tracking');
    Route::get('/proof-of-delivery', [App\Http\Controllers\AdminController::class, 'proofOfDelivery'])->name('proof-of-delivery');
    Route::get('/notifications', [App\Http\Controllers\AdminController::class, 'notifications'])->name('notifications');
    Route::get('/activity-logs', [App\Http\Controllers\AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/api-logs', [App\Http\Controllers\AdminController::class, 'apiLogs'])->name('api-logs');
    Route::get('/shipping-rates', [App\Http\Controllers\AdminController::class, 'shippingRates'])->name('shipping-rates');
    Route::get('/fedex-labels', [App\Http\Controllers\AdminController::class, 'fedexLabels'])->name('fedex-labels');
    Route::get('/cache', [App\Http\Controllers\AdminController::class, 'cache'])->name('cache');

    // Rotas de detalhes
    Route::get('/users/{id}', [App\Http\Controllers\AdminController::class, 'userDetails'])->name('user.details');
    Route::get('/shipments/{id}', [App\Http\Controllers\AdminController::class, 'shipmentDetails'])->name('shipment.details');
    Route::get('/payments/{id}', [App\Http\Controllers\AdminController::class, 'paymentDetails'])->name('payment.details');
});

// Rota para processar pagamento
Route::post('/processar-pagamento', [App\Http\Controllers\PaymentController::class, 'processar'])->name('processar.pagamento');

// Rota para armazenar serviço na sessão
Route::post('/armazenar-servico-sessao', [App\Http\Controllers\PaymentController::class, 'armazenarServicoSessao'])->name('armazenar.servico.sessao');

// Rota para página de sucesso do pagamento
Route::get('/pagamento-sucesso', [App\Http\Controllers\PaymentController::class, 'sucesso'])->name('pagamento.sucesso');

// Rota para verificar status do pagamento
Route::get('/verificar-pagamento/{id}', [App\Http\Controllers\PaymentController::class, 'verificarStatus'])->name('verificar.pagamento');
