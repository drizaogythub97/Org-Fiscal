<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/database.php';

/* ============================
   VALIDA ID
============================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Obrigação inválida.');
}

$lembreteId = (int) $_GET['id'];
$usuarioId = 1;

/* ============================
   BUSCA DADOS COMPLETOS
============================ */
$stmt = $pdo->prepare("
    SELECT
        o.nome,
        o.descricao,
        o.importancia,
        o.portal_nome,
        o.portal_url,
        o.passo_a_passo
    FROM lembretes l
    INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
    WHERE l.id = ? AND l.usuario_id = ?
");
$stmt->execute([$lembreteId, $usuarioId]);
$obrigacao = $stmt->fetch();

if (!$obrigacao) {
    die('Obrigação não encontrada.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($obrigacao['nome']) ?> — OrgFiscal</title>

    <!-- Fonte -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#3b6b8f">

</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/img/logo-orgfiscal.png" alt="OrgFiscal">
            </a>
        </div>
    </div>
</header>

<!-- CONTEÚDO -->
<main class="container">

    <h1 class="page-title"><?= htmlspecialchars($obrigacao['nome']) ?></h1>

    <div class="card">
        <h3>📌 O que é</h3>
        <p><?= nl2br(htmlspecialchars($obrigacao['descricao'])) ?></p>
    </div>

    <div class="card">
        <h3>⚠️ Por que é importante</h3>
        <p><?= nl2br(htmlspecialchars($obrigacao['importancia'])) ?></p>
    </div>

    <?php if (!empty($obrigacao['portal_url'])): ?>
        <div class="card">
            <h3>🌐 Onde fazer</h3>
            <p>
                Portal: <strong><?= htmlspecialchars($obrigacao['portal_nome']) ?></strong><br>
                <a href="<?= htmlspecialchars($obrigacao['portal_url']) ?>" target="_blank">
                    Acessar portal oficial
                </a>
            </p>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>🧭 Passo a passo resumido</h3>
        <p><?= nl2br(htmlspecialchars($obrigacao['passo_a_passo'])) ?></p>
    </div>

<div class="nav-bottom">
  <a href="index.php" class="btn-inicio">
    🏠 Início
  </a>

  <button type="button" class="btn-voltar" onclick="history.back()">
    ⬅ Voltar
  </button>
</div>

</main>

<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js');
  });
}
</script>

</body>
</html>
