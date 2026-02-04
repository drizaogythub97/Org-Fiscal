<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once BASE_PATH . '/app/services/NotificationEngine.php';

header('Content-Type: application/json');

// ===============================
// 🔐 AUTENTICAÇÃO VIA TOKEN
// ===============================
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token não informado']);
    exit;
}

$token = $matches[1];

// Valida token
$stmt = $pdo->prepare("
    SELECT usuario_id
    FROM auth_tokens
    WHERE token = ?
      AND ativo = 1
      AND (expira_em IS NULL OR expira_em > NOW())
");
$stmt->execute([$token]);

$usuarioId = $stmt->fetchColumn();

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido ou expirado']);
    exit;
}

// ===============================
// 🧠 GERA NOTIFICAÇÕES
// ===============================
try {
    $engine = new NotificationEngine($pdo);

    $notificacoes = $engine->gerarNotificacoesDoDia(
        (int) $usuarioId,
        new DateTime('now')
    );

    echo json_encode([
        'data' => [
            'gerado_em'   => date('Y-m-d H:i:s'),
            'notificacoes'=> $notificacoes
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno',
        'detail'=> $e->getMessage()
    ]);
}
