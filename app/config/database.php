<?php
declare(strict_types=1);

/**
 * Conexão PDO — OrgFiscal
 * Compatível com InfinityFree
 */

date_default_timezone_set('America/Sao_Paulo');

$host = 'localhost';
$dbname = 'u879355098_orgfiscal';
$user = 'u879355098_orgfiscal_user';
$pass = 'LGkp265d#';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        $options
    );
} catch (Throwable $e) {
    // Nunca exibir erro em produção
    error_log('Erro PDO OrgFiscal: ' . $e->getMessage());
    http_response_code(500);
    exit('Erro interno no servidor.');
}
