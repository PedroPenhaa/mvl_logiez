<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsCEPController extends Controller
{
    /**
     * Consulta CEP ou endereço via Google Maps Geocoding API
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

            // Obter chave da API do Google Maps
            $apiKey = config('services.google.maps_api_key');
            
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chave da API do Google Maps não configurada'
                ], 500);
            }

            // Preparar o endereço para consulta
            $enderecoConsulta = '';
            if ($tipoConsulta === 'cep') {
                // Determinar o país baseado no formato do CEP ou país selecionado
                $paisDetectado = '';
                if (!empty($paisSelecionado)) {
                    if (stripos($paisSelecionado, 'Brasil') !== false) {
                        $paisDetectado = 'Brasil';
                    } elseif (stripos($paisSelecionado, 'Estados Unidos') !== false || stripos($paisSelecionado, 'United States') !== false) {
                        $paisDetectado = 'Estados Unidos';
                    }
                }
                
                // Se não conseguiu detectar pelo país selecionado, tentar pelo formato do CEP
                if (empty($paisDetectado)) {
                    if (preg_match('/^\d{5}-?\d{3}$/', $cep)) {
                        $paisDetectado = 'Brasil';
                    } elseif (preg_match('/^\d{5}$/', $cep)) {
                        $paisDetectado = 'Estados Unidos';
                    }
                }

                if ($paisDetectado === 'Brasil') {
                    $enderecoConsulta = $cep . ', Brasil';
                } elseif ($paisDetectado === 'Estados Unidos') {
                    $enderecoConsulta = $cep . ', USA';
                } else {
                    $enderecoConsulta = $cep;
                }
            } else {
                $enderecoConsulta = $enderecoCompleto;
            }

            Log::info('Consultando Google Maps:', ['endereco' => $enderecoConsulta, 'tipo' => $tipoConsulta]);

            // Fazer a consulta na API do Google Maps
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $enderecoConsulta,
                'key' => $apiKey,
                'language' => 'pt-BR'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $result = $data['results'][0];
                    $addressComponents = $result['address_components'];
                    
                    Log::info('Resposta do Google Maps:', ['result' => $result]);
                    
                    if ($tipoConsulta === 'cep') {
                        // Extrair informações do endereço
                        $pais = '';
                        $estado = '';
                        $cidade = '';
                        $rua = '';
                        
                        foreach ($addressComponents as $component) {
                            $types = $component['types'];
                            
                            if (in_array('country', $types)) {
                                $pais = $component['long_name'];
                            } elseif (in_array('administrative_area_level_1', $types)) {
                                $estado = $component['long_name'];
                            } elseif (in_array('administrative_area_level_2', $types) || in_array('locality', $types)) {
                                $cidade = $component['long_name'];
                            } elseif (in_array('route', $types)) {
                                $rua = $component['long_name'];
                            }
                        }
                        
                        // Se não encontrou cidade no administrative_area_level_2, tentar locality
                        if (empty($cidade)) {
                            foreach ($addressComponents as $component) {
                                if (in_array('locality', $component['types'])) {
                                    $cidade = $component['long_name'];
                                    break;
                                }
                            }
                        }
                        
                        // Se ainda não encontrou cidade, tentar sublocality
                        if (empty($cidade)) {
                            foreach ($addressComponents as $component) {
                                if (in_array('sublocality', $component['types'])) {
                                    $cidade = $component['long_name'];
                                    break;
                                }
                            }
                        }
                        
                        // Para Estados Unidos, usar a sigla do estado
                        if ($pais === 'United States' && !empty($estado)) {
                            foreach ($addressComponents as $component) {
                                if (in_array('administrative_area_level_1', $component['types'])) {
                                    $estado = $component['short_name'];
                                    break;
                                }
                            }
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
                        $cep = '';
                        foreach ($addressComponents as $component) {
                            if (in_array('postal_code', $component['types'])) {
                                $cep = $component['long_name'];
                                break;
                            }
                        }
                        
                        if (!empty($cep)) {
                            return response()->json([
                                'success' => true,
                                'tipo' => 'endereco',
                                'data' => [
                                    'cep' => $cep
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
                        'error' => 'Endereço não encontrado: ' . $data['status']
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao consultar a API do Google Maps: ' . $response->status()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro no GoogleMapsCEPController:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}

