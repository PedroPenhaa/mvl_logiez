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
Route::get('/testar-fedex', function () {
    // Configurações da API FedEx
    $clientId = 'l7d8933648fbcf4414b354f41cf050530a';
    $clientSecret = '7b28b7ae75254bc681b3e899cf16607a';
    $authUrl = 'https://apis-sandbox.fedex.com/oauth/token';

    // Criar dados para autenticação
    $authData = [
        'grant_type' => 'client_credentials',
        'client_id' => $clientId,
        'client_secret' => $clientSecret
    ];

    // Inicializar cURL para autenticação
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $authUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($authData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $authResponse = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);

    $authResult = [
        'httpCode' => $httpCode,
        'erro' => $err,
        'resposta' => json_decode($authResponse, true)
    ];

    if ($httpCode == 200) {
        $authResponseData = json_decode($authResponse, true);
        $accessToken = $authResponseData['access_token'] ?? null;
        
        if ($accessToken) {
            // Configuração para requisição de cotação
            $rateUrl = 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes';
            $shipperAccount = '510087020';
            $shipDate = date('Y-m-d');
            
            // Payload simplificado para teste - ajustado conforme documentação
            $ratePayload = [
                "accountNumber" => [
                    "value" => $shipperAccount
                ],
                "rateRequestControlParameters" => [
                    "returnTransitTimes" => true,
                    "servicesNeededOnRateFailure" => true,
                    "variableOptions" => "FREIGHT_GUARANTEE",
                    "rateSortOrder" => "SERVICENAMETRADITIONAL"
                ],
                "requestedShipment" => [
                    "shipper" => [
                        "address" => [
                            "postalCode" => "37701246",
                            "countryCode" => "BR",
                            "residential" => false
                        ]
                    ],
                    "recipient" => [
                        "address" => [
                            "postalCode" => "10001",
                            "countryCode" => "US",
                            "residential" => false
                        ]
                    ],
                    "preferredCurrency" => "USD",
                    "rateRequestType" => ["LIST", "ACCOUNT"],
                    "shipDateStamp" => $shipDate,
                    "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                    "packagingType" => "YOUR_PACKAGING",
                    "requestedPackageLineItems" => [
                        [
                            "weight" => [
                                "units" => "KG",
                                "value" => 10
                            ],
                            "dimensions" => [
                                "length" => 10,
                                "width" => 10,
                                "height" => 10,
                                "units" => "CM"
                            ],
                            "groupPackageCount" => 1
                        ]
                    ],
                    "totalPackageCount" => 1,
                    "documentShipment" => false
                ],
                "carrierCodes" => ["FDXE"]
            ];
            
            // Inicializar cURL para requisição de cotação
            $rateCurl = curl_init();
            
            // Ativar informações detalhadas do cURL para debug
            $verbose = fopen('php://temp', 'w+');
            
            // ID de transação único
            $transactionId = uniqid('logiez_test_');
            
            curl_setopt_array($rateCurl, [
                CURLOPT_URL => $rateUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($ratePayload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                    'x-customer-transaction-id: ' . $transactionId,
                    'X-locale: pt_BR',
                    'Accept: application/json'
                ],
                CURLOPT_VERBOSE => true,
                CURLOPT_STDERR => $verbose,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLINFO_HEADER_OUT => true
            ]);
            
            $rateResponse = curl_exec($rateCurl);
            $rateHttpCode = curl_getinfo($rateCurl, CURLINFO_HTTP_CODE);
            $rateErr = curl_error($rateCurl);
            $requestHeaders = curl_getinfo($rateCurl, CURLINFO_HEADER_OUT);
            
            // Obter informações detalhadas sobre a requisição
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            
            curl_close($rateCurl);
            
            // Tentar decodificar a resposta para ver detalhes do erro
            $responseDecoded = json_decode($rateResponse, true);
            
            return response()->json([
                'autenticacao' => $authResult,
                'cotacao' => [
                    'httpCode' => $rateHttpCode,
                    'erro' => $rateErr,
                    'verboseLog' => $verboseLog,
                    'requestHeaders' => $requestHeaders,
                    'payload' => $ratePayload,
                    'resposta' => $responseDecoded,
                    'respostaRaw' => $rateResponse
                ]
            ]);
        }
    }
    
    return response()->json([
        'autenticacao' => $authResult,
        'cotacao' => 'Falha na autenticação'
    ]);
});

// Rota para testar a autenticação FedEx
Route::get('/test-fedex-auth', function() {
    $fedexService = new App\Services\FedexService();
    return response()->json(['token' => $fedexService->getAuthToken()]);
})->name('api.fedex.auth');
