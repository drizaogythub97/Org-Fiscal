<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once BASE_PATH . '/app/services/NotificationEngine.php';

header('Content-Type: application/json');

if (!usuarioLogado()) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

try {
    $engine = new NotificationEngine($pdo);

    $notificacoes = $engine->gerarNotificacoesDoDia(
        (int) $_SESSION['usuario_id'],
        new DateTime('now')
    );

    echo json_encode([
        'data' => [
            'gerado_em' => date('Y-m-d H:i:s'),
            'notificacoes' => $notificacoes
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error'  => 'Erro ao gerar notificações',
        'detail' => $e->getMessage()
    ]);
}
