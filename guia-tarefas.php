<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/bootstrap.php';
exigirLogin();



/* ============================
   BUSCA TODAS AS OBRIGAÇÕES
============================ */
$stmt = $pdo->query("
    SELECT id, nome, tipo
    FROM obrigacoes
    ORDER BY tipo DESC, nome
");
$obrigacoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Tarefas — OrgFiscal</title>

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

    <h1 class="page-title">Guia de Tarefas Fiscais</h1>

    <p class="progresso">
        Consulte abaixo todas as obrigações fiscais e clique para ver orientações detalhadas.
    </p>

    <?php foreach ($obrigacoes as $obrigacao): ?>
        <div class="card">
            <strong><?= htmlspecialchars($obrigacao['nome']) ?></strong>

            <p style="margin-top: 8px;">
                Tipo: <?= $obrigacao['tipo'] === 'mensal' ? 'Mensal' : 'Anual' ?>
            </p>

            <a
              href="tarefa.php?id=<?= $obrigacao['id'] ?>"
              class="tarefa-link"
              style="display:inline-block; margin-top: 12px;"
            >
                Saiba mais →
            </a>
        </div>
    <?php endforeach; ?>

<div class="nav-bottom">
  <a href="dashboard.php" class="btn-inicio">
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
