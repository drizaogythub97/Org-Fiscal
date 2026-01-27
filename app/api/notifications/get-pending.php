<?php
declare(strict_types=1);

// 🔧 Caminho corrigido
require_once __DIR__ . '/../../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

// 🔐 Exige sessão válida
if (!usuarioLogado()) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];

$hoje = new DateTime('now');
$dia  = (int) $hoje->format('d');
$mes  = (int) $hoje->format('m');
$ano  = (int) $hoje->format('Y');

// 🔎 Busca obrigações pendentes do período atual
$stmt = $pdo->prepare("
    SELECT
        l.id AS lembrete_id,
        o.nome,
        o.tipo,
        l.status,
        l.competencia_mes,
        l.competencia_ano
    FROM lembretes l
    INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
    WHERE l.usuario_id = ?
      AND l.status = 'pendente'
      AND (
            (o.tipo = 'mensal' AND l.competencia_mes = ? AND l.competencia_ano = ?)
         OR (o.tipo = 'anual' AND l.competencia_ano = ?)
      )
");

$stmt->execute([
    $usuarioId,
    $mes,
    $ano,
    $ano
]);

$pendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📤 Resposta JSON limpa (sem lógica de urgência ainda)
echo json_encode([
    'data' => [
        'data_atual' => $hoje->format('Y-m-d H:i:s'),
        'pendentes'  => $pendentes
    ]
]);
