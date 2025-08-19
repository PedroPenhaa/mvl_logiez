<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\GeminiCEPController;

// Rota de teste simples
Route::get('/teste', function() {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando!',
        'timestamp' => now()
    ]);
});

// Rota para consultar NCM via Gemini
Route::post('consulta/gemini', [ProdutosController::class, 'consultarGemini'])->name('api.consulta.gemini');

// Rota para consultar CEP/Endereço via Gemini
Route::post('consulta/gemini-cep', [GeminiCEPController::class, 'consultar'])->name('api.consulta.gemini-cep');

// Rota para consultar unidade tributária pelo NCM
Route::get('/unidade-tributaria', [ProdutosController::class, 'consultarUnidadeTributaria'])->name('api.unidade-tributaria');

// Rota para buscar produtos (sem dependência do arquivo JSON)
Route::get('/produtos', [ProdutosController::class, 'getProdutos'])->name('api.produtos.get');

// Rotas para processamento de envio e pagamento
Route::post('/envio/processar', [EnvioController::class, 'processar'])->name('api.envio.processar');
Route::post('/envio/processar-confirmados', [EnvioController::class, 'processarConfirmados'])
    ->middleware('auth:api')
    ->name('api.envio.processar-confirmados'); 

// Rota para consulta de etiqueta FedEx
Route::post('/fedex/etiqueta', [EtiquetaController::class, 'fedex'])->name('api.fedex.etiqueta'); 