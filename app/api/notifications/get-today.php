<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once BASE_PATH . '/app/helpers/api_token.php';
require_once BASE_PATH . '/app/services/NotificationEngine.php';

header('Content-Type: application/json');

// 🔐 Token vem no header Authorization
$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token não informado']);
    exit;
}

$token = trim($matches[1]);

$usuarioId = validarTokenApi($pdo, $token);

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

$engine = new NotificationEngine($pdo);

$notificacoes = $engine->gerarNotificacoesDoDia(
    $usuarioId,
    new DateTime('now')
);

echo json_encode([
    'data' => [
        'gerado_em' => date('Y-m-d H:i:s'),
        'notificacoes' => $notificacoes
    ]
]);
