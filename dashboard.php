<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/bootstrap.php';
exigirLogin();

/*
  DASHBOARD — OrgFiscal
*/
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrgFiscal — Dashboard</title>

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
            <img src="assets/img/logo-orgfiscal.png" alt="OrgFiscal">
        </div>
    </div>
</header>

<!-- CONTEÚDO -->
<main class="container">

    <h1 class="page-title">Bem-vindo ao OrgFiscal</h1>

    <p class="progresso">
        Organize, acompanhe e mantenha em dia as obrigações fiscais da sua empresa.
    </p>

    <!-- CARDS CLICÁVEIS -->
    <a href="guia-tarefas.php" class="card card-link">
        <h3>📘 Guia de Tarefas</h3>
        <p>
            Veja todas as obrigações fiscais, entenda o que são, por que existem e onde realizá-las.
        </p>
    </a>

    <a href="criar-lembrete.php" class="card card-link">
        <h3>➕ Incluir Tarefas</h3>
        <p>
            Crie lembretes fiscais por mês ou ano, conforme a necessidade da sua empresa.
        </p>
    </a>

    <a href="concluir-tarefa.php" class="card card-link">
        <h3>✅ Concluir Tarefas</h3>
        <p>
            Marque como concluídas as obrigações de uma competência específica.
        </p>
    </a>

    <a href="historico.php" class="card card-link">
        <h3>📊 Consultar Histórico</h3>
        <p>
            Consulte o histórico anual e acompanhe o status das obrigações por mês.
        </p>
    </a>

</main>

<a href="logout.php" class="btn-voltar">Sair</a>


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
