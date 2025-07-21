<?php
// Arquivo para consultar o Gemini diretamente
header('Content-Type: application/json');

// Verificar se o produto foi enviado (POST ou GET)
$produto = null;
if (isset($_POST['produto'])) {
    $produto = $_POST['produto'];
} elseif (isset($_GET['produto'])) {
    $produto = $_GET['produto'];
}

if (!$produto) {
    echo json_encode([
        'success' => false,
        'error' => 'Produto não informado'
    ]);
    exit;
}

// Executar o comando Artisan
$command = "php artisan consulta:gemini --produto=\"" . escapeshellarg($produto) . "\"";
$output = shell_exec($command);

// Decodificar a resposta JSON
$data = json_decode($output, true);

if (!$data || !isset($data['success'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro na execução do comando',
        'output' => $output
    ]);
    exit;
}

// Retornar a resposta
echo json_encode($data);
?> 