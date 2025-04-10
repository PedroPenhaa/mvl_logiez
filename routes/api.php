<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;

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