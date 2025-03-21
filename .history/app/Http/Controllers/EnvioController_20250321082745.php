<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index()
    {
        return view('envio.index');
    }
    
    public function store(Request $request)
    {
        // Validação dos dados do envio
        $validated = $request->validate([
            'nome_remetente' => 'required|string|max:255',
            'nome_destinatario' => 'required|string|max:255',
            'endereco_destinatario' => 'required|string|max:255',
            'tipo_envio' => 'required|string',
            // Adicione outros campos conforme necessário
        ]);
        
        // Aqui seria a lógica para salvar os dados do envio no banco
        // Por enquanto, apenas simulamos o sucesso
        
        return redirect()->route('envio.confirmacao')->with('success', 'Envio registrado com sucesso!');
    }
    
    public function confirmacao()
    {
        return view('envio.confirmacao');
    }
} 