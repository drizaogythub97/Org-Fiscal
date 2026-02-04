<?php
declare(strict_types=1);

/**
 * API Token helper — OrgFiscal
 */

function gerarTokenApi(PDO $pdo, int $usuarioId): string
{
    $token = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare("
        INSERT INTO api_tokens (usuario_id, token)
        VALUES (?, ?)
    ");
    $stmt->execute([$usuarioId, $token]);

    return $token;
}

function validarTokenApi(PDO $pdo, string $token): ?int
{
    $stmt = $pdo->prepare("
        SELECT usuario_id
        FROM api_tokens
        WHERE token = ?
          AND ativo = 1
        LIMIT 1
    ");
    $stmt->execute([$token]);

    $usuarioId = $stmt->fetchColumn();

    return $usuarioId ? (int) $usuarioId : null;
}
