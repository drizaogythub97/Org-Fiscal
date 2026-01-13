<?php
declare(strict_types=1);

/*
  HOME — OrgFiscal
  Dashboard inicial
*/
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrgFiscal — Início</title>

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

    <!-- AÇÕES PRINCIPAIS -->
    <div class="card">
        <a href="guia-tarefas.php" class="tarefa-link">
            📘 Guia de Tarefas
        </a>
        <p>
            Veja todas as obrigações fiscais, entenda o que são, por que existem e onde realizá-las.
        </p>
    </div>

    <div class="card">
        <a href="criar-lembrete.php" class="tarefa-link">
            ➕ Incluir Tarefas
        </a>
        <p>
            Crie lembretes fiscais por mês ou ano, conforme a necessidade da sua empresa.
        </p>
    </div>

    <div class="card">
        <a href="concluir-tarefa.php" class="tarefa-link">
            ✅ Concluir Tarefas
        </a>
        <p>
            Marque como concluídas as obrigações de uma competência específica.
        </p>
    </div>

    <div class="card">
        <a href="historico.php" class="tarefa-link">
            📊 Consultar Histórico
        </a>
        <p>
            Consulte o histórico anual e acompanhe o status das obrigações por mês.
        </p>
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
