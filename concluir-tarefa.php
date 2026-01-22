<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/bootstrap.php';
exigirLogin();


/* ============================
   DEFINIÇÃO DA COMPETÊNCIA
============================ */
$mes = isset($_GET['mes']) ? (int) $_GET['mes'] : null;
$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : null;

/* ============================
   PROCESSA CONCLUSÃO / REABERTURA
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lembrete_id'], $_POST['mes'], $_POST['ano'])) {

    $novoStatus = isset($_POST['status']) ? 'concluido' : 'pendente';
    $dataConclusao = $novoStatus === 'concluido' ? date('Y-m-d') : null;

    $update = $pdo->prepare("
        UPDATE lembretes
        SET status = ?, data_conclusao = ?
        WHERE id = ? AND usuario_id = ?
    ");
    $update->execute([
        $novoStatus,
        $dataConclusao,
        $_POST['lembrete_id'],
        $usuarioId
    ]);

    header("Location: concluir-tarefa.php?mes={$_POST['mes']}&ano={$_POST['ano']}");
    exit;
}

/* ============================
   SE COMPETÊNCIA FOI INFORMADA
============================ */
$lembretes = [];
$concluidos = 0;

if ($mes && $ano) {

    /* Busca lembretes */
    $stmt = $pdo->prepare("
    SELECT
        l.id AS lembrete_id,
        o.nome,
        o.tipo,
        l.status
    FROM lembretes l
    INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
    WHERE l.usuario_id = ?
      AND l.competencia_ano = ?
      AND (
            (o.tipo = 'mensal' AND l.competencia_mes = ?)
         OR (o.tipo = 'anual' AND ? = 3)
      )
    ORDER BY o.tipo DESC, o.id
");
$stmt->execute([$usuarioId, $ano, $mes, $mes]);
    $lembretes = $stmt->fetchAll();

    foreach ($lembretes as $l) {
        if ($l['status'] === 'concluido') {
            $concluidos++;
        }
    }
}

$stmt = $pdo->prepare("
  SELECT DISTINCT competencia_ano 
  FROM lembretes 
  WHERE usuario_id = ?
  ORDER BY competencia_ano DESC
");
$stmt->execute([$usuarioId]);
$anosDisponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concluir Tarefas — OrgFiscal</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#3b6b8f">

</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/img/logo-orgfiscal.png" alt="OrgFiscal">
            </a>
        </div>
    </div>
</header>

<main class="container">

<?php if (!$mes || !$ano): ?>

    <!-- ESTADO 1: SELEÇÃO -->
    <h1 class="page-title">Concluir Tarefas</h1>

    <form method="get" class="card">
        <div class="checklist-item">
            <div class="checklist-left">
                <label>Mês:</label>
                <select name="mes">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>"><?= str_pad((string)$m, 2, '0', STR_PAD_LEFT) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="checklist-item">
            <div class="checklist-left">
                <label>Ano:</label>
                <select name="ano">
  <?php foreach ($anosDisponiveis as $a): ?>
    <option value="<?= $a ?>"><?= $a ?></option>
  <?php endforeach; ?>
</select>

            </div>
        </div>

        <button type="submit" class="btn-principal">Buscar</button>
    </form>

<?php else: ?>

    <!-- ESTADO 2: CHECKLIST -->
    <h1 class="page-title">
        Concluir Tarefas — <?= str_pad((string)$mes, 2, '0', STR_PAD_LEFT) ?>/<?= $ano ?>
    </h1>

    <p class="progresso">
        <?= $concluidos ?> de <?= count($lembretes) ?> obrigações concluídas
    </p>

    <?php
    $mensais = [];
    $anuais = [];

    foreach ($lembretes as $l) {
        ($l['tipo'] === 'mensal') ? $mensais[] = $l : $anuais[] = $l;
    }
    ?>

    <?php if ($mensais): ?>
        <h2 class="section-title">Obrigações Mensais</h2>
        <?php foreach ($mensais as $l): ?>
            <?php $ehCritica = stripos($l['nome'], 'PGDAS') !== false; ?>
            <div class="card <?= $l['status'] === 'concluido' ? 'card-concluido' : '' ?> <?= $ehCritica && $l['status'] !== 'concluido' ? 'card-critica' : '' ?>">
                <form method="post">
                    <input type="hidden" name="lembrete_id" value="<?= $l['lembrete_id'] ?>">
                    <input type="hidden" name="mes" value="<?= $mes ?>">
                    <input type="hidden" name="ano" value="<?= $ano ?>">

                    <div class="checklist-item">
                        <div class="checklist-left">
                            <input type="checkbox" name="status" value="1" onchange="this.form.submit()" <?= $l['status'] === 'concluido' ? 'checked' : '' ?>>
                            <a class="tarefa-link" href="tarefa.php?id=<?= $l['lembrete_id'] ?>">
                                <?= htmlspecialchars($l['nome']) ?>
                            </a>
                        </div>

                        <?php if ($ehCritica && $l['status'] !== 'concluido'): ?>
                            <span class="badge-critica">Prioritária</span>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($anuais): ?>
        <h2 class="section-title">Obrigações Anuais</h2>
        <?php foreach ($anuais as $l): ?>
            <div class="card <?= $l['status'] === 'concluido' ? 'card-concluido' : '' ?>">
                <form method="post">
                    <input type="hidden" name="lembrete_id" value="<?= $l['lembrete_id'] ?>">
                    <input type="hidden" name="mes" value="<?= $mes ?>">
                    <input type="hidden" name="ano" value="<?= $ano ?>">

                    <div class="checklist-item">
                        <div class="checklist-left">
                            <input type="checkbox" name="status" value="1" onchange="this.form.submit()" <?= $l['status'] === 'concluido' ? 'checked' : '' ?>>
                            <a class="tarefa-link" href="tarefa.php?id=<?= $l['lembrete_id'] ?>">
                                <?= htmlspecialchars($l['nome']) ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>

    <!-- NAVEGAÇÃO -->
    <div class="nav-bottom">
        <a href="dashboard.php" class="btn-inicio">🏠 Início</a>
        <button type="button" class="btn-voltar" onclick="history.back()">⬅ Voltar</button>
    </div>

</main>

<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('./service-worker.js');
  });
}
</script>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">
        <span>OrgFiscal — Todos os direitos reservados a Adriano Cardoso</span>
    </div>
</footer>

</body>
</html>
