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

// Rota principal
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rotas do Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rotas de Cotação
Route::get('/cotacao', [CotacaoController::class, 'index'])->name('cotacao.index');
Route::post('/cotacao/calcular', [CotacaoController::class, 'calcular'])->name('cotacao.calcular');

// Rotas de Envio
Route::get('/envio', [EnvioController::class, 'index'])->name('envio.index');
Route::post('/envio', [EnvioController::class, 'store'])->name('envio.store');
Route::get('/envio/confirmacao', [EnvioController::class, 'confirmacao'])->name('envio.confirmacao');

// Rotas de Pagamento
Route::get('/pagamento', [PagamentoController::class, 'index'])->name('pagamento.index');
Route::post('/pagamento/processar', [PagamentoController::class, 'processar'])->name('pagamento.processar');
Route::get('/pagamento/confirmacao', [PagamentoController::class, 'confirmacao'])->name('pagamento.confirmacao');

// Rotas de Etiqueta
Route::get('/etiqueta', [EtiquetaController::class, 'index'])->name('etiqueta.index');
Route::post('/etiqueta/gerar', [EtiquetaController::class, 'gerar'])->name('etiqueta.gerar');

// Rotas de Rastreamento
Route::get('/rastreamento', [RastreamentoController::class, 'index'])->name('rastreamento.index');
Route::post('/rastreamento/rastrear', [RastreamentoController::class, 'rastrear'])->name('rastreamento.rastrear');

// Rotas de Perfil
Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
Route::post('/perfil/atualizar', [PerfilController::class, 'atualizar'])->name('perfil.atualizar');
