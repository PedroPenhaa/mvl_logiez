<?php
// Teste simples para verificar se o PHP está funcionando
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'PHP funcionando corretamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION
]);
?> 