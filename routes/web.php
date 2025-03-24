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

// Rota para testar a autenticação FedEx
Route::get('/testar-fedex-auth', function () {
    $fedexService = new \App\Services\FedexService();
    $resultado = $fedexService->testarAutenticacao();
    return response()->json($resultado);
})->middleware(['auth']); // Use middleware de autenticação para proteger esta rota
