<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RastreamentoController extends Controller
{
    public function index()
    {
        return view('rastreamento.index');
    }
    
    public function rastrear(Request $request)
    {
        // Validação do código de rastreio
        $validated = $request->validate([
            'codigo_rastreio' => 'required|string',
        ]);
        
        // Simulação de integração com API da DHL para rastreamento
        // Em produção, aqui seria feita a chamada real à API
        
        // Status fictícios para demonstração
        $statusEnvio = [
            [
                'data' => '2023-10-15 08:30:00',
                'status' => 'Registrado',
                'descricao' => 'Envio registrado no sistema',
                'local' => 'São Paulo, SP'
            ],
            [
                'data' => '2023-10-16 10:15:00',
                'status' => 'Em Processamento',
                'descricao' => 'Envio em processamento no centro de distribuição',
                'local' => 'São Paulo, SP'
            ],
            [
                'data' => '2023-10-17 16:45:00',
                'status' => 'Em Trânsito',
                'descricao' => 'Envio em trânsito para o destino',
                'local' => 'Aeroporto Internacional de Guarulhos, SP'
            ],
            [
                'data' => '2023-10-19 09:30:00',
                'status' => 'Chegada ao Destino',
                'descricao' => 'Envio chegou ao país de destino',
                'local' => 'Miami, FL, USA'
            ]
        ];
        
        return view('rastreamento.resultado', [
            'codigo' => $validated['codigo_rastreio'],
            'status' => $statusEnvio
        ]);
    }
} 