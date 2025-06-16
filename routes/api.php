<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\EtiquetaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('consulta/gemini', [ProdutosController::class, 'consultarGemini'])->name('api.consulta.gemini');

// Rota para consultar unidade tributária pelo NCM
Route::get('/unidade-tributaria', [ProdutosController::class, 'consultarUnidadeTributaria'])->name('api.unidade-tributaria');

// Rotas para processamento de envio e pagamento
Route::post('/envio/processar', [EnvioController::class, 'processar'])->name('api.envio.processar');
Route::post('/envio/processar-confirmados', [EnvioController::class, 'processarConfirmados'])
    ->middleware('auth:api')
    ->name('api.envio.processar-confirmados');

// Rota para etiqueta FedEx
Route::post('/fedex/etiqueta', [EtiquetaController::class, 'fedex'])
    ->name('api.fedex.etiqueta')
    ->middleware(['web', 'auth']); 