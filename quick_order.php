<?php
session_start();
$b2bUser = !empty($_SESSION['b2b_user_id']);
$b2bName = $_SESSION['b2b_user_name'] ?? '';

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];

$productsPath = __DIR__ . '/products.json';
$products = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = is_array($products) ? $products : [];
$productByArticle = [];
foreach ($products as $p) {
    $art = strtoupper(trim((string)($p['article'] ?? '')));
    if ($art !== '') $productByArticle[$art] = $p;
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Быстрый заказ — <?= htmlspecialchars($settings['site_short_name'] ?? 'TMOPRO') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,ui-sans-serif,system-ui,Arial;}</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
  <div class="max-w-3xl mx-auto px-4 py-12">
    <div class="mb-8">
      <a href="index.php" class="text-sm font-bold text-slate-500 hover:text-slate-900">← В каталог</a>
      <h1 class="text-3xl font-black mt-4">Быстрый заказ</h1>
      <p class="text-slate-500 font-semibold mt-2">Введите артикулы и количества. Каждый товар с новой строки: <code class="bg-slate-100 px-2 py-1 rounded font-mono text-sm">АРТИКУЛ КОЛИЧЕСТВО</code></p>
    </div>

    <form id="quickForm" class="space-y-4">
      <textarea id="lines" rows="10" class="w-full rounded-2xl border border-slate-200 bg-white p-5 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="CU001 10&#10;RIH-205 5&#10;TIM-90 20"></textarea>
      <div id="result" class="hidden rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="font-extrabold mb-3">Результат разбора</h3>
        <div id="resultBody"></div>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="parseLines()" class="rounded-2xl bg-slate-900 px-6 py-4 text-sm font-extrabold text-white hover:bg-slate-800 transition">Проверить</button>
        <button type="button" onclick="addToCart()" class="rounded-2xl bg-emerald-600 px-6 py-4 text-sm font-extrabold text-white hover:bg-emerald-700 transition">Добавить в корзину</button>
      </div>
    </form>
  </div>

  <script>
    const products = <?= json_encode($productByArticle, JSON_UNESCAPED_UNICODE) ?>;
    let parsed = [];

    function parseLines() {
      const raw = document.getElementById('lines').value;
      const lines = raw.split('\n').filter(l => l.trim());
      parsed = [];
      const body = document.getElementById('resultBody');
      body.innerHTML = '';
      let ok = 0, fail = 0;

      lines.forEach((line, idx) => {
        const parts = line.trim().split(/\s+/);
        const art = parts[0]?.toUpperCase() || '';
        const qty = parseInt(parts[1]) || 1;
        const p = products[art];
        if (p) {
          ok++;
          parsed.push({...p, qty});
          body.innerHTML += `<div class="flex justify-between py-2 border-b border-slate-100"><span class="font-bold">${p.name}</span><span class="text-emerald-600 font-extrabold">${qty} шт × ${p.price_base} ₽</span></div>`;
        } else {
          fail++;
          body.innerHTML += `<div class="flex justify-between py-2 border-b border-slate-100 text-red-500"><span class="font-bold">${art}</span><span class="font-extrabold">Не найден</span></div>`;
        }
      });

      body.innerHTML += `<div class="mt-3 text-sm font-bold text-slate-500">Найдено: ${ok}, не найдено: ${fail}</div>`;
      document.getElementById('result').classList.remove('hidden');
    }

    function addToCart() {
      if (!parsed.length) { parseLines(); if (!parsed.length) return; }
      let cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
      parsed.forEach(item => {
        const existing = cart.find(c => c.id === item.id);
        if (existing) existing.qty += item.qty;
        else cart.push({...item, qty: item.qty});
      });
      localStorage.setItem('tmopro_cart', JSON.stringify(cart));
      window.location.href = 'cart.php';
    }
  </script>
</body>
</html>
