<?php
require_once __DIR__ . '/app/config/database.php';

$stmt = $pdo->query("SELECT id, nome FROM obrigacoes ORDER BY id");
$obrigacoes = $stmt->fetchAll();
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

  <h1 class="page-title">Checklist Fiscal — Mês Atual</h1>

  <?php foreach ($obrigacoes as $obrigacao): ?>
    <div class="card">
      <div class="checklist-item">
        <input type="checkbox" disabled>
        <span><?= htmlspecialchars($obrigacao['nome']) ?></span>
      </div>
    </div>
  <?php endforeach; ?>

</main>


</body>
</html>
