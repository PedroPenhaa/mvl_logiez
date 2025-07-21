<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class GeminiCEPController extends Controller
{
    /**
     * Consulta CEP ou endereço via Gemini
     */
    public function consultar(Request $request)
    {
        try {
            $request->validate([
                'cep' => 'nullable|string',
                'endereco' => 'nullable|string',
                'pais' => 'nullable|string',
                'estado' => 'nullable|string',
                'cidade' => 'nullable|string'
            ]);

            $cep = $request->input('cep');
            $endereco = $request->input('endereco');
            $pais = $request->input('pais');
            $estado = $request->input('estado');
            $cidade = $request->input('cidade');

            // Se não tem CEP mas tem endereço completo, montar o endereço
            if (empty($cep) && !empty($endereco)) {
                $enderecoCompleto = $endereco;
                if (!empty($cidade)) {
                    $enderecoCompleto .= ', ' . $cidade;
                }
                if (!empty($estado)) {
                    $enderecoCompleto .= ', ' . $estado;
                }
                if (!empty($pais)) {
                    $enderecoCompleto .= ', ' . $pais;
                }
            } else {
                $enderecoCompleto = null;
            }

            // Determinar o tipo de consulta
            $tipoConsulta = !empty($cep) ? 'cep' : 'endereco';
            $valor = !empty($cep) ? $cep : $enderecoCompleto;

            if (empty($valor)) {
                return response()->json([
                    'success' => false,
                    'error' => 'CEP ou endereço deve ser fornecido'
                ], 400);
            }

            // Obter chave da API do Gemini
            $apiKey = config('services.gemini.api_key');
            
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chave da API do Gemini não configurada'
                ], 500);
            }
            
            // Configurar a chamada para a API do Gemini
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
            
            // Preparar a pergunta baseada no tipo de consulta
            if ($tipoConsulta === 'cep') {
                $pergunta = "Para o CEP $cep, forneça as seguintes informações: País, Estado, Cidade e Rua. Responda APENAS em formato JSON válido com as chaves: pais, estado, cidade, rua. Exemplo: {\"pais\": \"Brasil\", \"estado\": \"SP\", \"cidade\": \"São Paulo\", \"rua\": \"Rua das Flores\"}";
            } else {
                $pergunta = "Para o endereço: $enderecoCompleto, forneça o CEP correspondente. Responda APENAS com o CEP no formato 00000-000.";
            }
            
            // Preparar a requisição
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $pergunta
                            ]
                        ]
                    ]
                ]
            ]);
            
            // Verificar se a requisição foi bem-sucedida
            if ($response->successful()) {
                $data = $response->json();
                
                // Extrair a resposta do Gemini
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    if ($tipoConsulta === 'cep') {
                        // Tentar extrair JSON da resposta
                        $jsonMatch = preg_match('/\{.*\}/s', $result, $matches);
                        if ($jsonMatch) {
                            $jsonData = json_decode($matches[0], true);
                            if ($jsonData) {
                                return response()->json([
                                    'success' => true,
                                    'tipo' => 'cep',
                                    'data' => $jsonData,
                                    'raw_response' => $result
                                ]);
                            }
                        }
                        
                        // Se não conseguiu extrair JSON, tentar extrair informações manualmente
                        $pais = '';
                        $estado = '';
                        $cidade = '';
                        $rua = '';
                        
                        // Extrair informações usando regex
                        if (preg_match('/pais["\s:]+([^",}]+)/i', $result, $matches)) {
                            $pais = trim($matches[1]);
                        }
                        if (preg_match('/estado["\s:]+([^",}]+)/i', $result, $matches)) {
                            $estado = trim($matches[1]);
                        }
                        if (preg_match('/cidade["\s:]+([^",}]+)/i', $result, $matches)) {
                            $cidade = trim($matches[1]);
                        }
                        if (preg_match('/rua["\s:]+([^",}]+)/i', $result, $matches)) {
                            $rua = trim($matches[1]);
                        }
                        
                        return response()->json([
                            'success' => true,
                            'tipo' => 'cep',
                            'data' => [
                                'pais' => $pais,
                                'estado' => $estado,
                                'cidade' => $cidade,
                                'rua' => $rua
                            ],
                            'raw_response' => $result
                        ]);
                    } else {
                        // Consulta por endereço - extrair CEP
                        $cepMatch = preg_match('/(\d{5}-\d{3})/', $result, $matches);
                        if ($cepMatch) {
                            return response()->json([
                                'success' => true,
                                'tipo' => 'endereco',
                                'data' => [
                                    'cep' => $matches[1]
                                ],
                                'raw_response' => $result
                            ]);
                        }
                        
                        return response()->json([
                            'success' => false,
                            'error' => 'CEP não encontrado na resposta',
                            'raw_response' => $result
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Formato de resposta inesperado da API Gemini'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao consultar a API Gemini: ' . $response->status()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro na consulta Gemini CEP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
} 