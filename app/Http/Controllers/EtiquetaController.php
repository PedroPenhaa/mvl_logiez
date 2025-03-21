<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtiquetaController extends Controller
{
    public function index()
    {
        return view('etiqueta.index');
    }
    
    public function gerar(Request $request)
    {
        // Validação dos dados para geração da etiqueta
        $validated = $request->validate([
            'codigo_envio' => 'required|string',
        ]);
        
        // Simulação de integração com API da DHL para gerar etiqueta
        // Em produção, aqui seria feita a chamada real à API
        
        // Simulamos um link fictício para download da etiqueta
        $linkEtiqueta = "https://exemplo.com/etiquetas/12345.pdf";
        
        return view('etiqueta.exibir', [
            'link_etiqueta' => $linkEtiqueta,
            'codigo_envio' => $validated['codigo_envio']
        ]);
    }
} 