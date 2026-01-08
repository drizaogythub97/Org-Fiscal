<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/database.php';

$usuarioId = 1;
$mesAtual = (int) date('m');
$anoAtual = (int) date('Y');

/* ============================
   PROCESSA FORMULÁRIO
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $obrigacoesSelecionadas = $_POST['obrigacoes'] ?? [];
    $aplicarMeses = isset($_POST['aplicar_meses']);
    $aplicarAnos  = isset($_POST['aplicar_anos']);

    foreach ($obrigacoesSelecionadas as $obrigacaoId) {

        // Busca tipo da obrigação
        $stmt = $pdo->prepare("SELECT tipo FROM obrigacoes WHERE id = ?");
        $stmt->execute([$obrigacaoId]);
        $tipo = $stmt->fetchColumn();

        if (!$tipo) {
            continue;
        }

        // ===== OBRIGAÇÕES MENSAIS =====
        if ($tipo === 'mensal' && $aplicarMeses) {

            for ($mes = $mesAtual; $mes <= 12; $mes++) {

                $check = $pdo->prepare("
                    SELECT id FROM lembretes
                    WHERE usuario_id = ?
                      AND obrigacao_id = ?
                      AND competencia_mes = ?
                      AND competencia_ano = ?
                ");
                $check->execute([$usuarioId, $obrigacaoId, $mes, $anoAtual]);

                if (!$check->fetch()) {
                    $insert = $pdo->prepare("
                        INSERT INTO lembretes
                        (usuario_id, obrigacao_id, competencia_mes, competencia_ano)
                        VALUES (?, ?, ?, ?)
                    ");
                    $insert->execute([$usuarioId, $obrigacaoId, $mes, $anoAtual]);
                }
            }
        }

        // ===== OBRIGAÇÕES ANUAIS =====
        if ($tipo === 'anual' && $aplicarAnos) {

            for ($ano = $anoAtual; $ano <= $anoAtual + 5; $ano++) {

                $check = $pdo->prepare("
                    SELECT id FROM lembretes
                    WHERE usuario_id = ?
                      AND obrigacao_id = ?
                      AND competencia_ano = ?
                ");
                $check->execute([$usuarioId, $obrigacaoId, $ano]);

                if (!$check->fetch()) {
                    $insert = $pdo->prepare("
                        INSERT INTO lembretes
                        (usuario_id, obrigacao_id, competencia_ano)
                        VALUES (?, ?, ?)
                    ");
                    $insert->execute([$usuarioId, $obrigacaoId, $ano]);
                }
            }
        }
    }

    header('Location: index.php');
    exit;
}

/* ============================
   BUSCA OBRIGAÇÕES
============================ */
$stmt = $pdo->query("SELECT id, nome, tipo FROM obrigacoes ORDER BY tipo DESC, nome");
$obrigacoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Lembretes — OrgFiscal</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/main.css">
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

    <h1 class="page-title">Criar Lembretes</h1>

    <form method="post">

        <div class="card">
            <h3>Selecione as obrigações</h3>

            <?php foreach ($obrigacoes as $obrigacao): ?>
                <div class="checklist-item">
                    <div class="checklist-left">
                        <input
                            type="checkbox"
                            name="obrigacoes[]"
                            value="<?= $obrigacao['id'] ?>"
                        >
                        <span><?= htmlspecialchars($obrigacao['nome']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <h3>Aplicar lembretes para:</h3>

            <div class="checklist-item">
                <div class="checklist-left">
                    <input type="checkbox" name="aplicar_meses">
                    <span>Todos os meses restantes do ano</span>
                </div>
            </div>

            <div class="checklist-item">
                <div class="checklist-left">
                    <input type="checkbox" name="aplicar_anos">
                    <span>Próximos anos (obrigações anuais)</span>
                </div>
            </div>
        </div>

        <div class="card">
            <button type="submit" class="btn-principal">
                Criar lembretes
            </button>
        </div>

    </form>

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
