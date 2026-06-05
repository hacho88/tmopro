<?php
require_once __DIR__ . '/db.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money($value) {
    return number_format((float)$value, 0, ',', ' ') . ' ₽';
}

session_start();
if (empty($_SESSION['b2b_user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['b2b_user_id'];
$accountId = (int)$_SESSION['b2b_account_id'];
$userName = $_SESSION['b2b_user_name'] ?? '';
$companyName = $_SESSION['b2b_company'] ?? '';
$priceTier = $_SESSION['b2b_price_tier'] ?? 'default';

$pdo = tmopro_db();
$orders = [];
$account = null;

if ($pdo) {
    // Get account details
    $stmt = $pdo->prepare('SELECT * FROM b2b_accounts WHERE id = ?');
    $stmt->execute([$accountId]);
    $account = $stmt->fetch();

    // Get orders for this account
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE account_id = ? ORDER BY created_at DESC');
    $stmt->execute([$accountId]);
    $orders = $stmt->fetchAll();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];
$siteName = $settings['site_name'] ?? 'TMOPRO';
$themeColor = $settings['theme_color'] ?? 'emerald';
$accentBg = $themeColor === 'indigo' ? 'bg-indigo-600' : ($themeColor === 'slate' ? 'bg-slate-900' : 'bg-emerald-600');
$accentClass = $themeColor === 'indigo' ? 'text-indigo-600' : ($themeColor === 'slate' ? 'text-slate-900' : 'text-emerald-600');

// Status labels
$statusLabels = [
    'new' => ['label' => 'Новый', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
    'processing' => ['label' => 'В обработке', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
    'shipped' => ['label' => 'Отгружен', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
    'completed' => ['label' => 'Выполнен', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
    'cancelled' => ['label' => 'Отменен', 'class' => 'bg-red-50 text-red-700 border-red-200'],
];
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Личный кабинет — <?= e($siteName) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=lux-gold-f">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Segoe UI, Arial; background: #f8fafc; }
    .pd-hero { background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; }
    .card { background: #fff; border-radius: 24px; border: 1px solid rgba(15,23,42,0.06); box-shadow: 0 8px 32px rgba(15,23,42,.06); }
    .info-row { display:flex; justify-content:space-between; padding: 14px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .info-label { color: #94a3b8; font-weight: 700; }
    .info-value { color: #0f172a; font-weight: 800; }
    .order-badge { display:inline-flex; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 900; border: 1px solid; }
    .order-card { transition: all .2s; }
    .order-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(15,23,42,.08); }
  </style>
</head>
<body>

<div class="pd-hero">
  <div class="container py-6">
    <div class="flex items-center justify-between">
      <div>
        <a href="index.php" class="text-white font-extrabold text-lg tracking-tight" style="text-decoration:none;"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></a>
        <p class="text-sm font-bold text-gray-400 mt-1">B2B личный кабинет</p>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm font-bold text-gray-400"><?= e($userName) ?> / <?= e($companyName) ?></span>
        <a href="?logout=1" class="text-sm font-bold text-gray-400 hover:text-white transition">Выйти</a>
      </div>
    </div>
  </div>
</div>

<main class="container py-10">
  <div class="grid gap-8 lg:grid-cols-3">
    <!-- Sidebar: Account Info -->
    <div class="lg:col-span-1 space-y-6">
      <div class="card p-6">
        <h2 class="text-lg font-black text-gray-900 mb-6">Моя компания</h2>
        <?php if ($account): ?>
          <div class="info-row"><span class="info-label">Компания</span><span class="info-value"><?= e($account['company_name']) ?></span></div>
          <?php if ($account['inn']): ?>
            <div class="info-row"><span class="info-label">ИНН</span><span class="info-value"><?= e($account['inn']) ?></span></div>
          <?php endif; ?>
          <?php if ($account['email']): ?>
            <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?= e($account['email']) ?></span></div>
          <?php endif; ?>
          <?php if ($account['phone']): ?>
            <div class="info-row"><span class="info-label">Телефон</span><span class="info-value"><?= e($account['phone']) ?></span></div>
          <?php endif; ?>
          <div class="info-row" style="border:none;"><span class="info-label">Тариф</span><span class="info-value <?= e($accentClass) ?>"><?= e($account['price_tier']) ?></span></div>
        <?php else: ?>
          <p class="text-sm text-gray-400 font-bold">Данные недоступны</p>
        <?php endif; ?>
      </div>

      <div class="card p-6">
        <h2 class="text-lg font-black text-gray-900 mb-4">Быстрые ссылки</h2>
        <div class="space-y-3">
          <a href="index.php" class="flex items-center gap-3 text-sm font-extrabold text-gray-700 hover:<?= e($accentClass) ?> transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg>
            Каталог товаров
          </a>
          <a href="checkout.php" class="flex items-center gap-3 text-sm font-extrabold text-gray-700 hover:<?= e($accentClass) ?> transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
            Корзина
          </a>
        </div>
      </div>
    </div>

    <!-- Main: Orders -->
    <div class="lg:col-span-2">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-black text-gray-900">История заказов</h2>
        <span class="text-sm font-bold text-gray-400"><?= count($orders) ?> заказов</span>
      </div>

      <?php if (empty($orders)): ?>
        <div class="card p-8 text-center">
          <div class="text-5xl mb-4">📦</div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Пока нет заказов</h3>
          <p class="text-sm font-bold text-gray-400 mb-6">Сделайте первый заказ в каталоге</p>
          <a href="index.php" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white font-extrabold text-sm <?= e($accentBg) ?>" style="text-decoration:none;">Перейти в каталог →</a>
        </div>
      <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($orders as $order): ?>
            <?php
              $statusInfo = $statusLabels[$order['status']] ?? ['label' => $order['status'], 'class' => 'bg-gray-50 text-gray-700 border-gray-200'];
              // Get order items
              $items = [];
              if ($pdo) {
                  $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
                  $stmt->execute([$order['id']]);
                  $items = $stmt->fetchAll();
              }
            ?>
            <div class="card order-card p-6">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <div class="flex items-center gap-3 mb-1">
                    <span class="text-lg font-black text-gray-900">#<?= e($order['order_number']) ?></span>
                    <span class="order-badge <?= e($statusInfo['class']) ?>"><?= e($statusInfo['label']) ?></span>
                  </div>
                  <p class="text-sm font-bold text-gray-400"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="text-right">
                  <div class="text-xl font-black text-gray-900"><?= e(money($order['total'])) ?></div>
                  <div class="text-xs font-bold text-gray-400"><?= count($items) ?> позиций</div>
                </div>
              </div>

              <?php if (!empty($items)): ?>
                <div class="border-t border-gray-100 pt-4 mt-4">
                  <div class="space-y-3">
                    <?php foreach ($items as $item): ?>
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xs font-extrabold text-gray-500"><?= (int)$item['qty'] ?>x</div>
                          <div>
                            <div class="text-sm font-extrabold text-gray-900"><?= e($item['name']) ?></div>
                            <div class="text-xs font-bold text-gray-400"><?= e($item['article'] ?? '') ?> <?= e($item['brand'] ?? '') ?></div>
                          </div>
                        </div>
                        <div class="text-sm font-extrabold text-gray-900"><?= e(money($item['line_total'])) ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<footer class="container pb-12 text-center text-sm font-bold text-gray-400 mt-12">
  © <?= date('Y') ?> <?= e($siteName) ?>
</footer>

</body>
</html>
