<?php

// Configurar headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Se for uma requisição OPTIONS (preflight), retornar apenas os headers
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

try {
    // Obter dados da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
        exit;
    }
    
    $cep = $input['cep'] ?? '';
    $paisSelecionado = $input['pais_selecionado'] ?? '';
    
    if (empty($cep)) {
        echo json_encode(['success' => false, 'error' => 'CEP deve ser fornecido']);
        exit;
    }
    
    // Obter chave da API do Google Maps do arquivo .env
    $envFile = __DIR__ . '/../.env';
    $googleMapsApiKey = '';
    
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        if (preg_match('/GOOGLE_MAPS_API_KEY=(.+)/', $envContent, $matches)) {
            $googleMapsApiKey = trim($matches[1]);
        }
    }
    
    if (empty($googleMapsApiKey)) {
        echo json_encode(['success' => false, 'error' => 'Chave da API do Google Maps não configurada']);
        exit;
    }
    
    // Preparar o endereço para consulta
    $enderecoConsulta = '';
    
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
    
    // Fazer a consulta na API do Google Maps
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
        'address' => $enderecoConsulta,
        'key' => $googleMapsApiKey,
        'language' => 'pt-BR'
    ]);
    
    $response = file_get_contents($url);
    
    if ($response === false) {
        echo json_encode(['success' => false, 'error' => 'Erro ao consultar a API do Google Maps']);
        exit;
    }
    
    $data = json_decode($response, true);
    
    if ($data['status'] === 'OK' && !empty($data['results'])) {
        $result = $data['results'][0];
        $addressComponents = $result['address_components'];
        
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
        
        echo json_encode([
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
        echo json_encode([
            'success' => false,
            'error' => 'Endereço não encontrado: ' . $data['status']
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
}
