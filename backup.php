<?php
require_once __DIR__ . '/db.php';

header('Content-Type: text/plain; charset=utf-8');

$backupDir = __DIR__ . '/backups/' . date('Y-m-d_H-i-s');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Backup JSON files
$jsonFiles = ['products.json', 'categories.json', 'settings.json', 'pages.json', 'price_tiers.json'];
foreach ($jsonFiles as $file) {
    $src = __DIR__ . '/' . $file;
    if (file_exists($src)) {
        copy($src, $backupDir . '/' . $file);
        echo "OK: $file\n";
    } else {
        echo "SKIP: $file (not found)\n";
    }
}

// Backup MySQL via PDO
$sqlFile = $backupDir . '/database.sql';
$fp = fopen($sqlFile, 'w');

$pdo = tmopro_db_safe();
if (!$pdo) {
    echo "SKIP: database (no connection)\n";
    fclose($fp);
    exit;
}

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        fwrite($fp, "-- Table: $table\n");
        fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
        $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        fwrite($fp, $create['Create Table'] . ";\n\n");

        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $colStr = '`' . implode('`, `', $columns) . '`';
            foreach ($rows as $row) {
                $values = array_map(function ($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote((string)$v);
                }, array_values($row));
                fwrite($fp, "INSERT INTO `$table` ($colStr) VALUES (" . implode(', ', $values) . ");\n");
            }
        }
        fwrite($fp, "\n");
    }
    echo "OK: database.sql\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

fclose($fp);

echo "\nBackup saved to: $backupDir\n";
echo "Total size: " . number_format((float)(shell_exec("du -sb " . escapeshellarg($backupDir) . " 2>/dev/null") ?? 0), 0, ',', ' ') . " bytes\n";
