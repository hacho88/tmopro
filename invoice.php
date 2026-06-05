<?php
require_once __DIR__ . '/db.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money($value) {
    return number_format((float)$value, 0, ',', ' ') . ' ₽';
}

$orderNumber = trim((string)($_GET['order'] ?? ''));
if ($orderNumber === '') {
    http_response_code(404);
    header('Location: index.php');
    exit;
}

$pdo = tmopro_db();
$order = null;
$items = [];

if ($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_number = ?');
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if ($order) {
        $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$order['id']]);
        $items = $stmt->fetchAll();
    }
}

if (!$order) {
    http_response_code(404);
    echo '<!doctype html><html><body><h1>Счет не найден</h1><a href="index.php">В каталог</a></body></html>';
    exit;
}

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$settings = is_array($settings) ? $settings : [];
$siteName = $settings['site_name'] ?? 'TMOPRO';
$themeColor = $settings['theme_color'] ?? 'emerald';
$accentBg = $themeColor === 'indigo' ? 'bg-indigo-600' : ($themeColor === 'slate' ? 'bg-slate-900' : 'bg-emerald-600');
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Счет №<?= e($orderNumber) ?> — <?= e($siteName) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Arial; background: #f8fafc; }
    @media print {
      body { background: #fff; }
      .no-print { display: none !important; }
      .print-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
    .print-card { background: #fff; border-radius: 24px; border: 1px solid rgba(15,23,42,0.06); box-shadow: 0 24px 80px rgba(15,23,42,.08); }
    .invoice-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; border-radius: 20px; padding: 32px; }
    .invoice-table th { font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; text-align: left; }
    .invoice-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .invoice-total { font-size: 24px; font-weight: 900; letter-spacing: -0.03em; }
    .stamp { display: inline-block; padding: 8px 20px; border-radius: 999px; border: 2px solid #059669; color: #059669; font-size: 12px; font-weight: 900; text-transform: uppercase; }
  </style>
</head>
<body class="py-10 px-4">

  <div class="max-w-3xl mx-auto">
    <div class="no-print flex items-center justify-between mb-6">
      <a href="index.php" class="text-sm font-extrabold text-gray-500 hover:text-gray-900">← Вернуться в каталог</a>
      <button onclick="window.print()" class="px-6 py-3 rounded-2xl text-white font-extrabold text-sm <?= e($accentBg) ?>" style="border:none;cursor:pointer;">🖨 Печать счета</button>
    </div>

    <div class="print-card p-8 lg:p-12">
      <div class="invoice-header mb-8">
        <div class="flex flex-col lg:flex-row justify-between gap-4">
          <div>
            <div class="text-xs font-bold text-gray-400 mb-1">СЧЕТ НА ОПЛАТУ</div>
            <div class="text-3xl font-black tracking-tight">№ <?= e($orderNumber) ?></div>
            <div class="text-sm font-bold text-gray-400 mt-2">от <?= date('d.m.Y', strtotime($order['created_at'])) ?></div>
          </div>
          <div class="text-right">
            <div class="text-lg font-black"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></div>
            <div class="text-sm font-bold text-gray-400 mt-1"><?= e($settings['phone'] ?? '') ?></div>
            <div class="text-sm font-bold text-gray-400"><?= e($settings['email_manager'] ?? '') ?></div>
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-2 gap-8 mb-8">
        <div>
          <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Поставщик</div>
          <div class="text-sm font-extrabold text-gray-900"><?= e($siteName) ?></div>
          <div class="text-sm font-bold text-gray-500 mt-1">ИНН: —</div>
          <div class="text-sm font-bold text-gray-500"><?= e($settings['phone'] ?? '') ?></div>
        </div>
        <div>
          <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Покупатель</div>
          <div class="text-sm font-extrabold text-gray-900"><?= e($order['company_name'] ?: $order['contact_person'] ?: '—') ?></div>
          <?php if ($order['inn']): ?><div class="text-sm font-bold text-gray-500 mt-1">ИНН: <?= e($order['inn']) ?></div><?php endif; ?>
          <div class="text-sm font-bold text-gray-500 mt-1"><?= e($order['phone'] ?: '') ?></div>
          <div class="text-sm font-bold text-gray-500"><?= e($order['email'] ?: '') ?></div>
        </div>
      </div>

      <table class="w-full invoice-table mb-8">
        <thead>
          <tr>
            <th>№</th>
            <th>Товар</th>
            <th style="text-align:center;">Кол-во</th>
            <th style="text-align:right;">Цена</th>
            <th style="text-align:right;">Сумма</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $idx => $it): ?>
            <tr>
              <td class="text-gray-500 font-bold"><?= $idx + 1 ?></td>
              <td>
                <div class="font-extrabold text-gray-900"><?= e($it['name']) ?></div>
                <div class="text-xs font-bold text-gray-400 mt-1"><?= e($it['article'] ?? '') ?> · <?= e($it['brand'] ?? '') ?></div>
              </td>
              <td style="text-align:center;" class="font-extrabold text-gray-900"><?= (int)$it['qty'] ?></td>
              <td style="text-align:right;" class="font-bold text-gray-500"><?= e(money($it['unit_price'])) ?></td>
              <td style="text-align:right;" class="font-extrabold text-gray-900"><?= e(money($it['line_total'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="flex flex-col lg:flex-row justify-between items-end gap-6 mb-8">
        <div>
          <span class="stamp"><?= e($order['status'] === 'new' ? 'Новый' : ($order['status'] === 'done' ? 'Выполнен' : $order['status'])) ?></span>
        </div>
        <div class="text-right">
          <div class="text-sm font-bold text-gray-400 mb-1">Итого к оплате:</div>
          <div class="invoice-total text-gray-900"><?= e(money($order['total'])) ?></div>
          <div class="text-sm font-bold text-gray-400 mt-2">в т.ч. НДС 0%</div>
        </div>
      </div>

      <div class="border-t border-gray-100 pt-6 text-center text-sm font-bold text-gray-400">
        Спасибо за заказ! Для уточнений обращайтесь по телефону <?= e($settings['phone'] ?? '') ?>
      </div>
    </div>
  </div>

</body>
</html>
