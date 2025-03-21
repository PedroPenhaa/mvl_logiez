<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CotacaoController extends Controller
{
    public function index()
    {
        return view('cotacao.index');
    }
    
    public function calcular(Request $request)
    {
        // Validação dos dados de entrada
        $validated = $request->validate([
            'origem' => 'required|string',
            'destino' => 'required|string', 
            'altura' => 'required|numeric',
            'largura' => 'required|numeric',
            'comprimento' => 'required|numeric',
            'peso' => 'required|numeric',
        ]);
        
        // Calculando peso cubado
        $pesoCubado = ($validated['altura'] * $validated['largura'] * $validated['comprimento']) / 200;
        
        // Definir qual peso usar (o maior entre o real e o cubado)
        $pesoUtilizado = max($pesoCubado, $validated['peso']);
        
        // Simulação de integração com API DHL para obter preço e prazo
        // Em produção, aqui seria chamada a API real da DHL
        $preco = round($pesoUtilizado * 35.5, 2); // Simulação de cálculo
        $prazoEstimado = rand(3, 15); // Simulação de prazo em dias
        
        return response()->json([
            'sucesso' => true,
            'dados' => [
                'pesoCubado' => round($pesoCubado, 2),
                'pesoReal' => $validated['peso'],
                'pesoUtilizado' => round($pesoUtilizado, 2),
                'preco' => $preco,
                'prazoEstimado' => $prazoEstimado,
                'moeda' => 'BRL'
            ]
        ]);
    }
} 