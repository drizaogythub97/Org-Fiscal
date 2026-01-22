<?php
declare(strict_types=1);

/**
 * Bootstrap global do OrgFiscal
 * Carregado em TODAS as páginas internas
 */

// ===============================
// BASE PATH
// ===============================
define('BASE_PATH', dirname(__DIR__, 2));

// ===============================
// SESSÃO (inicia uma única vez)
// ===============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===============================
// HELPERS
// ===============================
require_once BASE_PATH . '/app/helpers/auth.php';

// ===============================
// BANCO DE DADOS
// ===============================
require_once BASE_PATH . '/app/config/database.php';

// ===============================
// USUÁRIO LOGADO
// ===============================
$usuarioId = $_SESSION['usuario_id'] ?? null;
