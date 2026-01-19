<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/database.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $senha = $_POST['senha'] ?? '';

  if ($nome === '' || $email === '' || $senha === '') {
    $erro = 'Preencha todos os campos.';
  } else {
    $check = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $check->execute([$email]);

    if ($check->fetch()) {
      $erro = 'Este email já está cadastrado.';
    } else {
      $hash = password_hash($senha, PASSWORD_DEFAULT);

      $insert = $pdo->prepare(
        'INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)'
      );
      $insert->execute([$nome, $email, $hash]);

      $sucesso = 'Cadastro realizado com sucesso. Faça login.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OrgFiscal — Cadastro</title>

  <link rel="stylesheet" href="assets/css/reset.css">
  <link rel="stylesheet" href="assets/css/variables.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<header class="header">
  <div class="header-container">
    <div class="logo">
      <img src="assets/img/logo-orgfiscal.png" alt="OrgFiscal">
    </div>
  </div>
</header>

<main class="container">
  <h1 class="page-title">Criar Conta</h1>

  <?php if ($erro): ?>
    <p class="progresso" style="color:#d9534f;"><?= htmlspecialchars($erro) ?></p>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <p class="progresso" style="color:#2e7d32;"><?= htmlspecialchars($sucesso) ?></p>
  <?php endif; ?>

  <form method="post" class="card">
    <div class="checklist-item">
      <input type="text" name="nome" placeholder="Nome" required style="width:100%;">
    </div>
    <div class="checklist-item">
      <input type="email" name="email" placeholder="Email" required style="width:100%;">
    </div>
    <div class="checklist-item">
      <input type="password" name="senha" placeholder="Senha" required style="width:100%;">
    </div>

    <button class="btn-principal" type="submit">Cadastrar</button>
  </form>

  <p class="progresso">
    Já tem conta? <a class="tarefa-link" href="index.php">Entrar</a>
  </p>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <span>OrgFiscal — Todos os direitos reservados a Adriano Cardoso</span>
  </div>
</footer>

</body>
</html>
