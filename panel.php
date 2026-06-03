<?php
session_start();
header('Cache-Control: no-store');

const ADMIN_PASSWORD = 'admin123';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function read_json($path, $fallback) {
    if (!file_exists($path)) return $fallback;
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : $fallback;
}

function save_json($path, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
}

function handle_upload($field, $subfolder) {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $uploadDir = __DIR__ . '/uploads/' . $subfolder . '/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp','svg'];
    if (!in_array($ext, $allowed, true)) return 'err_ext';
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $path = $uploadDir . $name;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
        return 'uploads/' . $subfolder . '/' . $name;
    }
    return 'err_move';
}

$settingsPath = __DIR__ . '/settings.json';
$productsPath = __DIR__ . '/products.json';
$categoriesPath = __DIR__ . '/categories.json';
$categories = read_json($categoriesPath, []);

$defaultSettings = [
    'site_name' => 'TMOPRO — Сантехника оптом',
    'site_short_name' => 'TMOPRO',
    'phone' => '+7 (800) 555-35-35',
    'email_manager' => 'info@tmopro.ru',
    'theme_color' => 'emerald',
    'default_view' => 'table',
    'logo_type' => 'image',
    'logo_text' => 'TMOPRO',
    'logo_url' => 'logo.svg',
    'background_type' => 'gradient',
    'background_color' => '#f8fafc',
    'background_image' => '',
    'hero_title' => 'Сантехника оптом от производителя. Все на одной площадке.',
    'hero_subtitle' => 'Шаровые краны, фитинги, трубы и комплектующие для инженерных систем. Оптовые цены от 10 шт.'
];

$settings = array_merge($defaultSettings, read_json($settingsPath, $defaultSettings));
$products = read_json($productsPath, []);
$tab = $_GET['tab'] ?? 'overview';
$error = '';
$success = '';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_password'])) {
    if (hash_equals(ADMIN_PASSWORD, (string)$_POST['login_password'])) {
        $_SESSION['tmopro_admin'] = true;
        header('Location: panel.php');
        exit;
    }
    $error = 'Неверный пароль.';
}

$isAuthorized = !empty($_SESSION['tmopro_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAuthorized) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $settings = array_merge($settings, [
            'site_name' => trim($_POST['site_name'] ?? $settings['site_name']),
            'site_short_name' => trim($_POST['site_short_name'] ?? $settings['site_short_name']),
            'phone' => trim($_POST['phone'] ?? $settings['phone']),
            'email_manager' => trim($_POST['email_manager'] ?? $settings['email_manager']),
            'theme_color' => in_array($_POST['theme_color'] ?? '', ['indigo','emerald','slate']) ? $_POST['theme_color'] : 'indigo',
            'default_view' => in_array($_POST['default_view'] ?? '', ['table','grid']) ? $_POST['default_view'] : 'table',
            'logo_type' => in_array($_POST['logo_type'] ?? '', ['text','image']) ? $_POST['logo_type'] : 'text',
            'logo_text' => trim($_POST['logo_text'] ?? 'TMO'),
            'logo_url' => trim($_POST['logo_url'] ?? ''),
            'background_type' => in_array($_POST['background_type'] ?? '', ['gradient','solid','image']) ? $_POST['background_type'] : 'gradient',
            'background_color' => trim($_POST['background_color'] ?? '#f8fafc'),
            'background_image' => trim($_POST['background_image'] ?? ''),
            'hero_title' => trim($_POST['hero_title'] ?? $settings['hero_title']),
            'hero_subtitle' => trim($_POST['hero_subtitle'] ?? $settings['hero_subtitle'])
        ]);
        if (!empty($_FILES['logo_file']['tmp_name'])) {
            $logo = handle_upload('logo_file', 'logos');
            if ($logo && !str_starts_with($logo, 'err_')) { $settings['logo_url'] = $logo; $settings['logo_type'] = 'image'; }
            elseif ($logo) $error = 'Ошибка загрузки логотипа: ' . $logo;
        }
        if (!empty($_FILES['bg_file']['tmp_name'])) {
            $bg = handle_upload('bg_file', 'backgrounds');
            if ($bg && !str_starts_with($bg, 'err_')) { $settings['background_image'] = $bg; $settings['background_type'] = 'image'; }
            elseif ($bg) $error = 'Ошибка загрузки фона: ' . $bg;
        }
        if (save_json($settingsPath, $settings)) $success = ($success ?: '') . ' Настройки сохранены.';
        else $error = 'Ошибка записи settings.json';
        $tab = 'settings';
    }

    if ($action === 'save_products') {
        $saved = [];
        foreach (($_POST['id'] ?? []) as $i => $id) {
            $name = trim($_POST['name'][$i] ?? '');
            if ($name === '') continue;
            $img = $products[$i]['image'] ?? '';
            $fileKey = 'product_image_' . $id;
            if (!empty($_FILES[$fileKey]['tmp_name'])) {
                $up = handle_upload($fileKey, 'products');
                if ($up && !str_starts_with($up, 'err_')) $img = $up;
            }
            $saved[] = [
                'id' => (int)$id,
                'article' => trim($_POST['article'][$i] ?? ''),
                'name' => $name,
                'category' => trim($_POST['category'][$i] ?? ''),
                'brand' => trim($_POST['brand'][$i] ?? ''),
                'stock' => max(0, (int)($_POST['stock'][$i] ?? 0)),
                'price_base' => max(0, (float)($_POST['price_base'][$i] ?? 0)),
                'price_wholesale' => max(0, (float)($_POST['price_wholesale'][$i] ?? 0)),
                'image' => $img
            ];
        }
        $products = $saved;
        if (save_json($productsPath, $products)) $success = 'Каталог сохранен.';
        else $error = 'Ошибка записи products.json';
        $tab = 'products';
    }

    if ($action === 'add_product') {
        $maxId = 0;
        foreach ($products as $p) $maxId = max($maxId, (int)($p['id'] ?? 0));
        $products[] = ['id' => $maxId + 1, 'article' => 'NEW-' . ($maxId + 1), 'name' => 'Новый товар', 'category' => 'Смесители для умывальника', 'brand' => 'TIM', 'stock' => 0, 'price_base' => 0, 'price_wholesale' => 0];
        if (save_json($productsPath, $products)) $success = 'Товар добавлен.';
        $tab = 'products';
    }

    if ($action === 'delete_product') {
        $did = (int)($_POST['delete_id'] ?? 0);
        $products = array_values(array_filter($products, fn($p) => (int)($p['id'] ?? 0) !== $did));
        if (save_json($productsPath, $products)) $success = 'Товар удален.';
        $tab = 'products';
    }
}

$themeColor = ['indigo' => '#4f46e5', 'emerald' => '#059669', 'slate' => '#0f172a'][$settings['theme_color']] ?? '#4f46e5';
$totalStock = array_sum(array_map(fn($p) => (int)($p['stock'] ?? 0), $products));
$totalRetail = array_sum(array_map(fn($p) => (float)($p['price_base'] ?? 0), $products));
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Админка tmopro.ru</title>
<style>
* { box-sizing: border-box; margin: 0; }
body { font-family: system-ui, -apple-system, Segoe UI, Arial, sans-serif; background: #f6f7fb; color: #0f172a; line-height: 1.5; }
a { color: inherit; text-decoration: none; }
input, select, textarea, button { font: inherit; }

.wrap { max-width: 1100px; margin: 0 auto; padding: 24px; }
.card { background: #fff; border-radius: 20px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,.06); margin-bottom: 20px; }
.btn { display: inline-block; background: <?= e($themeColor) ?>; color: #fff; padding: 12px 20px; border-radius: 12px; font-weight: 700; border: 0; cursor: pointer; }
.btn-dark { background: #0f172a; }
.btn-red { background: #dc2626; }

.field { width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; }
label { display: block; margin-bottom: 16px; }
label span { display: block; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }

.login-box { max-width: 400px; margin: 80px auto; text-align: center; }
.login-box .logo { width: 64px; height: 64px; line-height: 64px; border-radius: 16px; color: #fff; font-weight: 900; margin: 0 auto 20px; background: <?= e($themeColor) ?>; }
.login-box h1 { font-size: 24px; margin-bottom: 8px; }
.login-box p { color: #64748b; margin-bottom: 24px; font-size: 14px; }
.login-box input { margin-bottom: 12px; }
.login-box .btn { width: 100%; }

.nav { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
.nav a { padding: 10px 16px; border-radius: 12px; background: #f1f5f9; color: #475569; font-weight: 700; font-size: 14px; }
.nav a.active { background: <?= e($themeColor) ?>; color: #fff; }

.grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
.stat { text-align: center; }
.stat .num { font-size: 32px; font-weight: 900; }
.stat .lbl { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; }

.product-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; }
@media (max-width: 768px) { .product-row { grid-template-columns: 1fr 1fr; } }

.msg { padding: 14px 18px; border-radius: 12px; margin-bottom: 16px; font-weight: 700; font-size: 14px; }
.msg.ok { background: #ecfdf5; color: #047857; }
.msg.err { background: #fef2f2; color: #b91c1c; }

.sticky-save { position: sticky; bottom: 16px; background: #fff; padding: 12px; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,.12); text-align: center; }

.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 12px; }
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.gap-2 { gap: 8px; }
.text-sm { font-size: 14px; }
.font-bold { font-weight: 700; }
</style>
</head>
<body>

<?php if (!$isAuthorized): ?>
<div class="login-box">
  <div class="logo">TMO</div>
  <h1>Панель управления</h1>
  <p>Вход для администратора сайта</p>
  <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="password" name="login_password" placeholder="Пароль" required class="field">
    <button class="btn">Войти</button>
  </form>
</div>

<?php else: ?>
<div class="wrap">
  <div class="card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
      <div style="font-size:12px;font-weight:700;color:#64748b;">Без MySQL · JSON CMS</div>
      <h1 style="font-size:28px;margin-top:4px;">Админ-панель</h1>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="index.php" target="_blank" class="btn btn-dark" style="text-decoration:none;">Открыть сайт</a>
      <a href="panel.php?logout=1" style="padding:10px 16px;border-radius:12px;background:#f1f5f9;color:#475569;font-weight:700;font-size:14px;text-decoration:none;">Выйти</a>
    </div>
  </div>

  <?php if ($success): ?><div class="msg ok"><?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>

  <div class="nav">
    <a href="panel.php?tab=overview" class="<?= $tab==='overview'?'active':'' ?>">Обзор</a>
    <a href="panel.php?tab=settings" class="<?= $tab==='settings'?'active':'' ?>">Настройки сайта</a>
    <a href="panel.php?tab=products" class="<?= $tab==='products'?'active':'' ?>">Товары</a>
  </div>

  <?php if ($tab === 'overview'): ?>
    <div class="grid-3">
      <div class="card stat"><div class="lbl">Товаров</div><div class="num"><?= count($products) ?></div></div>
      <div class="card stat"><div class="lbl">Остаток</div><div class="num"><?= e($totalStock) ?></div></div>
      <div class="card stat"><div class="lbl">Сумма цен</div><div class="num"><?= e(number_format($totalRetail,0,',',' ')) ?> ₽</div></div>
    </div>
    <div class="card">
      <h2 style="font-size:20px;margin-bottom:12px;">Как работать</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
        <a href="panel.php?tab=settings" class="card" style="text-decoration:none;">
          <b>1. Настроить сайт</b>
          <p style="margin-top:6px;color:#64748b;font-size:14px;">Название, телефон, почта, логотип, фон и главный экран.</p>
        </a>
        <a href="panel.php?tab=products" class="card" style="text-decoration:none;">
          <b>2. Заполнить товары</b>
          <p style="margin-top:6px;color:#64748b;font-size:14px;">Артикулы, бренды, остатки, розничные и оптовые цены.</p>
        </a>
        <a href="index.php" target="_blank" class="card" style="text-decoration:none;">
          <b>3. Проверить витрину</b>
          <p style="margin-top:6px;color:#64748b;font-size:14px;">Откройте сайт и проверьте, как покупатель видит каталог.</p>
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'settings'): ?>
    <form method="post" class="card" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save_settings">
      <h2 style="font-size:20px;margin-bottom:16px;">Настройки сайта</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
        <label><span>Название сайта</span><input name="site_name" value="<?= e($settings['site_name']) ?>" class="field"></label>
        <label><span>Короткое имя</span><input name="site_short_name" value="<?= e($settings['site_short_name']) ?>" class="field"></label>
        <label><span>Телефон</span><input name="phone" value="<?= e($settings['phone']) ?>" class="field"></label>
        <label><span>Email менеджера</span><input name="email_manager" type="email" value="<?= e($settings['email_manager']) ?>" class="field"></label>
        <label><span>Цвет темы</span>
          <select name="theme_color" class="field">
            <option value="indigo" <?= $settings['theme_color']==='indigo'?'selected':'' ?>>Индиго</option>
            <option value="emerald" <?= $settings['theme_color']==='emerald'?'selected':'' ?>>Изумрудный</option>
            <option value="slate" <?= $settings['theme_color']==='slate'?'selected':'' ?>>Серый</option>
          </select>
        </label>
        <label><span>Вид каталога</span>
          <select name="default_view" class="field">
            <option value="table" <?= $settings['default_view']==='table'?'selected':'' ?>>Таблица</option>
            <option value="grid" <?= $settings['default_view']==='grid'?'selected':'' ?>>Плитка</option>
          </select>
        </label>
        <label><span>Тип логотипа</span>
          <select name="logo_type" class="field">
            <option value="text" <?= $settings['logo_type']==='text'?'selected':'' ?>>Текст</option>
            <option value="image" <?= $settings['logo_type']==='image'?'selected':'' ?>>Картинка</option>
          </select>
        </label>
        <label><span>Текст логотипа</span><input name="logo_text" value="<?= e($settings['logo_text']) ?>" class="field"></label>
        <label style="grid-column:1/-1;"><span>Ссылка на логотип</span><input name="logo_url" value="<?= e($settings['logo_url']) ?>" placeholder="logo.png" class="field"></label>
        <label style="grid-column:1/-1;"><span>Или загрузить логотип</span><input type="file" name="logo_file" accept="image/*" class="field" style="padding:8px;"></label>
        <?php if ($settings['logo_url']): ?><div style="grid-column:1/-1;"><img src="<?= e($settings['logo_url']) ?>" style="max-height:60px;border-radius:8px;"></div><?php endif; ?>
        <label><span>Тип фона</span>
          <select name="background_type" class="field">
            <option value="gradient" <?= $settings['background_type']==='gradient'?'selected':'' ?>>Градиент</option>
            <option value="solid" <?= $settings['background_type']==='solid'?'selected':'' ?>>Цвет</option>
            <option value="image" <?= $settings['background_type']==='image'?'selected':'' ?>>Картинка</option>
          </select>
        </label>
        <label><span>Цвет фона</span><input name="background_color" type="color" value="<?= e($settings['background_color']) ?>" class="field" style="height:48px;padding:4px;"></label>
        <label style="grid-column:1/-1;"><span>Ссылка на фон</span><input name="background_image" value="<?= e($settings['background_image']) ?>" placeholder="background.jpg" class="field"></label>
        <label style="grid-column:1/-1;"><span>Или загрузить фон</span><input type="file" name="bg_file" accept="image/*" class="field" style="padding:8px;"></label>
        <?php if ($settings['background_image']): ?><div style="grid-column:1/-1;"><img src="<?= e($settings['background_image']) ?>" style="max-height:120px;border-radius:8px;"></div><?php endif; ?>
        <label style="grid-column:1/-1;"><span>Главный заголовок</span><textarea name="hero_title" rows="2" class="field"><?= e($settings['hero_title']) ?></textarea></label>
        <label style="grid-column:1/-1;"><span>Подзаголовок</span><textarea name="hero_subtitle" rows="3" class="field"><?= e($settings['hero_subtitle']) ?></textarea></label>
      </div>
      <button class="btn" style="margin-top:16px;">Сохранить настройки</button>
      <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:12px;font-size:13px;color:#64748b;">
        <b>Проверка записи:</b> settings.json <?= is_writable($settingsPath)?'✅ доступен':'❌ нет прав' ?> | products.json <?= is_writable($productsPath)?'✅ доступен':'❌ нет прав' ?>
      </div>
    </form>
  <?php endif; ?>

  <?php if ($tab === 'products'): ?>
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
        <div>
          <h2 style="font-size:20px;">Товары</h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;">Одна карточка — один товар. После правок нажмите «Сохранить каталог».</p>
        </div>
        <form method="post"><input type="hidden" name="action" value="add_product"><button class="btn btn-dark">+ Добавить</button></form>
      </div>

      <form method="post" id="prodForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_products">
        <?php foreach ($products as $i => $product): ?>
          <div style="background:#f8fafc;border-radius:16px;padding:16px;margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
              <span style="font-size:12px;font-weight:700;color:#64748b;">ID <?= e($product['id'] ?? $i+1) ?></span>
              <button type="submit" form="del<?= e($product['id'] ?? 0) ?>" class="btn btn-red" style="font-size:12px;padding:6px 12px;" onclick="return confirm('Удалить?')">Удалить</button>
            </div>
            <input type="hidden" name="id[]" value="<?= e($product['id'] ?? $i+1) ?>">
            <div class="product-row">
              <label><span>Название</span><input name="name[]" value="<?= e($product['name'] ?? '') ?>" class="field"></label>
              <label><span>Артикул</span><input name="article[]" value="<?= e($product['article'] ?? '') ?>" class="field"></label>
              <label><span>Бренд</span><input name="brand[]" value="<?= e($product['brand'] ?? '') ?>" class="field"></label>
              <label><span>Категория</span>
                <select name="category[]" class="field">
                  <?php foreach ($categories as $cat): ?>
                    <optgroup label="<?= e($cat['name']) ?>">
                      <?php foreach ($cat['subcategories'] as $sub): ?>
                        <option value="<?= e($sub['name']) ?>" <?= ($product['category'] ?? '') === $sub['name'] ? 'selected' : '' ?>><?= e($sub['name']) ?></option>
                      <?php endforeach; ?>
                    </optgroup>
                  <?php endforeach; ?>
                </select>
              </label>
              <label><span>Остаток</span><input name="stock[]" type="number" value="<?= e($product['stock'] ?? 0) ?>" class="field"></label>
              <label><span>Цена до 10</span><input name="price_base[]" type="number" step="0.01" value="<?= e($product['price_base'] ?? 0) ?>" class="field"></label>
              <label><span>Опт от 10</span><input name="price_wholesale[]" type="number" step="0.01" value="<?= e($product['price_wholesale'] ?? 0) ?>" class="field"></label>
            </div>
            <label style="margin-top:12px;"><span>Фото товара</span>
              <?php if (!empty($product['image'])): ?><div style="margin-bottom:8px;"><img src="<?= e($product['image']) ?>" style="max-height:80px;border-radius:8px;"></div><?php endif; ?>
              <input type="file" name="product_image_<?= e($product['id'] ?? 0) ?>" accept="image/*" class="field" style="padding:8px;">
            </label>
          </div>
        <?php endforeach; ?>
        <div class="sticky-save"><button class="btn" style="width:100%;">Сохранить каталог</button></div>
      </form>

      <?php foreach ($products as $product): ?>
        <form method="post" id="del<?= e($product['id'] ?? 0) ?>">
          <input type="hidden" name="action" value="delete_product">
          <input type="hidden" name="delete_id" value="<?= e($product['id'] ?? 0) ?>">
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>

</body>
</html>
