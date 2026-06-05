<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money($value) {
    return number_format((float)$value, 0, ',', ' ') . ' ₽';
}

$settingsPath = __DIR__ . '/settings.json';
$productsPath = __DIR__ . '/products.json';
$categoriesPath = __DIR__ . '/categories.json';

$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$products = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$categories = file_exists($categoriesPath) ? json_decode(file_get_contents($categoriesPath), true) : [];

$settings = is_array($settings) ? $settings : [];
$products = is_array($products) ? $products : [];

$productId = (int)($_GET['id'] ?? 0);
$product = null;
foreach ($products as $p) {
    if ((int)($p['id'] ?? 0) === $productId) {
        $product = $p;
        break;
    }
}

if (!$product) {
    http_response_code(404);
    header('Location: index.php');
    exit;
}

$siteName = $settings['site_name'] ?? 'TMOPRO';
$productName = (string)($product['name'] ?? '');
$productArticle = (string)($product['article'] ?? '');
$productBrand = (string)($product['brand'] ?? '');
$productCategory = (string)($product['category'] ?? '');
$productImage = (string)($product['image'] ?? '');
$productStock = (int)($product['stock'] ?? 0);
$productDescription = (string)($product['description'] ?? '');
$productTags = is_array($product['tags'] ?? null) ? $product['tags'] : [];
$priceBase = (float)($product['price_base'] ?? 0);
$priceWholesale = (float)($product['price_wholesale'] ?? 0);

$themeColor = $settings['theme_color'] ?? 'emerald';
$accentClass = $themeColor === 'indigo' ? 'text-indigo-600' : ($themeColor === 'slate' ? 'text-slate-900' : 'text-emerald-600');
$accentBg = $themeColor === 'indigo' ? 'bg-indigo-600' : ($themeColor === 'slate' ? 'bg-slate-900' : 'bg-emerald-600');
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($productName) ?> — <?= e($siteName) ?></title>
  <meta name="description" content="<?= e($productName . ' ' . $productArticle . ' ' . $productBrand) ?>. Оптовые цены от производителя.">
  <meta name="theme-color" content="#008A4E">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=lux-gold-f">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Segoe UI, Arial; background: #f8fafc; }
    .pd-hero { background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; }
    .pd-breadcrumb a { color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 800; }
    .pd-breadcrumb a:hover { color: #fff; }
    .pd-breadcrumb span { color: #64748b; font-size: 13px; font-weight: 800; }
    .pd-media { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-radius: 28px; border: 1px solid rgba(15,23,42,0.06); box-shadow: 0 24px 80px rgba(15,23,42,.10); overflow: hidden; }
    .pd-img { width:100%; height:100%; object-fit:contain; display:block; }
    .pd-placeholder { width:100%; height:100%; display:block; background: radial-gradient(circle at 30% 30%, rgba(10,163,107,0.12), transparent 60%), radial-gradient(circle at 70% 70%, rgba(201,163,94,0.12), transparent 60%), linear-gradient(135deg, #f1f5f9, #e2e8f0); }
    .pd-price-old { text-decoration: line-through; color: #94a3b8; font-size: 18px; font-weight: 800; }
    .pd-price-current { font-size: 36px; font-weight: 900; letter-spacing: -0.03em; }
    .pd-price-wholesale { font-size: 14px; font-weight: 900; color: #059669; }
    .pd-badge { display:inline-flex; align-items:center; gap:6px; padding: 6px 14px; border-radius: 999px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.10); font-size: 12px; font-weight: 900; color: #cbd5e1; }
    .pd-tag { display:inline-flex; padding: 6px 12px; border-radius: 999px; background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 12px; font-weight: 900; color: #334155; }
    .pd-desc { line-height: 1.75; color: #334155; }
    .pd-desc p { margin-bottom: 12px; }
    .pd-section-title { font-size: 18px; font-weight: 900; letter-spacing: -0.02em; color: #0f172a; }
    .pd-add-btn { width: 100%; padding: 16px; border-radius: 20px; font-size: 16px; font-weight: 900; letter-spacing: -0.01em; border: none; cursor: pointer; transition: all .2s ease; }
    .pd-add-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 48px rgba(5,150,105,.35); }
    .pd-info-row { display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    .pd-info-label { color: #64748b; font-weight: 800; }
    .pd-info-value { color: #0f172a; font-weight: 900; }
  </style>
</head>
<body>

<div class="pd-hero">
  <div class="container py-6">
    <div class="flex items-center justify-between mb-6">
      <a href="index.php" class="text-white font-extrabold text-lg tracking-tight" style="text-decoration:none;"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></a>
      <a href="index.php" class="pd-breadcrumb">← Назад в каталог</a>
    </div>
    <div class="pd-breadcrumb mb-4">
      <a href="index.php">Каталог</a>
      <span> / </span>
      <span><?= e($productCategory) ?></span>
      <span> / </span>
      <span><?= e($productName) ?></span>
    </div>
  </div>
</div>

<main class="container py-10 lg:py-16">
  <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
    <!-- Media -->
    <div class="pd-media" style="aspect-ratio: 4/3;">
      <?php if ($productImage): ?>
        <img src="<?= e($productImage) ?>" alt="<?= e($productName) ?>" class="pd-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <div class="pd-placeholder" style="display:none;"></div>
      <?php else: ?>
        <div class="pd-placeholder"></div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div>
      <div class="flex flex-wrap gap-2 mb-4">
        <span class="pd-badge">Артикул: <?= e($productArticle) ?></span>
        <span class="pd-badge">Бренд: <?= e($productBrand) ?></span>
        <span class="pd-badge">В наличии: <?= e($productStock) ?> шт</span>
      </div>

      <h1 class="text-3xl lg:text-4xl font-black tracking-tight text-gray-900 mb-6" style="line-height:1.15;"><?= e($productName) ?></h1>

      <div class="mb-8">
        <div class="pd-price-current <?= e($accentClass) ?>"><?= e(money($priceBase)) ?></div>
        <div class="pd-price-wholesale mt-2">Опт от 10 шт: <span class="font-extrabold"><?= e(money($priceWholesale)) ?></span></div>
      </div>

      <div class="mb-8">
        <div class="flex gap-3 items-center mb-4">
          <button id="qtyMinus" class="qty-btn" style="width:44px;height:44px;font-size:18px;">−</button>
          <input id="qtyInput" type="number" value="1" min="1" class="qty-input" style="height:44px;font-size:16px;">
          <button id="qtyPlus" class="qty-btn" style="width:44px;height:44px;font-size:18px;">+</button>
        </div>
        <button id="addToCart" class="pd-add-btn <?= e($accentBg) ?> text-white">Добавить в заявку</button>
        <div id="cartMsg" class="mt-3 text-sm font-extrabold text-emerald-700" style="display:none;">Добавлено в заявку. <a href="checkout.php" style="text-decoration:underline;">Оформить →</a></div>
      </div>

      <?php if ($productDescription): ?>
        <div class="mb-8">
          <div class="pd-section-title mb-4">Описание</div>
          <div class="pd-desc"><?= nl2br(e($productDescription)) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($productTags)): ?>
        <div class="mb-8">
          <div class="pd-section-title mb-4">Теги</div>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($productTags as $tag): ?>
              <span class="pd-tag"><?= e($tag) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-8">
        <div class="pd-section-title mb-4">Характеристики</div>
        <div>
          <div class="pd-info-row"><span class="pd-info-label">Артикул</span><span class="pd-info-value"><?= e($productArticle) ?></span></div>
          <div class="pd-info-row"><span class="pd-info-label">Бренд</span><span class="pd-info-value"><?= e($productBrand) ?></span></div>
          <div class="pd-info-row"><span class="pd-info-label">Категория</span><span class="pd-info-value"><?= e($productCategory) ?></span></div>
          <div class="pd-info-row"><span class="pd-info-label">Остаток</span><span class="pd-info-value"><?= e($productStock) ?> шт</span></div>
          <div class="pd-info-row"><span class="pd-info-label">Розничная цена</span><span class="pd-info-value"><?= e(money($priceBase)) ?></span></div>
          <div class="pd-info-row"><span class="pd-info-label">Оптовая цена (от 10 шт)</span><span class="pd-info-value"><?= e(money($priceWholesale)) ?></span></div>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="container pb-12 text-center text-sm font-bold text-gray-400">
  © <?= date('Y') ?> <?= e($siteName) ?>
</footer>

<script>
  const product = <?= json_encode($product, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

  const qtyInput = document.getElementById('qtyInput');
  const qtyMinus = document.getElementById('qtyMinus');
  const qtyPlus = document.getElementById('qtyPlus');
  const addBtn = document.getElementById('addToCart');
  const cartMsg = document.getElementById('cartMsg');

  function setQty(v) {
    const n = Math.max(1, parseInt(v || 1, 10));
    qtyInput.value = n;
    return n;
  }

  qtyMinus.addEventListener('click', () => setQty(Number(qtyInput.value) - 1));
  qtyPlus.addEventListener('click', () => setQty(Number(qtyInput.value) + 1));
  qtyInput.addEventListener('change', () => setQty(qtyInput.value));

  addBtn.addEventListener('click', () => {
    const amount = setQty(qtyInput.value);
    const cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
    const existing = cart.find(item => item.id === product.id);
    if (existing) { existing.qty += amount; }
    else { cart.push({ ...product, qty: amount }); }
    localStorage.setItem('tmopro_cart', JSON.stringify(cart));
    cartMsg.style.display = 'block';
    setTimeout(() => { cartMsg.style.display = 'none'; }, 3000);
  });
</script>

</body>
</html>
