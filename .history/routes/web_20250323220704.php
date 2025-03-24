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
Route::middleware([])->group(function () {
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

// Rota para exibir formulário de cotação FedEx
Route::get('/cotacao-fedex', function () {
    return view('cotacao-fedex');
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
    
    // Retornar para a view com os resultados
    return view('cotacao-fedex', [
        'dados' => $validated,
        'resultado' => $cotacao
    ]);
});

// Rota para exportar cotação em PDF
Route::get('/exportar-cotacao-pdf', function (Illuminate\Http\Request $request) {
    $hash = $request->query('hash');
    
    if (!$hash || !\Illuminate\Support\Facades\Cache::has('cotacao_' . $hash)) {
        return redirect('/cotacao-fedex')->with('error', 'Cotação não encontrada ou expirada');
    }
    
    $cotacaoData = \Illuminate\Support\Facades\Cache::get('cotacao_' . $hash);
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotacao-fedex', [
        'dados' => $cotacaoData['dados'],
        'resultado' => $cotacaoData['resultado']
    ]);
    
    return $pdf->download('Cotacao_FedEx_' . date('Y-m-d_His') . '.pdf');
});
