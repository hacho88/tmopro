<?php
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money($value) {
    return number_format((float)$value, 0, ',', ' ') . ' ₽';
}

function db_try_save_order($orderNumber, $company, $inn, $contactPerson, $phone, $email, $baseTotal, $total, $cart) {
    $dbPath = __DIR__ . '/db.php';
    if (!file_exists($dbPath)) return;
    require_once $dbPath;
    if (!function_exists('tmopro_db')) return;
    $pdo = tmopro_db();
    if (!$pdo) return;

    $accountId = !empty($_SESSION['b2b_account_id']) ? (int)$_SESSION['b2b_account_id'] : null;

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO orders (order_number, source, status, account_id, company_name, inn, contact_person, phone, email, total_base, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $orderNumber,
            'site',
            'new',
            $accountId,
            $company,
            ($inn !== '' ? $inn : null),
            ($contactPerson !== '' ? $contactPerson : null),
            ($phone !== '' ? $phone : null),
            ($email !== '' ? $email : null),
            (float)$baseTotal,
            (float)$total,
        ]);
        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, article, name, brand, category, qty, price_base, price_wholesale, unit_price, line_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($cart as $item) {
            $qty = max(1, (int)($item['qty'] ?? 1));
            $priceBase = (float)($item['price_base'] ?? 0);
            $priceWholesale = (float)($item['price_wholesale'] ?? 0);
            $unitPrice = $qty >= 10 ? $priceWholesale : $priceBase;
            $lineTotal = $unitPrice * $qty;

            $itemStmt->execute([
                $orderId,
                isset($item['id']) ? (int)$item['id'] : null,
                (string)($item['article'] ?? ''),
                (string)($item['name'] ?? ''),
                (string)($item['brand'] ?? ''),
                (string)($item['category'] ?? ''),
                $qty,
                $priceBase,
                $priceWholesale,
                $unitPrice,
                $lineTotal,
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        try { $pdo->rollBack(); } catch (Throwable $e2) {}
    }
}

$settingsPath = __DIR__ . '/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$managerEmail = $settings['email_manager'] ?? 'info@tmopro.ru';
$siteName = $settings['site_name'] ?? 'tmopro.ru — Сантехника Оптом';

$company = trim($_POST['company'] ?? '');
$inn = trim($_POST['inn'] ?? '');
$contactPerson = trim($_POST['contact_person'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$cartJson = $_POST['cart_json'] ?? '[]';
$cart = json_decode($cartJson, true);

if (!is_array($cart)) {
    $cart = [];
}

$orderNumber = date('ymd') . '-' . random_int(1000, 9999);
$baseTotal = 0;
$total = 0;
$rows = '';

foreach ($cart as $item) {
    $qty = max(1, (int)($item['qty'] ?? 1));
    $priceBase = (float)($item['price_base'] ?? 0);
    $priceWholesale = (float)($item['price_wholesale'] ?? 0);
    $unitPrice = $qty >= 10 ? $priceWholesale : $priceBase;
    $lineBase = $priceBase * $qty;
    $lineTotal = $unitPrice * $qty;
    $baseTotal += $lineBase;
    $total += $lineTotal;

    $rows .= '<tr>';
    $rows .= '<td style="padding:14px;border-bottom:1px solid #e2e8f0;"><strong>' . e($item['name'] ?? '') . '</strong><br><span style="color:#64748b;font-size:12px;">' . e($item['article'] ?? '') . ' · ' . e($item['brand'] ?? '') . '</span></td>';
    $rows .= '<td style="padding:14px;border-bottom:1px solid #e2e8f0;text-align:center;">' . $qty . '</td>';
    $rows .= '<td style="padding:14px;border-bottom:1px solid #e2e8f0;text-align:right;">' . money($unitPrice) . '</td>';
    $rows .= '<td style="padding:14px;border-bottom:1px solid #e2e8f0;text-align:right;"><strong>' . money($lineTotal) . '</strong></td>';
    $rows .= '</tr>';
}

$savings = max(0, $baseTotal - $total);

$subject = 'Заказ №' . $orderNumber . ' с сайта tmopro.ru';
$message = '<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>' . e($subject) . '</title></head>';
$message .= '<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">';
$message .= '<div style="max-width:760px;margin:0 auto;padding:32px;">';
$message .= '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,.04);">';
$message .= '<div style="font-size:13px;font-weight:700;color:#4f46e5;text-transform:uppercase;letter-spacing:.08em;">' . e($siteName) . '</div>';
$message .= '<h1 style="margin:10px 0 6px;font-size:28px;line-height:1.15;">Новый заказ №' . e($orderNumber) . '</h1>';
$message .= '<p style="margin:0 0 24px;color:#64748b;">Покупатель запросил счет и резерв товара.</p>';
$message .= '<h2 style="font-size:18px;margin:0 0 12px;">Реквизиты покупателя</h2>';
$message .= '<table style="width:100%;border-collapse:collapse;margin-bottom:26px;background:#f8fafc;border-radius:16px;overflow:hidden;">';
$message .= '<tr><td style="padding:10px 14px;color:#64748b;">Компания / ИП</td><td style="padding:10px 14px;"><strong>' . e($company) . '</strong></td></tr>';
$message .= '<tr><td style="padding:10px 14px;color:#64748b;">ИНН</td><td style="padding:10px 14px;">' . e($inn) . '</td></tr>';
$message .= '<tr><td style="padding:10px 14px;color:#64748b;">Контактное лицо</td><td style="padding:10px 14px;">' . e($contactPerson) . '</td></tr>';
$message .= '<tr><td style="padding:10px 14px;color:#64748b;">Телефон</td><td style="padding:10px 14px;">' . e($phone) . '</td></tr>';
$message .= '<tr><td style="padding:10px 14px;color:#64748b;">Email</td><td style="padding:10px 14px;">' . e($email) . '</td></tr>';
$message .= '</table>';
$message .= '<h2 style="font-size:18px;margin:0 0 12px;">Состав заказа</h2>';
$message .= '<table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">';
$message .= '<thead><tr style="background:#f8fafc;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.08em;"><th style="padding:14px;text-align:left;">Товар</th><th style="padding:14px;text-align:center;">Кол-во</th><th style="padding:14px;text-align:right;">Цена</th><th style="padding:14px;text-align:right;">Сумма</th></tr></thead>';
$message .= '<tbody>' . $rows . '</tbody>';
$message .= '</table>';
$message .= '<div style="margin-top:24px;background:#f8fafc;border-radius:18px;padding:18px;">';
$message .= '<div style="display:flex;justify-content:space-between;margin-bottom:8px;color:#64748b;"><span>Сумма по рознице</span><strong>' . money($baseTotal) . '</strong></div>';
$message .= '<div style="display:flex;justify-content:space-between;margin-bottom:8px;color:#059669;"><span>Оптовая экономия</span><strong>−' . money($savings) . '</strong></div>';
$message .= '<div style="display:flex;justify-content:space-between;font-size:22px;"><span><strong>Итого</strong></span><strong>' . money($total) . '</strong></div>';
$message .= '</div>';
$message .= '</div></div></body></html>';

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=UTF-8';
$headers[] = 'From: noreply@tmopro.ru';
$headers[] = 'Reply-To: ' . ($email !== '' ? $email : 'noreply@tmopro.ru');

$invoiceUrl = 'https://tmopro.ru/invoice.php?order=' . urlencode($orderNumber);

// Manager email (add invoice link)
$managerMsg = $message;
$managerMsg = str_replace('</div></div></body></html>', '<div style="margin-top:20px;padding:16px;background:#ecfdf5;border-radius:14px;border:1px solid #a7f3d0;"><div style="font-size:13px;font-weight:900;color:#059669;margin-bottom:8px;">Счет для клиента</div><a href="' . $invoiceUrl . '" style="color:#059669;font-weight:900;">' . $invoiceUrl . '</a></div></div></div></body></html>', $managerMsg);

// Customer confirmation email
$customerSubject = 'Ваш заказ №' . $orderNumber . ' принят — ' . e($siteName);
$customerMsg = '<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>' . e($customerSubject) . '</title></head>';
$customerMsg .= '<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">';
$customerMsg .= '<div style="max-width:600px;margin:0 auto;padding:32px;">';
$customerMsg .= '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,.04);">';
$customerMsg .= '<div style="font-size:13px;font-weight:700;color:#4f46e5;text-transform:uppercase;letter-spacing:.08em;">' . e($siteName) . '</div>';
$customerMsg .= '<h1 style="margin:10px 0 6px;font-size:26px;line-height:1.15;">Заказ принят!</h1>';
$customerMsg .= '<p style="margin:0 0 24px;color:#64748b;">Спасибо за заказ. Наш менеджер свяжется с вами в ближайшее время.</p>';
$customerMsg .= '<div style="background:#f8fafc;border-radius:16px;padding:18px;margin-bottom:24px;">';
$customerMsg .= '<div style="font-size:12px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">Номер заказа</div>';
$customerMsg .= '<div style="font-size:22px;font-weight:900;">' . e($orderNumber) . '</div>';
$customerMsg .= '</div>';
$customerMsg .= '<h2 style="font-size:16px;margin:0 0 12px;">Состав заказа</h2>';
$customerMsg .= '<table style="width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">';
$customerMsg .= '<thead><tr style="background:#f8fafc;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.08em;"><th style="padding:14px;text-align:left;">Товар</th><th style="padding:14px;text-align:center;">Кол-во</th><th style="padding:14px;text-align:right;">Сумма</th></tr></thead>';
$customerMsg .= '<tbody>' . $rows . '</tbody>';
$customerMsg .= '</table>';
$customerMsg .= '<div style="margin-top:24px;background:#f8fafc;border-radius:18px;padding:18px;">';
$customerMsg .= '<div style="display:flex;justify-content:space-between;font-size:20px;"><span><strong>Итого</strong></span><strong>' . money($total) . '</strong></div>';
$customerMsg .= '</div>';
$customerMsg .= '<div style="margin-top:24px;text-align:center;">';
$customerMsg .= '<a href="' . $invoiceUrl . '" style="display:inline-block;padding:14px 28px;background:#059669;color:#fff;border-radius:16px;font-weight:900;text-decoration:none;">Открыть счет →</a>';
$customerMsg .= '</div>';
$customerMsg .= '<div style="margin-top:24px;border-top:1px solid #e2e8f0;padding-top:16px;text-align:center;font-size:13px;color:#64748b;font-weight:700;">';
$customerMsg .= 'Если у вас есть вопросы, позвоните: ' . e($settings['phone'] ?? '') . '<br>' . e($settings['email_manager'] ?? '') . '</div>';
$customerMsg .= '</div></div></body></html>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $company !== '' && count($cart) > 0) {
    db_try_save_order($orderNumber, $company, $inn, $contactPerson, $phone, $email, $baseTotal, $total, $cart);
    mail($managerEmail, '=?UTF-8?B?' . base64_encode($subject) . '?=', $managerMsg, implode("\r\n", $headers));
    if ($email !== '') {
        mail($email, '=?UTF-8?B?' . base64_encode($customerSubject) . '?=', $customerMsg, implode("\r\n", $headers));
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Заказ принят — tmopro.ru</title>
  <meta name="theme-color" content="#059669">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Segoe UI, Arial; }
    .bg-orb { position: fixed; inset: 0; pointer-events: none; z-index: -1; }
    .bg-orb::before {
      content: '';
      position: absolute;
      left: 50%;
      top: -240px;
      width: 820px;
      height: 520px;
      transform: translateX(-50%);
      border-radius: 999px;
      background: radial-gradient(closest-side, rgba(255,255,255,.0), rgba(255,255,255,0)),
                  radial-gradient(closest-side, rgba(5,150,105,.22), transparent 70%);
      filter: blur(44px);
      opacity: .55;
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50/50 text-slate-950 antialiased">
  <div class="bg-orb"></div>
  <main class="grid min-h-screen place-items-center px-4 py-12">
    <section class="w-full max-w-xl rounded-2xl border border-slate-100 bg-white p-8 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:p-12">
      <div class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-2xl bg-emerald-600 text-white shadow-lg">
        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
      </div>
      <div class="mb-3 inline-flex rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 ring-1 ring-inset ring-emerald-100">Заявка отправлена</div>
      <h1 class="text-3xl font-extrabold tracking-[-0.03em] sm:text-4xl">Заказ №<?= e($orderNumber) ?> успешно принят!</h1>
      <p class="mt-4 text-lg leading-8 text-slate-600">Менеджер сформирует счет и свяжется с вами для подтверждения резерва.</p>
      <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="invoice.php?order=<?= e($orderNumber) ?>" target="_blank" class="inline-flex rounded-2xl bg-emerald-600 px-6 py-4 text-sm font-extrabold text-white transition hover:-translate-y-0.5 hover:shadow-lg">Открыть счет →</a>
        <a href="index.php" class="inline-flex rounded-2xl bg-slate-950 px-6 py-4 text-sm font-extrabold text-white transition hover:-translate-y-0.5 hover:shadow-lg">Вернуться в каталог</a>
      </div>
    </section>
  </main>
</body>
</html>
