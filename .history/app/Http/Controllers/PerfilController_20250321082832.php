<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index()
    {
        // Simulação de dados do usuário para o exemplo
        $user = [
            'nome' => 'João Silva',
            'email' => 'joao.silva@exemplo.com',
            'cpf' => '123.456.789-00',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310-100',
            'rua' => 'Avenida Paulista',
            'numero' => '1000',
            'complemento' => 'Apto 123',
            'telefone' => '(11) 98765-4321'
        ];
        
        return view('perfil.index', ['usuario' => $user]);
    }
    
    public function atualizar(Request $request)
    {
        // Validação dos dados do perfil
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cpf' => 'required|string|max:14',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'cep' => 'required|string|max:10',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'telefone' => 'required|string|max:20',
        ]);
        
        // Aqui seria feita a atualização no banco de dados
        // Por enquanto, apenas retornamos sucesso
        
        return redirect()->route('perfil.index')->with('success', 'Perfil atualizado com sucesso!');
    }
} 