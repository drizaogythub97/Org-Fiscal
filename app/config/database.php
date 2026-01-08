<?php
declare(strict_types=1);

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
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    $options
  );
} catch (PDOException $e) {
  die('Erro PDO: ' . $e->getMessage());
}
