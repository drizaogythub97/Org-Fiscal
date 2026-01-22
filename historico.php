<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config/bootstrap.php';
exigirLogin();



/* ============================
   ANO SELECIONADO
============================ */
$anoSelecionado = isset($_GET['ano']) ? (int) $_GET['ano'] : (int) date('Y');

/* ============================
   BUSCA ANOS DISPONÍVEIS
============================ */
$stmt = $pdo->prepare("
  SELECT DISTINCT competencia_ano
  FROM lembretes
  WHERE usuario_id = ?
  ORDER BY competencia_ano DESC
");
$stmt->execute([$usuarioId]);
$anosDisponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ============================
   TOTAL DE OBRIGAÇÕES MENSAIS
============================ */
$stmt = $pdo->query("
  SELECT COUNT(*) FROM obrigacoes WHERE tipo = 'mensal'
");
$totalMensais = (int) $stmt->fetchColumn();

/* ============================
   FUNÇÃO STATUS DO MÊS
============================ */
function statusDoMes(
    PDO $pdo,
    int $usuarioId,
    int $mes,
    int $ano,
    int $totalMensais
): string {

    // Busca mensais do mês
    $stmt = $pdo->prepare("
        SELECT l.status
        FROM lembretes l
        INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
        WHERE l.usuario_id = ?
          AND l.competencia_mes = ?
          AND l.competencia_ano = ?
          AND o.tipo = 'mensal'
    ");
    $stmt->execute([$usuarioId, $mes, $ano]);
    $mensais = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Março: verifica DEFIS
    if ($mes === 3) {
        $stmt = $pdo->prepare("
            SELECT l.status
            FROM lembretes l
            INNER JOIN obrigacoes o ON o.id = l.obrigacao_id
            WHERE l.usuario_id = ?
              AND l.competencia_ano = ?
              AND o.tipo = 'anual'
        ");
        $stmt->execute([$usuarioId, $ano]);
        $defis = $stmt->fetchColumn();

        $totalEsperado = $totalMensais + 1;

        if ($defis !== false) {
            $mensais[] = $defis;
        }

    } else {
        $totalEsperado = $totalMensais;
    }

    // Se não cadastrou tudo → vermelho
    if (count($mensais) < $totalEsperado) {
        return 'atraso';
    }

    // Se tudo concluído → verde
    if (!in_array('pendente', $mensais, true)) {
        return 'ok';
    }

    // Prazo: dia 20 do mês seguinte
    $anoPrazo = $mes === 12 ? $ano + 1 : $ano;
    $mesPrazo = $mes === 12 ? 1 : $mes + 1;

    $prazo = DateTime::createFromFormat(
    'Y-m-d',
    sprintf('%04d-%02d-20', $anoPrazo, $mesPrazo)
    );

    $hoje = new DateTime();

    return ($hoje <= $prazo) ? 'atencao' : 'atraso';
}

/* ============================
   MESES
============================ */
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
    4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
    10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Histórico — OrgFiscal</title>

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

    <h1 class="page-title">Histórico Anual</h1>

    <form method="get" class="card">
        <div class="checklist-item">
            <div class="checklist-left">
                <label>Ano:</label>
                <select name="ano">
                    <?php foreach ($anosDisponiveis as $a): ?>
                        <option value="<?= $a ?>" <?= $a === $anoSelecionado ? 'selected' : '' ?>>
                            <?= $a ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-principal" style="margin-left:12px;">
                    Buscar
                </button>
            </div>
        </div>
    </form>

    <?php foreach ($meses as $num => $nome): ?>
        <?php
        $status = statusDoMes($pdo, $usuarioId, $num, $anoSelecionado, $totalMensais);
        $classe = $status === 'ok' ? 'mes-ok' : ($status === 'atencao' ? 'mes-atencao' : 'mes-atraso');
        ?>
        <a href="concluir-tarefa.php?mes=<?= $num ?>&ano=<?= $anoSelecionado ?>" style="text-decoration:none;">
            <div class="card <?= $classe ?>">
                <strong><?= $nome ?></strong>
            </div>
        </a>
    <?php endforeach; ?>

    <div class="nav-bottom">
        <a href="dashboard.php" class="btn-inicio">🏠 Início</a>
        <button type="button" class="btn-voltar" onclick="history.back()">⬅ Voltar</button>
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
