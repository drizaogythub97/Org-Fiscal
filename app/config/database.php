<?php
declare(strict_types=1);

/**
 * Conexão PDO — OrgFiscal
 * Compatível com InfinityFree
 */

date_default_timezone_set('America/Sao_Paulo');

$host = 'sql109.infinityfree.com';
$dbname = 'if0_40840312_orgfiscal_db';
$user = 'if0_40840312';
$pass = 'AyIQwoVIaluy';

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
