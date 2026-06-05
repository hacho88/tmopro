<?php
header('Content-Type: text/html; charset=utf-8');

echo '<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>TMOPRO Setup</title><style>body{font-family:Inter,system-ui,sans-serif;background:#f8fafc;color:#0f172a;padding:40px;max-width:760px;margin:0 auto;}h1{font-size:28px;margin-bottom:8px;}p{color:#64748b;margin-bottom:24px;}.step{padding:16px 20px;border-radius:16px;margin-bottom:12px;border:1px solid #e2e8f0;background:#fff;}.ok{color:#059669;font-weight:900;}.err{color:#dc2626;font-weight:900;}pre{background:#f1f5f9;padding:12px;border-radius:12px;overflow:auto;font-size:13px;}code{font-weight:900;}</style></head><body>';
echo '<h1>TMOPRO — Настройка базы данных</h1>';
echo '<p>Этот скрипт один раз создаст базу и таблицы. После успеха удалите <code>setup.php</code> с сервера.</p>';

$dbJson = __DIR__ . '/db.json';
$schemaFile = __DIR__ . '/sql/schema.sql';

if (!file_exists($dbJson)) {
    echo '<div class="step err">❌ Не найден <code>db.json</code>. Скопируйте <code>db.json.example → db.json</code> и внесите свои данные.</div>';
    echo '</body></html>';
    exit;
}

$cfg = json_decode(file_get_contents($dbJson), true);
if (!is_array($cfg) || empty($cfg['host']) || empty($cfg['name']) || empty($cfg['user'])) {
    echo '<div class="step err">❌ <code>db.json</code> некорректный. Должен содержать host, name, user, pass.</div>';
    echo '</body></html>';
    exit;
}

$host = $cfg['host'];
$name = $cfg['name'];
$user = $cfg['user'];
$pass = $cfg['pass'] ?? '';

try {
    // Step 1: connect without DB to create it
    $dsnNoDb = 'mysql:host=' . $host . ';charset=utf8mb4';
    $pdo = new PDO($dsnNoDb, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $name) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo '<div class="step ok">✅ База данных <code>' . htmlspecialchars($name) . '</code> создана (или уже существовала).</div>';

    // Step 2: use DB and run schema
    $pdo->exec('USE `' . str_replace('`', '``', $name) . '`');

    if (!file_exists($schemaFile)) {
        echo '<div class="step err">❌ Не найден <code>sql/schema.sql</code></div>';
        echo '</body></html>';
        exit;
    }

    $sql = file_get_contents($schemaFile);
    // Split by semicolon to run statements one by one
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        $pdo->exec($stmt . ';');
    }
    echo '<div class="step ok">✅ Таблицы созданы: <code>b2b_accounts, b2b_users, orders, order_items</code>.</div>';

    // Step 3: verify tables exist
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo '<div class="step ok">✅ Таблицы в базе: <code>' . htmlspecialchars(implode(', ', $tables)) . '</code></div>';

    echo '<div class="step" style="background:#ecfdf5;border-color:#a7f3d0;">';
    echo '<div class="ok" style="font-size:18px;">🎉 Готово! Удалите <code>setup.php</code> с сервера.</div>';
    echo '<div style="margin-top:12px;font-size:14px;color:#334155;">Теперь заказы будут сохраняться в MySQL, а DeepSeek-генерация будет работать при наличии ключа в <code>deepseek_key.json</code>.</div>';
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="step err">❌ Ошибка подключения к MySQL: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<div class="step">Проверь: хост, логин, пароль в <code>db.json</code>. На Timeweb хост обычно <code>localhost</code>.</div>';
}

echo '</body></html>';
