<?php
declare(strict_types=1);

/*
 | Logout seguro — OrgFiscal
 | Compatível com InfinityFree
 */

require_once __DIR__ . '/app/config/bootstrap.php';

/* Garante sessão ativa */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* Limpa variáveis */
$_SESSION = [];

/* Remove cookie de sessão */
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        true,
        true
    );
}

/* Destroi sessão */
session_destroy();

/* Fallback de redirecionamento (InfinityFree-safe) */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0;url=/index.php">
  <script>
    window.location.href = "/index.php";
  </script>
  <title>Saindo…</title>
</head>
<body>
</body>
</html>
