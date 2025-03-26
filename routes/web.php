<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckAuthenticated;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CotacaoController;

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
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [HomeController::class, 'register'])->name('register.form');
Route::post('/register', [HomeController::class, 'storeUser'])->name('register.store');

// Rotas protegidas por autenticação (temporariamente sem verificação)
Route::middleware('web')->group(function () {
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
    
    // Processamento de dados
    Route::post('/cotacao/calcular', [SectionController::class, 'calcularCotacao'])->name('cotacao.calcular');
    Route::post('/envio/processar', [SectionController::class, 'processarEnvio'])->name('envio.processar');
    Route::post('/pagamento/processar', [SectionController::class, 'processarPagamento'])->name('pagamento.processar');
    Route::post('/rastreamento/buscar', [SectionController::class, 'buscarRastreamento'])->name('rastreamento.buscar');
    Route::post('/perfil/atualizar', [SectionController::class, 'atualizarPerfil'])->name('perfil.atualizar');
});

// Esta rota deve estar definida em algum lugar do seu código
Route::get('/api/sections/{section}', [App\Http\Controllers\SectionController::class, 'getSection'])->name('section.get');

// Rota para cálculo de cotação mantida fora do grupo api para corresponder à URL no JavaScript
Route::post('/calcular-cotacao', [App\Http\Controllers\SectionController::class, 'calcularCotacao'])->name('cotacao.calcular');

// Rota para testar diretamente a API FedEx
Route::get('/testar-fedex', function() {
    // Inicializar o serviço FedEx
    $fedexService = app(App\Services\FedexService::class);
    
    // Teste de autenticação para obter token
    $tokenResponse = null;
    $httpCode = null;
    $ch = curl_init();
    
    // Construir URLs corretas a partir da configuração
    $authUrl = config('services.fedex.api_url') . config('services.fedex.auth_endpoint');
    $rateUrl = config('services.fedex.api_url') . config('services.fedex.rate_endpoint');
    
    curl_setopt($ch, CURLOPT_URL, $authUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'client_credentials',
        'client_id' => config('services.fedex.client_id'),
        'client_secret' => config('services.fedex.client_secret')
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ]);
    
    $tokenResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $tokenData = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;
    
    // Teste de cotação usando o token (tentativa real)
    $rateResponse = null;
    $rateHttpCode = 0;
    $requestHeaders = null;
    
    if ($accessToken) {
        // Payload simplificado para teste da API
        $payload = [
            'accountNumber' => [
                'value' => config('services.fedex.shipper_account')
            ],
            'requestedShipment' => [
                'shipper' => [
                    'address' => [
                        'postalCode' => '49424',
                        'countryCode' => 'US'
                    ]
                ],
                'recipient' => [
                    'address' => [
                        'postalCode' => '27577',
                        'countryCode' => 'US'
                    ]
                ],
                'preferredCurrency' => 'USD',
                'rateRequestType' => ['LIST'],
                'shipDateStamp' => date('Y-m-d'),
                'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                'requestedPackageLineItems' => [
                    [
                        'weight' => [
                            'units' => 'KG',
                            'value' => 10
                        ],
                        'dimensions' => [
                            'length' => 20,
                            'width' => 20,
                            'height' => 20,
                            'units' => 'CM'
                        ]
                    ]
                ]
            ]
        ];
        
        // Inicializa a requisição cURL para a cotação
        $ch = curl_init();
        
        // Configura um stream temporário para o output do modo verbose (para debug)
        $verbose = fopen('php://temp', 'w+');
        
        curl_setopt($ch, CURLOPT_URL, $rateUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'X-locale: en_US'
        ]);
        
        // Habilita output verbose para debug
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        $rateResponse = curl_exec($ch);
        $rateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $requestHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        curl_close($ch);
        
        // Limpa caracteres UTF-8 malformados que possam existir na resposta
        $rateResponse = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $rateResponse);
    }
    
    // Usando a função simulada para demonstração
    $simulacao = $fedexService->simularCotacao(
        ['postalCode' => '49424', 'countryCode' => 'US'], // origem
        ['postalCode' => '27577', 'countryCode' => 'US'], // destino
        20, // altura em cm
        20, // largura em cm
        20, // comprimento em cm
        10  // peso em kg
    );
    
    // Retorna as informações para debug
    return response()->json([
        'autenticacao' => [
            'httpCode' => $httpCode,
            'response' => $tokenData,
            'authUrl' => $authUrl
        ],
        'cotacao' => [
            'httpCode' => $rateHttpCode,
            'requestHeaders' => $requestHeaders,
            'payload' => $payload ?? null,
            'respostaRaw' => substr($rateResponse, 0, 1000), // Limita a 1000 caracteres para não sobrecarregar o browser
            'status' => 'Erro 403: Problema de permissão ou credencial. Usando simulação enquanto não resolvido.',
            'rateUrl' => $rateUrl
        ],
        'simulacao' => $simulacao
    ]);
});

// Rota para testar a autenticação FedEx
Route::get('/test-fedex-auth', function() {
    $fedexService = new App\Services\FedexService();
    return response()->json(['token' => $fedexService->getAuthToken()]);
})->name('api.fedex.auth');

// Rota para exibir formulário de cotação FedEx - Agora redireciona para a aba correta
Route::get('/cotacao-fedex', function () {
    return redirect('/api/sections/cotacao#fedex');
});

// Rota para processar cotação FedEx
Route::post('/processar-cotacao-fedex', function (Illuminate\Http\Request $request) {
    // Validar dados do formulário
    $validated = $request->validate([
        'origem_cep' => 'required|string|max:20',
        'origem_pais' => 'required|string|size:2',
        'destino_cep' => 'required|string|max:20',
        'destino_pais' => 'required|string|size:2',
        'altura' => 'required|numeric|min:1|max:500',
        'largura' => 'required|numeric|min:1|max:500',
        'comprimento' => 'required|numeric|min:1|max:500',
        'peso' => 'required|numeric|min:0.1|max:999',
        'simular' => 'nullable'
    ]);
    
    // Inicializar serviço FedEx
    $fedexService = app(App\Services\FedexService::class);
    
    // Determinar se deve forçar simulação
    $forcarSimulacao = $request->has('simular');
    
    // Obter cotação
    $cotacao = $fedexService->calcularCotacao(
        ['postalCode' => $validated['origem_cep'], 'countryCode' => $validated['origem_pais']],
        ['postalCode' => $validated['destino_cep'], 'countryCode' => $validated['destino_pais']],
        $validated['altura'],
        $validated['largura'],
        $validated['comprimento'],
        $validated['peso'],
        $forcarSimulacao
    );
    
    // Guardar a cotação em cache para uso na exportação PDF
    $hash = md5(json_encode($cotacao));
    \Illuminate\Support\Facades\Cache::put('cotacao_' . $hash, [
        'dados' => $validated,
        'resultado' => $cotacao
    ], now()->addMinutes(30));
    
    // Armazenar dados na sessão para exibição na aba de cotação
    session(['dados_fedex' => $validated, 'resultado_fedex' => $cotacao]);
    
    // Redirecionamento para a seção de cotação com a aba FedEx aberta
    if ($request->wantsJson()) {
        return response()->json(['dados' => $validated, 'resultado' => $cotacao]);
    }
    
    return redirect('/api/sections/cotacao#fedex');
});

// Rota para exportar cotação em PDF
Route::get('/exportar-cotacao-pdf', function (Illuminate\Http\Request $request) {
    $hash = $request->query('hash');
    
    if (!$hash || !\Illuminate\Support\Facades\Cache::has('cotacao_' . $hash)) {
        return redirect('/cotacao-fedex')->with('error', 'Cotação não encontrada ou expirada');
    }
    
    $cotacaoData = \Illuminate\Support\Facades\Cache::get('cotacao_' . $hash);
    $dados = $cotacaoData['dados'];
    $resultado = $cotacaoData['resultado'];
    
    // Criar HTML diretamente
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
});

// Rota temporária para testar o PDF diretamente
Route::get('/testar-pdf', function () {
    // Criar dados simulados
    $dados = [
        'origem_cep' => '12345678',
        'origem_pais' => 'BR',
        'destino_cep' => '10001',
        'destino_pais' => 'US',
        'comprimento' => 20,
        'largura' => 20,
        'altura' => 20,
        'peso' => 1,
    ];
    
    $resultado = [
        'pesoReal' => 1,
        'pesoCubico' => 1.33,
        'pesoUtilizado' => 1.33,
        'simulado' => true,
        'mensagem' => 'Cotação simulada para fins de demonstração',
        'dataConsulta' => date('Y-m-d H:i:s'),
        'cotacoesFedEx' => [
            [
                'servico' => 'FEDEX INTERNATIONAL PRIORITY',
                'valorTotal' => 120.50,
                'moeda' => 'USD',
                'tempoEntrega' => '3-5 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+5 days')),
            ],
            [
                'servico' => 'FEDEX INTERNATIONAL ECONOMY',
                'valorTotal' => 85.75,
                'moeda' => 'USD',
                'tempoEntrega' => '5-8 dias úteis',
                'dataEntrega' => date('Y-m-d', strtotime('+8 days')),
            ]
        ]
    ];
    
    // Criar HTML diretamente
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
    
    return $pdf->download('Cotacao_FedEx_Teste.pdf');
});
