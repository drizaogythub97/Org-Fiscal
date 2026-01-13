<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/database.php';

$usuarioId = 1;

/* ============================
   PROCESSA FORMULÁRIO
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $obrigacaoId = (int) $_POST['obrigacao_id'];
    $mes = (int) $_POST['mes'];
    $ano = (int) $_POST['ano'];
    $todosMeses = isset($_POST['todos_meses']);

    // Descobre tipo da obrigação
    $stmt = $pdo->prepare("SELECT tipo FROM obrigacoes WHERE id = ?");
    $stmt->execute([$obrigacaoId]);
    $tipo = $stmt->fetchColumn();

    if ($tipo === 'mensal') {

        $mesInicial = $todosMeses ? 1 : $mes;
        $mesFinal   = $todosMeses ? 12 : $mes;

        for ($m = $mesInicial; $m <= $mesFinal; $m++) {

            $check = $pdo->prepare("
                SELECT id FROM lembretes
                WHERE usuario_id = ?
                  AND obrigacao_id = ?
                  AND competencia_mes = ?
                  AND competencia_ano = ?
            ");
            $check->execute([$usuarioId, $obrigacaoId, $m, $ano]);

            if (!$check->fetch()) {
                $insert = $pdo->prepare("
                    INSERT INTO lembretes
                    (usuario_id, obrigacao_id, competencia_mes, competencia_ano)
                    VALUES (?, ?, ?, ?)
                ");
                $insert->execute([$usuarioId, $obrigacaoId, $m, $ano]);
            }
        }

    } elseif ($tipo === 'anual') {

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

    header('Location: index.php');
    exit;
}

/* ============================
   BUSCA OBRIGAÇÕES
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
    
    <title>Incluir Tarefa — OrgFiscal</title>

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

    <h1 class="page-title">Incluir Tarefa</h1>

    <form method="post" class="card">

        <div class="checklist-item">
            <div class="checklist-left">
                <label>Tipo de tarefa:</label>
                <select name="obrigacao_id" id="obrigacaoSelect" required>
                    <?php foreach ($obrigacoes as $o): ?>
                        <option value="<?= $o['id'] ?>">
                            <?= htmlspecialchars($o['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

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
                    <?php for ($a = date('Y'); $a <= date('Y') + 5; $a++): ?>
                        <option value="<?= $a ?>"><?= $a ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

      <div class="checklist-item" id="todosMesesBox" style="display:none;">
  <div class="checklist-left">
    <input type="checkbox" name="todos_meses">
    <span>Incluir tarefa para todos os meses do ano</span>
  </div>
</div>

        <button type="submit" class="btn-principal">
            Salvar
        </button>

    </form>

    <!-- Navegação -->
    <div class="nav-bottom">
        <a href="index.php" class="btn-inicio">🏠 Início</a>
        <button type="button" class="btn-voltar" onclick="history.back()">⬅ Voltar</button>
    </div>

</main>

<script>
const select = document.getElementById('obrigacaoSelect');
const box = document.getElementById('todosMesesBox');

select.addEventListener('change', function () {
  const texto = select.options[select.selectedIndex].text.toLowerCase();

  if (texto.includes('defis')) {
    box.style.display = 'none';
  } else {
    box.style.display = 'block';
  }
});

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js');
  });
}
</script>

</body>
</html>
