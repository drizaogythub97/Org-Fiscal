<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/bootstrap.php';

// Se já estiver logado, vai direto para o dashboard
if (usuarioLogado()) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, nome, senha_hash FROM usuarios WHERE email = ?'
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            session_regenerate_id(true);

            $_SESSION['usuario_id']   = (int) $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Email ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OrgFiscal — Login</title>

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
  <h1 class="page-title">Entrar</h1>

  <?php if ($erro): ?>
    <p class="progresso" style="color:#d9534f;">
        <?= htmlspecialchars($erro) ?>
    </p>
  <?php endif; ?>

  <form method="post" class="card">
    <div class="checklist-item">
      <input type="email" name="email" placeholder="Email" required style="width:100%;">
    </div>

    <div class="checklist-item">
      <input type="password" name="senha" placeholder="Senha" required style="width:100%;">
    </div>

    <button class="btn-principal" type="submit">Entrar</button>
  </form>

  <p class="progresso">
    Não tem conta?
    <a class="tarefa-link" href="register.php">Cadastre-se</a>
  </p>
</main>

<footer class="footer">
  <div class="footer-container">
    <span>OrgFiscal — Todos os direitos reservados a Adriano Cardoso</span>
  </div>
</footer>

</body>
</html>
