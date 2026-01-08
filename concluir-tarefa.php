<?php
declare(strict_types=1);

/* ============================
   CONFIGURAÇÃO E CONEXÃO
============================ */
require_once __DIR__ . '/app/config/database.php';

/* ============================
   USUÁRIO FIXO (MVP)
============================ */
$usuarioId = 1;

/* ============================
   COMPETÊNCIA ATUAL
============================ */
$mesAtual = (int) date('m');
$anoAtual = (int) date('Y');

/* ============================
   PROCESSA CONCLUSÃO DO LEMBRETE
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lembrete_id'])) {

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

    header('Location: index.php');
    exit;
}

/* ============================
   BUSCA OBRIGAÇÕES
============================ */
$stmt = $pdo->query("SELECT id, nome FROM obrigacoes ORDER BY id");
$obrigacoes = $stmt->fetchAll();

/* ============================
   CRIA LEMBRETES DO MÊS (SE NÃO EXISTIREM)
============================ */
foreach ($obrigacoes as $obrigacao) {
    $check = $pdo->prepare("
        SELECT id FROM lembretes
        WHERE usuario_id = ?
          AND obrigacao_id = ?
          AND competencia_mes = ?
          AND competencia_ano = ?
    ");
    $check->execute([
        $usuarioId,
        $obrigacao['id'],
        $mesAtual,
        $anoAtual
    ]);

    if (!$check->fetch()) {
        $insert = $pdo->prepare("
            INSERT INTO lembretes
            (usuario_id, obrigacao_id, competencia_mes, competencia_ano)
            VALUES (?, ?, ?, ?)
        ");
        $insert->execute([
            $usuarioId,
            $obrigacao['id'],
            $mesAtual,
            $anoAtual
        ]);
    }
}

/* ============================
   BUSCA LEMBRETES DO MÊS
============================ */
$stmt = $pdo->prepare("
    SELECT
        l.id AS lembrete_id,
        o.nome,
        o.tipo,
        l.status
    FROM lembretes l
    INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
    WHERE l.usuario_id = ?
      AND l.competencia_mes = ?
      AND l.competencia_ano = ?
    ORDER BY o.tipo DESC, o.id
");
$stmt->execute([$usuarioId, $mesAtual, $anoAtual]);
$lembretes = $stmt->fetchAll();
$totalLembretes = count($lembretes);
$concluidos = 0;
$mensais = [];
$anuais = [];

foreach ($lembretes as $lembrete) {
    if ($lembrete['tipo'] === 'mensal') {
        $mensais[] = $lembrete;
    } else {
        $anuais[] = $lembrete;
    }
}

foreach ($lembretes as $lembrete) {
    if ($lembrete['status'] === 'concluido') {
        $concluidos++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>OrgFiscal — Dashboard</title>

    <!-- Fonte -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="header-container">
        <div class="logo">
            <img src="assets/img/logo-orgfiscal.png" alt="OrgFiscal">
        </div>
    </div>
</header>

<!-- CONTEÚDO -->
<main class="container">

    <h1 class="page-title">
        <p class="progresso">
    <?= $concluidos ?> de <?= $totalLembretes ?> obrigações concluídas
</p>
        Checklist Fiscal — <?= str_pad((string)$mesAtual, 2, '0', STR_PAD_LEFT) ?>/<?= $anoAtual ?>
    </h1>

   <?php if (!empty($mensais)): ?>
  <h2 class="section-title">Obrigações Mensais</h2>

  <?php foreach ($mensais as $lembrete): ?>
    <?php
        $ehCritica = stripos($lembrete['nome'], 'PGDAS') !== false;
    ?>
    <div class="card 
    <?= $lembrete['status'] === 'concluido' ? 'card-concluido' : '' ?>
    <?= $ehCritica && $lembrete['status'] !== 'concluido' ? 'card-critica' : '' ?>
    ">

      <form method="post">
        <input type="hidden" name="lembrete_id" value="<?= $lembrete['lembrete_id'] ?>">
       <div class="checklist-item">

  <div class="checklist-left">
    <input
      type="checkbox"
      name="status"
      value="1"
      onchange="this.form.submit()"
      <?= $lembrete['status'] === 'concluido' ? 'checked' : '' ?>
    >
    <a class="tarefa-link" href="tarefa.php?id=<?= $lembrete['lembrete_id'] ?>">
      <?= htmlspecialchars($lembrete['nome']) ?>
    </a>
  </div>

  <?php if ($ehCritica && $lembrete['status'] !== 'concluido'): ?>
    <span class="badge-critica">Prioritária</span>
  <?php endif; ?>

</div>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($anuais)): ?>
  <h2 class="section-title">Obrigações Anuais</h2>

  <?php foreach ($anuais as $lembrete): ?>
    <div class="card <?= $lembrete['status'] === 'concluido' ? 'card-concluido' : '' ?>">
      <form method="post">
        <input type="hidden" name="lembrete_id" value="<?= $lembrete['lembrete_id'] ?>">
        <div class="checklist-item">
          <input
            type="checkbox"
            name="status"
            value="1"
            onchange="this.form.submit()"
            <?= $lembrete['status'] === 'concluido' ? 'checked' : '' ?>
          >
          <a class="tarefa-link" href="tarefa.php?id=<?= $lembrete['lembrete_id'] ?>">
            <?= htmlspecialchars($lembrete['nome']) ?>
          </a>
        </div>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<div class="nav-bottom">
  <a href="index.php" class="btn-inicio">
    🏠 Início
  </a>

  <button type="button" class="btn-voltar" onclick="history.back()">
    ⬅ Voltar
  </button>
</div>

</main>

</body>
</html>
