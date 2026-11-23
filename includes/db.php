<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = require __DIR__.'/config.php';
    $dsn = "mysql:host={$c['host']};port={$c['porta']};dbname={$c['banco']};charset=utf8mb4";
    return $pdo = new PDO($dsn, $c['usuario'], $c['senha'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
