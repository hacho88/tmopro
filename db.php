<?php

function tmopro_db() {
    static $pdo = null;
    if ($pdo) return $pdo;

    $host = getenv('TMOPRO_DB_HOST') ?: '';
    $name = getenv('TMOPRO_DB_NAME') ?: '';
    $user = getenv('TMOPRO_DB_USER') ?: '';
    $pass = getenv('TMOPRO_DB_PASS') ?: '';

    // Fallback to db.json if env vars are not set
    if ($host === '' || $name === '' || $user === '') {
        $jsonPath = __DIR__ . '/db.json';
        if (file_exists($jsonPath)) {
            $cfg = json_decode(file_get_contents($jsonPath), true);
            if (is_array($cfg)) {
                $host = (string)($cfg['host'] ?? '');
                $name = (string)($cfg['name'] ?? '');
                $user = (string)($cfg['user'] ?? '');
                $pass = (string)($cfg['pass'] ?? '');
            }
        }
    }

    if ($host === '' || $name === '' || $user === '') {
        return null;
    }

    $dsn = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
