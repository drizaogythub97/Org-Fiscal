<?php
declare(strict_types=1);

/**
 * Autenticação — OrgFiscal
 * Seguro para InfinityFree
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function usuarioLogado(): bool
{
    return isset($_SESSION['usuario_id']) && is_numeric($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        header('Location: /index.php');
        exit;
    }
}

function logout(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    header('Location: /index.php');
    exit;
}
