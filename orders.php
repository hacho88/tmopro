<?php
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money($value) {
    return number_format((float)$value, 0, ',', ' ') . ' ₽';
}

require_once __DIR__ . '/db.php';

$searchEmail = trim((string)($_GET['email'] ?? ''));
$searchPhone = trim((string)($_GET['phone'] ?? ''));
$orders = [];

$pdo = tmopro_db_safe();
if ($pdo && ($searchEmail !== '' || $searchPhone !== '')) {
    $sql = 'SELECT * FROM orders WHERE 1=0';
    $params = [];
    if ($searchEmail !== '') { $sql = 'SELECT * FROM orders WHERE email = ? ORDER BY id DESC'; $params[] = $searchEmail; }
    elseif ($searchPhone !== '') { $sql = 'SELECT * FROM orders WHERE phone = ? ORDER BY id DESC'; $params[] = $searchPhone; }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
}

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Мои заказы — <?= e($settings['site_short_name'] ?? 'TMOPRO') ?></title>
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,ui-sans-serif,system-ui,Segoe UI,Arial;}</style>
</head>
<body class="min-h-screen bg-slate-50/50 text-slate-950 antialiased">
  <div class="pointer-events-none fixed inset-0 -z-10">
    <div class="absolute left-1/2 top-[-220px] h-[440px] w-[760px] -translate-x-1/2 rounded-full bg-emerald-500/20 blur-3xl"></div>
  </div>

  <header class="border-b border-slate-100 bg-white/75 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
      <a href="index.php" class="flex items-center gap-3">
        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-600 text-white shadow-lg">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18h-5"/><path d="M18 14H8"/><path d="M4 22h16"/><path d="M6 22V5a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v17"/></svg>
        </span>
        <span class="block text-lg font-extrabold tracking-tight"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></span>
      </a>
      <a href="index.php" class="rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">В каталог</a>
    </div>
  </header>

  <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <h1 class="text-3xl font-extrabold tracking-[-0.03em]">Мои заказы</h1>
    <p class="mt-2 text-slate-600">Введите email или телефон, указанные при оформлении.</p>

    <form method="get" class="mt-6 flex flex-col sm:flex-row gap-3">
      <input name="email" value="<?= e($searchEmail) ?>" placeholder="Email" class="flex-1 rounded-2xl border border-slate-100 bg-white px-4 py-3 font-semibold outline-none focus:ring-4 focus:ring-emerald-100">
      <input name="phone" value="<?= e($searchPhone) ?>" placeholder="Телефон" class="flex-1 rounded-2xl border border-slate-100 bg-white px-4 py-3 font-semibold outline-none focus:ring-4 focus:ring-emerald-100">
      <button class="rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg transition hover:-translate-y-0.5">Найти</button>
    </form>

    <?php if ($searchEmail !== '' || $searchPhone !== ''): ?>
      <?php if (empty($orders)): ?>
        <div class="mt-8 rounded-2xl bg-white border border-slate-100 p-8 text-center text-slate-500 font-bold">Заказы не найдены.</div>
      <?php else: ?>
        <div class="mt-8 space-y-4">
          <?php foreach ($orders as $o): ?>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
              <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div class="text-sm font-extrabold text-slate-900">#<?= e($o['order_number']) ?></div>
                <span class="rounded-full px-3 py-1 text-xs font-bold
                  <?php
                    $st = $o['status'] ?? 'new';
                    $map = ['new'=>'bg-blue-50 text-blue-700','processing'=>'bg-amber-50 text-amber-700','invoiced'=>'bg-violet-50 text-violet-700','shipped'=>'bg-sky-50 text-sky-700','done'=>'bg-emerald-50 text-emerald-700','cancelled'=>'bg-red-50 text-red-700'];
                    echo $map[$st] ?? 'bg-slate-50 text-slate-700';
                  ?>">
                  <?= e($o['status'] ?? 'new') ?>
                </span>
              </div>
              <div class="text-sm text-slate-600 font-semibold mb-2"><?= e($o['company_name']) ?> · <?= e($o['created_at']) ?></div>
              <div class="flex flex-wrap gap-4 text-xs text-slate-500 font-bold mb-3">
                <span><?= e($o['contact_person']) ?></span>
                <span><?= e($o['phone']) ?></span>
                <?php if ($o['city']): ?><span>📍 <?= e($o['city']) ?></span><?php endif; ?>
              </div>
              <?php
                $items = json_decode($o['items_json'] ?? '[]', true);
                if ($items):
              ?>
                <div class="rounded-xl bg-slate-50 p-3 space-y-1">
                  <?php foreach ($items as $it): ?>
                    <div class="flex justify-between text-sm font-bold text-slate-700">
                      <span><?= e($it['name'] ?? '') ?> (<?= e($it['article'] ?? '') ?>)</span>
                      <span><?= (int)($it['qty'] ?? 0) ?> × <?= money($it['unit_price'] ?? 0) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <div class="mt-3 flex items-center justify-between">
                <a href="invoice.php?order=<?= e($o['order_number']) ?>" target="_blank" class="text-sm font-extrabold text-emerald-600 hover:underline">Открыть счет →</a>
                <div class="text-lg font-extrabold"><?= money($o['total'] ?? 0) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </main>
</body>
</html>
