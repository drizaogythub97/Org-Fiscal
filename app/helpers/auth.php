<?php
declare(strict_types=1);
session_start();

function usuarioLogado(): bool {
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void {
    if (!usuarioLogado()) {
        header('Location: index.php');
        exit;
    }
}
