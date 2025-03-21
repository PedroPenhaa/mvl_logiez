<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckAuthenticated;

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

// Rotas públicas
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

// Rotas de autenticação
Route::get('/login', [HomeController::class, 'login'])->name('login.form');
Route::post('/login', [HomeController::class, 'authenticate'])->name('login.authenticate');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/register', [HomeController::class, 'register'])->name('register.form');
Route::post('/register', [HomeController::class, 'storeUser'])->name('register.store');

// Páginas informativas
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HomeController::class, 'help'])->name('help');

// Definição simples da proteção de autenticação temporária
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(CheckAuthenticated::class)
    ->name('dashboard');

// Rotas da API para carregamento de seções (AJAX)
Route::prefix('api')->name('api.')->middleware(CheckAuthenticated::class)->group(function () {
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
