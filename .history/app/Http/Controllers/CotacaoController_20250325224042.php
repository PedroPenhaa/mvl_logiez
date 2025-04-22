<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FedexService;

class CotacaoController extends Controller
{
    protected $fedexService;
    
    public function __construct(FedexService $fedexService)
    {
        $this->fedexService = $fedexService;
    }
    
    public function index()
    {
        return view('cotacao.index');
    }
    
    public function calcular(Request $request)
    {
        // Validar os dados de entrada
        $validated = $request->validate([
            'origem' => 'required',
            'destino' => 'required',
            'peso' => 'required|numeric',
            'comprimento' => 'required|numeric',
            'largura' => 'required|numeric',
            'altura' => 'required|numeric',
        ]);
        
        try {
            // Usar o serviço FedEx para calcular a cotação
            $resultado = $this->fedexService->calcularCotacao(
                $request->origem,
                $request->destino,
                $request->altura,
                $request->largura,
                $request->comprimento,
                $request->peso,
                $request->forcarSimulacao ?? false
            );
            
            return response()->json([
                'status' => 'success',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 