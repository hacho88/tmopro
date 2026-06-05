<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$settingsPath = __DIR__ . '/settings.json';
$pagesPath = __DIR__ . '/pages.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];
$pages = file_exists($pagesPath) ? json_decode(file_get_contents($pagesPath), true) : [];
$pages = is_array($pages) ? $pages : [];

$slug = trim((string)($_GET['slug'] ?? ''));
$page = null;
foreach ($pages as $p) {
    if (($p['slug'] ?? '') === $slug) {
        $page = $p;
        break;
    }
}

if (!$page) {
    http_response_code(404);
    header('Location: index.php');
    exit;
}

$siteName = $settings['site_name'] ?? 'TMOPRO';
$title = (string)($page['title'] ?? '');
$content = (string)($page['content'] ?? '');
$meta = (string)($page['meta'] ?? '');
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> — <?= e($siteName) ?></title>
  <meta name="description" content="<?= e($meta ?: $title) ?>">
  <link rel="canonical" href="https://tmopro.ru/page.php?slug=<?= e($slug) ?>">
  <link rel="sitemap" type="application/xml" title="Sitemap" href="https://tmopro.ru/sitemap.php">
  <meta name="theme-color" content="#008A4E">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=lux-gold-g">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Segoe UI, Arial; background: #f8fafc; }
    .page-hero { background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; }
    .page-content { line-height: 1.75; color: #334155; }
    .page-content p { margin-bottom: 16px; }
    .page-content h2 { font-size: 20px; font-weight: 900; margin: 24px 0 12px; color: #0f172a; }
    .page-content ul { margin-bottom: 16px; padding-left: 24px; }
    .page-content li { margin-bottom: 8px; }
    .page-card { background: #fff; border-radius: 28px; border: 1px solid rgba(15,23,42,0.06); box-shadow: 0 8px 32px rgba(15,23,42,.06); }
  </style>
</head>
<body>

<div class="page-hero">
  <div class="container py-6">
    <div class="flex items-center justify-between mb-6">
      <a href="index.php" class="text-white font-extrabold text-lg tracking-tight" style="text-decoration:none;"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></a>
      <a href="index.php" class="text-sm font-bold text-gray-400 hover:text-white transition">← Назад в каталог</a>
    </div>
    <div class="text-xs font-bold text-gray-400 mb-2">
      <a href="index.php" style="color:#94a3b8;text-decoration:none;">Каталог</a>
      <span style="color:#64748b;"> / </span>
      <span style="color:#64748b;"><?= e($title) ?></span>
    </div>
  </div>
</div>

<main class="container py-10 lg:py-16">
  <div class="max-w-3xl mx-auto">
    <div class="page-card p-8 lg:p-12">
      <h1 class="text-3xl lg:text-4xl font-black text-gray-900 mb-8 tracking-tight" style="line-height:1.15;"><?= e($title) ?></h1>
      <div class="page-content"><?= $content ?></div>
    </div>
  </div>
</main>

<footer class="container pb-12 text-center text-sm font-bold text-gray-400 mt-12">
  © <?= date('Y') ?> <?= e($siteName) ?>
</footer>

</body>
</html>
