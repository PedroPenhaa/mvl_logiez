<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Obter o produto da requisição
$input = json_decode(file_get_contents('php://input'), true);
$produto = $input['produto'] ?? null;

if (!$produto) {
    echo json_encode(['success' => false, 'error' => 'Produto não informado']);
    exit;
}

try {
    // Carregar configurações do Laravel
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Obter chave da API do Gemini
    $apiKey = config('services.gemini.api_key');
    
    if (empty($apiKey)) {
        echo json_encode(['success' => false, 'error' => 'Chave da API do Gemini não configurada']);
        exit;
    }
    
    // Configurar a chamada para a API do Gemini
    $model = config('services.gemini.model', 'gemini-2.0-flash');
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    
    // Prompt otimizado para extrair NCM, descrição e unidade
    $prompt = "Para o produto '{$produto}', retorne APENAS o NCM (código de 8 dígitos no formato XXXX.XX.XX), a descrição completa do produto e a unidade de medida (UN, KG, L, M, etc). Formato da resposta: NCM: XXXX.XX.XX | Descrição: [descrição completa] | Unidade: [unidade]";
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];
    
    // Fazer a requisição para a API do Gemini
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint . '?key=' . $apiKey);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo json_encode([
            'success' => false,
            'error' => 'Erro na API do Gemini: HTTP ' . $httpCode,
            'response' => $response
        ]);
        exit;
    }
    
    $responseData = json_decode($response, true);
    
    if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Resposta inválida da API do Gemini',
            'response' => $responseData
        ]);
        exit;
    }
    
    $geminiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
    
    // Extrair NCM, descrição e unidade da resposta
    $ncm = null;
    $descricao = null;
    $unidade = null;
    
    // Padrão para extrair NCM
    if (preg_match('/NCM:\s*(\d{4}\.\d{2}\.\d{2})/i', $geminiResponse, $ncmMatches)) {
        $ncm = $ncmMatches[1];
    } elseif (preg_match('/(\d{4}\.\d{2}\.\d{2})/', $geminiResponse, $ncmMatches)) {
        $ncm = $ncmMatches[1];
    }
    
    // Padrão para extrair descrição
    if (preg_match('/Descrição:\s*(.+?)(?:\s*\||\s*Unidade:|$)/i', $geminiResponse, $descMatches)) {
        $descricao = trim($descMatches[1]);
    } elseif (preg_match('/-\s*(.+?)(?:\s*\||\s*Unidade:|$)/i', $geminiResponse, $descMatches)) {
        $descricao = trim($descMatches[1]);
    } else {
        $descricao = $produto; // Fallback
    }
    
    // Padrão para extrair unidade
    if (preg_match('/Unidade:\s*([A-Z]{2,3})/i', $geminiResponse, $unidadeMatches)) {
        $unidade = strtoupper($unidadeMatches[1]);
    } else {
        // Determinar unidade baseada no tipo de produto
        $produtoLower = strtolower($produto);
        if (strpos($produtoLower, 'calçado') !== false || strpos($produtoLower, 'sapato') !== false || strpos($produtoLower, 'tenis') !== false) {
            $unidade = 'PAR'; // Par de calçados
        } elseif (strpos($produtoLower, 'roupa') !== false || strpos($produtoLower, 'camisa') !== false || strpos($produtoLower, 'calça') !== false) {
            $unidade = 'UN'; // Unidade
        } elseif (strpos($produtoLower, 'notebook') !== false || strpos($produtoLower, 'computador') !== false || strpos($produtoLower, 'celular') !== false) {
            $unidade = 'UN'; // Unidade
        } else {
            $unidade = 'UN'; // Unidade padrão
        }
    }
    
    if ($ncm) {
        echo json_encode([
            'success' => true,
            'ncm' => $ncm,
            'descricao' => $descricao,
            'unidade' => $unidade,
            'raw_response' => $geminiResponse
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'NCM não encontrado na resposta do Gemini',
            'raw_response' => $geminiResponse
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?> 