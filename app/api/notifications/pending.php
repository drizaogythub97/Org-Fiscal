<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Endpoint: Notificações pendentes
 * Método: GET
 * Autenticação: sessão obrigatória
 */

header('Content-Type: application/json');

// Garante login
if (!usuarioLogado()) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$hoje = new DateTime();

/**
 * Busca lembretes pendentes do usuário
 */
$stmt = $pdo->prepare("
    SELECT
        l.id              AS lembrete_id,
        l.competencia_mes,
        l.competencia_ano,
        l.status,
        o.nome            AS obrigacao_nome,
        o.tipo            AS obrigacao_tipo
    FROM lembretes l
    INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
    WHERE l.usuario_id = ?
      AND l.status = 'pendente'
");
$stmt->execute([$usuarioId]);

$lembretes = $stmt->fetchAll();

$notificacoes = [];

foreach ($lembretes as $l) {

    $mes = (int) $l['competencia_mes'];
    $ano = (int) $l['competencia_ano'];

    /**
     * Define prazo base por tipo de obrigação
     */
    $prazo = null;
    $urgente = false;

    // PGDAS → prazo dia 5
    if (stripos($l['obrigacao_nome'], 'pgdas') !== false) {
        $prazo = DateTime::createFromFormat('Y-m-d', "$ano-$mes-05");
    }

    // DAS → prazo dia 20
    elseif (stripos($l['obrigacao_nome'], 'das') !== false) {
        $prazo = DateTime::createFromFormat('Y-m-d', "$ano-$mes-20");
    }

    // Fechamento de faturamento → dias 1 e 2 do mês seguinte
    elseif (stripos($l['obrigacao_nome'], 'faturamento') !== false) {
        $prazo = DateTime::createFromFormat(
            'Y-m-d',
            sprintf('%04d-%02d-02', $ano, $mes + 1)
        );
    }

    if ($prazo && $hoje > $prazo) {
        $urgente = true;
    }

    $notificacoes[] = [
        'lembrete_id' => $l['lembrete_id'],
        'titulo'      => $l['obrigacao_nome'],
        'competencia' => sprintf('%02d/%04d', $mes, $ano),
        'urgente'     => $urgente,
        'deep_link'   => "/tarefa.php?id={$l['lembrete_id']}"
    ];
}

echo json_encode([
    'count' => count($notificacoes),
    'notificacoes' => $notificacoes
]);
