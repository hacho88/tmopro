<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function json_out($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json($path, $fallback) {
    if (!file_exists($path)) return $fallback;
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : $fallback;
}

function save_json($path, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
}

$token = getenv('DEEPSEEK_API_KEY') ?: '';
if ($token === '') {
    json_out(['ok' => false, 'error' => 'DEEPSEEK_API_KEY is not set'], 500);
}

$id = (int)($_POST['id'] ?? 0);
$name = trim((string)($_POST['name'] ?? ''));
$article = trim((string)($_POST['article'] ?? ''));
$brand = trim((string)($_POST['brand'] ?? ''));
$category = trim((string)($_POST['category'] ?? ''));

if ($id <= 0 || $name === '') {
    json_out(['ok' => false, 'error' => 'Invalid product payload'], 400);
}

$prompt = "Ты — эксперт по B2B сантехнике. Сгенерируй JSON строго такого вида: {\"description\":string,\"tags\":[string,...]}.\n\nТовар: {$name}\nАртикул: {$article}\nБренд: {$brand}\nКатегория: {$category}\n\nТребования: описание 600-1200 знаков, технический и продающий тон, без воды, без эмодзи. tags: 8-16 коротких ключевых слов/фраз для поиска.";

$payload = [
    'model' => 'deepseek-chat',
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant that outputs valid JSON only.'],
        ['role' => 'user', 'content' => $prompt],
    ],
    'temperature' => 0.6,
];

$ch = curl_init('https://api.deepseek.com/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 35,
]);

$raw = curl_exec($ch);
$err = curl_error($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($raw === false) {
    json_out(['ok' => false, 'error' => 'DeepSeek request failed: ' . $err], 502);
}

if ($code < 200 || $code >= 300) {
    json_out(['ok' => false, 'error' => 'DeepSeek HTTP ' . $code, 'response' => $raw], 502);
}

$resp = json_decode($raw, true);
$content = $resp['choices'][0]['message']['content'] ?? '';

$data = json_decode($content, true);
if (!is_array($data) || !isset($data['description']) || !isset($data['tags']) || !is_array($data['tags'])) {
    json_out(['ok' => false, 'error' => 'Model returned invalid JSON', 'content' => $content], 502);
}

$description = trim((string)$data['description']);
$tags = array_values(array_filter(array_map(fn($t) => trim((string)$t), $data['tags']), fn($t) => $t !== ''));

$productsPath = __DIR__ . '/../products.json';
$products = read_json($productsPath, []);

$found = false;
foreach ($products as &$p) {
    if ((int)($p['id'] ?? 0) === $id) {
        $p['description'] = $description;
        $p['tags'] = $tags;
        $found = true;
        break;
    }
}
unset($p);

if (!$found) {
    json_out(['ok' => false, 'error' => 'Product not found in products.json'], 404);
}

if (!save_json($productsPath, $products)) {
    json_out(['ok' => false, 'error' => 'Failed to write products.json'], 500);
}

json_out(['ok' => true, 'description' => $description, 'tags' => $tags]);
