<?php
header('Content-Type: application/json; charset=utf-8');

$productsPath = __DIR__ . '/../products.json';
$products = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = is_array($products) ? $products : [];

$query = mb_strtolower(trim((string)($_GET['q'] ?? '')), 'UTF-8');
$limit = max(1, min(20, (int)($_GET['limit'] ?? 10)));

if ($query === '') {
    echo json_encode(['results' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$results = [];
foreach ($products as $p) {
    $name = mb_strtolower((string)($p['name'] ?? ''), 'UTF-8');
    $article = mb_strtolower((string)($p['article'] ?? ''), 'UTF-8');
    $brand = mb_strtolower((string)($p['brand'] ?? ''), 'UTF-8');
    $category = mb_strtolower((string)($p['category'] ?? ''), 'UTF-8');

    $score = 0;
    if (str_starts_with($article, $query)) $score += 100;
    elseif (str_contains($article, $query)) $score += 60;
    if (str_starts_with($name, $query)) $score += 80;
    elseif (str_contains($name, $query)) $score += 50;
    if (str_contains($brand, $query)) $score += 30;
    if (str_contains($category, $query)) $score += 20;

    if ($score > 0) {
        $results[] = [
            'id' => $p['id'] ?? 0,
            'name' => $p['name'] ?? '',
            'article' => $p['article'] ?? '',
            'brand' => $p['brand'] ?? '',
            'category' => $p['category'] ?? '',
            'price_base' => (float)($p['price_base'] ?? 0),
            'price_wholesale' => (float)($p['price_wholesale'] ?? 0),
            'image' => $p['image'] ?? '',
            'stock' => (int)($p['stock'] ?? 0),
            'score' => $score,
        ];
    }
}

usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
$results = array_slice($results, 0, $limit);

echo json_encode(['results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
