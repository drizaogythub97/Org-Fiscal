<?php
declare(strict_types=1);

session_start();

/**
 * Logout simples, confiável e compatível com WebView/PWA
 */

// Limpa sessão
$_SESSION = [];

// Remove cookie da sessão
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroi sessão
session_destroy();

// Redirecionamento RELATIVO
header('Location: index.php');
exit;
