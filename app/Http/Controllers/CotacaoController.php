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
        // Validar os dados de entrada
        $validated = $request->validate([
            'origem' => 'required',
            'destino' => 'required',
            'peso' => 'required|numeric',
            'comprimento' => 'required|numeric',
            'largura' => 'required|numeric',
            'altura' => 'required|numeric',
        ]);
        
        // Seu código de requisição à API FedEx
        $clientId = 'l7d8933648fbcf4414b354f41cf050530a';
        $clientSecret = '7b28b7ae75254bc681b3e899cf16607a';
        $url = 'https://apis-sandbox.fedex.com/oauth/token';

        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "Accept: application/json"
        ];

        $data = http_build_query([
            'grant_type' => 'client_credentials'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "$clientId:$clientSecret",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);
        
        // Processar resposta e obter token
        $responseData = json_decode($response, true);
        
        // Aqui você usaria o token para fazer a requisição de cotação
        // e retornaria os resultados
        
        return response()->json([
            'status' => 'success',
            'data' => $responseData
        ]);
    }
} 