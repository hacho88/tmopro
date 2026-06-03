<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

const ADMIN_PASSWORD = 'admin123';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function read_json($path, $fallback) {
    if (!file_exists($path)) {
        return $fallback;
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : $fallback;
}

function save_json($path, $data, &$error) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        $error = 'Не удалось подготовить JSON для сохранения.';
        return false;
    }
    if (file_put_contents($path, $json . PHP_EOL, LOCK_EX) === false) {
        $error = 'Не удалось сохранить файл. Проверьте права на запись для ' . basename($path) . '.';
        return false;
    }
    return true;
}

$settingsPath = __DIR__ . '/settings.json';
$productsPath = __DIR__ . '/products.json';

$defaultSettings = [
    'site_name' => 'tmopro.ru — Сантехника Оптом',
    'site_short_name' => 'tmopro.ru',
    'phone' => '+7 (800) 555-35-35',
    'email_manager' => 'info@tmopro.ru',
    'theme_color' => 'indigo',
    'default_view' => 'table',
    'logo_type' => 'text',
    'logo_text' => 'TMO',
    'logo_url' => '',
    'background_type' => 'gradient',
    'background_color' => '#f8fafc',
    'background_image' => '',
    'hero_title' => 'Премиальная сантехника оптом для комплектации объектов.',
    'hero_subtitle' => 'Подберите позиции, укажите количество и отправьте заявку на счет. Оптовая цена включается автоматически от 10 штук.'
];

$settings = array_merge($defaultSettings, read_json($settingsPath, $defaultSettings));
$products = read_json($productsPath, []);
$error = '';
$success = '';
$tab = $_GET['tab'] ?? 'overview';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_password'])) {
    if (hash_equals(ADMIN_PASSWORD, (string)$_POST['login_password'])) {
        $_SESSION['tmopro_admin'] = true;
        header('Location: admin.php');
        exit;
    }
    $error = 'Неверный пароль.';
}

$isAuthorized = !empty($_SESSION['tmopro_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAuthorized) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $theme = in_array($_POST['theme_color'] ?? 'indigo', ['indigo', 'emerald', 'slate'], true) ? $_POST['theme_color'] : 'indigo';
        $view = in_array($_POST['default_view'] ?? 'table', ['table', 'grid'], true) ? $_POST['default_view'] : 'table';
        $logoType = in_array($_POST['logo_type'] ?? 'text', ['text', 'image'], true) ? $_POST['logo_type'] : 'text';
        $backgroundType = in_array($_POST['background_type'] ?? 'gradient', ['gradient', 'solid', 'image'], true) ? $_POST['background_type'] : 'gradient';

        $settings = [
            'site_name' => trim($_POST['site_name'] ?? $defaultSettings['site_name']),
            'site_short_name' => trim($_POST['site_short_name'] ?? $defaultSettings['site_short_name']),
            'phone' => trim($_POST['phone'] ?? $defaultSettings['phone']),
            'email_manager' => trim($_POST['email_manager'] ?? $defaultSettings['email_manager']),
            'theme_color' => $theme,
            'default_view' => $view,
            'logo_type' => $logoType,
            'logo_text' => trim($_POST['logo_text'] ?? 'TMO'),
            'logo_url' => trim($_POST['logo_url'] ?? ''),
            'background_type' => $backgroundType,
            'background_color' => trim($_POST['background_color'] ?? '#f8fafc'),
            'background_image' => trim($_POST['background_image'] ?? ''),
            'hero_title' => trim($_POST['hero_title'] ?? $defaultSettings['hero_title']),
            'hero_subtitle' => trim($_POST['hero_subtitle'] ?? $defaultSettings['hero_subtitle'])
        ];

        if (save_json($settingsPath, $settings, $error)) {
            $success = 'Настройки сайта сохранены.';
        }
        $tab = 'settings';
    }

    if ($action === 'save_products') {
        $saved = [];
        foreach (($_POST['id'] ?? []) as $index => $id) {
            $name = trim($_POST['name'][$index] ?? '');
            if ($name === '') {
                continue;
            }
            $saved[] = [
                'id' => (int)$id,
                'article' => trim($_POST['article'][$index] ?? ''),
                'name' => $name,
                'category' => trim($_POST['category'][$index] ?? ''),
                'brand' => trim($_POST['brand'][$index] ?? ''),
                'stock' => max(0, (int)($_POST['stock'][$index] ?? 0)),
                'price_base' => max(0, (float)($_POST['price_base'][$index] ?? 0)),
                'price_wholesale' => max(0, (float)($_POST['price_wholesale'][$index] ?? 0))
            ];
        }
        $products = array_values($saved);
        if (save_json($productsPath, $products, $error)) {
            $success = 'Каталог товаров сохранен.';
        }
        $tab = 'products';
    }

    if ($action === 'add_product') {
        $maxId = 0;
        foreach ($products as $product) {
            $maxId = max($maxId, (int)($product['id'] ?? 0));
        }
        $products[] = [
            'id' => $maxId + 1,
            'article' => 'NEW-' . ($maxId + 1),
            'name' => 'Новый товар',
            'category' => 'Смесители',
            'brand' => 'Grohe',
            'stock' => 0,
            'price_base' => 0,
            'price_wholesale' => 0
        ];
        if (save_json($productsPath, $products, $error)) {
            $success = 'Товар добавлен. Заполните поля и сохраните каталог.';
        }
        $tab = 'products';
    }

    if ($action === 'delete_product') {
        $deleteId = (int)($_POST['delete_id'] ?? 0);
        $products = array_values(array_filter($products, fn($product) => (int)($product['id'] ?? 0) !== $deleteId));
        if (save_json($productsPath, $products, $error)) {
            $success = 'Товар удален.';
        }
        $tab = 'products';
    }
}

$themeColor = ['indigo' => '#4f46e5', 'emerald' => '#059669', 'slate' => '#0f172a'][$settings['theme_color']] ?? '#4f46e5';
$totalStock = array_sum(array_map(fn($product) => (int)($product['stock'] ?? 0), $products));
$totalRetail = array_sum(array_map(fn($product) => (float)($product['price_base'] ?? 0), $products));
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Админка tmopro.ru</title>
  <meta name="theme-color" content="<?= e($themeColor) ?>">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .field { width: 100%; border-radius: 18px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 14px 16px; font-weight: 700; outline: none; transition: .18s ease; }
    .field:focus { background: #fff; border-color: transparent; box-shadow: 0 0 0 4px rgba(79,70,229,.14); }
    .label { margin-bottom: 8px; display: block; font-size: 11px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: #94a3b8; }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: #f6f7fb; color: #020617; }
    main { padding: 32px; }
    body > main:first-child { min-height: 100vh; display: grid; place-items: center; }
    section, aside { background: #fff; border-radius: 28px; box-shadow: 0 12px 40px rgba(15,23,42,.08); }
    a { color: inherit; text-decoration: none; }
    button { border: 0; cursor: pointer; }
    input, textarea, select, button { font: inherit; }
    .min-h-screen { min-height: 100vh; }
    .grid { display: grid; }
    .flex { display: flex; }
    .hidden { display: none; }
    .block { display: block; }
    .inline-flex { display: inline-flex; }
    .items-center { align-items: center; }
    .items-end { align-items: flex-end; }
    .items-start { align-items: flex-start; }
    .justify-between { justify-content: space-between; }
    .place-items-center { place-items: center; }
    .text-center { text-align: center; }
    .gap-1 { gap: 4px; }
    .gap-2 { gap: 8px; }
    .gap-3 { gap: 12px; }
    .gap-4 { gap: 16px; }
    .gap-5 { gap: 20px; }
    .gap-6 { gap: 24px; }
    .space-y-2 > * + * { margin-top: 8px; }
    .space-y-3 > * + * { margin-top: 12px; }
    .space-y-4 > * + * { margin-top: 16px; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .mb-3 { margin-bottom: 12px; }
    .mb-4 { margin-bottom: 16px; }
    .mb-5 { margin-bottom: 20px; }
    .mb-6 { margin-bottom: 24px; }
    .mb-8 { margin-bottom: 32px; }
    .mt-1 { margin-top: 4px; }
    .mt-2 { margin-top: 8px; }
    .mt-3 { margin-top: 12px; }
    .mt-4 { margin-top: 16px; }
    .mt-5 { margin-top: 20px; }
    .mt-6 { margin-top: 24px; }
    .p-2 { padding: 8px; }
    .p-3 { padding: 12px; }
    .p-4 { padding: 16px; }
    .p-5 { padding: 20px; }
    .p-6 { padding: 24px; }
    .p-8 { padding: 32px; }
    .px-3 { padding-left: 12px; padding-right: 12px; }
    .px-4 { padding-left: 16px; padding-right: 16px; }
    .px-5 { padding-left: 20px; padding-right: 20px; }
    .px-6 { padding-left: 24px; padding-right: 24px; }
    .px-7 { padding-left: 28px; padding-right: 28px; }
    .py-2 { padding-top: 8px; padding-bottom: 8px; }
    .py-3 { padding-top: 12px; padding-bottom: 12px; }
    .py-4 { padding-top: 16px; padding-bottom: 16px; }
    .w-full { width: 100%; }
    .max-w-md { max-width: 448px; }
    .max-w-2xl { max-width: 672px; }
    .h-12 { height: 48px; }
    .h-16 { height: 64px; }
    .w-12 { width: 48px; }
    .w-16 { width: 64px; }
    .rounded-xl { border-radius: 12px; }
    .rounded-2xl { border-radius: 16px; }
    .rounded-3xl, .rounded-\[28px\], .rounded-\[32px\] { border-radius: 28px; }
    .rounded-full { border-radius: 999px; }
    .bg-white { background: #fff; }
    .bg-slate-50 { background: #f8fafc; }
    .bg-slate-100 { background: #f1f5f9; }
    .bg-slate-950 { background: #020617; }
    .bg-red-50 { background: #fef2f2; }
    .bg-emerald-50 { background: #ecfdf5; }
    .text-white { color: #fff; }
    .text-slate-950 { color: #020617; }
    .text-slate-600 { color: #475569; }
    .text-slate-500 { color: #64748b; }
    .text-slate-400 { color: #94a3b8; }
    .text-red-600 { color: #dc2626; }
    .text-emerald-600, .text-emerald-700 { color: #059669; }
    .text-xs { font-size: 12px; }
    .text-sm { font-size: 14px; }
    .text-xl { font-size: 20px; }
    .text-2xl { font-size: 24px; }
    .text-3xl { font-size: 30px; }
    .text-4xl { font-size: 36px; }
    .font-medium { font-weight: 500; }
    .font-semibold { font-weight: 600; }
    .font-bold { font-weight: 700; }
    .font-black { font-weight: 900; }
    .leading-7 { line-height: 1.75; }
    .shadow-lg, .shadow-xl, .shadow-md { box-shadow: 0 12px 32px rgba(15,23,42,.14); }
    .border, .border-b, .border-r { border: 1px solid #e2e8f0; }
    .sticky { position: sticky; }
    .bottom-4 { bottom: 16px; }
    .top-0 { top: 0; }
    .top-24 { top: 96px; }
    .z-40 { z-index: 40; }
    .overflow-hidden { overflow: hidden; }
    .transition, .transition-all { transition: .18s ease; }
    .hover\:bg-white:hover { background: #fff; }
    .hover\:shadow-lg:hover, .hover\:shadow-md:hover { box-shadow: 0 16px 42px rgba(15,23,42,.14); }
    .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    @media (min-width: 768px) {
      .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .md\:col-span-2 { grid-column: span 2 / span 2; }
    }
    @media (min-width: 1024px) {
      .lg\:grid { display: grid; }
      .lg\:grid-cols-\[290px_1fr\] { grid-template-columns: 290px 1fr; }
      .lg\:grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
      .lg\:sticky { position: sticky; }
      .lg\:top-0 { top: 0; }
      .lg\:h-screen { height: 100vh; }
      .lg\:p-6 { padding: 24px; }
      .lg\:px-10 { padding-left: 40px; padding-right: 40px; }
      .lg\:block { display: block; }
      .lg\:inline-flex { display: inline-flex; }
      .lg\:text-left { text-align: left; }
    }
    @media (min-width: 1280px) {
      .xl\:grid-cols-\[1fr_360px\] { grid-template-columns: 1fr 360px; }
      .xl\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
      .xl\:col-span-2 { grid-column: span 2 / span 2; }
      .xl\:flex-row { flex-direction: row; }
      .xl\:items-end { align-items: flex-end; }
    }
  </style>
</head>
<body class="min-h-screen bg-[#f6f7fb] text-slate-950 antialiased">
<?php if (!$isAuthorized): ?>
  <main class="grid min-h-screen place-items-center px-4">
    <section class="w-full max-w-md rounded-[32px] border border-white bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,.12)]">
      <div class="mb-8 text-center">
        <div class="mx-auto mb-5 grid h-16 w-16 place-items-center rounded-3xl text-white shadow-xl" style="background: <?= e($themeColor) ?>;">TMO</div>
        <h1 class="text-3xl font-black tracking-[-.04em]">Панель управления</h1>
        <p class="mt-2 text-sm font-semibold text-slate-500">Вход для администратора сайта</p>
      </div>
      <?php if ($error): ?><div class="mb-4 rounded-2xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="space-y-4">
        <input name="login_password" type="password" required autofocus placeholder="Пароль" class="field">
        <button class="w-full rounded-2xl px-6 py-4 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5" style="background: <?= e($themeColor) ?>;">Войти</button>
      </form>
    </section>
  </main>
<?php else: ?>
  <div class="min-h-screen lg:grid lg:grid-cols-[290px_1fr]">
    <aside class="border-b border-slate-200 bg-white/90 p-4 backdrop-blur-xl lg:sticky lg:top-0 lg:h-screen lg:border-b-0 lg:border-r lg:p-6">
      <div class="mb-6 flex items-center justify-between gap-4 lg:block">
        <div class="flex items-center gap-3">
          <div class="grid h-12 w-12 place-items-center rounded-2xl text-sm font-black text-white" style="background: <?= e($themeColor) ?>;"><?= e($settings['logo_text'] ?: 'TMO') ?></div>
          <div>
            <div class="font-black"><?= e($settings['site_short_name']) ?></div>
            <div class="text-xs font-bold text-slate-400">управление сайтом</div>
          </div>
        </div>
        <a href="admin.php?logout=1" class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-black text-slate-600 lg:mt-6 lg:inline-flex">Выйти</a>
      </div>
      <nav class="grid grid-cols-3 gap-2 lg:grid-cols-1">
        <?php $tabs = ['overview' => 'Обзор', 'settings' => 'Сайт', 'products' => 'Товары']; ?>
        <?php foreach ($tabs as $key => $title): ?>
          <a href="admin.php?tab=<?= e($key) ?>" class="rounded-2xl px-4 py-3 text-center text-sm font-black transition lg:text-left <?= $tab === $key ? 'text-white shadow-lg' : 'bg-slate-50 text-slate-500 hover:bg-white hover:shadow-md' ?>" style="<?= $tab === $key ? 'background:' . e($themeColor) . ';' : '' ?>"><?= e($title) ?></a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <main class="px-4 py-6 sm:px-6 lg:px-10">
      <div class="mb-8 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="mb-3 inline-flex rounded-full bg-white px-4 py-2 text-sm font-black text-slate-500 shadow-sm ring-1 ring-slate-100">Без MySQL · JSON CMS</div>
          <h1 class="text-4xl font-black tracking-[-.05em] sm:text-5xl">Админ-панель</h1>
          <p class="mt-3 max-w-2xl font-medium leading-7 text-slate-500">Простое управление сайтом: контакты, внешний вид, товары, цены и остатки.</p>
        </div>
        <a href="index.php" target="_blank" class="rounded-2xl bg-slate-950 px-6 py-4 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5">Открыть сайт</a>
      </div>

      <?php if ($success): ?><div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-sm font-black text-emerald-700 ring-1 ring-emerald-100"><?= e($success) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="mb-6 rounded-2xl bg-red-50 px-5 py-4 text-sm font-black text-red-600 ring-1 ring-red-100"><?= e($error) ?></div><?php endif; ?>

      <?php if ($tab === 'overview'): ?>
        <section class="grid gap-5 md:grid-cols-3">
          <div class="rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]"><div class="text-sm font-black text-slate-400">Товаров</div><div class="mt-3 text-4xl font-black"><?= count($products) ?></div></div>
          <div class="rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]"><div class="text-sm font-black text-slate-400">Остаток</div><div class="mt-3 text-4xl font-black"><?= e($totalStock) ?></div></div>
          <div class="rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]"><div class="text-sm font-black text-slate-400">Сумма цен</div><div class="mt-3 text-4xl font-black"><?= e(number_format($totalRetail, 0, ',', ' ')) ?> ₽</div></div>
        </section>
        <section class="mt-6 rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]">
          <h2 class="text-2xl font-black tracking-[-.03em]">Как работать</h2>
          <div class="mt-5 grid gap-4 md:grid-cols-3">
            <a href="admin.php?tab=settings" class="rounded-2xl bg-slate-50 p-5 transition hover:-translate-y-1 hover:bg-white hover:shadow-lg"><b>1. Настроить сайт</b><p class="mt-2 text-sm font-semibold text-slate-500">Название, телефон, почта, логотип, фон и главный экран.</p></a>
            <a href="admin.php?tab=products" class="rounded-2xl bg-slate-50 p-5 transition hover:-translate-y-1 hover:bg-white hover:shadow-lg"><b>2. Заполнить товары</b><p class="mt-2 text-sm font-semibold text-slate-500">Артикулы, бренды, остатки, розничные и оптовые цены.</p></a>
            <a href="index.php" target="_blank" class="rounded-2xl bg-slate-50 p-5 transition hover:-translate-y-1 hover:bg-white hover:shadow-lg"><b>3. Проверить витрину</b><p class="mt-2 text-sm font-semibold text-slate-500">Откройте сайт и проверьте, как покупатель видит каталог.</p></a>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($tab === 'settings'): ?>
        <form method="post" class="grid gap-6 xl:grid-cols-[1fr_360px]">
          <input type="hidden" name="action" value="save_settings">
          <section class="rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]">
            <h2 class="mb-6 text-2xl font-black tracking-[-.03em]">Настройки сайта</h2>
            <div class="grid gap-5 md:grid-cols-2">
              <label class="md:col-span-2"><span class="label">Название сайта</span><input name="site_name" value="<?= e($settings['site_name']) ?>" class="field"></label>
              <label><span class="label">Короткое имя</span><input name="site_short_name" value="<?= e($settings['site_short_name']) ?>" class="field"></label>
              <label><span class="label">Телефон</span><input name="phone" value="<?= e($settings['phone']) ?>" class="field"></label>
              <label><span class="label">Email менеджера</span><input name="email_manager" type="email" value="<?= e($settings['email_manager']) ?>" class="field"></label>
              <label><span class="label">Цвет</span><select name="theme_color" class="field"><option value="indigo" <?= $settings['theme_color'] === 'indigo' ? 'selected' : '' ?>>Синий indigo</option><option value="emerald" <?= $settings['theme_color'] === 'emerald' ? 'selected' : '' ?>>Зеленый emerald</option><option value="slate" <?= $settings['theme_color'] === 'slate' ? 'selected' : '' ?>>Темный slate</option></select></label>
              <label><span class="label">Вид каталога</span><select name="default_view" class="field"><option value="table" <?= $settings['default_view'] === 'table' ? 'selected' : '' ?>>Таблица</option><option value="grid" <?= $settings['default_view'] === 'grid' ? 'selected' : '' ?>>Плитка</option></select></label>
              <label><span class="label">Тип логотипа</span><select name="logo_type" class="field"><option value="text" <?= $settings['logo_type'] === 'text' ? 'selected' : '' ?>>Текст</option><option value="image" <?= $settings['logo_type'] === 'image' ? 'selected' : '' ?>>Картинка</option></select></label>
              <label><span class="label">Текст логотипа</span><input name="logo_text" value="<?= e($settings['logo_text']) ?>" class="field"></label>
              <label class="md:col-span-2"><span class="label">Ссылка на логотип</span><input name="logo_url" value="<?= e($settings['logo_url']) ?>" placeholder="logo.png или /logo.png" class="field"></label>
              <label><span class="label">Тип фона</span><select name="background_type" class="field"><option value="gradient" <?= $settings['background_type'] === 'gradient' ? 'selected' : '' ?>>Градиент</option><option value="solid" <?= $settings['background_type'] === 'solid' ? 'selected' : '' ?>>Цвет</option><option value="image" <?= $settings['background_type'] === 'image' ? 'selected' : '' ?>>Картинка</option></select></label>
              <label><span class="label">Цвет фона</span><input name="background_color" type="color" value="<?= e($settings['background_color']) ?>" class="h-[54px] w-full rounded-2xl border border-slate-200 bg-slate-50 p-2"></label>
              <label class="md:col-span-2"><span class="label">Ссылка на фон</span><input name="background_image" value="<?= e($settings['background_image']) ?>" placeholder="background.jpg или /background.jpg" class="field"></label>
              <label class="md:col-span-2"><span class="label">Главный заголовок</span><textarea name="hero_title" rows="2" class="field"><?= e($settings['hero_title']) ?></textarea></label>
              <label class="md:col-span-2"><span class="label">Подзаголовок</span><textarea name="hero_subtitle" rows="3" class="field"><?= e($settings['hero_subtitle']) ?></textarea></label>
            </div>
            <button class="mt-6 rounded-2xl px-7 py-4 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5" style="background: <?= e($themeColor) ?>;">Сохранить настройки</button>
          </section>
          <aside class="h-fit rounded-[28px] bg-white p-6 shadow-[0_12px_40px_rgba(15,23,42,.06)]">
            <h3 class="text-xl font-black">Проверка записи</h3>
            <div class="mt-4 space-y-3 text-sm font-bold text-slate-500">
              <div class="rounded-2xl bg-slate-50 p-4">settings.json: <?= is_writable($settingsPath) ? '<span class="text-emerald-600">можно сохранять</span>' : '<span class="text-red-600">нет прав записи</span>' ?></div>
              <div class="rounded-2xl bg-slate-50 p-4">products.json: <?= is_writable($productsPath) ? '<span class="text-emerald-600">можно сохранять</span>' : '<span class="text-red-600">нет прав записи</span>' ?></div>
            </div>
          </aside>
        </form>
      <?php endif; ?>

      <?php if ($tab === 'products'): ?>
        <section class="rounded-[28px] bg-white p-5 shadow-[0_12px_40px_rgba(15,23,42,.06)]">
          <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-2xl font-black tracking-[-.03em]">Товары</h2><p class="mt-1 text-sm font-semibold text-slate-500">Одна карточка — один товар. После правок нажмите “Сохранить каталог”.</p></div>
            <form method="post"><input type="hidden" name="action" value="add_product"><button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">+ Добавить</button></form>
          </div>
          <form method="post" id="productsForm">
            <input type="hidden" name="action" value="save_products">
            <div class="grid gap-4">
              <?php foreach ($products as $index => $product): ?>
                <article class="rounded-3xl border border-slate-100 bg-slate-50 p-4">
                  <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="text-sm font-black text-slate-500">ID <?= e($product['id'] ?? $index + 1) ?></div>
                    <button type="submit" form="deleteProduct<?= e($product['id'] ?? 0) ?>" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-black text-red-600" onclick="return confirm('Удалить товар?')">Удалить</button>
                  </div>
                  <input type="hidden" name="id[]" value="<?= e($product['id'] ?? $index + 1) ?>">
                  <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <label class="xl:col-span-2"><span class="label">Название</span><input name="name[]" value="<?= e($product['name'] ?? '') ?>" class="field"></label>
                    <label><span class="label">Артикул</span><input name="article[]" value="<?= e($product['article'] ?? '') ?>" class="field"></label>
                    <label><span class="label">Бренд</span><input name="brand[]" value="<?= e($product['brand'] ?? '') ?>" class="field"></label>
                    <label><span class="label">Категория</span><input name="category[]" value="<?= e($product['category'] ?? '') ?>" class="field"></label>
                    <label><span class="label">Остаток</span><input name="stock[]" type="number" value="<?= e($product['stock'] ?? 0) ?>" class="field"></label>
                    <label><span class="label">Цена до 10</span><input name="price_base[]" type="number" step="0.01" value="<?= e($product['price_base'] ?? 0) ?>" class="field"></label>
                    <label><span class="label">Опт от 10</span><input name="price_wholesale[]" type="number" step="0.01" value="<?= e($product['price_wholesale'] ?? 0) ?>" class="field"></label>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
            <div class="sticky bottom-4 mt-6 rounded-3xl border border-slate-200 bg-white/90 p-3 shadow-[0_18px_60px_rgba(15,23,42,.18)] backdrop-blur-xl">
              <button class="w-full rounded-2xl px-7 py-4 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5" style="background: <?= e($themeColor) ?>;">Сохранить каталог</button>
            </div>
          </form>
          <?php foreach ($products as $product): ?>
            <form method="post" id="deleteProduct<?= e($product['id'] ?? 0) ?>">
              <input type="hidden" name="action" value="delete_product">
              <input type="hidden" name="delete_id" value="<?= e($product['id'] ?? 0) ?>">
            </form>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </main>
  </div>
<?php endif; ?>
</body>
</html>
