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

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function() {
        return view('dashboard', [
            'dashboardContent' => view('sections.dashboard')->render()
        ]);
    })->name('dashboard');
});

// Rota padrão - redireciona para a página welcome
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

// Páginas informativas
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HomeController::class, 'help'])->name('help');

// Rotas da API para carregamento de seções (AJAX) - sem verificação de autenticação
Route::prefix('api')->name('api.')->group(function () {
    // Seções
    Route::get('/sections/dashboard', [SectionController::class, 'dashboard'])->name('sections.dashboard');
    Route::get('/sections/cotacao', [SectionController::class, 'cotacao'])->name('sections.cotacao');
    Route::get('/sections/envio', [SectionController::class, 'envio'])->name('sections.envio');
    Route::get('/sections/pagamento', [SectionController::class, 'pagamento'])->name('sections.pagamento');
    Route::get('/sections/etiqueta', [SectionController::class, 'etiqueta'])->name('sections.etiqueta');
    Route::get('/sections/rastreamento', [SectionController::class, 'rastreamento'])->name('sections.rastreamento');
    Route::get('/sections/perfil', [SectionController::class, 'perfil'])->name('sections.perfil');
    
    // Produtos da Receita
    Route::get('/produtos', [ProdutosController::class, 'getProdutos'])->name('produtos.get');
    
    // Consulta de NCM via Gemini
    Route::post('/consulta-gemini', [ProdutosController::class, 'consultarGemini'])->name('consulta.gemini');
    
    // Consulta de unidade tributária por NCM
    Route::get('/unidade-tributaria', [ProdutosController::class, 'consultarUnidadeTributaria'])->name('unidade-tributaria');
    
    // Processamento de dados
    Route::post('/envio/processar', [SectionController::class, 'processarEnvio'])->name('envio.processar');
    Route::post('/pagamento/processar', [SectionController::class, 'processarPagamento'])->name('pagamento.processar');
    Route::post('/rastreamento/buscar', [SectionController::class, 'buscarRastreamento'])->name('rastreamento.buscar');
    Route::post('/rastreamento/comprovante', [SectionController::class, 'solicitarComprovanteEntrega'])->name('rastreamento.comprovante');
    Route::post('/perfil/atualizar', [SectionController::class, 'atualizarPerfil'])->name('perfil.atualizar');
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
Route::post('/teste-envio', function(Illuminate\Http\Request $request) {
    // Registrar no log
    \Illuminate\Support\Facades\Log::info('TESTE-ENVIO: Dados recebidos', [
        'all' => $request->all(),
        'produtos_json' => $request->produtos_json,
        'origem_nome' => $request->origem_nome,
        'destino_nome' => $request->destino_nome,
        'altura' => $request->altura,
        'largura' => $request->largura,
        'comprimento' => $request->comprimento,
        'peso_caixa' => $request->peso_caixa,
        'servico_entrega' => $request->servico_entrega
    ]);
    
    // Retornar resposta de sucesso simulada
    return response()->json([
        'success' => true,
        'trackingNumber' => 'TEST'.rand(1000000, 9999999),
        'shipmentId' => 'SIM'.rand(1000000, 9999999),
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
Route::get('/test-fedex-auth', function() {
    $fedexService = app(App\Services\FedexService::class);
    return response()->json(['token' => $fedexService->getAuthToken()]);
})->name('api.fedex.auth');

// Rota para exportar cotação em PDF
Route::get('/exportar-cotacao-pdf', function (Illuminate\Http\Request $request, App\Http\Controllers\SectionController $sectionController) {
    $hash = $request->query('hash');
    
    if (!$hash) {
        return redirect('/cotacao')->with('error', 'Cotação não encontrada ou expirada');
    }
    
    $cotacaoData = $sectionController->getCotacaoFromCache($hash);
    
    if (!$cotacaoData) {
        return redirect('/cotacao')->with('error', 'Cotação não encontrada ou expirada');
    }
    
    $dados = $cotacaoData['dados'];
    $resultado = $cotacaoData['resultado'];
    
    // Criar HTML para o PDF
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cotação FedEx</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .container { width: 100%; margin: 0 auto; }
        .header { padding: 10px 0; border-bottom: 2px solid #4472C4; margin-bottom: 20px; }
        .logo { float: left; width: 150px; }
        .document-title { float: right; font-size: 20px; color: #4472C4; margin-top: 20px; }
        .clearfix:after { content: ""; display: table; clear: both; }
        .info-box { background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .box-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #4472C4; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .rates-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .rates-table th { background-color: #4472C4; color: white; font-weight: bold; text-align: left; padding: 8px; }
        .rates-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10px; color: #777; }
        .alert { padding: 10px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <div class="logo">
                <strong style="font-size: 24px; color: #4472C4;">LOGIEZ</strong><br>
                <span style="font-size: 12px;">Soluções em Logística</span>
            </div>
            <div class="document-title">
                COTAÇÃO DE FRETE FedEx
            </div>
        </div>
        
        <div class="info-box">
            <div class="box-title">Informações da Cotação</div>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="top">
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Origem:</strong> ' . $dados['origem_cep'] . ' (' . $dados['origem_pais'] . ')</div>
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Destino:</strong> ' . $dados['destino_cep'] . ' (' . $dados['destino_pais'] . ')</div>
                    </td>
                    <td width="50%" valign="top">
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Dimensões:</strong> ' . $dados['comprimento'] . ' x ' . $dados['largura'] . ' x ' . $dados['altura'] . ' cm</div>
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Peso Real:</strong> ' . $resultado['pesoReal'] . ' kg</div>
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Peso Cubado:</strong> ' . $resultado['pesoCubico'] . ' kg</div>
                        <div style="margin-bottom: 5px;"><strong style="display: inline-block; width: 150px;">Peso Utilizado:</strong> ' . $resultado['pesoUtilizado'] . ' kg</div>
                    </td>
                </tr>
            </table>
        </div>';
        
    if ($resultado['simulado']) {
        $html .= '
        <div class="alert">
            <strong>Observação:</strong> ' . ($resultado['mensagem'] ?? 'Cotação simulada') . '
        </div>';
    }
    
    $html .= '
        <div class="box-title">Opções de Serviço</div>
        <table class="rates-table">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th class="text-center">Valor</th>
                    <th class="text-center">Moeda</th>
                    <th class="text-center">Prazo</th>
                    <th class="text-center">Entrega Estimada</th>
                </tr>
            </thead>
            <tbody>';
    
    if (count($resultado['cotacoesFedEx']) > 0) {
        foreach ($resultado['cotacoesFedEx'] as $cotacao) {
            $html .= '
                <tr>
                    <td>' . $cotacao['servico'] . '</td>
                    <td class="text-center">' . $cotacao['valorTotal'] . '</td>
                    <td class="text-center">' . $cotacao['moeda'] . '</td>
                    <td class="text-center">' . ($cotacao['tempoEntrega'] ?? 'N/A') . '</td>
                    <td class="text-center">' . ($cotacao['dataEntrega'] ?? 'N/A') . '</td>
                </tr>';
        }
    } else {
        $html .= '
                <tr>
                    <td colspan="5" class="text-center">Nenhuma cotação disponível</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="footer">
            <p>Data da Consulta: ' . ($resultado['dataConsulta'] ?? date('Y-m-d H:i:s')) . '</p>
            <p>Esta cotação é válida por 7 dias a partir da data de consulta. Valores sujeitos a alterações conforme disponibilidade e regras da FedEx.</p>
            <p>LOGIEZ - Soluções em Logística | CNPJ: 00.000.000/0001-00 | contato@logiez.com.br | (11) 0000-0000</p>
        </div>
    </div>
</body>
</html>';
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    
    return $pdf->download('Cotacao_FedEx_' . date('Y-m-d_His') . '.pdf');
})->name('cotacao.exportar.pdf');
