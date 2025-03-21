<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function dashboard()
    {
        return view('sections.dashboard');
    }
    
    public function cotacao()
    {
        return view('sections.cotacao');
    }
    
    public function envio()
    {
        return view('sections.envio');
    }
    
    public function pagamento()
    {
        return view('sections.pagamento');
    }
    
    public function etiqueta()
    {
        return view('sections.etiqueta');
    }
    
    public function rastreamento()
    {
        return view('sections.rastreamento');
    }
    
    public function perfil()
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
        
        return view('sections.perfil', ['usuario' => $user]);
    }
} 