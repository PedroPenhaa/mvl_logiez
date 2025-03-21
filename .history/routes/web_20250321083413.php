<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CotacaoController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\RastreamentoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SectionController;

// Rota principal
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Dashboard principal
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// API para carregar seções
Route::prefix('api')->group(function () {
    Route::get('/sections/dashboard', [SectionController::class, 'dashboard']);
    Route::get('/sections/cotacao', [SectionController::class, 'cotacao']);
    Route::get('/sections/envio', [SectionController::class, 'envio']);
    Route::get('/sections/pagamento', [SectionController::class, 'pagamento']);
    Route::get('/sections/etiqueta', [SectionController::class, 'etiqueta']);
    Route::get('/sections/rastreamento', [SectionController::class, 'rastreamento']);
    Route::get('/sections/perfil', [SectionController::class, 'perfil']);
    
    // APIs para processamento de dados
    Route::post('/cotacao/calcular', [CotacaoController::class, 'calcular'])->name('api.cotacao.calcular');
    Route::post('/envio/store', [EnvioController::class, 'store'])->name('api.envio.store');
    Route::post('/pagamento/processar', [PagamentoController::class, 'processar'])->name('api.pagamento.processar');
    Route::post('/etiqueta/gerar', [EtiquetaController::class, 'gerar'])->name('api.etiqueta.gerar');
    Route::post('/rastreamento/rastrear', [RastreamentoController::class, 'rastrear'])->name('api.rastreamento.rastrear');
    Route::post('/perfil/atualizar', [PerfilController::class, 'atualizar'])->name('api.perfil.atualizar');
});
