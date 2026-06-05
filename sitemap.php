<?php
header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://tmopro.ru';
$productsPath = __DIR__ . '/products.json';
$products = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = is_array($products) ? $products : [];

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Homepage
echo "  <url>\n";
echo "    <loc>$baseUrl/</loc>\n";
echo "    <priority>1.0</priority>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "  </url>\n";

// Product pages
foreach ($products as $p) {
    $id = (int)($p['id'] ?? 0);
    if ($id <= 0) continue;
    $url = "$baseUrl/product.php?id=$id";
    echo "  <url>\n";
    echo "    <loc>$url</loc>\n";
    echo "    <priority>0.8</priority>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "  </url>\n";
}

// Login (for B2B clients)
echo "  <url>\n";
echo "    <loc>$baseUrl/login.php</loc>\n";
echo "    <priority>0.5</priority>\n";
echo "    <changefreq>monthly</changefreq>\n";
echo "  </url>\n";

echo '</urlset>' . PHP_EOL;
