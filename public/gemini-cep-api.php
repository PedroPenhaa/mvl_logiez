<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Inicializar o Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\GeminiCEPController;

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
    
    // Simular uma requisição Laravel
    $request = new Request();
    $request->merge($input);
    
    // Criar o controller
    $controller = new GeminiCEPController();
    
    // Fazer a consulta
    $response = $controller->consultar($request);
    
    // Retornar a resposta
    echo json_encode($response->getData());
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
} 