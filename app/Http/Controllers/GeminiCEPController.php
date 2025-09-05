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
                'cidade' => 'nullable|string',
                'pais_selecionado' => 'nullable|string'
            ]);

            $cep = $request->input('cep');
            $endereco = $request->input('endereco');
            $pais = $request->input('pais');
            $estado = $request->input('estado');
            $cidade = $request->input('cidade');
            $paisSelecionado = $request->input('pais_selecionado');

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
                // Usar o país selecionado pelo usuário em vez de detectar automaticamente
                $paisDetectado = '';
                Log::info('País selecionado recebido:', ['pais_selecionado' => $paisSelecionado]);
                if (!empty($paisSelecionado)) {
                    if (stripos($paisSelecionado, 'Brasil') !== false) {
                        $paisDetectado = 'Brasil';
                    } elseif (stripos($paisSelecionado, 'Estados Unidos') !== false || stripos($paisSelecionado, 'United States') !== false) {
                        $paisDetectado = 'Estados Unidos';
                    }
                }
                Log::info('País detectado:', ['pais_detectado' => $paisDetectado]);
                
                // Se não conseguiu detectar pelo país selecionado, tentar pelo formato do CEP
                if (empty($paisDetectado)) {
                    if (preg_match('/^\d{5}-?\d{3}$/', $cep)) {
                        $paisDetectado = 'Brasil';
                    } elseif (preg_match('/^\d{5}$/', $cep)) {
                        $paisDetectado = 'Estados Unidos';
                    }
                }
                
                if ($paisDetectado === 'Brasil') {
                    $pergunta = "No Brasil, o CEP $cep é de qual estado e cidade? Me retorne exatamente apenas o nome do estado e o nome da cidade";
                } elseif ($paisDetectado === 'Estados Unidos') {
                    $pergunta = "Nos Estados Unidos, o CEP $cep é de qual estado e cidade? Me retorne exatamente apenas o nome do estado e o nome da cidade";
                } else {
                    $pergunta = "Para o CEP $cep, forneça as seguintes informações: País, Estado (sigla de 2 letras), Cidade e Rua. Responda APENAS em formato JSON válido com as chaves: pais, estado, cidade, rua. Exemplo: {\"pais\": \"Brasil\", \"estado\": \"SP\", \"cidade\": \"São Paulo\", \"rua\": \"Rua das Flores\"}";
                }
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
                    Log::info('Resposta bruta do Gemini:', ['result' => $result, 'cep' => $cep, 'pais_selecionado' => $paisSelecionado]);
                    
                    if ($tipoConsulta === 'cep') {
                        // Determinar o país baseado no formato do CEP
                        $paisDetectado = '';
                        if (preg_match('/^\d{5}-?\d{3}$/', $cep)) {
                            $paisDetectado = 'Brasil';
                        } elseif (preg_match('/^\d{5}$/', $cep)) {
                            $paisDetectado = 'Estados Unidos';
                        }
                        
                        // Para Brasil e Estados Unidos, processar resposta simples (estado e cidade)
                        if ($paisDetectado === 'Brasil' || $paisDetectado === 'Estados Unidos') {
                            $estado = '';
                            $cidade = '';
                            
                            Log::info('Processando resposta:', ['result' => $result]);
                            
                            // SIMPLES: Dividir por vírgula - PRIMEIRO é ESTADO, SEGUNDO é CIDADE
                            $parts = explode(',', $result);
                            if (count($parts) >= 2) {
                                $estado = trim($parts[0]);
                                $cidade = trim($parts[1]);
                                Log::info('Extraído por vírgula:', ['estado' => $estado, 'cidade' => $cidade]);
                            } else {
                                // Se não tem vírgula, tentar por quebra de linha
                                $lines = explode("\n", $result);
                                foreach ($lines as $line) {
                                    $line = trim($line);
                                    if (stripos($line, 'estado') !== false) {
                                        $estado = preg_replace('/estado[:\s]*/i', '', $line);
                                        $estado = trim($estado);
                                    }
                                    if (stripos($line, 'cidade') !== false) {
                                        $cidade = preg_replace('/cidade[:\s]*/i', '', $line);
                                        $cidade = trim($cidade);
                                    }
                                }
                                Log::info('Extraído por linha:', ['cidade' => $cidade, 'estado' => $estado]);
                            }
                            
                            $responseData = [
                                'success' => true,
                                'tipo' => 'cep',
                                'data' => [
                                    'pais' => $paisDetectado,
                                    'estado' => $estado,
                                    'cidade' => $cidade,
                                    'rua' => ''
                                ],
                                'raw_response' => $result
                            ];
                            Log::info('Resposta final para CEP:', $responseData);
                            return response()->json($responseData);
                        }
                        
                        // Para outros países, tentar extrair JSON da resposta
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
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
} 