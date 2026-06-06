<?php
session_start();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$b2bUser = !empty($_SESSION['b2b_user_id']);
$b2bName = $_SESSION['b2b_user_name'] ?? '';

$pagesPath = __DIR__ . '/pages.json';
$footerPages = file_exists($pagesPath) ? json_decode(file_get_contents($pagesPath), true) : [];
$footerPages = is_array($footerPages) ? $footerPages : [];

$tiersPath = __DIR__ . '/price_tiers.json';
$priceTiers = file_exists($tiersPath) ? json_decode(file_get_contents($tiersPath), true) : [];
$priceTiers = is_array($priceTiers) ? $priceTiers : [];

$b2bTier = $_SESSION['b2b_price_tier'] ?? 'default';
$priceTiers = array_map(fn($t) => ['label' => $t['label'] ?? '', 'discount' => (float)($t['discount'] ?? 0)], $priceTiers);

$blocksPath = __DIR__ . '/blocks.json';
$blocks = file_exists($blocksPath) ? json_decode(file_get_contents($blocksPath), true) : [];
$blocks = is_array($blocks) ? $blocks : [];

$productsPath = __DIR__ . '/products.json';
$allProducts = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$allProducts = is_array($allProducts) ? $allProducts : [];

$categoriesPath = __DIR__ . '/categories.json';
$allCategories = file_exists($categoriesPath) ? json_decode(file_get_contents($categoriesPath), true) : [];
$allCategories = is_array($allCategories) ? $allCategories : [];

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];
$siteName = $settings['site_name'] ?? 'TMOPRO — Сантехника Оптом';
$heroTitle = $settings['hero_title'] ?? 'Сантехника оптом от производителя';
$heroSub = $settings['hero_subtitle'] ?? 'Премиальные решения для водоснабжения и отопления';
$heroBg = '/uploads/hero-bg.jpg'; // Замените на реальное имя файла из папки /uploads/
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($heroSub, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="keywords" content="сантехника оптом, смесители оптом, запорная арматура, трубы фитинги, водоснабжение, отопление, TIM, Riho, Kroner">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TMOPRO">
  <meta property="og:title" content="TMOPRO — Сантехника Оптом">
  <meta property="og:description" content="Премиальные решения для водоснабжения и отопления. Смесители, запорная арматура, трубы и фитинги оптом от производителя.">
  <meta property="og:url" content="https://tmopro.ru/">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="canonical" href="https://tmopro.ru/">
  <link rel="sitemap" type="application/xml" title="Sitemap" href="https://tmopro.ru/sitemap.php">
  <meta name="theme-color" content="#008A4E">
  <link rel="manifest" href="manifest.json">
  <?php if (!empty($settings['favicon'])): ?><link rel="icon" href="<?= e($settings['favicon']) ?>"><?php else: ?><link rel="icon" href="icon.svg" type="image/svg+xml"><?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php
  $fontFamily = $settings['font_family'] ?? 'Inter';
  $fontSlug = str_replace(' ', '+', $fontFamily);
  ?>
  <link href="https://fonts.googleapis.com/css2?family=<?= e($fontSlug) ?>:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>:root{ --font-sans: '<?= e($fontFamily) ?>', 'Noto Sans', system-ui, -apple-system, Segoe UI, Arial, sans-serif; }</style>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      corePlugins: {
        preflight: false,
      }
    }
  </script>
  <link rel="stylesheet" href="style.css?v=lux-dark-q">
  <script src="vue.global.prod.js"></script>
  <style>
    .fallback { max-width: 760px; margin: 80px auto; padding: 32px; border-radius: 24px; background: #fff; box-shadow: 0 24px 80px rgba(15,23,42,.12); font-family: var(--font-sans); color: #0f172a; }
    .fallback h1 { margin: 0 0 12px; font-size: 32px; line-height: 1.1; }
    .fallback p { margin: 0 0 20px; color: #64748b; line-height: 1.7; }
    .fallback a { display: inline-flex; margin-right: 10px; border-radius: 16px; background: #008A4E; color: #fff; padding: 13px 18px; font-weight: 800; text-decoration: none; }
    [v-cloak] { display: none !important; }
    .lux-header { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(226,232,240,0.6); }
  </style>
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","name":"TMOPRO","url":"https://tmopro.ru","logo":"https://tmopro.ru/logo.svg","contactPoint":{"@type":"ContactPoint","telephone":"+7-966-085-34-70","contactType":"sales","areaServed":"RU","availableLanguage":"Russian"},"sameAs":[]}</script>
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebSite","name":"TMOPRO — Сантехника Оптом","url":"https://tmopro.ru","potentialAction":{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"https://tmopro.ru/?search={search_term_string}"},"query-input":"required name=search_term_string"}}</script>
</head>
<body class="theme-luxury">
  <div id="app" v-cloak class="min-h-screen pb-16 md:pb-0">
    <!-- Header — transparent absolute over hero -->
    <header class="absolute top-0 left-0 right-0 z-50">
      <div class="max-w-7xl mx-auto px-4 w-full flex items-center justify-between py-5">
        <!-- Logo -->
        <a href="index.php" class="flex flex-col leading-none">
          <span class="text-2xl font-black tracking-tight" style="color: #d4af37;">TMOPRO</span>
          <span class="text-[10px] font-bold tracking-[0.2em] text-white/60 uppercase mt-0.5">Сантехника оптом</span>
        </a>
        <!-- Nav -->
        <nav class="hidden lg:flex items-center gap-8">
          <a href="#catalog" class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">Каталог</a>
          <a href="#catalog" class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">О компании</a>
          <a href="#catalog" class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">Партнерам</a>
          <a href="#catalog" class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">Доставка</a>
          <a href="#catalog" class="text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">Контакты</a>
        </nav>
        <!-- Actions -->
        <div class="hidden sm:flex items-center gap-5">
          <?php if ($b2bUser): ?>
            <a href="profile.php" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors duration-200">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <?= htmlspecialchars($b2bName, ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php else: ?>
            <a href="login.php" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors duration-200">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Вход для клиентов
            </a>
          <?php endif; ?>
          <a href="checkout.php" class="flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors duration-200 relative">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
            Корзина
            <span v-if="cartCount > 0" class="absolute -top-2 -right-3 min-w-[18px] h-[18px] rounded-full flex items-center justify-center text-[9px] font-bold text-black px-1" style="background: #d4af37;">{{ cartCount }}</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Toast Notifications -->
    <div class="toast-container">
      <div v-for="(t, i) in toasts" :key="t.id" :class="['toast', t.visible ? 'show' : '']">{{ t.message }}</div>
    </div>

    <!-- Dynamic Blocks -->
    <?php if (empty($blocks)): ?>
      <!-- Hero — Premium Dark -->
      <section class="relative bg-[#0d0d0d] min-h-[520px] md:min-h-[580px] w-full overflow-hidden flex items-center">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
          <img src="/uploads/hero-bg.jpg" alt="" class="w-full h-full object-cover object-center" onerror="this.style.display='none'">
        </div>
        <!-- Dark Overlay -->
        <div class="absolute inset-0 z-10" style="background: linear-gradient(90deg, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.75) 45%, rgba(0,0,0,0.35) 100%);"></div>
        <!-- Content -->
        <div class="relative z-20 max-w-7xl mx-auto px-4 w-full pt-24 pb-16">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
              <span class="text-xs font-semibold uppercase tracking-[0.15em] mb-3 block" style="color: #d4af37;">ПРЕМИАЛЬНАЯ САНТЕХНИКА</span>
              <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">Сантехника оптом<br>от производителя</h1>
              <p class="text-base md:text-lg mb-8 max-w-lg" style="color: #9ca3af;">TMOPRO — ваш надежный партнер в сфере оптовых поставок сантехники премиум-класса.</p>
              <div class="flex flex-wrap gap-3">
                <a href="#catalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 hover:brightness-110" style="background: #d4af37; color: #000;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
                  МГНОВЕННЫЙ РАСЧЕТ
                </a>
                <a href="#catalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm border border-white/30 text-white transition-all duration-200 hover:border-white hover:bg-white/10">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  СКАЧАТЬ ПРАЙС-ЛИСТ
                </a>
              </div>
            </div>
            <div class="hidden md:block"></div>
          </div>
          <!-- Bottom right badge -->
          <div class="absolute bottom-6 right-4 md:right-8 flex items-center gap-3 px-5 py-3 rounded-xl backdrop-blur-md border border-white/10" style="background: rgba(13,13,13,0.7);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <div>
              <div class="text-[11px] font-bold uppercase tracking-wider" style="color: #d4af37;">Гарантия качества</div>
              <div class="text-[10px] text-gray-400 mt-0.5">Вся продукция сертифицирована</div>
            </div>
          </div>
        </div>
      </section>
    <?php else: ?>
      <?php foreach ($blocks as $bi => $b):
        $bt = $b['type'] ?? '';
        $btitle = $b['title'] ?? '';
        $bsub = $b['subtitle'] ?? '';
        $bcont = $b['content'] ?? '';
        $bbtn = $b['button_text'] ?? '';
        $blink = $b['button_link'] ?? '';
      ?>
        <?php if ($bt === 'hero'): ?>
          <section class="lux-hero" style="margin-bottom:0;">
            <div class="container lux-hero-inner">
              <div class="lux-hero-copy">
                <?php if ($bsub): ?><div class="lux-kicker"><?= e($bsub) ?></div><?php endif; ?>
                <h1 class="lux-title"><?= e($btitle) ?></h1>
                <?php if ($bcont): ?><p class="lux-subtitle"><?= $bcont ?></p><?php endif; ?>
                <?php if ($bbtn): ?>
                  <div class="lux-hero-actions">
                    <a href="<?= e($blink ?: '#catalog') ?>" class="lux-btn-gold">
                      <span><?= e($bbtn) ?></span>
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
        <?php elseif ($bt === 'text'): ?>
          <section class="container py-16">
            <div class="max-w-3xl mx-auto text-center">
              <?php if ($btitle): ?><h2 class="text-3xl font-black text-gray-900 mb-6"><?= e($btitle) ?></h2><?php endif; ?>
              <?php if ($bsub): ?><p class="text-lg text-gray-500 font-bold mb-4"><?= e($bsub) ?></p><?php endif; ?>
              <?php if ($bcont): ?><div class="text-gray-600 leading-relaxed"><?= $bcont ?></div><?php endif; ?>
              <?php if ($bbtn): ?><a href="<?= e($blink ?: '#') ?>" class="btn btn-primary mt-8 inline-block"><?= e($bbtn) ?></a><?php endif; ?>
            </div>
          </section>
        <?php elseif ($bt === 'features'): ?>
          <section class="container py-16">
            <?php if ($btitle): ?><h2 class="text-3xl font-black text-center text-gray-900 mb-12"><?= e($btitle) ?></h2><?php endif; ?>
            <div class="grid md:grid-cols-3 gap-8">
              <?php
                $featLines = array_filter(array_map('trim', explode("\n", $bcont)));
                $featIcons = ['💎','⚡','🛡️','📦','🔄','⭐'];
                foreach ($featLines as $fi => $line):
                  $parts = explode(':', $line, 2);
                  $fTitle = trim($parts[0] ?? $line);
                  $fDesc = trim($parts[1] ?? '');
              ?>
                <div class="text-center p-6 rounded-2xl bg-gray-50">
                  <div style="font-size:40px;margin-bottom:12px;"><?= $featIcons[$fi % count($featIcons)] ?></div>
                  <h3 class="text-lg font-extrabold text-gray-900 mb-2"><?= e($fTitle) ?></h3>
                  <?php if ($fDesc): ?><p class="text-sm text-gray-500 font-bold"><?= e($fDesc) ?></p><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php elseif ($bt === 'products'): ?>
          <section class="container py-16">
            <?php if ($btitle): ?><h2 class="text-3xl font-black text-center text-gray-900 mb-12"><?= e($btitle) ?></h2><?php endif; ?>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
              <?php
                $showProducts = array_slice($allProducts, 0, 4);
                foreach ($showProducts as $p):
                  $pStock = (int)($p['stock'] ?? 0);
                  $pStockLabel = $pStock <= 0 ? 'Нет' : ($pStock < 10 ? 'Мало' : 'В наличии');
                  $pStockCls = $pStock <= 0 ? 'bg-red-50 text-red-700' : ($pStock < 10 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700');
              ?>
                <a href="product.php?id=<?= e($p['id'] ?? 0) ?>" style="text-decoration:none;display:block;background:#fff;border-radius:20px;border:1px solid #e2e8f0;padding:16px;transition:all .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 48px rgba(15,23,42,.08)';" onmouseout="this.style.transform='';this.style.boxShadow='';">
                  <div style="aspect-ratio:1;background:#f1f5f9;border-radius:16px;overflow:hidden;margin-bottom:12px;">
                    <?php if (!empty($p['image'])): ?><img src="<?= e($p['image']) ?>" style="width:100%;height:100%;object-fit:contain;padding:12px;"><?php endif; ?>
                  </div>
                  <div style="font-size:11px;font-weight:900;color:#94a3b8;margin-bottom:4px;"><?= e($p['article'] ?? '') ?></div>
                  <div style="font-size:14px;font-weight:900;color:#0f172a;margin-bottom:8px;line-height:1.3;"><?= e($p['name'] ?? '') ?></div>
                  <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:16px;font-weight:900;color:#0f172a;"><?= number_format((float)($p['price_base'] ?? 0), 0, ',', ' ') ?> ₽</span>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg <?= e($pStockCls) ?>"><?= e($pStockLabel) ?></span>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
            <?php if ($bbtn): ?><div class="text-center mt-8"><a href="<?= e($blink ?: '#catalog') ?>" class="btn btn-primary"><?= e($bbtn) ?></a></div><?php endif; ?>
          </section>
        <?php elseif ($bt === 'categories'): ?>
          <section class="container py-16">
            <?php if ($btitle): ?><h2 class="text-3xl font-black text-center text-gray-900 mb-12"><?= e($btitle) ?></h2><?php endif; ?>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
              <?php
                $showCats = [];
                foreach ($allCategories as $cat) {
                  foreach ($cat['subcategories'] ?? [] as $sub) {
                    $showCats[] = $sub;
                  }
                }
                $showCats = array_slice($showCats, 0, 8);
                foreach ($showCats as $sc):
              ?>
                <div style="background:#f8fafc;border-radius:20px;padding:20px;text-align:center;border:1px solid #e2e8f0;">
                  <div style="font-size:32px;margin-bottom:8px;">🏷️</div>
                  <div style="font-weight:900;color:#0f172a;"><?= e($sc['name'] ?? '') ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php elseif ($bt === 'cta'): ?>
          <section style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);padding:80px 0;">
            <div class="container text-center">
              <?php if ($btitle): ?><h2 class="text-3xl lg:text-4xl font-black text-white mb-6"><?= e($btitle) ?></h2><?php endif; ?>
              <?php if ($bsub): ?><p class="text-lg text-gray-300 font-bold mb-8 max-w-2xl mx-auto"><?= e($bsub) ?></p><?php endif; ?>
              <?php if ($bbtn): ?><a href="<?= e($blink ?: '#catalog') ?>" class="btn btn-primary" style="font-size:18px;padding:16px 32px;"><?= e($bbtn) ?></a><?php endif; ?>
            </div>
          </section>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Features — dark premium -->
    <section class="bg-[#121212] border-y border-white/[0.06]">
      <div class="max-w-7xl mx-auto px-4 w-full py-10 md:py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M2 12h20"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Оптовые цены</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Прямые поставки от производителя без посредников</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Мгновенный расчет</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Цена автоматически пересчитывается от количества</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Быстрая доставка</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Отгрузка в день заказа по всей России</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Сертификация</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Вся продукция с официальными сертификатами</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Light section: Categories + Catalog -->
    <div class="bg-[#f9fafb] py-12">
      <section class="max-w-7xl mx-auto px-4 w-full py-10 lg:py-16">
        <div class="section-head mb-8">
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900">Категории</h2>
          <p class="text-sm sm:text-base text-gray-500 font-semibold mt-2">Быстрый доступ к основным группам товара</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
          <button v-for="cat in topCategories" :key="cat.name" @click="toggleCategory(cat.name); document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth' });"
            class="category-tile hover-lift">
            <div v-if="cat.image" class="category-tile-bg" :style="{ backgroundImage: 'url(' + cat.image + ')' }"></div>
            <div v-else class="category-tile-fallback"></div>
            <div class="category-tile-overlay"></div>
            <div class="category-tile-content">
              <div class="category-tile-name">{{ cat.name }}</div>
              <div class="category-tile-count">{{ cat.count }} позиций</div>
            </div>
            <div class="category-tile-arrow" aria-hidden="true">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
            </div>
          </button>
        </div>
      </section>

      <!-- Catalog -->
    <main id="catalog" class="max-w-7xl mx-auto px-4 w-full py-10 lg:py-16 catalog-shell pb-20 md:pb-10">
      <div class="grid gap-8 lg:grid-cols-sidebar">
        <!-- Sidebar Filters -->
        <aside class="h-fit lg:sticky lg:top-24">
          <div class="card p-5 lg:p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
              <h2 class="text-base font-extrabold">Фильтры</h2>
              <button @click="resetFilters" class="text-xs font-bold text-gray-400 transition hover:text-gray-900">Сбросить</button>
            </div>

            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Например, Cu001</label>
            <live-search v-model="search" @select="onSearchSelect"></live-search>
            <div class="mb-4"></div>

            <div class="mb-6">
              <button @click="showFavoritesOnly = !showFavoritesOnly"
                :class="['chip w-full justify-between mb-2', showFavoritesOnly ? 'chip-active' : 'chip-default']">
                <span class="flex items-center gap-2">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  Избранное
                </span>
                <span class="opacity-60" style="font-size: 11px;">{{ favoritesCount }}</span>
              </button>
            </div>

            <div class="mb-6">
              <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Категория</div>
              <div class="space-y-2">
                <div v-for="cat in categoryList" :key="cat.id" class="sidebar-cat-group">
                  <div class="sidebar-cat-title">{{ cat.name }}</div>
                  <div class="sidebar-subcat-list">
                    <button v-for="sub in cat.subcategories" :key="sub.id" @click="toggleCategory(sub.name)"
                      :class="['sidebar-subcat-btn', selectedCategories.includes(sub.name) ? 'active' : '']">
                      <span class="flex items-center gap-2.5 min-w-0">
                        <span v-if="sub.image" class="sidebar-subcat-thumb" :style="{ backgroundImage: 'url(' + sub.image + ')' }"></span>
                        <span v-else class="sidebar-subcat-dot"></span>
                        <span class="truncate">{{ sub.name }}</span>
                      </span>
                      <span class="count">{{ countBy('category', sub.name) }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Бренд</div>
              <div class="flex flex-wrap gap-2">
                <button v-for="brand in brands" :key="brand" @click="toggleBrand(brand)"
                  :class="['chip', selectedBrands.includes(brand) ? 'chip-active' : 'chip-default']">
                  {{ brand }}
                </button>
              </div>
            </div>

            <!-- Compare -->
            <div v-if="compareList.length" class="mt-6 p-4 rounded-2xl bg-blue-50 border border-blue-100">
              <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-bold uppercase tracking-wider text-blue-400">⚖️ Сравнение ({{ compareList.length }})</div>
                <button @click="compareList = []; localStorage.setItem('tmopro_compare', '[]');" class="text-xs font-bold text-blue-400 hover:text-blue-600">Очистить</button>
              </div>
              <div class="space-y-2">
                <div v-for="cid in compareList" :key="cid" class="flex items-center justify-between">
                  <span class="text-xs font-extrabold text-blue-900 truncate" style="max-width: 160px;">{{ (products.find(p => p.id === cid)?.name || cid) }}</span>
                  <button @click="toggleCompare(cid)" class="text-blue-400 hover:text-blue-600 text-xs">×</button>
                </div>
              </div>
              <button @click="compareModal = true" class="btn btn-sm btn-primary w-full mt-3" style="font-size:12px;">Сравнить</button>
            </div>

            <!-- Recently Viewed -->
            <div v-if="recentViews.length" class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-100">
              <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">🕘 Недавно просмотрены</div>
              <div class="space-y-2">
                <a v-for="rid in recentViews" :key="rid" :href="'product.php?id=' + rid" @click.prevent="recordView(productById(rid)); window.location.href='product.php?id=' + rid" class="flex items-center gap-2 text-xs font-extrabold text-slate-700 hover:text-emerald-600 transition">
                  <img v-if="productById(rid)?.image" :src="productById(rid).image" class="w-8 h-8 rounded-lg object-cover" style="min-width:32px;">
                  <span v-else class="w-8 h-8 rounded-lg bg-slate-200 inline-block"></span>
                  <span class="truncate">{{ productById(rid)?.name || rid }}</span>
                </a>
              </div>
            </div>
          </div>
        </aside>

        <!-- Compare Modal -->
        <div v-if="compareModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="background: rgba(0,0,0,.5);" @click="compareModal = false">
          <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-auto p-6" @click.stop>
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-black">Сравнение товаров</h3>
              <button @click="compareModal = false" class="text-2xl font-bold text-gray-400 hover:text-gray-900">×</button>
            </div>
            <div class="overflow-x-auto">
              <table class="table w-full">
                <thead>
                  <tr>
                    <th class="text-left">Параметр</th>
                    <th v-for="cid in compareList" :key="cid" class="text-center" style="min-width:180px;">
                      <a :href="'product.php?id=' + cid" class="font-extrabold text-emerald-600 hover:underline">{{ (products.find(p => p.id === cid)?.name || cid) }}</a>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td class="font-bold text-gray-500">Артикул</td><td v-for="cid in compareList" :key="cid" class="text-center font-extrabold">{{ products.find(p => p.id === cid)?.article || '—' }}</td></tr>
                  <tr><td class="font-bold text-gray-500">Бренд</td><td v-for="cid in compareList" :key="cid" class="text-center">{{ products.find(p => p.id === cid)?.brand || '—' }}</td></tr>
                  <tr><td class="font-bold text-gray-500">Категория</td><td v-for="cid in compareList" :key="cid" class="text-center">{{ products.find(p => p.id === cid)?.category || '—' }}</td></tr>
                  <tr><td class="font-bold text-gray-500">Цена</td><td v-for="cid in compareList" :key="cid" class="text-center font-extrabold text-emerald-600">{{ money(products.find(p => p.id === cid)?.price_base || 0) }}</td></tr>
                  <tr><td class="font-bold text-gray-500">Опт от 10 шт</td><td v-for="cid in compareList" :key="cid" class="text-center font-extrabold">{{ money(products.find(p => p.id === cid)?.price_wholesale || 0) }}</td></tr>
                  <tr><td class="font-bold text-gray-500">Остаток</td><td v-for="cid in compareList" :key="cid" class="text-center">{{ products.find(p => p.id === cid)?.stock || 0 }} шт</td></tr>
                  <tr><td class="font-bold text-gray-500"></td><td v-for="cid in compareList" :key="cid" class="text-center"><button @click="addToCart(products.find(p => p.id === cid))" class="btn btn-sm btn-primary">В корзину</button></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Products -->
        <div>
          <!-- Toolbar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 p-4 toolbar-glass">
            <div class="text-sm font-bold text-gray-500">
              Найдено: <span class="text-gray-900 font-extrabold">{{ filteredProducts.length }}</span> товаров
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              <select v-model="sortBy" class="field" style="height:36px; font-size:13px; font-weight:800; padding:0 28px 0 12px; min-width:140px;">
                <option value="default">По умолчанию</option>
                <option value="price_asc">Дешевле</option>
                <option value="price_desc">Дороже</option>
                <option value="stock_asc">По остатку</option>
                <option value="name_asc">По названию</option>
              </select>
              <label class="flex items-center gap-2 text-xs font-extrabold text-gray-600 cursor-pointer select-none">
                <input type="checkbox" v-model="showInStockOnly" class="accent-emerald-600" style="width:16px;height:16px;">
                Только в наличии
              </label>
              <div class="flex items-center gap-2">
                <input v-model.number="minPrice" type="number" placeholder="от ₽" class="field" style="width:80px;height:32px;font-size:12px;padding:0 8px;">
                <span class="text-xs font-bold text-gray-400">—</span>
                <input v-model.number="maxPrice" type="number" placeholder="до ₽" class="field" style="width:80px;height:32px;font-size:12px;padding:0 8px;">
              </div>
              <button type="button" @click="toggleDense" :class="['density-toggle', dense ? 'is-on' : '']">
                <span class="density-dot" aria-hidden="true"></span>
                <span>Компактно</span>
              </button>
              <div class="segmented">
                <button @click="view = 'grid'" :class="['segmented-item', view === 'grid' ? 'is-active' : '']">Сетка</button>
                <button @click="view = 'table'" :class="['segmented-item', view === 'table' ? 'is-active' : '']">Таблица</button>
              </div>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="card p-12 text-center">
            <div :class="['mx-auto mb-4 animate-spin rounded-full border-4 border-t-transparent', accentBorder]" style="width: 40px; height: 40px;"></div>
            <p class="font-bold text-gray-500">Загрузка...</p>
          </div>

          <!-- Table View -->
          <div v-else-if="view === 'table'" class="table-wrap">
            <table class="table min-w-900">
              <thead>
                <tr>
                  <th>Артикул</th>
                  <th>Категория</th>
                  <th>Остаток</th>
                  <th>Цена</th>
                  <th>Кол-во</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in paginatedProducts" :key="product.id" class="animate-fadeIn">
                  <td>
                    <div class="flex items-center gap-3">
                      <a :href="'product.php?id=' + product.id" class="shrink-0">
                        <img v-if="product.image" :src="product.image" @error="onProductImgError(product)" class="rounded-lg object-cover" style="width: 48px; height: 48px;">
                        <span v-else class="table-img-placeholder" aria-hidden="true" style="display:inline-block;width:48px;height:48px;"></span>
                      </a>
                      <div>
                        <a :href="'product.php?id=' + product.id" class="font-extrabold text-gray-900 hover:underline" style="text-decoration:none;">{{ product.name }}</a>
                        <div class="mt-1 text-xs font-bold text-gray-400">{{ product.article }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge badge-gray">{{ product.category }}</span></td>
                  <td><span :class="['text-xs font-extrabold px-2.5 py-1 rounded-lg', stockStatus(product.stock).cls]">{{ stockStatus(product.stock).label }}</span></td>
                  <td><price-block :product="product" :qty="qty[product.id]" :tier="b2bTier" :tiers="priceTiers"></price-block></td>
                  <td>
                    <qty-control :model-value="qty[product.id]" @update:model-value="setQty(product.id, $event)"></qty-control>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <button @click="addToCart(product)" :class="['btn btn-sm btn-primary', cartBump ? 'animate-bounce' : '']">В корзину</button>
                      <button type="button" @click.stop.prevent="recordView(product); quickViewProduct = product" class="flex items-center justify-center" style="width:28px;height:28px;border-radius:8px;border:none;background:transparent;cursor:pointer; color:#64748b;" :title="'Быстрый просмотр'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                      </button>
                      <button type="button" @click.stop.prevent="toggleFavorite(product.id)" class="flex items-center justify-center" style="width:28px;height:28px;border-radius:8px;border:none;background:transparent;cursor:pointer;" :style="isFavorite(product.id) ? 'color:#ef4444;' : 'color:#cbd5e1;'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Grid View — premium white cards -->
          <div v-else class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 mt-8">
            <article v-for="product in paginatedProducts" :key="product.id" class="flex flex-col bg-white rounded-xl border border-gray-100 p-4 min-h-[380px] hover:shadow-lg transition-all duration-300 animate-fadeIn">
              <a :href="'product.php?id=' + product.id" @click.prevent="recordView(product); window.location.href='product.php?id=' + product.id" class="block">
                <div class="w-full h-44 bg-white flex items-center justify-center mb-4 p-2 rounded-lg">
                  <img v-if="product.image" :src="product.image" class="max-h-full max-w-full object-contain" @error="onProductImgError(product)">
                  <div v-else class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-md"></div>
                </div>
                <div class="text-xs text-gray-400 mb-1">{{ product.brand || product.article }}</div>
                <h3 class="text-sm font-medium text-gray-900 line-clamp-2 min-h-[2.5rem] mb-2">{{ product.name }}</h3>
              </a>
              <div class="mt-auto">
                <div class="flex items-center justify-between gap-2 mb-3">
                  <span class="text-base font-bold text-gray-950">{{ product.price_base.toLocaleString('ru-RU') }} ₽</span>
                  <span :class="['text-[10px] font-extrabold px-2 py-1 rounded-md', stockStatus(product.stock).cls]">{{ stockStatus(product.stock).label }}</span>
                </div>
                <button @click="addToCart(product)" class="w-full py-2.5 rounded-lg bg-[#0f172a] text-white text-xs font-extrabold uppercase tracking-wider transition-all duration-200 hover:bg-[#1e293b]">В заявку</button>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-8">
            <button @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="btn btn-sm btn-ghost" style="padding: 6px 12px;">←</button>
            <button v-for="p in totalPages" :key="p" @click="page = p" :class="['btn btn-sm', page === p ? 'btn-dark' : 'btn-ghost']" style="padding: 6px 12px; min-width: 36px;">{{ p }}</button>
            <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages" class="btn btn-sm btn-ghost" style="padding: 6px 12px;">→</button>
          </div>
        </div>
      </div>
    </main>
    </div>

    <!-- Bottom Navigation — mobile only (<768px) -->
    <!-- Mobile Bottom Nav -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 h-16 bg-[#0d0d0d]/95 backdrop-blur-md border-t border-white/10 flex md:hidden justify-around items-center" aria-label="Bottom navigation">
      <div class="grid grid-cols-5 w-full">
        <a href="index.php" class="active flex flex-col items-center justify-center gap-1 py-2 text-white/60 transition-colors hover:text-white">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg>
          <span class="text-[9px] font-semibold tracking-wide">Главная</span>
        </a>
        <button @click="scrollToCatalog(); showFavoritesOnly = false;" class="flex flex-col items-center justify-center gap-1 py-2 text-white/60 transition-colors hover:text-white">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 7h16"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M6 7 7 20a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
          <span class="text-[9px] font-semibold tracking-wide">Каталог</span>
        </button>
        <button @click="showFavoritesOnly = !showFavoritesOnly; scrollToCatalog();" :class="['flex flex-col items-center justify-center gap-1 py-2 transition-colors hover:text-white', showFavoritesOnly ? 'text-rose-400' : 'text-white/60']">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          <span class="text-[9px] font-semibold tracking-wide">Избранное</span>
        </button>
        <a href="checkout.php" class="relative flex flex-col items-center justify-center gap-1 py-2 text-white/60 transition-colors hover:text-white">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          <span class="text-[9px] font-semibold tracking-wide">Корзина</span>
          <b v-if="cartCount > 0" class="absolute top-0.5 right-2 rounded-full px-1 text-white text-[8px] font-black bg-[#C9A35E]">{{ cartCount }}</b>
        </a>
        <a :href="'tel:' + settings.phone" class="flex flex-col items-center justify-center gap-1 py-2 text-white/60 transition-colors hover:text-white">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.1 5.18 2 2 0 0 1 5.11 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.6a2 2 0 0 1-.45 2.11L9 10.7a16 16 0 0 0 4.3 4.3l1.27-1.27a2 2 0 0 1 2.11-.45c.83.3 1.7.51 2.6.63A2 2 0 0 1 22 16.92Z"/></svg>
          <span class="text-[9px] font-semibold tracking-wide">Позвонить</span>
        </a>
      </div>
    </nav>

    <!-- Footer spacer for mobile -->
    <!-- Quick View Modal -->
    <div v-if="quickViewProduct" style="position:fixed; inset:0; z-index:90; display:flex; align-items:center; justify-content:center; padding:16px;" @click.self="quickViewProduct = null">
      <div style="position:absolute; inset:0; background:rgba(15,23,42,.5); backdrop-filter:blur(4px);"></div>
      <div style="position:relative; background:#fff; border-radius:24px; max-width:720px; width:100%; max-height:90vh; overflow:auto; box-shadow:0 40px 100px rgba(15,23,42,.24); padding:28px;">
        <button @click="quickViewProduct = null" style="position:absolute; top:16px; right:16px; width:36px; height:36px; border-radius:12px; border:none; background:#f1f5f9; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:900; color:#64748b;">×</button>
        <div class="grid md:grid-cols-2 gap-6">
          <div style="background:#f1f5f9; border-radius:20px; aspect-ratio:1; display:flex; align-items:center; justify-content:center; overflow:hidden;">
            <img v-if="quickViewProduct.image" :src="quickViewProduct.image" style="width:100%; height:100%; object-fit:contain; padding:20px;">
            <div v-else style="color:#94a3b8; font-size:14px; font-weight:900;">Нет фото</div>
          </div>
          <div>
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">{{ quickViewProduct.article }}</div>
            <h3 class="text-xl font-black text-gray-900 mb-4" style="line-height:1.25;">{{ quickViewProduct.name }}</h3>
            <div class="flex flex-wrap gap-2 mb-4">
              <span class="badge badge-primary">{{ quickViewProduct.brand }}</span>
              <span class="badge badge-gray">{{ quickViewProduct.category }}</span>
              <span :class="['text-xs font-extrabold px-2.5 py-1 rounded-lg', stockStatus(quickViewProduct.stock).cls]">{{ stockStatus(quickViewProduct.stock).label }}</span>
            </div>
            <div class="mb-6">
              <price-block :product="quickViewProduct" :qty="qty[quickViewProduct.id]" :tier="b2bTier" :tiers="priceTiers"></price-block>
            </div>
            <div class="flex items-center gap-3 mb-4">
              <qty-control :model-value="qty[quickViewProduct.id]" @update:model-value="setQty(quickViewProduct.id, $event)"></qty-control>
              <button @click="addToCart(quickViewProduct); quickViewProduct = null" class="btn btn-primary" style="flex:1;">Добавить в заявку</button>
            </div>
            <a :href="'product.php?id=' + quickViewProduct.id" class="text-sm font-extrabold text-emerald-600 hover:underline">Подробнее →</a>
          </div>
        </div>
      </div>
    </div>

    <div class="md:hidden" style="height: 100px;"></div>
  </div>

  <div id="app-fallback" class="fallback">
    <h1>Сайт загружается</h1>
    <p>Если каталог не появился через несколько секунд, браузер не смог загрузить скрипты интерфейса. Попробуйте обновить страницу или открыть сайт в другом браузере.</p>
    <a href="index.php?v=3">Обновить</a>
    <a href="panel.php">Админка</a>
  </div>

  <script>
    if (!window.Vue) { throw new Error('Vue CDN failed to load'); }
    const { createApp } = Vue;

    const QtyControl = {
      props: ['modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="qty-control">
          <button type="button" @click="update(Math.max(1, Number(modelValue) - 1))" class="qty-btn">−</button>
          <input :value="modelValue" @input="update($event.target.value)" type="number" min="1" class="qty-input">
          <button type="button" @click="update(Number(modelValue) + 1)" class="qty-btn">+</button>
        </div>
      `,
      methods: {
        update(value) {
          const normalized = Math.max(1, parseInt(value || 1, 10));
          this.$emit('update:modelValue', normalized);
        }
      }
    };

    const PriceBlock = {
      props: ['product', 'qty', 'tier', 'tiers'],
      template: `
        <div style="min-width: 130px;">
          <div v-if="Number(qty) >= 10" class="flex flex-col">
            <span class="text-xs font-bold text-gray-400 line-through number-smooth">{{ money(product.price_base) }}</span>
            <span class="text-lg font-extrabold text-primary number-smooth">{{ money(price(product.price_wholesale)) }}</span>
          </div>
          <div v-else class="flex flex-col">
            <span class="text-lg font-extrabold text-gray-900 number-smooth">{{ money(price(product.price_base)) }}</span>
            <span class="text-xs font-bold text-gray-400">опт от 10 шт: {{ money(price(product.price_wholesale)) }}</span>
          </div>
          <span v-if="discount > 0" class="text-[10px] font-black text-emerald-600 mt-1">-{{ discount }}% B2B</span>
        </div>
      `,
      computed: {
        discount() {
          return this.tier && this.tiers && this.tiers[this.tier] ? (this.tiers[this.tier].discount || 0) : 0;
        }
      },
      methods: {
        price(value) {
          const d = this.discount;
          return d > 0 ? Math.round(value * (100 - d) / 100) : value;
        },
        money(value) {
          return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value);
        }
      }
    };

    const LiveSearch = {
      props: ['modelValue'],
      emits: ['update:modelValue', 'select'],
      data() {
        return {
          query: this.modelValue || '',
          results: [],
          show: false,
          activeIndex: -1,
          loading: false,
          debounceTimer: null
        };
      },
      watch: {
        modelValue(val) { this.query = val; }
      },
      template: `
        <div class="live-search" style="position:relative;">
          <input
            v-model="query"
            @input="onInput"
            @keydown.down.prevent="moveDown"
            @keydown.up.prevent="moveUp"
            @keydown.enter.prevent="selectActive"
            @keydown.esc="show = false"
            @focus="onFocus"
            @blur="onBlur"
            type="text"
            placeholder="Например, Cu001"
            class="input"
            style="width:100%;"
            autocomplete="off"
          >
          <div v-if="loading" class="live-search-loader">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
          </div>
          <div v-if="show && results.length" class="live-search-dropdown">
            <a
              v-for="(item, idx) in results"
              :key="item.id"
              :href="'product.php?id=' + item.id"
              @mouseenter="activeIndex = idx"
              :class="['live-search-item', idx === activeIndex ? 'is-active' : '']"
              @click.prevent="goToProduct(item)"
            >
              <div class="live-search-img-wrap">
                <img v-if="item.image" :src="item.image" class="live-search-img">
                <div v-else class="live-search-img-placeholder"></div>
              </div>
              <div class="live-search-info">
                <div class="live-search-name">{{ item.name }}</div>
                <div class="live-search-meta">{{ item.article }} · {{ item.brand }} · {{ item.category }}</div>
                <div class="live-search-price">{{ money(item.price_base) }}</div>
              </div>
            </a>
          </div>
          <div v-else-if="show && query.length >= 2 && !loading && !results.length" class="live-search-dropdown">
            <div class="live-search-empty">Например, Cu001</div>
          </div>
        </div>
      `,
      methods: {
        money(value) {
          return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value);
        },
        onInput() {
          this.$emit('update:modelValue', this.query);
          clearTimeout(this.debounceTimer);
          if (this.query.length < 2) { this.results = []; this.show = false; return; }
          this.loading = true;
          this.debounceTimer = setTimeout(() => this.fetchResults(), 250);
        },
        async fetchResults() {
          try {
            const res = await fetch('api/search.php?q=' + encodeURIComponent(this.query) + '&limit=8');
            const data = await res.json();
            this.results = data.results || [];
            this.activeIndex = -1;
            this.show = true;
          } catch (e) {
            this.results = [];
          } finally {
            this.loading = false;
          }
        },
        onFocus() {
          if (this.query.length >= 2 && this.results.length) this.show = true;
        },
        onBlur() {
          setTimeout(() => { this.show = false; }, 200);
        },
        moveDown() {
          if (!this.results.length) return;
          this.activeIndex = (this.activeIndex + 1) % this.results.length;
        },
        moveUp() {
          if (!this.results.length) return;
          this.activeIndex = (this.activeIndex - 1 + this.results.length) % this.results.length;
        },
        selectActive() {
          if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
            this.goToProduct(this.results[this.activeIndex]);
          }
        },
        goToProduct(item) {
          this.$emit('select', item);
          window.location.href = 'product.php?id=' + item.id;
        }
      }
    };

    createApp({
      components: { QtyControl, PriceBlock, LiveSearch },
      data() {
        return {
          settings: { site_name: 'TMOPRO — Сантехника Оптом', site_short_name: 'TMOPRO', phone: '+7 (966) 085-34-70', email_manager: 'info@tmopro.ru', theme_color: 'emerald', default_view: 'table', logo_type: 'text', logo_text: 'TMO', logo_url: '', background_type: 'gradient', background_color: '#f8fafc', background_image: '', background_image_mobile: '', hero_title: 'Сантехника оптом от производителя. Все на одной площадке.', hero_subtitle: 'Подберите позиции, укажите количество и отправьте заявку на счет. Оптовая цена включается автоматически от 10 штук.' },
          b2bTier: '<?= htmlspecialchars($b2bTier, ENT_QUOTES, 'UTF-8') ?>',
          priceTiers: <?= json_encode($priceTiers, JSON_UNESCAPED_UNICODE) ?>,
          products: [],
          categories: [],
          qty: {},
          selectedCategories: [],
          selectedBrands: [],
          search: '',
          view: 'grid',
          dense: false,
          loading: true,
          cartBump: false,
          showFavoritesOnly: false,
          showInStockOnly: false,
          minPrice: '',
          maxPrice: '',
          favorites: [],
          toasts: [],
          toastId: 0,
          sortBy: 'default',
          quickViewProduct: null,
          page: 1,
          perPage: 24,
          compareList: JSON.parse(localStorage.getItem('tmopro_compare') || '[]'),
          compareModal: false,
          recentViews: JSON.parse(localStorage.getItem('tmopro_recent') || '[]')
        };
      },
      computed: {
        categoryList() { return this.categories; },
        topCategories() {
          const byName = new Map();
          (this.categories || []).forEach(cat => {
            (cat.subcategories || []).forEach(sub => {
              const name = (sub && sub.name) ? String(sub.name) : '';
              if (!name) return;
              const count = this.countBy('category', name);
              const image = (sub && sub.image) ? String(sub.image) : '';
              const existing = byName.get(name);
              const shouldReplace = !existing
                || (Number(existing.count) || 0) < (count || 0)
                || (!existing.image && image);
              if (shouldReplace) byName.set(name, { name, count, image });
            });
          });
          return Array.from(byName.values()).sort((a, b) => (b.count || 0) - (a.count || 0)).slice(0, 9);
        },
        brands() { return [...new Set(this.products.map(item => item.brand))]; },
        cart() { return JSON.parse(localStorage.getItem('tmopro_cart') || '[]'); },
        cartCount() { return this.cart.reduce((sum, item) => sum + Number(item.qty), 0); },
        favoritesCount() { return this.favorites.length; },
        filteredProducts() {
          const min = parseFloat(this.minPrice);
          const max = parseFloat(this.maxPrice);
          let list = this.products.filter(product => {
            const byCategory = !this.selectedCategories.length || this.selectedCategories.includes(product.category);
            const byBrand = !this.selectedBrands.length || this.selectedBrands.includes(product.brand);
            const bySearch = !this.search || product.article.toLowerCase().includes(this.search.toLowerCase()) || product.name.toLowerCase().includes(this.search.toLowerCase());
            const byFav = !this.showFavoritesOnly || this.favorites.includes(product.id);
            const byStock = !this.showInStockOnly || (Number(product.stock) || 0) > 0;
            const price = Number(product.price_base) || 0;
            const byPrice = (!this.minPrice || price >= min) && (!this.maxPrice || price <= max);
            return byCategory && byBrand && bySearch && byFav && byStock && byPrice;
          });
          switch (this.sortBy) {
            case 'price_asc': list.sort((a, b) => (Number(a.price_base) || 0) - (Number(b.price_base) || 0)); break;
            case 'price_desc': list.sort((a, b) => (Number(b.price_base) || 0) - (Number(a.price_base) || 0)); break;
            case 'stock_asc': list.sort((a, b) => (Number(a.stock) || 0) - (Number(b.stock) || 0)); break;
            case 'name_asc': list.sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), 'ru')); break;
          }
          return list;
        },
        paginatedProducts() {
          const start = (this.page - 1) * this.perPage;
          return this.filteredProducts.slice(start, start + this.perPage);
        },
        totalPages() { return Math.ceil(this.filteredProducts.length / this.perPage) || 1; },
        accentBg() { return { indigo: 'bg-primary', emerald: 'bg-primary', slate: 'bg-dark-2' }[this.settings.theme_color] || 'bg-primary'; },
        accentBorder() { return { indigo: 'border-primary', emerald: 'border-primary', slate: 'border-dark-2' }[this.settings.theme_color] || 'border-primary'; }
      },
      async mounted() {
        try {
          const [settingsResponse, productsResponse, categoriesResponse] = await Promise.all([fetch('settings.json'), fetch('products.json'), fetch('categories.json')]);
          this.settings = await settingsResponse.json();
          this.products = await productsResponse.json();
          this.categories = await categoriesResponse.json();
          this.view = this.settings.default_view || 'grid';
          this.dense = localStorage.getItem('tmopro_dense') === '1';
          this.favorites = JSON.parse(localStorage.getItem('tmopro_favorites') || '[]');
          this.products.forEach(product => this.qty[product.id] = 1);
          document.title = this.settings.site_name;
        } finally {
          this.loading = false;
        }
      },
      methods: {
        money(value) { return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value); },
        stockStatus(stock) {
          const n = Number(stock) || 0;
          if (n <= 0) return { label: 'Нет в наличии', cls: 'bg-red-50 text-red-700' };
          if (n < 10) return { label: 'Заканчивается', cls: 'bg-amber-50 text-amber-700' };
          return { label: 'В наличии', cls: 'bg-emerald-50 text-emerald-700' };
        },
        onProductImgError(product) { try { product.image = ''; } catch (e) {} },
        setQty(id, value) { this.qty[id] = Math.max(1, parseInt(value || 1, 10)); },
        toggleDense() {
          this.dense = !this.dense;
          localStorage.setItem('tmopro_dense', this.dense ? '1' : '0');
        },
        scrollToCatalog() { document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth' }); },
        toggleCategory(category) { this.selectedCategories = this.toggle(this.selectedCategories, category); this.page = 1; },
        toggleBrand(brand) { this.selectedBrands = this.toggle(this.selectedBrands, brand); this.page = 1; },
        toggle(list, value) { return list.includes(value) ? list.filter(item => item !== value) : [...list, value]; },
        countBy(field, value) { return this.products.filter(product => product[field] === value).length; },
        resetFilters() { this.selectedCategories = []; this.selectedBrands = []; this.search = ''; this.showFavoritesOnly = false; this.minPrice = ''; this.maxPrice = ''; this.showInStockOnly = false; this.page = 1; },
        isFavorite(id) { return this.favorites.includes(id); },
        toggleFavorite(id) {
          const product = this.products.find(p => p.id === id);
          if (this.favorites.includes(id)) {
            this.favorites = this.favorites.filter(fid => fid !== id);
            this.showToast((product?.name || 'Товар') + ' удален из избранного');
          } else {
            this.favorites = [...this.favorites, id];
            this.showToast((product?.name || 'Товар') + ' добавлен в избранное');
          }
          localStorage.setItem('tmopro_favorites', JSON.stringify(this.favorites));
        },
        isInCompare(id) { return this.compareList.includes(id); },
        toggleCompare(id) {
          if (this.compareList.includes(id)) {
            this.compareList = this.compareList.filter(cid => cid !== id);
          } else {
            if (this.compareList.length >= 4) { this.showToast('Максимум 4 товара для сравнения'); return; }
            this.compareList = [...this.compareList, id];
          }
          localStorage.setItem('tmopro_compare', JSON.stringify(this.compareList));
        },
        recordView(product) {
          if (!product || !product.id) return;
          let list = this.recentViews.filter(rid => rid !== product.id);
          list.unshift(product.id);
          this.recentViews = list.slice(0, 8);
          localStorage.setItem('tmopro_recent', JSON.stringify(this.recentViews));
        },
        productById(id) { return this.products.find(p => p.id === id) || null; },
        showToast(message) {
          const id = ++this.toastId;
          this.toasts.push({ id, message, visible: false });
          requestAnimationFrame(() => {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.visible = true;
          });
          setTimeout(() => {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 400);
          }, 2500);
        },
        onSearchSelect(item) {
          this.search = item.article || item.name;
        },
        addToCart(product) {
          const cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
          const amount = Number(this.qty[product.id] || 1);
          const existing = cart.find(item => item.id === product.id);
          if (existing) { existing.qty += amount; }
          else { cart.push({ ...product, qty: amount }); }
          localStorage.setItem('tmopro_cart', JSON.stringify(cart));
          this.showToast(product.name + ' добавлен в заявку (' + amount + ' шт)');
          this.cartBump = false;
          requestAnimationFrame(() => this.cartBump = true);
          setTimeout(() => this.cartBump = false, 700);
        }
      }
    }).mount('#app');
    document.getElementById('app-fallback')?.remove();

    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('sw.js'));
    }
  </script>

  <footer style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color:#94a3b8;">
    <div class="container" style="padding-top:64px;padding-bottom:40px;">
      <div class="grid md:grid-cols-3 gap-12">
        <div>
          <div style="font-size:22px;font-weight:900;color:#fff;letter-spacing:-0.02em;"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></div>
          <p style="margin-top:12px;font-size:14px;line-height:1.7;color:#94a3b8;">Сантехника оптом от производителя. Шаровые краны, фитинги, трубы и комплектующие для инженерных систем.</p>
          <?php if (!empty($settings['address'])): ?><p style="margin-top:12px;font-size:13px;color:#64748b;"><?= e($settings['address']) ?></p><?php endif; ?>
        </div>
        <div>
          <div style="font-size:11px;font-weight:800;color:#c9a35e;text-transform:uppercase;letter-spacing:.12em;margin-bottom:20px;">Контакты</div>
          <?php if (!empty($settings['phone'])): ?><a href="tel:<?= e(preg_replace('/\D/', '', $settings['phone'])) ?>" style="display:block;color:#e2e8f0;font-size:15px;font-weight:700;margin-bottom:10px;text-decoration:none;"><?= e($settings['phone']) ?></a><?php endif; ?>
          <?php if (!empty($settings['phone2'])): ?><a href="tel:<?= e(preg_replace('/\D/', '', $settings['phone2'])) ?>" style="display:block;color:#e2e8f0;font-size:15px;font-weight:700;margin-bottom:10px;text-decoration:none;"><?= e($settings['phone2']) ?></a><?php endif; ?>
          <?php if (!empty($settings['phone3'])): ?><a href="tel:<?= e(preg_replace('/\D/', '', $settings['phone3'])) ?>" style="display:block;color:#e2e8f0;font-size:15px;font-weight:700;margin-bottom:10px;text-decoration:none;"><?= e($settings['phone3']) ?></a><?php endif; ?>
          <?php if (!empty($settings['email_manager'])): ?><a href="mailto:<?= e($settings['email_manager']) ?>" style="display:block;color:#94a3b8;font-size:14px;font-weight:600;text-decoration:none;"><?= e($settings['email_manager']) ?></a><?php endif; ?>
        </div>
        <div>
          <div style="font-size:11px;font-weight:800;color:#c9a35e;text-transform:uppercase;letter-spacing:.12em;margin-bottom:20px;">Навигация</div>
          <?php foreach ($footerPages as $fp): ?>
            <a href="page.php?slug=<?= e($fp['slug'] ?? '') ?>" style="display:block;color:#94a3b8;font-size:14px;font-weight:600;margin-bottom:10px;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='#94a3b8'"><?= e($fp['title'] ?? '') ?></a>
          <?php endforeach; ?>
          <a href="index.php" style="display:block;color:#94a3b8;font-size:14px;font-weight:600;margin-bottom:10px;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='#94a3b8'">Каталог</a>
        </div>
      </div>
      <div style="margin-top:48px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <span style="font-size:13px;font-weight:600;color:#475569;">© <?= date('Y') ?> <?= e($settings['site_short_name'] ?? 'TMOPRO') ?></span>
        <span style="font-size:12px;font-weight:600;color:#475569;">Сантехника оптом</span>
      </div>
    </div>
  </footer>

  <!-- Floating WhatsApp Button -->
  <?php
  $waPhone = !empty($settings['whatsapp']) ? $settings['whatsapp'] : ($settings['phone'] ?? '');
  $waClean = preg_replace('/\D/', '', $waPhone);
  if ($waClean):
  ?>
  <a href="https://wa.me/<?= e($waClean) ?>" target="_blank" class="fixed bottom-6 right-6 z-50 flex items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg hover:bg-emerald-600 transition" style="width:56px;height:56px;" title="WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </a>
  <?php endif; ?>
</body>
</html>
