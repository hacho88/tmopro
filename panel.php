<?php
session_start();
header('Cache-Control: no-store');

const ADMIN_LOGIN = 'admin';
const ADMIN_PASSWORD = 'tatar';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Admin translations
$adminLangFile = __DIR__ . '/admin_lang.json';
$adminLangData = file_exists($adminLangFile) ? json_decode(file_get_contents($adminLangFile), true) : [];
if (!is_array($adminLangData)) $adminLangData = [];

$allowedAdminLangs = ['ru','zh','ky'];
$adminLang = 'ru';
if (isset($_GET['admin_lang']) && in_array($_GET['admin_lang'], $allowedAdminLangs, true)) {
    $adminLang = $_GET['admin_lang'];
    setcookie('admin_lang', $adminLang, time() + 86400 * 365, '/');
} elseif (isset($_COOKIE['admin_lang']) && in_array($_COOKIE['admin_lang'], $allowedAdminLangs, true)) {
    $adminLang = $_COOKIE['admin_lang'];
}

function admin_t($key, $data = []) {
    global $adminLangData, $adminLang;
    $str = $adminLangData[$adminLang][$key] ?? ($adminLangData['ru'][$key] ?? $key);
    return $str;
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

function tmopro_db_safe() {
    $dbPath = __DIR__ . '/db.php';
    if (!file_exists($dbPath)) return null;
    require_once $dbPath;
    if (!function_exists('tmopro_db')) return null;
    try {
        return tmopro_db();
    } catch (Throwable $e) {
        return null;
    }
}

$settingsPath = __DIR__ . '/settings.json';
$productsPath = __DIR__ . '/products.json';
$categoriesPath = __DIR__ . '/categories.json';
$pagesPath = __DIR__ . '/pages.json';
$categories = read_json($categoriesPath, []);

$defaultSettings = [
    'site_name' => 'TMOPRO — Сантехника оптом',
    'site_short_name' => 'TMOPRO',
    'phone' => '+7 (966) 085-34-70',
    'phone2' => '+7 (925) 536-07-22',
    'phone3' => '+7 (926) 869-04-28',
    'whatsapp' => '',
    'email_manager' => 'info@tmopro.ru',
    'address' => '',
    'theme_color' => 'emerald',
    'default_view' => 'table',
    'logo_type' => 'image',
    'logo_text' => 'TMOPRO',
    'logo_url' => 'logo.svg',
    'favicon' => '',
    'font_family' => 'Inter',
    'background_type' => 'gradient',
    'background_color' => '#f8fafc',
    'background_image' => '',
    'hero_title' => 'Сантехника оптом от производителя. Все на одной площадке.',
    'hero_subtitle' => 'Шаровые краны, фитинги, трубы и комплектующие для инженерных систем. Оптовые цены от 10 шт.'
];

$settings = array_merge($defaultSettings, read_json($settingsPath, $defaultSettings));
$products = read_json($productsPath, []);
$pages = read_json($pagesPath, []);
$tab = $_GET['tab'] ?? 'overview';
$error = '';
$success = '';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_name']) && isset($_POST['login_password'])) {
    if (hash_equals(ADMIN_LOGIN, (string)$_POST['login_name']) && hash_equals(ADMIN_PASSWORD, (string)$_POST['login_password'])) {
        $_SESSION['tmopro_admin'] = true;
        header('Location: panel.php');
        exit;
    }
    $error = 'Неверный логин или пароль.';
}

$isAuthorized = !empty($_SESSION['tmopro_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAuthorized) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $settings = array_merge($settings, [
            'site_name' => trim($_POST['site_name'] ?? $settings['site_name']),
            'site_short_name' => trim($_POST['site_short_name'] ?? $settings['site_short_name']),
            'phone' => trim($_POST['phone'] ?? $settings['phone']),
            'phone2' => trim($_POST['phone2'] ?? $settings['phone2']),
            'phone3' => trim($_POST['phone3'] ?? $settings['phone3']),
            'whatsapp' => trim($_POST['whatsapp'] ?? $settings['whatsapp']),
            'email_manager' => trim($_POST['email_manager'] ?? $settings['email_manager']),
            'address' => trim($_POST['address'] ?? $settings['address']),
            'theme_color' => in_array($_POST['theme_color'] ?? '', ['indigo','emerald','slate']) ? $_POST['theme_color'] : 'indigo',
            'default_view' => in_array($_POST['default_view'] ?? '', ['table','grid']) ? $_POST['default_view'] : 'table',
            'logo_type' => in_array($_POST['logo_type'] ?? '', ['text','image']) ? $_POST['logo_type'] : 'text',
            'logo_text' => trim($_POST['logo_text'] ?? 'TMO'),
            'logo_url' => trim($_POST['logo_url'] ?? ''),
            'favicon' => trim($_POST['favicon'] ?? ''),
            'font_family' => in_array($_POST['font_family'] ?? '', ['Inter','Roboto','Open Sans','Manrope','Noto Sans']) ? $_POST['font_family'] : 'Inter',
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
        if (!empty($_FILES['favicon_file']['tmp_name'])) {
            $fav = handle_upload('favicon_file', 'uploads');
            if ($fav && !str_starts_with($fav, 'err_')) { $settings['favicon'] = $fav; }
            elseif ($fav) $error = 'Ошибка загрузки favicon: ' . $fav;
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
            $tagsRaw = (string)($_POST['tags'][$i] ?? '');
            $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw)), fn($t) => $t !== ''));
            $saved[] = [
                'id' => (int)$id,
                'article' => trim($_POST['article'][$i] ?? ''),
                'name' => $name,
                'category' => trim($_POST['category'][$i] ?? ''),
                'brand' => trim($_POST['brand'][$i] ?? ''),
                'stock' => max(0, (int)($_POST['stock'][$i] ?? 0)),
                'price_base' => max(0, (float)($_POST['price_base'][$i] ?? 0)),
                'price_wholesale' => max(0, (float)($_POST['price_wholesale'][$i] ?? 0)),
                'image' => $img,
                'description' => trim((string)($_POST['description'][$i] ?? ($products[$i]['description'] ?? ''))),
                'tags' => $tags
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

    if ($action === 'duplicate_product') {
        $dupId = (int)($_POST['duplicate_id'] ?? 0);
        $source = null;
        foreach ($products as $p) {
            if ((int)($p['id'] ?? 0) === $dupId) { $source = $p; break; }
        }
        if ($source) {
            $maxId = 0;
            foreach ($products as $p) $maxId = max($maxId, (int)($p['id'] ?? 0));
            $newProduct = $source;
            $newProduct['id'] = $maxId + 1;
            $newProduct['name'] = ($source['name'] ?? '') . ' (copy)';
            $newProduct['article'] = ($source['article'] ?? '') . '-COPY';
            $newProduct['stock'] = 0;
            $products[] = $newProduct;
            if (save_json($productsPath, $products)) $success = admin_t('add');
        }
        $tab = 'products';
    }

    if ($action === 'bulk_delete_products') {
        $ids = array_map('intval', $_POST['bulk_ids'] ?? []);
        if ($ids) {
            $products = array_values(array_filter($products, fn($p) => !in_array((int)($p['id'] ?? 0), $ids)));
            if (save_json($productsPath, $products)) $success = 'Удалено: ' . count($ids);
        }
        $tab = 'products';
    }

    if ($action === 'save_categories') {
        $updated = [];
        $missing = [];

        foreach ($categories as $cat) {
            $cid = (int)($cat['id'] ?? 0);
            $cat['name'] = trim((string)($_POST['cat_name_' . $cid] ?? $cat['name']));
            $subs = [];
            foreach (($cat['subcategories'] ?? []) as $sub) {
                $sid = (int)($sub['id'] ?? 0);
                $sub['name'] = trim((string)($_POST['sub_name_' . $sid] ?? $sub['name']));
                $sub['slug'] = trim((string)($_POST['sub_slug_' . $sid] ?? $sub['slug']));
                $img = (string)($sub['image'] ?? '');
                $fileKey = 'subcat_image_' . $sid;
                if (!empty($_FILES[$fileKey]['tmp_name'])) {
                    $up = handle_upload($fileKey, 'categories');
                    if ($up && !str_starts_with($up, 'err_')) $img = $up;
                    elseif ($up) $error = 'Ошибка загрузки изображения: ' . $up;
                }
                $img = trim((string)$img);
                if ($img === '') {
                    $missing[] = (string)($sub['name'] ?: ('ID ' . $sid));
                }
                $sub['image'] = $img;
                $subs[] = $sub;
            }

            $cat['subcategories'] = $subs;
            $updated[] = $cat;
        }

        if (!empty($missing)) {
            $error = 'Нельзя сохранить категории без фото. Заполни изображение для: ' . implode(', ', array_slice($missing, 0, 8)) . (count($missing) > 8 ? '…' : '');
        } else {
            $categories = $updated;
            if (save_json($categoriesPath, $categories)) $success = 'Категории сохранены.';
            else $error = 'Ошибка записи categories.json';
        }
        $tab = 'categories';
    }

    if ($action === 'add_subcategory') {
        $parentId = (int)($_POST['parent_category_id'] ?? 0);
        $name = trim((string)($_POST['sub_name'] ?? ''));
        $slug = trim((string)($_POST['sub_slug'] ?? ''));
        if ($name === '' || $slug === '') {
            $error = 'Название и slug обязательны.';
        } else {
            $img = '';
            if (!empty($_FILES['sub_image']['tmp_name'])) {
                $up = handle_upload('sub_image', 'categories');
                if ($up && !str_starts_with($up, 'err_')) $img = $up;
                elseif ($up) $error = 'Ошибка загрузки изображения: ' . $up;
            }
            if ($img === '') {
                $error = ($error ?: '') . ' Фото для категории обязательно.';
            } else {
                $maxId = 0;
                foreach ($categories as $cat) {
                    foreach (($cat['subcategories'] ?? []) as $sub) {
                        $maxId = max($maxId, (int)($sub['id'] ?? 0));
                    }
                }
                $newSub = ['id' => $maxId + 1, 'name' => $name, 'slug' => $slug, 'image' => $img];
                foreach ($categories as &$cat) {
                    if ((int)($cat['id'] ?? 0) === $parentId) {
                        if (!isset($cat['subcategories']) || !is_array($cat['subcategories'])) $cat['subcategories'] = [];
                        $cat['subcategories'][] = $newSub;
                        break;
                    }
                }
                unset($cat);
                if (save_json($categoriesPath, $categories)) $success = 'Категория добавлена.';
                else $error = 'Ошибка записи categories.json';
            }
        }
        $tab = 'categories';
    }

    if ($action === 'delete_subcategory') {
        $catId = (int)($_POST['category_id'] ?? 0);
        $subId = (int)($_POST['subcategory_id'] ?? 0);
        $updated = [];
        foreach ($categories as $cat) {
            if ((int)($cat['id'] ?? 0) === $catId) {
                $cat['subcategories'] = array_values(array_filter($cat['subcategories'] ?? [], fn($s) => (int)($s['id'] ?? 0) !== $subId));
            }
            $updated[] = $cat;
        }
        $categories = $updated;
        if (save_json($categoriesPath, $categories)) $success = 'Подкатегория удалена.';
        else $error = 'Ошибка записи categories.json';
        $tab = 'categories';
    }

    if ($action === 'delete_category') {
        $catId = (int)($_POST['category_id'] ?? 0);
        $categories = array_values(array_filter($categories, fn($c) => (int)($c['id'] ?? 0) !== $catId));
        if (save_json($categoriesPath, $categories)) $success = 'Категория удалена.';
        else $error = 'Ошибка записи categories.json';
        $tab = 'categories';
    }

    if ($action === 'add_category') {
        $name = trim((string)($_POST['cat_name'] ?? ''));
        $slug = trim((string)($_POST['cat_slug'] ?? ''));
        if ($name === '' || $slug === '') {
            $error = 'Название и slug обязательны.';
        } else {
            $maxId = 0;
            foreach ($categories as $cat) { $maxId = max($maxId, (int)($cat['id'] ?? 0)); }
            $categories[] = ['id' => $maxId + 1, 'name' => $name, 'slug' => $slug, 'subcategories' => []];
            if (save_json($categoriesPath, $categories)) $success = 'Категория добавлена.';
            else $error = 'Ошибка записи categories.json';
        }
        $tab = 'categories';
    }

    if ($action === 'update_order_status') {
        $pdo = tmopro_db_safe();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = trim((string)($_POST['status'] ?? ''));
        $note = trim((string)($_POST['status_note'] ?? ''));
        $allowed = ['new','processing','invoiced','shipped','done','cancelled'];
        if (!$pdo) {
            $error = 'База не подключена (проверь TMOPRO_DB_*).';
        } elseif ($orderId <= 0 || !in_array($status, $allowed, true)) {
            $error = 'Некорректные данные для статуса заказа.';
        } else {
            try {
                $old = $pdo->prepare('SELECT status, email, order_number, company_name FROM orders WHERE id = ?')->execute([$orderId]) ? $pdo->prepare('SELECT status, email, order_number, company_name FROM orders WHERE id = ?') : null;
                $prev = $old ? $old->fetch(PDO::FETCH_ASSOC) : null;
                $prevStatus = $prev['status'] ?? '';
                $customerEmail = $prev['email'] ?? '';
                $orderNumber = $prev['order_number'] ?? '';

                $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
                $stmt->execute([$status, $orderId]);

                try {
                    $pdo->exec("CREATE TABLE IF NOT EXISTS order_status_history (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, status VARCHAR(32) NOT NULL, note TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(order_id))");
                    $hist = $pdo->prepare('INSERT INTO order_status_history (order_id, status, note) VALUES (?, ?, ?)');
                    $hist->execute([$orderId, $status, $note]);
                } catch (Throwable $e) {}

                // Notify customer
                if ($customerEmail !== '' && $prevStatus !== $status) {
                    $statusLabels = ['new'=>'Новый','processing'=>'В работе','invoiced'=>'Счет выставлен','shipped'=>'Отгружен','done'=>'Закрыт','cancelled'=>'Отмена'];
                    $subj = '=?UTF-8?B?' . base64_encode('Статус заказа №' . $orderNumber . ' изменен — ' . ($statusLabels[$status] ?? $status)) . '?=';
                    $body = '<!doctype html><html lang="ru"><head><meta charset="utf-8"></head><body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;"><div style="max-width:600px;margin:0 auto;padding:32px;"><div style="background:#fff;border:1px solid #e2e8f0;border-radius:24px;padding:28px;"><h1 style="font-size:22px;margin:0 0 12px;">Статус заказа обновлен</h1><p>Заказ №' . e($orderNumber) . ' компании <strong>' . e($prev['company_name'] ?? '') . '</strong></p><div style="margin:16px 0;padding:16px;background:#f1f5f9;border-radius:16px;font-size:18px;font-weight:900;">Новый статус: ' . e($statusLabels[$status] ?? $status) . '</div>' . ($note ? '<p style="color:#64748b;">Комментарий: ' . e($note) . '</p>' : '') . '<p><a href="https://tmopro.ru/invoice.php?order=' . e($orderNumber) . '" style="color:#059669;font-weight:900;">Открыть счет →</a></p></div></div></body></html>';
                    $hdrs = ['MIME-Version: 1.0','Content-type: text/html; charset=UTF-8','From: noreply@tmopro.ru'];
                    mail($customerEmail, $subj, $body, implode("\r\n", $hdrs));
                }

                $success = 'Статус заказа обновлен.';
            } catch (Throwable $e) {
                $error = 'Ошибка обновления статуса заказа.';
            }
        }
        $tab = 'orders';
    }

    if ($action === 'bulk_update_order_status') {
        $pdo = tmopro_db_safe();
        $ids = array_filter(array_map('intval', (array)($_POST['bulk_order_ids'] ?? [])));
        $status = trim((string)($_POST['bulk_status'] ?? ''));
        $allowed = ['new','processing','invoiced','shipped','done','cancelled'];
        if (!$pdo) {
            $error = 'База не подключена (проверь TMOPRO_DB_*).';
        } elseif (empty($ids) || !in_array($status, $allowed, true)) {
            $error = 'Выберите заказы и статус.';
        } else {
            try {
                $in = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id IN ($in)");
                $stmt->execute(array_merge([$status], $ids));

                try {
                    $pdo->exec("CREATE TABLE IF NOT EXISTS order_status_history (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, status VARCHAR(32) NOT NULL, note TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(order_id))");
                    $hist = $pdo->prepare('INSERT INTO order_status_history (order_id, status, note) VALUES (?, ?, ?)');
                    foreach ($ids as $oid) { $hist->execute([$oid, $status, 'Массовое изменение']); }
                } catch (Throwable $e) {}

                $success = 'Статус обновлен у ' . count($ids) . ' заказов.';
            } catch (Throwable $e) {
                $error = 'Ошибка массового обновления статуса.';
            }
        }
        $tab = 'orders';
    }

    if ($action === 'create_b2b_account') {
        $pdo = tmopro_db_safe();
        $company = trim((string)($_POST['company_name'] ?? ''));
        $inn = trim((string)($_POST['inn'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $priceTier = trim((string)($_POST['price_tier'] ?? 'default'));
        if (!$pdo) {
            $error = 'База не подключена.';
        } elseif ($company === '') {
            $error = 'Укажите название компании.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO b2b_accounts (company_name, inn, email, phone, price_tier) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$company, $inn ?: null, $email ?: null, $phone ?: null, $priceTier]);
                $success = 'Компания создана. ID: ' . $pdo->lastInsertId();
            } catch (Throwable $e) {
                $error = 'Ошибка создания компании.';
            }
        }
        $tab = 'clients';
    }

    if ($action === 'create_b2b_user') {
        $pdo = tmopro_db_safe();
        $accountId = (int)($_POST['account_id'] ?? 0);
        $name = trim((string)($_POST['user_name'] ?? ''));
        $email = trim((string)($_POST['user_email'] ?? ''));
        $password = (string)($_POST['user_password'] ?? '');
        $role = trim((string)($_POST['user_role'] ?? 'buyer'));
        if (!$pdo) {
            $error = 'База не подключена.';
        } elseif ($accountId <= 0 || $name === '' || $email === '' || strlen($password) < 6) {
            $error = 'Заполните все поля (пароль мин. 6 символов).';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO b2b_users (account_id, name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$accountId, $name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
                $success = 'Пользователь создан.';
            } catch (Throwable $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Email уже используется.';
                } else {
                    $error = 'Ошибка создания пользователя.';
                }
            }
        }
        $tab = 'clients';
    }

    if ($action === 'update_account_tier') {
        $pdo = tmopro_db_safe();
        $accountId = (int)($_POST['account_id'] ?? 0);
        $priceTier = trim((string)($_POST['price_tier'] ?? 'default'));
        if ($pdo && $accountId > 0) {
            $stmt = $pdo->prepare('UPDATE b2b_accounts SET price_tier = ? WHERE id = ?');
            $stmt->execute([$priceTier, $accountId]);
            $success = 'Тариф обновлен.';
        }
        $tab = 'clients';
    }

    if ($action === 'export_products_csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tmopro-products-' . date('Y-m-d') . '.csv');
        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF");
        fputcsv($out, ['id','article','name','category','brand','stock','price_base','price_wholesale','image','description'], ';');
        foreach ($products as $p) {
            fputcsv($out, [
                $p['id'] ?? '', $p['article'] ?? '', $p['name'] ?? '', $p['category'] ?? '',
                $p['brand'] ?? '', $p['stock'] ?? 0, $p['price_base'] ?? 0, $p['price_wholesale'] ?? 0,
                $p['image'] ?? '', $p['description'] ?? ''
            ], ';');
        }
        fclose($out);
        exit;
    }

    if ($action === 'save_pages') {
        $pagesPath = __DIR__ . '/pages.json';
        $slugs = $_POST['page_slug'] ?? [];
        $titles = $_POST['page_title'] ?? [];
        $contents = $_POST['page_content'] ?? [];
        $metas = $_POST['page_meta'] ?? [];
        $saved = [];
        foreach ($slugs as $i => $slug) {
            $slug = trim((string)$slug);
            if ($slug === '') continue;
            $saved[] = [
                'slug' => $slug,
                'title' => trim((string)($titles[$i] ?? '')),
                'content' => trim((string)($contents[$i] ?? '')),
                'meta' => trim((string)($metas[$i] ?? '')),
            ];
        }
        if (save_json($pagesPath, $saved)) {
            $success = 'Страницы сохранены.';
        } else {
            $error = 'Не удалось сохранить страницы.';
        }
        $tab = 'pages';
    }

    if ($action === 'add_page') {
        $pagesPath = __DIR__ . '/pages.json';
        $pages = file_exists($pagesPath) ? json_decode(file_get_contents($pagesPath), true) : [];
        $pages = is_array($pages) ? $pages : [];
        $pages[] = ['slug' => 'new-page', 'title' => 'Новая страница', 'content' => '<p>Содержимое страницы...</p>', 'meta' => ''];
        save_json($pagesPath, $pages);
        $success = 'Страница добавлена.';
        $tab = 'pages';
    }

    if ($action === 'delete_page') {
        $did = trim((string)($_POST['delete_slug'] ?? ''));
        $pagesPath = __DIR__ . '/pages.json';
        $pages = file_exists($pagesPath) ? json_decode(file_get_contents($pagesPath), true) : [];
        $pages = is_array($pages) ? $pages : [];
        $pages = array_values(array_filter($pages, fn($p) => ($p['slug'] ?? '') !== $did));
        save_json($pagesPath, $pages);
        $success = 'Страница удалена.';
        $tab = 'pages';
    }

    if ($action === 'create_coupon') {
        $couponsPath = __DIR__ . '/coupons.json';
        $coupons = file_exists($couponsPath) ? json_decode(file_get_contents($couponsPath), true) : [];
        if (!is_array($coupons)) $coupons = [];
        $code = strtoupper(trim((string)($_POST['coupon_code'] ?? '')));
        $type = in_array($_POST['coupon_type'] ?? '', ['percent','fixed'], true) ? $_POST['coupon_type'] : 'percent';
        $value = (float)($_POST['coupon_value'] ?? 0);
        $min = (float)($_POST['coupon_min'] ?? 0);
        $expires = trim((string)($_POST['coupon_expires'] ?? ''));
        if ($code === '' || $value <= 0) {
            $error = 'Заполните код и значение скидки.';
        } elseif (count(array_filter($coupons, fn($c) => ($c['code'] ?? '') === $code)) > 0) {
            $error = 'Промокод с таким кодом уже есть.';
        } else {
            $coupons[] = ['code' => $code, 'type' => $type, 'value' => $value, 'min_order' => $min, 'expires' => $expires, 'active' => true];
            save_json($couponsPath, $coupons);
            $success = 'Промокод добавлен.';
        }
        $tab = 'coupons';
    }

    if ($action === 'save_coupons') {
        $couponsPath = __DIR__ . '/coupons.json';
        $codes = (array)($_POST['code'] ?? []);
        $types = (array)($_POST['type'] ?? []);
        $values = (array)($_POST['value'] ?? []);
        $mins = (array)($_POST['min_order'] ?? []);
        $expires = (array)($_POST['expires'] ?? []);
        $newCoupons = [];
        foreach ($codes as $i => $code) {
            $newCoupons[] = [
                'code' => strtoupper(trim((string)$code)),
                'type' => in_array($types[$i] ?? '', ['percent','fixed'], true) ? $types[$i] : 'percent',
                'value' => (float)($values[$i] ?? 0),
                'min_order' => (float)($mins[$i] ?? 0),
                'expires' => trim((string)($expires[$i] ?? '')),
                'active' => !empty($_POST['active'][$i])
            ];
        }
        save_json($couponsPath, $newCoupons);
        $success = 'Промокоды сохранены.';
        $tab = 'coupons';
    }

    if ($action === 'delete_coupon') {
        $did = trim((string)($_POST['delete_code'] ?? ''));
        $couponsPath = __DIR__ . '/coupons.json';
        $coupons = file_exists($couponsPath) ? json_decode(file_get_contents($couponsPath), true) : [];
        $coupons = is_array($coupons) ? array_values(array_filter($coupons, fn($c) => ($c['code'] ?? '') !== $did)) : [];
        save_json($couponsPath, $coupons);
        $success = 'Промокод удален.';
        $tab = 'coupons';
    }

    if ($action === 'save_blocks') {
        $blocksPath = __DIR__ . '/blocks.json';
        $types = (array)($_POST['block_type'] ?? []);
        $titles = (array)($_POST['block_title'] ?? []);
        $subtitles = (array)($_POST['block_subtitle'] ?? []);
        $contents = (array)($_POST['block_content'] ?? []);
        $btns = (array)($_POST['block_button'] ?? []);
        $links = (array)($_POST['block_link'] ?? []);
        $newBlocks = [];
        foreach ($types as $i => $type) {
            $newBlocks[] = [
                'type' => trim((string)$type),
                'title' => trim((string)($titles[$i] ?? '')),
                'subtitle' => trim((string)($subtitles[$i] ?? '')),
                'content' => trim((string)($contents[$i] ?? '')),
                'button_text' => trim((string)($btns[$i] ?? '')),
                'button_link' => trim((string)($links[$i] ?? ''))
            ];
        }
        save_json($blocksPath, $newBlocks);
        $success = admin_t('save');
        $tab = 'blocks';
    }

    if ($action === 'delete_block') {
        $idx = (int)($_POST['block_idx'] ?? -1);
        $blocksPath = __DIR__ . '/blocks.json';
        $blocks = file_exists($blocksPath) ? json_decode(file_get_contents($blocksPath), true) : [];
        if (is_array($blocks) && isset($blocks[$idx])) {
            array_splice($blocks, $idx, 1);
            save_json($blocksPath, array_values($blocks));
            $success = admin_t('delete');
        }
        $tab = 'blocks';
    }

    if ($action === 'move_block') {
        $idx = (int)($_POST['block_idx'] ?? -1);
        $dir = ($_POST['direction'] ?? '') === 'up' ? -1 : 1;
        $blocksPath = __DIR__ . '/blocks.json';
        $blocks = file_exists($blocksPath) ? json_decode(file_get_contents($blocksPath), true) : [];
        if (is_array($blocks) && isset($blocks[$idx]) && isset($blocks[$idx + $dir]) && ($idx + $dir) >= 0) {
            $tmp = $blocks[$idx];
            $blocks[$idx] = $blocks[$idx + $dir];
            $blocks[$idx + $dir] = $tmp;
            save_json($blocksPath, array_values($blocks));
        }
        $tab = 'blocks';
    }

    if ($action === 'reorder_blocks') {
        $blocksPath = __DIR__ . '/blocks.json';
        $blocks = file_exists($blocksPath) ? json_decode(file_get_contents($blocksPath), true) : [];
        $order = array_map('intval', (array)($_POST['block_order'] ?? []));
        if (is_array($blocks) && count($order) === count($blocks)) {
            $newBlocks = [];
            foreach ($order as $idx) {
                if (isset($blocks[$idx])) $newBlocks[] = $blocks[$idx];
            }
            if (count($newBlocks) === count($blocks)) {
                save_json($blocksPath, $newBlocks);
                $success = admin_t('save');
            }
        }
        $tab = 'blocks';
    }

    if ($action === 'import_products_csv') {
        $file = $_FILES['csv_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Ошибка загрузки файла.';
        } else {
            $handle = fopen($file['tmp_name'], 'r');
            if (!$handle) { $error = 'Не удалось открыть файл.'; }
            else {
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") { rewind($handle); }
                $headers = fgetcsv($handle, 0, ';');
                $expected = ['id','article','name','category','brand','stock','price_base','price_wholesale','image','description'];
                if ($headers !== $expected) {
                    $error = 'Неверный формат CSV. Ожидаются колонки: ' . implode(', ', $expected);
                } else {
                    $updated = 0; $created = 0;
                    $maxId = 0;
                    foreach ($products as $p) $maxId = max($maxId, (int)($p['id'] ?? 0));
                    while (($row = fgetcsv($handle, 0, ';')) !== false) {
                        if (count($row) < 8) continue;
                        $id = (int)($row[0] ?? 0);
                        $article = trim($row[1] ?? '');
                        $name = trim($row[2] ?? '');
                        if ($article === '' || $name === '') continue;
                        $category = trim($row[3] ?? 'Смесители для умывальника');
                        $brand = trim($row[4] ?? 'TIM');
                        $stock = (int)($row[5] ?? 0);
                        $priceBase = (float)($row[6] ?? 0);
                        $priceWholesale = (float)($row[7] ?? 0);
                        $image = trim($row[8] ?? '');
                        $description = trim($row[9] ?? '');

                        $found = false;
                        foreach ($products as &$p) {
                            if ((int)($p['id'] ?? 0) === $id && $id > 0) {
                                $p['article'] = $article;
                                $p['name'] = $name;
                                $p['category'] = $category;
                                $p['brand'] = $brand;
                                $p['stock'] = $stock;
                                $p['price_base'] = $priceBase;
                                $p['price_wholesale'] = $priceWholesale;
                                if ($image !== '') $p['image'] = $image;
                                if ($description !== '') $p['description'] = $description;
                                $updated++;
                                $found = true;
                                break;
                            }
                        }
                        unset($p);
                        if (!$found) {
                            $maxId++;
                            $products[] = [
                                'id' => $maxId,
                                'article' => $article,
                                'name' => $name,
                                'category' => $category,
                                'brand' => $brand,
                                'stock' => $stock,
                                'price_base' => $priceBase,
                                'price_wholesale' => $priceWholesale,
                                'image' => $image,
                                'description' => $description,
                                'tags' => []
                            ];
                            $created++;
                        }
                    }
                    save_json($productsPath, $products);
                    $success = "Импорт завершен. Создано: $created, обновлено: $updated.";
                }
                fclose($handle);
            }
        }
        $tab = 'import';
    }

    if ($action === 'export_orders_csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fprintf($output, "\xEF\xBB\xBF");
        fputcsv($output, ['order_number','company_name','inn','contact_person','phone','email','city','address','zip','delivery_note','status','total','total_base','created_at'], ';');
        $pdo = tmopro_db_safe();
        if ($pdo) {
            $stmt = $pdo->query('SELECT order_number, company_name, inn, contact_person, phone, email, city, address, zip, delivery_note, status, total, total_base, created_at FROM orders ORDER BY id DESC');
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['order_number'], $row['company_name'], $row['inn'] ?? '', $row['contact_person'] ?? '', $row['phone'] ?? '', $row['email'] ?? '',
                    $row['city'] ?? '', $row['address'] ?? '', $row['zip'] ?? '', $row['delivery_note'] ?? '',
                    $row['status'], $row['total'], $row['total_base'], $row['created_at']
                ], ';');
            }
        }
        fclose($output);
        exit;
    }

    if ($action === 'bulk_update_orders_status') {
        $ids = array_map('intval', $_POST['bulk_order_ids'] ?? []);
        $newStatus = trim((string)($_POST['bulk_status'] ?? ''));
        $pdo = tmopro_db_safe();
        if ($ids && $newStatus !== '' && $pdo) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id IN ($placeholders)");
            $stmt->execute(array_merge([$newStatus], $ids));
            $success = 'Обновлено: ' . $stmt->rowCount();
        }
        $tab = 'orders';
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
<meta name="theme-color" content="<?= e($themeColor) ?>">
<link rel="icon" href="icon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; }
body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Arial, sans-serif; background: #0d0d0d; color: #f8fafc; line-height: 1.5; }
a { color: inherit; text-decoration: none; }
input, select, textarea, button { font: inherit; }

.wrap { max-width: 1180px; margin: 0 auto; padding: 24px; }
.card { background: #121212; border-radius: 20px; padding: 24px; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 20px; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #d4af37; color: #000; padding: 12px 20px; border-radius: 12px; font-weight: 700; border: 0; cursor: pointer; transition: all .2s ease; }
.btn:hover { background: #c5a059; transform: translateY(-1px); }
.btn-dark { background: #1a1a1a; color: #fff; }
.btn-dark:hover { background: #d4af37; color: #000; }
.btn-red { background: #dc2626; color: #fff; }
.btn-red:hover { background: #b91c1c; }
.btn-ghost { background: rgba(255,255,255,0.06); color: #9ca3af; }
.btn-ghost:hover { background: rgba(255,255,255,0.1); color: #fff; }

.field { width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: #1a1a1a; color: #f8fafc; font-weight: 600; }
.field:focus { border-color: #d4af37; outline: none; box-shadow: 0 0 0 2px rgba(212,175,55,0.15); }
label { display: block; margin-bottom: 16px; }
label span { display: block; font-size: 11px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }

.login-box { max-width: 400px; margin: 80px auto; text-align: center; }
.login-box .logo { width: 64px; height: 64px; line-height: 64px; border-radius: 16px; color: #000; font-weight: 900; margin: 0 auto 20px; background: #d4af37; font-size: 18px; }
.login-box h1 { font-size: 24px; margin-bottom: 8px; color: #fff; }
.login-box p { color: #9ca3af; margin-bottom: 24px; font-size: 14px; }
.login-box input { margin-bottom: 12px; }
.login-box .btn { width: 100%; }

.nav { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
.nav a { padding: 10px 16px; border-radius: 12px; background: rgba(255,255,255,0.06); color: #9ca3af; font-weight: 700; font-size: 14px; transition: all .2s ease; }
.nav a:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav a.active { background: #d4af37; color: #000; }

.grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
.stat { text-align: center; }
.stat .num { font-size: 32px; font-weight: 900; color: #d4af37; }
.stat .lbl { font-size: 12px; color: #9ca3af; font-weight: 700; text-transform: uppercase; }

.product-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; }
@media (max-width: 768px) { .product-row { grid-template-columns: 1fr 1fr; } }

.msg { padding: 14px 18px; border-radius: 12px; margin-bottom: 16px; font-weight: 700; font-size: 14px; border: 1px solid transparent; }
.msg.ok { background: rgba(212,175,55,0.08); color: #d4af37; border-color: rgba(212,175,55,0.2); }
.msg.err { background: rgba(220,38,38,0.08); color: #ef4444; border-color: rgba(220,38,38,0.2); }

.sticky-save { position: sticky; bottom: 16px; background: rgba(18,18,18,0.95); padding: 12px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(16px); text-align: center; }

.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 12px; }
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.gap-2 { gap: 8px; }
.text-sm { font-size: 14px; }
.font-bold { font-weight: 700; }
</style>
<style>
:root {
  --accent: #d4af37;
  --bg: #0d0d0d;
  --card: #121212;
  --text: #f8fafc;
  --muted: #9ca3af;
  --border: rgba(255,255,255,0.06);
  --shadow-sm: 0 10px 30px rgba(0,0,0,.3);
  --shadow-md: 0 24px 80px rgba(0,0,0,.4);
}

body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Arial, sans-serif; background: var(--bg); color: var(--text); }
.wrap { max-width: 1180px; padding: 28px 16px; }
@media (min-width: 768px) { .wrap { padding: 40px 24px; } }
.card { border-radius: 20px; box-shadow: var(--shadow-sm); border: 1px solid var(--border); }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; border-radius: 12px; font-weight: 700; transition: all .2s ease; }
.btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
.btn:active { transform: translateY(0px); }
.btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(212,175,55,.2); }
.btn-dark { background: #1a1a1a; color: #fff; }
.btn-dark:hover { background: #d4af37; color: #000; }
.btn-ghost { background: rgba(255,255,255,0.06); color: #9ca3af; box-shadow: none; }
.btn-ghost:hover { background: rgba(255,255,255,0.1); color: #fff; }
.field { border-radius: 12px; padding: 12px 14px; font-weight: 600; }
.field:focus { box-shadow: 0 0 0 2px rgba(212,175,55,.15); border-color: #d4af37; }
.msg { border-radius: 12px; font-weight: 700; border: 1px solid transparent; }
.msg.ok { border-color: rgba(212,175,55,0.2); }
.msg.err { border-color: rgba(220,38,38,0.2); }
.sticky-save { border-radius: 16px; background: rgba(18,18,18,0.95); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(16px); box-shadow: var(--shadow-md); }

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
  background: radial-gradient(closest-side, rgba(212,175,55,.06), transparent 70%);
  filter: blur(60px);
  opacity: .8;
}

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; font-weight: 700; }
tr:hover td { background: rgba(255,255,255,0.02); }
</style>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body>

<div class="bg-orb"></div>

<?php if (!$isAuthorized): ?>
<div class="login-box">
  <img src="logo.svg?v=2" alt="TMOPRO" style="height: 72px; width: auto; margin: 0 auto 20px; display: block;">
  <h1>Панель управления</h1>
  <p>Вход для администратора сайта</p>
  <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="text" name="login_name" placeholder="Логин" required class="field" value="admin" style="margin-bottom:12px;">
    <input type="password" name="login_password" placeholder="Пароль" required class="field">
    <button class="btn">Войти</button>
  </form>
</div>

<?php else: ?>
<div class="wrap">
  <div class="card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
      <div style="font-size:12px;font-weight:700;color:#9ca3af;">Без MySQL · JSON CMS</div>
      <h1 style="font-size:28px;margin-top:4px;">Админ-панель</h1>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <form method="get" style="display:flex;gap:4px;align-items:center;">
        <?php foreach ($allowedAdminLangs as $l): ?>
          <button type="submit" name="admin_lang" value="<?= e($l) ?>" style="padding:4px 10px;border-radius:8px;border:1px solid <?= $adminLang===$l?'#d4af37':'rgba(255,255,255,0.1)' ?>;background:<?= $adminLang===$l?'#d4af37':'transparent' ?>;color:<?= $adminLang===$l?'#000':'#9ca3af' ?>;font-size:12px;font-weight:900;cursor:pointer;"><?= e(strtoupper($l)) ?></button>
        <?php endforeach; ?>
      </form>
      <a href="index.php" target="_blank" class="btn btn-dark" style="text-decoration:none;">🌐 <?= admin_t('preview') ?></a>
      <a href="panel.php?logout=1" class="btn btn-ghost" style="text-decoration:none;">🚪 <?= admin_t('logout') ?></a>
    </div>
  </div>

  <?php if ($success): ?><div class="msg ok"><?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>

  <div class="nav">
    <a href="panel.php?tab=overview" class="<?= $tab==='overview'?'active':'' ?>">📊 <?= admin_t('tab_overview') ?></a>
    <a href="panel.php?tab=settings" class="<?= $tab==='settings'?'active':'' ?>">⚙️ <?= admin_t('tab_settings') ?></a>
    <a href="panel.php?tab=categories" class="<?= $tab==='categories'?'active':'' ?>">📂 <?= admin_t('tab_categories') ?></a>
    <a href="panel.php?tab=orders" class="<?= $tab==='orders'?'active':'' ?>">📦 <?= admin_t('tab_orders') ?></a>
    <a href="panel.php?tab=clients" class="<?= $tab==='clients'?'active':'' ?>">👥 <?= admin_t('tab_clients') ?></a>
    <a href="panel.php?tab=products" class="<?= $tab==='products'?'active':'' ?>">🛒 <?= admin_t('tab_products') ?></a>
    <a href="panel.php?tab=import" class="<?= $tab==='import'?'active':'' ?>">📤 <?= admin_t('tab_import') ?></a>
    <a href="panel.php?tab=pages" class="<?= $tab==='pages'?'active':'' ?>">📄 <?= admin_t('tab_pages') ?></a>
    <a href="panel.php?tab=analytics" class="<?= $tab==='analytics'?'active':'' ?>">📈 <?= admin_t('tab_analytics') ?></a>
    <a href="panel.php?tab=coupons" class="<?= $tab==='coupons'?'active':'' ?>">🏷️ <?= admin_t('tab_coupons') ?></a>
    <a href="panel.php?tab=blocks" class="<?= $tab==='blocks'?'active':'' ?>">🧩 <?= admin_t('tab_blocks') ?></a>
  </div>

  <?php if ($tab === 'overview'): ?>
    <div class="grid-3">
      <div class="card stat"><div class="lbl">Товаров</div><div class="num"><?= count($products) ?></div></div>
      <div class="card stat"><div class="lbl">Остаток</div><div class="num"><?= e($totalStock) ?></div></div>
      <div class="card stat"><div class="lbl">Сумма цен</div><div class="num"><?= e(number_format($totalRetail,0,',',' ')) ?> ₽</div></div>
    </div>
    <div class="card">
      <h2 style="font-size:20px;margin-bottom:12px;">📖 <?= admin_t('overview_howto') ?></h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
        <a href="panel.php?tab=settings" class="card" style="text-decoration:none;">
          <b>1. <?= admin_t('tab_settings') ?></b>
          <p style="margin-top:6px;color:#9ca3af;font-size:14px;"><?= admin_t('overview_step1') ?></p>
        </a>
        <a href="panel.php?tab=products" class="card" style="text-decoration:none;">
          <b>2. <?= admin_t('tab_products') ?></b>
          <p style="margin-top:6px;color:#9ca3af;font-size:14px;"><?= admin_t('overview_step2') ?></p>
        </a>
        <a href="index.php" target="_blank" class="card" style="text-decoration:none;">
          <b>3. <?= admin_t('preview') ?></b>
          <p style="margin-top:6px;color:#9ca3af;font-size:14px;"><?= admin_t('overview_step3') ?></p>
        </a>
        <a href="backup.php" target="_blank" class="card" style="text-decoration:none;">
          <b>4. <?= admin_t('backup') ?></b>
          <p style="margin-top:6px;color:#9ca3af;font-size:14px;"><?= admin_t('overview_step4') ?></p>
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'orders'): ?>
    <?php
      $statusMap = ['new'=>'Новый','processing'=>'В работе','invoiced'=>'Счет выставлен','shipped'=>'Отгружен','done'=>'Закрыт','cancelled'=>'Отмена'];
      $statusColors = ['new'=>'#3b82f6','processing'=>'#f59e0b','invoiced'=>'#8b5cf6','shipped'=>'#0ea5e9','done'=>'#059669','cancelled'=>'#ef4444'];
      $filterStatus = $_GET['order_status'] ?? '';
      $searchOrder = trim((string)($_GET['q'] ?? ''));
      $pdo = tmopro_db_safe();
      $orders = [];
      $itemsByOrder = [];
      $historyByOrder = [];
      $statusCounts = [];
      if ($pdo) {
        try {
          $statusCounts = $pdo->query("SELECT status, COUNT(*) as cnt FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
          $sql = 'SELECT id, order_number, status, company_name, inn, contact_person, phone, email, total_base, total, created_at FROM orders';
          $params = [];
          $where = [];
          if ($filterStatus && isset($statusMap[$filterStatus])) {
            $where[] = 'status = ?';
            $params[] = $filterStatus;
          }
          if ($searchOrder !== '') {
            $where[] = '(order_number LIKE ? OR company_name LIKE ? OR inn LIKE ? OR phone LIKE ?)';
            $like = '%' . $searchOrder . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
          }
          if (!empty($where)) $sql .= ' WHERE ' . implode(' AND ', $where);
          $sql .= ' ORDER BY id DESC LIMIT 50';
          $stmt = $pdo->prepare($sql);
          $stmt->execute($params);
          $orders = $stmt->fetchAll();
          if (!empty($orders)) {
            $ids = array_map(fn($o) => (int)$o['id'], $orders);
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare('SELECT order_id, article, name, brand, category, qty, unit_price, line_total FROM order_items WHERE order_id IN (' . $in . ') ORDER BY id ASC');
            $stmt->execute($ids);
            foreach ($stmt->fetchAll() as $it) {
              $oid = (int)$it['order_id'];
              if (!isset($itemsByOrder[$oid])) $itemsByOrder[$oid] = [];
              $itemsByOrder[$oid][] = $it;
            }
            // Load status history
            try {
              $histStmt = $pdo->prepare('SELECT order_id, status, note, created_at FROM order_status_history WHERE order_id IN (' . $in . ') ORDER BY created_at DESC');
              $histStmt->execute($ids);
              foreach ($histStmt->fetchAll() as $h) {
                $oid = (int)$h['order_id'];
                if (!isset($historyByOrder[$oid])) $historyByOrder[$oid] = [];
                $historyByOrder[$oid][] = $h;
              }
            } catch (Throwable $e) {}
          }
        } catch (Throwable $e) {
          $orders = [];
        }
      }
    ?>
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;">📦 <?= admin_t('tab_orders') ?></h2>
          <p style="color:#9ca3af;font-size:13px;margin-top:4px;"><?= count($orders) ?> <?= admin_t('tab_orders') ?></p>
        </div>
        <form method="post" style="display:inline;">
          <input type="hidden" name="action" value="export_orders_csv">
          <button class="btn btn-dark">📥 <?= admin_t('export') ?> CSV</button>
        </form>
      </div>

      <form method="get" style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;">
        <input type="hidden" name="tab" value="orders">
        <?php if ($filterStatus !== ''): ?><input type="hidden" name="order_status" value="<?= e($filterStatus) ?>"><?php endif; ?>
        <input name="q" value="<?= e($searchOrder) ?>" class="field" style="flex:1;max-width:320px;height:40px;font-size:13px;" placeholder="<?= admin_t('search') ?>...">
        <button type="submit" class="btn btn-dark" style="height:40px;"><?= admin_t('search_btn') ?></button>
        <?php if ($searchOrder !== ''): ?><a href="panel.php?tab=orders<?= $filterStatus !== '' ? '&order_status=' . e($filterStatus) : '' ?>" class="btn" style="height:40px;"><?= admin_t('reset') ?></a><?php endif; ?>
      </form>

      <!-- Status Filter -->
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
        <a href="panel.php?tab=orders<?= $searchOrder !== '' ? '&q=' . e($searchOrder) : '' ?>" class="chip <?= $filterStatus==='' ? 'chip-active' : 'chip-default' ?>" style="text-decoration:none;"><?= admin_t('all') ?> <?= array_sum($statusCounts) ?></a>
        <?php foreach ($statusMap as $k => $v): ?>
          <a href="panel.php?tab=orders&order_status=<?= e($k) ?>" class="chip <?= $filterStatus===$k ? 'chip-active' : 'chip-default' ?>" style="text-decoration:none;<?= $filterStatus===$k ? '' : 'border-left:3px solid '.e($statusColors[$k]).';' ?>">
            <?= e($v) ?> <?= (int)($statusCounts[$k] ?? 0) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if (!$pdo): ?>
        <div class="msg err">База не подключена.</div>
      <?php endif; ?>

      <?php if (empty($orders)): ?>
        <div style="padding:14px;border-radius:16px;background:#121212;border:1px solid rgba(255,255,255,0.06);color:#9ca3af;font-weight:800;"><?= admin_t('tab_orders') ?> — 0</div>
      <?php else: ?>
        <form method="post" id="bulkStatusForm" style="margin-bottom:12px;">
          <input type="hidden" name="action" value="bulk_update_orders_status">
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:800;color:#d4af37;">
              <input type="checkbox" id="orderSelectAll" style="width:16px;height:16px;">
              Выбрать все
            </label>
            <select name="bulk_status" class="field" style="height:40px;">
              <?php foreach ($statusMap as $k=>$v): ?>
                <option value="<?= e($k) ?>"><?= e($v) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-dark" style="height:40px;" onclick="return confirm('<?= admin_t('confirm') ?>?')"><?= admin_t('status') ?></button>
          </div>
        </form>

        <?php foreach ($orders as $o): $oid = (int)$o['id']; ?>
          <div style="background:#121212;border-radius:16px;padding:16px;margin-bottom:12px;border:1px solid rgba(255,255,255,0.06);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
              <div style="display:flex;align-items:flex-start;gap:10px;">
                <input type="checkbox" name="bulk_order_ids[]" value="<?= e($oid) ?>" form="bulkStatusForm" class="order-bulk-cb" style="margin-top:4px; width:18px; height:18px; accent-color:#d4af37; cursor:pointer;">
                <div>
                  <div style="font-size:12px;font-weight:900;color:#9ca3af;">#<?= e($o['order_number']) ?> · <?= e($o['created_at']) ?></div>
                <div style="font-size:18px;font-weight:1000;margin-top:6px;"><?= e($o['company_name'] ?: '—') ?></div>
                <div style="margin-top:6px;font-size:13px;color:#9ca3af;font-weight:800;"><?= admin_t('inn') ?>: <?= e($o['inn'] ?: '—') ?> · <?= admin_t('contact_person') ?>: <?= e($o['contact_person'] ?: '—') ?> · <?= e($o['phone'] ?: '—') ?> · <?= e($o['email'] ?: '—') ?></div>
                </div>
              </div>
              <div style="min-width:260px;flex:0 0 auto;">
                <div style="display:flex;justify-content:space-between;gap:12px;font-weight:1000;">
                  <div style="color:#9ca3af;"><?= admin_t('total') ?></div>
                  <div><?= e(number_format((float)$o['total'], 0, ',', ' ')) ?> ₽</div>
                </div>
                <div style="display:flex;justify-content:space-between;gap:12px;font-size:13px;color:#d4af37;font-weight:900;margin-top:6px;">
                  <div><?= admin_t('retail') ?></div>
                  <div><?= e(number_format((float)$o['total_base'], 0, ',', ' ')) ?> ₽</div>
                </div>
                <?php if (!empty($o['city']) || !empty($o['address'])): ?>
                <div style="margin-top:8px;padding:8px 10px;background:#1a1a1a;border:1px solid rgba(255,255,255,0.06);border-radius:10px;font-size:12px;color:#9ca3af;font-weight:800;">
                  <span style="color:#f8fafc;font-weight:900;">📍 <?= admin_t('delivery_address') ?>:</span> <?= e($o['city'] ?? '') ?><?= ($o['city'] && $o['address']) ? ', ' : '' ?><?= e($o['address'] ?? '') ?><?= ($o['zip']) ? ' (' . e($o['zip']) . ')' : '' ?>
                  <?php if (!empty($o['delivery_note'])): ?><br><span style="color:#d4af37;">📝 <?= e($o['delivery_note']) ?></span><?php endif; ?>
                </div>
                <?php endif; ?>
                <form method="post" style="margin-top:10px;">
                  <input type="hidden" name="action" value="update_order_status">
                  <input type="hidden" name="order_id" value="<?= e($oid) ?>">
                  <div style="display:flex;gap:8px;align-items:center;">
                    <select name="status" class="field" style="height:42px;">
                      <?php foreach ($statusMap as $k=>$v): ?>
                        <option value="<?= e($k) ?>" <?= ($o['status'] ?? '')===$k?'selected':'' ?>><?= e($v) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-dark" style="height:42px;">💾 <?= admin_t('save') ?></button>
                  </div>
                  <input name="status_note" class="field" style="margin-top:6px;height:36px;font-size:12px;" placeholder="<?= admin_t('delivery_note') ?>">
                </form>
              </div>
            </div>

            <?php $items = $itemsByOrder[$oid] ?? []; ?>
            <?php if (!empty($items)): ?>
              <div style="margin-top:14px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                <div style="display:grid;grid-template-columns:1.2fr 90px 120px 120px;gap:0;">
                  <div style="padding:10px 12px;color:#64748b;font-size:12px;font-weight:1000;"><?= admin_t('product') ?></div>
                  <div style="padding:10px 12px;color:#64748b;font-size:12px;font-weight:1000;text-align:center;"><?= admin_t('qty') ?></div>
                  <div style="padding:10px 12px;color:#64748b;font-size:12px;font-weight:1000;text-align:right;"><?= admin_t('price') ?></div>
                  <div style="padding:10px 12px;color:#64748b;font-size:12px;font-weight:1000;text-align:right;"><?= admin_t('total') ?></div>
                </div>
                <?php foreach ($items as $it): ?>
                  <div style="display:grid;grid-template-columns:1.2fr 90px 120px 120px;border-top:1px solid #e2e8f0;">
                    <div style="padding:12px;">
                      <div style="font-weight:1000;"><?= e($it['name'] ?? '') ?></div>
                      <div style="font-size:12px;color:#64748b;font-weight:800;margin-top:3px;"><?= e($it['article'] ?? '') ?> · <?= e($it['brand'] ?? '') ?> · <?= e($it['category'] ?? '') ?></div>
                    </div>
                    <div style="padding:12px;text-align:center;font-weight:1000;"><?= e($it['qty'] ?? 0) ?></div>
                    <div style="padding:12px;text-align:right;font-weight:1000;"><?= e(number_format((float)($it['unit_price'] ?? 0), 0, ',', ' ')) ?> ₽</div>
                    <div style="padding:12px;text-align:right;font-weight:1000;"><?= e(number_format((float)($it['line_total'] ?? 0), 0, ',', ' ')) ?> ₽</div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php $hist = $historyByOrder[$oid] ?? []; ?>
            <?php if (!empty($hist)): ?>
              <div style="margin-top:10px;font-size:12px;">
                <div style="font-weight:900;color:#64748b;margin-bottom:6px;">История статусов</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                  <?php foreach ($hist as $h): ?>
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;background:#fff;border:1px solid #e2e8f0;color:#334155;font-weight:800;">
                      <span style="width:6px;height:6px;border-radius:50%;background:<?= e($statusColors[$h['status']] ?? '#cbd5e1') ?>;"></span>
                      <?= e($statusMap[$h['status']] ?? $h['status']) ?> · <?= e($h['created_at']) ?>
                      <?php if (!empty($h['note'])): ?><span style="color:#64748b;">(<?= e($h['note']) ?>)</span><?php endif; ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <script>
      document.getElementById('orderSelectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('.order-bulk-cb').forEach(cb => cb.checked = e.target.checked);
      });
    </script>
  <?php endif; ?>

  <?php if ($tab === 'clients'): ?>
    <div class="card" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;">👥 <?= admin_t('tab_clients') ?></h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;">B2B</p>
        </div>
      </div>

      <?php
        $searchClient = trim((string)($_GET['q'] ?? ''));
        $pdo = tmopro_db_safe();
        $accounts = [];
        $usersByAccount = [];
        if ($pdo) {
          try {
            if ($searchClient !== '') {
              $like = '%' . $searchClient . '%';
              $stmt = $pdo->prepare('SELECT * FROM b2b_accounts WHERE company_name LIKE ? OR inn LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY id DESC');
              $stmt->execute([$like, $like, $like, $like]);
              $accounts = $stmt->fetchAll();
            } else {
              $accounts = $pdo->query('SELECT * FROM b2b_accounts ORDER BY id DESC')->fetchAll();
            }
            if (!empty($accounts)) {
              $aids = array_map(fn($a)=>(int)$a['id'], $accounts);
              $placeholders = implode(',', array_fill(0, count($aids), '?'));
              $stmt = $pdo->prepare("SELECT * FROM b2b_users WHERE account_id IN ($placeholders) ORDER BY id DESC");
              $stmt->execute($aids);
              foreach ($stmt->fetchAll() as $u) {
                $aid = (int)$u['account_id'];
                if (!isset($usersByAccount[$aid])) $usersByAccount[$aid] = [];
                $usersByAccount[$aid][] = $u;
              }
            }
          } catch (Throwable $e) {
            echo '<div class="msg err">Ошибка загрузки клиентов: ' . e($e->getMessage()) . '</div>';
          }
        }
      ?>

      <form method="get" style="margin-bottom:16px;display:flex;gap:8px;">
        <input type="hidden" name="tab" value="clients">
        <input name="q" value="<?= e($searchClient) ?>" class="field" style="flex:1;max-width:400px;height:40px;font-size:13px;" placeholder="Поиск по компании, ИНН, email, телефону...">
        <button type="submit" class="btn btn-dark" style="height:40px;">Найти</button>
        <?php if ($searchClient !== ''): ?><a href="panel.php?tab=clients" class="btn" style="height:40px;">Сброс</a><?php endif; ?>
      </form>

      <!-- Create Account Form -->
      <form method="post" class="card" style="margin-bottom:16px;background:#f8fafc;">
        <input type="hidden" name="action" value="create_b2b_account">
        <h3 style="font-size:16px;margin-bottom:12px;">Создать компанию</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
          <label><span>Название компании *</span><input name="company_name" class="field" required></label>
          <label><span>ИНН</span><input name="inn" class="field"></label>
          <label><span>Email</span><input type="email" name="email" class="field"></label>
          <label><span>Телефон</span><input name="phone" class="field"></label>
          <label><span>Тариф</span>
            <select name="price_tier" class="field">
              <option value="default">default</option>
              <option value="bronze">bronze</option>
              <option value="silver">silver</option>
              <option value="gold">gold</option>
              <option value="platinum">platinum</option>
            </select>
          </label>
        </div>
        <button class="btn" style="margin-top:12px;">Создать компанию</button>
      </form>

      <?php if (empty($accounts)): ?>
        <p style="color:#64748b;font-size:14px;">Пока нет B2B компаний. Создайте первую выше.</p>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($accounts as $acc): ?>
            <?php $aid = (int)$acc['id']; $users = $usersByAccount[$aid] ?? []; ?>
            <div class="card" style="padding:16px;">
              <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                <div>
                  <div style="font-size:16px;font-weight:1000;"><?= e($acc['company_name']) ?> <span style="color:#64748b;font-size:12px;">#<?= $aid ?></span></div>
                  <div style="font-size:12px;color:#64748b;margin-top:4px;font-weight:700;">
                    <?= e($acc['inn'] ?? '—') ?> · <?= e($acc['email'] ?? '—') ?> · <?= e($acc['phone'] ?? '—') ?>
                  </div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                  <form method="post" style="display:flex;gap:8px;align-items:center;">
                    <input type="hidden" name="action" value="update_account_tier">
                    <input type="hidden" name="account_id" value="<?= $aid ?>">
                    <select name="price_tier" class="field" style="height:36px;font-size:12px;min-width:100px;">
                      <?php foreach (['default','bronze','silver','gold','platinum'] as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($acc['price_tier'] ?? '')===$t?'selected':'' ?>><?= e($t) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-dark" style="height:36px;font-size:12px;">Обновить</button>
                  </form>
                </div>
              </div>

              <?php if (!empty($users)): ?>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid #e2e8f0;">
                  <div style="font-size:12px;font-weight:1000;color:#64748b;margin-bottom:8px;">Пользователи (<?= count($users) ?>)</div>
                  <div style="display:flex;flex-direction:column;gap:6px;">
                    <?php foreach ($users as $u): ?>
                      <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#f8fafc;border-radius:10px;">
                        <div>
                          <span style="font-weight:800;font-size:13px;"><?= e($u['name']) ?></span>
                          <span style="color:#64748b;font-size:12px;margin-left:8px;"><?= e($u['email']) ?></span>
                        </div>
                        <span style="font-size:11px;font-weight:900;padding:2px 8px;border-radius:999px;background:#e2e8f0;color:#475569;"><?= e($u['role']) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Add User Form -->
              <form method="post" style="margin-top:12px;padding-top:12px;border-top:1px solid #e2e8f0;">
                <input type="hidden" name="action" value="create_b2b_user">
                <input type="hidden" name="account_id" value="<?= $aid ?>">
                <div style="font-size:12px;font-weight:1000;color:#64748b;margin-bottom:8px;">Добавить пользователя</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;">
                  <input name="user_name" class="field" placeholder="Имя" required style="height:36px;font-size:13px;">
                  <input type="email" name="user_email" class="field" placeholder="Email" required style="height:36px;font-size:13px;">
                  <input type="password" name="user_password" class="field" placeholder="Пароль (мин. 6)" required minlength="6" style="height:36px;font-size:13px;">
                  <select name="user_role" class="field" style="height:36px;font-size:13px;">
                    <option value="buyer">buyer</option>
                    <option value="manager">manager</option>
                    <option value="admin">admin</option>
                  </select>
                </div>
                <button class="btn btn-dark" style="margin-top:8px;height:32px;font-size:12px;">Добавить пользователя</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'categories'): ?>
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;">📂 <?= admin_t('tab_categories') ?></h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;"><?= admin_t('save_categories') ?></p>
        </div>
      </div>

      <form method="post" class="card" style="margin-bottom:16px;">
        <input type="hidden" name="action" value="add_category">
        <h3 style="font-size:16px;margin-bottom:12px;">➕ Добавить родительскую категорию</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
          <label><span>Название</span><input name="cat_name" class="field" required></label>
          <label><span>Slug</span><input name="cat_slug" class="field" required placeholder="bytovaya-santehnika"></label>
        </div>
        <button class="btn" style="margin-top:12px;">Добавить категорию</button>
      </form>

      <form method="post" class="card" enctype="multipart/form-data" style="margin-bottom:16px;">
        <input type="hidden" name="action" value="add_subcategory">
        <h3 style="font-size:16px;margin-bottom:12px;">➕ Добавить подкатегорию (обязательно с фото)</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
          <label><span>Родительская категория</span>
            <select name="parent_category_id" class="field" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat['id'] ?? 0) ?>"><?= e($cat['name'] ?? '') ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label><span>Название</span><input name="sub_name" class="field" required></label>
          <label><span>Slug</span><input name="sub_slug" class="field" required placeholder="latunnye-fitingi"></label>
          <label><span>Фото</span><input type="file" name="sub_image" class="field" accept="image/*" required style="padding:8px;"></label>
        </div>
        <button class="btn" style="margin-top:12px;">Добавить подкатегорию</button>
      </form>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_categories">
        <?php foreach ($categories as $cat): ?>
          <div style="background:#f8fafc;border-radius:16px;padding:16px;margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
              <label style="margin:0;flex:1;"><span>Название категории</span><input name="cat_name_<?= e($cat['id'] ?? 0) ?>" value="<?= e($cat['name'] ?? '') ?>" class="field"></label>
              <button type="button" class="btn" style="margin-left:12px;background:#fee2e2;color:#991b1b;border-color:#fecaca;" onclick="delCat(<?= (int)($cat['id'] ?? 0) ?>)">🗑 Удалить</button>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
              <?php foreach (($cat['subcategories'] ?? []) as $sub): ?>
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:12px;">
                  <div style="display:flex;gap:10px;align-items:center;">
                    <div style="width:56px;height:44px;border-radius:12px;overflow:hidden;border:1px solid rgba(15,23,42,0.08);background:#f1f5f9;flex:0 0 auto;">
                      <?php if (!empty($sub['image'])): ?><img src="<?= e($sub['image']) ?>" style="width:100%;height:100%;object-fit:cover;" /><?php endif; ?>
                    </div>
                    <div style="min-width:0;flex:1;">
                      <input name="sub_name_<?= e($sub['id'] ?? 0) ?>" value="<?= e($sub['name'] ?? '') ?>" class="field" style="margin-bottom:4px;font-weight:900;padding:6px 8px;font-size:13px;">
                      <input name="sub_slug_<?= e($sub['id'] ?? 0) ?>" value="<?= e($sub['slug'] ?? '') ?>" class="field" style="padding:6px 8px;font-size:12px;color:#64748b;" placeholder="slug">
                    </div>
                  </div>
                  <div style="margin-top:10px;">
                    <label style="margin:0;"><span>Фото (обязательно)</span>
                      <input type="file" name="subcat_image_<?= e($sub['id'] ?? 0) ?>" accept="image/*" class="field" style="padding:8px;">
                    </label>
                    <div style="margin-top:8px;font-size:12px;color:#64748b;font-weight:700;">Текущее: <?= e($sub['image'] ?? '') ?></div>
                    <button type="button" class="btn" style="margin-top:8px;background:#fee2e2;color:#991b1b;border-color:#fecaca;width:100%;" onclick="delSub(<?= (int)($cat['id'] ?? 0) ?>,<?= (int)($sub['id'] ?? 0) ?>)">🗑 Удалить подкатегорию</button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="sticky-save"><button class="btn" style="width:100%;">💾 <?= admin_t('save_categories') ?></button></div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'settings'): ?>
    <form method="post" class="card" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save_settings">
      <h2 style="font-size:20px;margin-bottom:16px;">⚙️ <?= admin_t('tab_settings') ?></h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
        <label><span>Название сайта</span><input name="site_name" value="<?= e($settings['site_name']) ?>" class="field"></label>
        <label><span>Короткое имя</span><input name="site_short_name" value="<?= e($settings['site_short_name']) ?>" class="field"></label>
        <label><span>Телефон 1</span><input name="phone" value="<?= e($settings['phone']) ?>" class="field"></label>
        <label><span>Телефон 2</span><input name="phone2" value="<?= e($settings['phone2']) ?>" class="field"></label>
        <label><span>Телефон 3</span><input name="phone3" value="<?= e($settings['phone3']) ?>" class="field"></label>
        <label><span>WhatsApp</span><input name="whatsapp" value="<?= e($settings['whatsapp']) ?>" class="field" placeholder="+7... (без + и пробелов)"></label>
        <label><span>Email менеджера</span><input name="email_manager" type="email" value="<?= e($settings['email_manager']) ?>" class="field"></label>
        <label><span>Адрес компании</span><textarea name="address" rows="2" class="field"><?= e($settings['address']) ?></textarea></label>
        <label><span>Шрифт сайта</span>
          <select name="font_family" class="field">
            <option value="Inter" <?= $settings['font_family']==='Inter'?'selected':'' ?>>Inter</option>
            <option value="Roboto" <?= $settings['font_family']==='Roboto'?'selected':'' ?>>Roboto</option>
            <option value="Open Sans" <?= $settings['font_family']==='Open Sans'?'selected':'' ?>>Open Sans</option>
            <option value="Manrope" <?= $settings['font_family']==='Manrope'?'selected':'' ?>>Manrope</option>
            <option value="Noto Sans" <?= $settings['font_family']==='Noto Sans'?'selected':'' ?>>Noto Sans</option>
          </select>
        </label>
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
        <label><span>Favicon (ссылка)</span><input name="favicon" value="<?= e($settings['favicon']) ?>" placeholder="favicon.ico или .png" class="field"></label>
        <label style="grid-column:1/-1;"><span>Или загрузить favicon</span><input type="file" name="favicon_file" accept="image/*" class="field" style="padding:8px;"></label>
        <?php if ($settings['favicon']): ?><div style="grid-column:1/-1;"><img src="<?= e($settings['favicon']) ?>" style="max-height:32px;border-radius:4px;"></div><?php endif; ?>
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
      <button class="btn" style="margin-top:16px;">💾 <?= admin_t('save') ?></button>
      <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:12px;font-size:13px;color:#64748b;">
        <b>Проверка записи:</b> settings.json <?= is_writable($settingsPath)?'✅ доступен':'❌ нет прав' ?> | products.json <?= is_writable($productsPath)?'✅ доступен':'❌ нет прав' ?>
      </div>
    </form>
  <?php endif; ?>

  <?php if ($tab === 'products'): ?>
    <?php
      $searchQ = trim((string)($_GET['q'] ?? ''));
      $filteredProducts = $products;
      if ($searchQ !== '') {
        $lq = mb_strtolower($searchQ);
        $filteredProducts = array_filter($products, fn($p) =>
          mb_strpos(mb_strtolower($p['name'] ?? ''), $lq) !== false ||
          mb_strpos(mb_strtolower($p['article'] ?? ''), $lq) !== false ||
          mb_strpos(mb_strtolower($p['brand'] ?? ''), $lq) !== false
        );
      }
      $lowStock = array_filter($filteredProducts, fn($p) => (int)($p['stock'] ?? 0) > 0 && (int)($p['stock'] ?? 0) < 10);
      $outStock = array_filter($filteredProducts, fn($p) => (int)($p['stock'] ?? 0) <= 0);
    ?>
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
        <div>
          <h2 style="font-size:20px;">🛒 <?= admin_t('tab_products') ?></h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;"><?= admin_t('save_catalog') ?></p>
        </div>
        <form method="post"><input type="hidden" name="action" value="add_product"><button class="btn btn-dark">+ <?= admin_t('add_product') ?></button></form>
      </div>

      <form method="get" style="margin-bottom:16px;display:flex;gap:8px;">
        <input type="hidden" name="tab" value="products">
        <input name="q" value="<?= e($searchQ) ?>" class="field" style="flex:1;max-width:400px;height:40px;font-size:13px;" placeholder="<?= admin_t('search') ?>...">
        <button type="submit" class="btn btn-dark" style="height:40px;"><?= admin_t('search_btn') ?></button>
        <?php if ($searchQ !== ''): ?><a href="panel.php?tab=products" class="btn" style="height:40px;"><?= admin_t('reset') ?></a><?php endif; ?>
      </form>

      <?php if (!empty($lowStock) || !empty($outStock)): ?>
        <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
          <?php if (!empty($lowStock)): ?>
            <div style="flex:1;min-width:220px;background:#fffbeb;border:1px solid #fcd34d;border-radius:16px;padding:14px 18px;">
              <div style="font-size:13px;font-weight:900;color:#b45309;">⚠️ Заканчивается: <?= count($lowStock) ?> товаров</div>
              <div style="font-size:12px;color:#92400e;font-weight:700;margin-top:4px;">Остаток менее 10 шт</div>
            </div>
          <?php endif; ?>
          <?php if (!empty($outStock)): ?>
            <div style="flex:1;min-width:220px;background:#fef2f2;border:1px solid #fecaca;border-radius:16px;padding:14px 18px;">
              <div style="font-size:13px;font-weight:900;color:#b91c1c;">❌ Нет в наличии: <?= count($outStock) ?> товаров</div>
              <div style="font-size:12px;color:#991b1b;font-weight:700;margin-top:4px;">Остаток 0 шт</div>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form method="post" id="prodForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_products">
        <div id="prodBulkBar" style="display:none;align-items:center;gap:12px;margin-bottom:12px;padding:12px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:800;color:#1e40af;">
            <input type="checkbox" id="prodSelectAll" style="width:16px;height:16px;">
            Выбрать все
          </label>
          <button type="submit" form="bulkDelProd" class="btn btn-red" style="font-size:12px;padding:6px 12px;" onclick="return confirm('<?= admin_t('delete') ?> ' + document.querySelectorAll('input[name=\"bulk_ids[]\"]:checked').length + '?')">🗑️ <?= admin_t('delete') ?> выбранные</button>
        </div>
        <?php foreach ($filteredProducts as $i => $product): ?>
          <div style="background:#f8fafc;border-radius:16px;padding:16px;margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="bulk_ids[]" value="<?= e($product['id'] ?? 0) ?>" class="prod-bulk-cb" style="width:16px;height:16px;" onchange="document.getElementById('prodBulkBar').style.display = document.querySelectorAll('.prod-bulk-cb:checked').length ? 'flex' : 'none';">
                <span style="font-size:12px;font-weight:700;color:#64748b;">ID <?= e($product['id'] ?? $i+1) ?></span>
              </label>
              <div style="display:flex;gap:6px;">
                <button type="submit" form="dup<?= e($product['id'] ?? 0) ?>" class="btn" style="font-size:12px;padding:6px 12px;">📋 <?= admin_t('add') ?></button>
                <button type="submit" form="del<?= e($product['id'] ?? 0) ?>" class="btn btn-red" style="font-size:12px;padding:6px 12px;" onclick="return confirm('<?= admin_t('delete') ?>?')">🗑️ <?= admin_t('delete') ?></button>
              </div>
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
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;margin-top:12px;">
              <label style="grid-column:1/-1;"><span>Описание (AI / ручное)</span>
                <textarea name="description[]" rows="4" class="field"><?= e($product['description'] ?? '') ?></textarea>
              </label>
              <label style="grid-column:1/-1;"><span>Теги (через запятую)</span>
                <input name="tags[]" value="<?= e(is_array($product['tags'] ?? null) ? implode(', ', $product['tags']) : '') ?>" class="field" placeholder="латунь, резьба, 1/2, водоснабжение">
              </label>
              <div style="grid-column:1/-1;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <button type="button" class="btn btn-dark" onclick="tmoproGen(<?= e($product['id'] ?? 0) ?>, this)">Сгенерировать описание (DeepSeek)</button>
                <span class="tmopro-gen-status" style="font-size:12px;color:#64748b;font-weight:800;"></span>
              </div>
            </div>
            <label style="margin-top:12px;"><span>Фото товара</span>
              <?php if (!empty($product['image'])): ?><div style="margin-bottom:8px;"><img src="<?= e($product['image']) ?>" style="max-height:80px;border-radius:8px;"></div><?php endif; ?>
              <input type="file" name="product_image_<?= e($product['id'] ?? 0) ?>" accept="image/*" class="field" style="padding:8px;">
            </label>
          </div>
        <?php endforeach; ?>
        <div class="sticky-save"><button class="btn" style="width:100%;">Сохранить каталог</button></div>
      </form>

      <script>
        async function tmoproGen(id, btn) {
          try {
            const card = btn.closest('div[style*="background:#f8fafc"]');
            const status = card ? card.querySelector('.tmopro-gen-status') : null;
            if (status) status.textContent = 'Генерация…';
            btn.disabled = true;

            const nameEl = card ? card.querySelector('input[name="name[]"]') : null;
            const articleEl = card ? card.querySelector('input[name="article[]"]') : null;
            const brandEl = card ? card.querySelector('input[name="brand[]"]') : null;
            const categoryEl = card ? card.querySelector('select[name="category[]"]') : null;

            const name = nameEl ? nameEl.value : '';
            const article = articleEl ? articleEl.value : '';
            const brand = brandEl ? brandEl.value : '';
            const category = categoryEl ? categoryEl.value : '';

            const fd = new FormData();
            fd.append('id', String(id));
            fd.append('name', name);
            fd.append('article', article);
            fd.append('brand', brand);
            fd.append('category', category);

            const res = await fetch('api/deepseek_generate.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Ошибка генерации');

            const desc = card.querySelector('textarea[name="description[]"]');
            const tags = card.querySelector('input[name="tags[]"]');
            if (desc) desc.value = data.description || '';
            if (tags) tags.value = Array.isArray(data.tags) ? data.tags.join(', ') : '';
            if (status) status.textContent = 'Готово. Нажми «Сохранить каталог».';
          } catch (e) {
            const card = btn.closest('div[style*="background:#f8fafc"]');
            const status = card ? card.querySelector('.tmopro-gen-status') : null;
            if (status) status.textContent = 'Ошибка: ' + (e && e.message ? e.message : e);
          } finally {
            btn.disabled = false;
          }
        }
      </script>

      <form method="post" id="bulkDelProd">
        <input type="hidden" name="action" value="bulk_delete_products">
      </form>
      <script>
        document.getElementById('prodSelectAll')?.addEventListener('change', function(e) {
          document.querySelectorAll('.prod-bulk-cb').forEach(cb => cb.checked = e.target.checked);
          document.getElementById('prodBulkBar').style.display = e.target.checked ? 'flex' : 'none';
        });
      </script>
      <?php foreach ($products as $product): ?>
        <form method="post" id="del<?= e($product['id'] ?? 0) ?>">
          <input type="hidden" name="action" value="delete_product">
          <input type="hidden" name="delete_id" value="<?= e($product['id'] ?? 0) ?>">
        </form>
        <form method="post" id="dup<?= e($product['id'] ?? 0) ?>">
          <input type="hidden" name="action" value="duplicate_product">
          <input type="hidden" name="duplicate_id" value="<?= e($product['id'] ?? 0) ?>">
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'import'): ?>
    <div class="card" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;">Импорт / Экспорт товаров</h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;">Работа с CSV: скачать список товаров или загрузить обновления.</p>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;">
        <!-- Export -->
        <div class="card" style="background:#f8fafc;">
          <h3 style="font-size:16px;margin-bottom:12px;">Экспорт CSV</h3>
          <p style="color:#64748b;font-size:13px;margin-bottom:16px;">Скачайте все товары в CSV для редактирования в Excel.</p>
          <form method="post">
            <input type="hidden" name="action" value="export_products_csv">
            <button class="btn btn-dark">Скачать CSV (<?= count($products) ?> товаров)</button>
          </form>
          <div style="margin-top:12px;font-size:12px;color:#64748b;font-weight:700;">
            Колонки: id, article, name, category, brand, stock, price_base, price_wholesale, image, description
          </div>
        </div>

        <!-- Import -->
        <div class="card" style="background:#f8fafc;">
          <h3 style="font-size:16px;margin-bottom:12px;">📥 <?= admin_t('import_csv') ?></h3>
          <p style="color:#64748b;font-size:13px;margin-bottom:16px;">CSV <?= admin_t('import_csv') ?>. ID = update, empty = create.</p>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import_products_csv">
            <label style="display:block;margin-bottom:12px;">
              <span style="font-size:12px;font-weight:800;color:#64748b;">CSV</span>
              <input type="file" name="csv_file" accept=".csv,text/csv" class="field" required style="padding:8px;">
            </label>
            <button class="btn">📥 <?= admin_t('import_csv') ?></button>
          </form>
          <div style="margin-top:12px;font-size:12px;color:#64748b;font-weight:700;">
            Формат: id;article;name;category;brand;stock;price_base;price_wholesale;image;description
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'pages'): ?>
    <div class="card" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;">📄 <?= admin_t('tab_pages') ?></h2>
          <p style="color:#64748b;font-size:13px;margin-top:4px;">CMS pages. Slug = URL.</p>
        </div>
        <form method="post" style="display:inline;">
          <input type="hidden" name="action" value="add_page">
          <button class="btn btn-dark">➕ <?= admin_t('add_page') ?></button>
        </form>
      </div>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_pages">
        <?php foreach ($pages as $idx => $p): ?>
          <div class="card" style="padding:16px;margin-bottom:12px;background:#f8fafc;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:12px;">
              <label><span>Slug (URL) *</span><input name="page_slug[]" value="<?= e($p['slug'] ?? '') ?>" class="field" required></label>
              <label><span>Заголовок</span><input name="page_title[]" value="<?= e($p['title'] ?? '') ?>" class="field"></label>
              <label><span>Meta description</span><input name="page_meta[]" value="<?= e($p['meta'] ?? '') ?>" class="field"></label>
              <div style="display:flex;align-items:flex-end;gap:8px;">
                <a href="page.php?slug=<?= e($p['slug'] ?? '') ?>" target="_blank" class="btn btn-dark" style="height:42px;font-size:12px;text-decoration:none;">Открыть</a>
                <button type="button" class="btn btn-ghost" style="height:42px;font-size:12px;" onclick="if(confirm('Удалить страницу?')){document.getElementById('delpage<?= $idx ?>').submit();}">Удалить</button>
              </div>
            </div>
            <label><span>Содержимое (HTML)</span><textarea name="page_content[]" rows="4" class="field"><?= e($p['content'] ?? '') ?></textarea></label>
          </div>
        <?php endforeach; ?>
        <?php if (empty($pages)): ?>
          <p style="color:#64748b;font-size:14px;">Пока нет страниц. Добавьте первую выше.</p>
        <?php endif; ?>
        <button class="btn" style="margin-top:8px;">💾 <?= admin_t('save_pages') ?></button>
      </form>

      <?php foreach ($pages as $idx => $p): ?>
        <form method="post" id="delpage<?= $idx ?>" style="display:none;">
          <input type="hidden" name="action" value="delete_page">
          <input type="hidden" name="delete_slug" value="<?= e($p['slug'] ?? '') ?>">
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'analytics'): ?>
    <?php
      $pdo = tmopro_db_safe();
      $metrics = [
        'total_orders' => 0, 'last30_orders' => 0, 'avg_check' => 0,
        'total_revenue' => 0, 'status_counts' => [], 'top_products' => [],
        'top_clients' => [], 'monthly_sales' => []
      ];
      if ($pdo) {
        $metrics['total_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $metrics['last30_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
        $metrics['avg_check'] = (float)$pdo->query("SELECT AVG(total) FROM orders")->fetchColumn();
        $metrics['total_revenue'] = (float)$pdo->query("SELECT SUM(total) FROM orders")->fetchColumn();
        $metrics['status_counts'] = $pdo->query("SELECT status, COUNT(*) as cnt FROM orders GROUP BY status ORDER BY cnt DESC")->fetchAll();
        $metrics['top_products'] = $pdo->query("SELECT name, article, SUM(qty) as total_qty, SUM(line_total) as total_sum FROM order_items GROUP BY name, article ORDER BY total_sum DESC LIMIT 8")->fetchAll();
        $metrics['top_clients'] = $pdo->query("SELECT company_name, COUNT(*) as order_count, SUM(total) as total_spent FROM orders WHERE company_name IS NOT NULL AND company_name != '' GROUP BY company_name ORDER BY total_spent DESC LIMIT 8")->fetchAll();
        $metrics['monthly_sales'] = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as cnt, SUM(total) as revenue FROM orders GROUP BY month ORDER BY month DESC LIMIT 12")->fetchAll();
      }
    ?>
    <div class="card" style="margin-bottom:16px;">
      <h2 style="font-size:20px;margin-bottom:16px;">📈 <?= admin_t('tab_analytics') ?></h2>
      <?php if (!$pdo): ?>
        <div class="msg err">DB error</div>
      <?php else: ?>
        <!-- KPI Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:24px;">
          <div class="card stat" style="padding:16px;">
            <div class="lbl"><?= admin_t('tab_orders') ?></div>
            <div class="num"><?= number_format($metrics['total_orders'], 0, ',', ' ') ?></div>
          </div>
          <div class="card stat" style="padding:16px;">
            <div class="lbl">30d</div>
            <div class="num"><?= number_format($metrics['last30_orders'], 0, ',', ' ') ?></div>
          </div>
          <div class="card stat" style="padding:16px;">
            <div class="lbl"><?= admin_t('analytics_avg_check') ?></div>
            <div class="num"><?= number_format($metrics['avg_check'], 0, ',', ' ') ?> ₽</div>
          </div>
          <div class="card stat" style="padding:16px;">
            <div class="lbl"><?= admin_t('analytics_revenue') ?></div>
            <div class="num"><?= number_format($metrics['total_revenue'], 0, ',', ' ') ?> ₽</div>
          </div>
        </div>

        <!-- Monthly Sales Chart -->
        <?php if (!empty($metrics['monthly_sales'])): ?>
          <div class="card" style="margin-bottom:16px;padding:16px;background:#f8fafc;">
            <div style="font-size:14px;font-weight:900;margin-bottom:12px;"><?= admin_t('analytics_monthly_sales') ?></div>
            <?php
              $maxRevenue = max(array_map(fn($r)=>(float)$r['revenue'], $metrics['monthly_sales'])) ?: 1;
              foreach (array_reverse($metrics['monthly_sales']) as $m):
                $pct = min(100, round(((float)$m['revenue'] / $maxRevenue) * 100));
            ?>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div style="width:70px;font-size:12px;font-weight:800;color:#64748b;flex-shrink:0;"><?= e($m['month']) ?></div>
                <div style="flex:1;height:28px;background:#e2e8f0;border-radius:8px;overflow:hidden;">
                  <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,#059669,#34d399);border-radius:8px;display:flex;align-items:center;padding-left:10px;">
                    <span style="font-size:11px;font-weight:900;color:#fff;white-space:nowrap;"><?= number_format((float)$m['revenue'],0,',',' ') ?> ₽ (<?= (int)$m['cnt'] ?>)</span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Two columns -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;">
          <!-- Status Distribution -->
          <div class="card" style="padding:16px;background:#f8fafc;">
            <div style="font-size:14px;font-weight:900;margin-bottom:12px;">Заказы по статусам</div>
            <?php if (empty($metrics['status_counts'])): ?>
              <p style="color:#64748b;font-size:13px;">Нет данных.</p>
            <?php else:
              $totalStatus = array_sum(array_map(fn($s)=>(int)$s['cnt'], $metrics['status_counts']));
              $statusMap = ['new'=>'Новый','processing'=>'В работе','invoiced'=>'Счет выставлен','shipped'=>'Отгружен','done'=>'Закрыт','cancelled'=>'Отмена'];
              foreach ($metrics['status_counts'] as $s):
                $pct = round(((int)$s['cnt'] / max(1,$totalStatus)) * 100);
                $label = $statusMap[$s['status']] ?? $s['status'];
            ?>
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:13px;font-weight:800;"><?= e($label) ?></span>
                <span style="font-size:13px;font-weight:900;color:#64748b;"><?= (int)$s['cnt'] ?> (<?= $pct ?>%)</span>
              </div>
              <div style="height:6px;background:#e2e8f0;border-radius:999px;margin-bottom:10px;overflow:hidden;">
                <div style="height:100%;width:<?= $pct ?>%;background:#4f46e5;border-radius:999px;"></div>
              </div>
            <?php endforeach; endif; ?>
          </div>

          <!-- Top Products -->
          <div class="card" style="padding:16px;background:#f8fafc;">
            <div style="font-size:14px;font-weight:900;margin-bottom:12px;"><?= admin_t('analytics_top_products') ?></div>
            <?php if (empty($metrics['top_products'])): ?>
              <p style="color:#64748b;font-size:13px;">Нет данных.</p>
            <?php else: ?>
              <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach ($metrics['top_products'] as $tp): ?>
                  <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#fff;border-radius:10px;">
                    <div style="min-width:0;">
                      <div style="font-size:13px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($tp['name']) ?></div>
                      <div style="font-size:11px;font-weight:700;color:#64748b;"><?= e($tp['article']) ?> · <?= (int)$tp['total_qty'] ?> шт</div>
                    </div>
                    <div style="font-size:13px;font-weight:900;color:#0f172a;flex-shrink:0;margin-left:8px;"><?= number_format((float)$tp['total_sum'],0,',',' ') ?> ₽</div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Top Clients -->
          <div class="card" style="padding:16px;background:#f8fafc;">
            <div style="font-size:14px;font-weight:900;margin-bottom:12px;"><?= admin_t('analytics_top_clients') ?></div>
            <?php if (empty($metrics['top_clients'])): ?>
              <p style="color:#64748b;font-size:13px;">Нет данных.</p>
            <?php else: ?>
              <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach ($metrics['top_clients'] as $tc): ?>
                  <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#fff;border-radius:10px;">
                    <div style="min-width:0;">
                      <div style="font-size:13px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($tc['company_name']) ?></div>
                      <div style="font-size:11px;font-weight:700;color:#64748b;"><?= (int)$tc['order_count'] ?> заказов</div>
                    </div>
                    <div style="font-size:13px;font-weight:900;color:#0f172a;flex-shrink:0;margin-left:8px;"><?= number_format((float)$tc['total_spent'],0,',',' ') ?> ₽</div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'coupons'): ?>
    <?php
      $couponsPath = __DIR__ . '/coupons.json';
      $coupons = file_exists($couponsPath) ? json_decode(file_get_contents($couponsPath), true) : [];
      if (!is_array($coupons)) $coupons = [];
    ?>
    <div class="card">
      <h2 style="font-size:20px;margin-bottom:16px;">🏷️ <?= admin_t('tab_coupons') ?></h2>
      <p style="color:#64748b;font-size:13px;margin-bottom:16px;">Percent / Fixed</p>

      <form method="post" style="margin-bottom:24px;">
        <input type="hidden" name="action" value="create_coupon">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:flex-end;">
          <label><span>Код *</span><input name="coupon_code" class="field" placeholder="SUMMER2024" required></label>
          <label><span>Тип</span>
            <select name="coupon_type" class="field">
              <option value="percent">Процент (%)</option>
              <option value="fixed">Фикс. сумма (₽)</option>
            </select>
          </label>
          <label><span>Значение *</span><input name="coupon_value" type="number" step="0.01" class="field" placeholder="10" required></label>
          <label><span>Мин. сумма заказа</span><input name="coupon_min" type="number" step="0.01" class="field" placeholder="0"></label>
          <label><span>Действует до</span><input name="coupon_expires" type="date" class="field"></label>
          <button class="btn btn-dark" style="height:46px;">+ Добавить</button>
        </div>
      </form>

      <?php if (empty($coupons)): ?>
        <div style="padding:14px;border-radius:16px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;font-weight:800;">Пока нет промокодов.</div>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="action" value="save_coupons">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($coupons as $i => $c): ?>
              <div style="display:grid;grid-template-columns:1.5fr 100px 100px 120px 140px 80px 40px;gap:10px;align-items:center;background:#f8fafc;border-radius:12px;padding:12px;">
                <input name="code[]" value="<?= e($c['code'] ?? '') ?>" class="field" style="font-weight:900;">
                <select name="type[]" class="field">
                  <option value="percent" <?= ($c['type']??'')==='percent'?'selected':'' ?>>%</option>
                  <option value="fixed" <?= ($c['type']??'')==='fixed'?'selected':'' ?>>₽</option>
                </select>
                <input name="value[]" type="number" step="0.01" value="<?= e($c['value'] ?? 0) ?>" class="field">
                <input name="min_order[]" type="number" step="0.01" value="<?= e($c['min_order'] ?? 0) ?>" class="field" placeholder="мин.">
                <input name="expires[]" type="date" value="<?= e($c['expires'] ?? '') ?>" class="field">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:800;cursor:pointer;">
                  <input type="checkbox" name="active[<?= $i ?>]" <?= !empty($c['active']) ? 'checked' : '' ?>> Активен
                </label>
                <button type="submit" form="delCoupon<?= $i ?>" class="btn btn-red" style="padding:6px 10px;font-size:12px;" onclick="return confirm('Удалить?')">×</button>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="btn" style="margin-top:12px;">💾 <?= admin_t('save_coupons') ?></button>
        </form>
        <?php foreach ($coupons as $i => $c): ?>
          <form method="post" id="delCoupon<?= $i ?>" style="display:none;">
            <input type="hidden" name="action" value="delete_coupon">
            <input type="hidden" name="delete_code" value="<?= e($c['code'] ?? '') ?>">
          </form>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($tab === 'blocks'): ?>
    <?php
      $blocksPath = __DIR__ . '/blocks.json';
      $blocks = file_exists($blocksPath) ? json_decode(file_get_contents($blocksPath), true) : [];
      if (!is_array($blocks)) $blocks = [];
      $blockTypes = [
        'hero' => admin_t('block_hero'),
        'text' => admin_t('block_text'),
        'features' => admin_t('block_features'),
        'products' => admin_t('block_products'),
        'categories' => admin_t('block_categories'),
        'cta' => admin_t('block_cta')
      ];
    ?>
    <div class="card">
      <h2 style="font-size:20px;margin-bottom:8px;">🧩 <?= admin_t('blocks_title') ?></h2>
      <p style="color:#64748b;font-size:13px;margin-bottom:20px;"><?= admin_t('blocks_desc') ?></p>

      <!-- Drag-and-drop reorder form -->
      <form method="post" style="margin-bottom:24px;" id="reorderForm">
        <input type="hidden" name="action" value="reorder_blocks">
        <div id="sortable-blocks" style="display:flex;flex-direction:column;gap:16px;">
          <?php foreach ($blocks as $i => $b): ?>
            <div data-idx="<?= $i ?>" style="background:#f8fafc;border-radius:16px;padding:20px;border:2px solid #e2e8f0;cursor:move;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                  <span style="font-size:18px;cursor:grab;">☰</span>
                  <span style="font-size:14px;font-weight:900;color:#0f172a;">#<?= $i+1 ?> <?= e($blockTypes[$b['type'] ?? ''] ?? $b['type'] ?? '') ?></span>
                </div>
                <button type="submit" form="delBlock<?= $i ?>" class="btn btn-red" style="padding:6px 10px;font-size:12px;" onclick="return confirm('<?= admin_t('delete') ?>?')">🗑️</button>
              </div>
              <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                <input type="hidden" name="block_order[]" value="<?= $i ?>">
                <div><span style="font-size:12px;font-weight:800;color:#64748b;">📝 <?= admin_t('name') ?></span><div class="field" style="background:#fff;padding:8px 12px;font-size:13px;"><?= e($b['title'] ?? '') ?: '—' ?></div></div>
                <div><span style="font-size:12px;font-weight:800;color:#64748b;">📄 Подзаголовок</span><div class="field" style="background:#fff;padding:8px 12px;font-size:13px;"><?= e($b['subtitle'] ?? '') ?: '—' ?></div></div>
                <div style="grid-column:1/-1;"><span style="font-size:12px;font-weight:800;color:#64748b;">📝 Тип: <?= e($b['type'] ?? '') ?></span><div class="field" style="background:#fff;padding:8px 12px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e(mb_substr($b['content'] ?? '', 0, 120)) ?><?= mb_strlen($b['content'] ?? '') > 120 ? '...' : '' ?></div></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($blocks) > 1): ?>
          <button class="btn" style="margin-top:12px;">💾 <?= admin_t('save') ?> <?= admin_t('tab_blocks') ?></button>
        <?php endif; ?>
      </form>
      <script>
        if (document.getElementById('sortable-blocks')) {
          new Sortable(document.getElementById('sortable-blocks'), {
            animation: 200,
            handle: '[style*="cursor:grab"]',
            onEnd: function() {
              const items = document.querySelectorAll('#sortable-blocks > [data-idx]');
              items.forEach((el, i) => {
                const input = el.querySelector('input[name="block_order[]"]');
                if (input) input.value = el.dataset.idx;
              });
            }
          });
        }
      </script>

      <!-- Edit blocks content -->
      <form method="post" style="margin-bottom:24px;">
        <input type="hidden" name="action" value="save_blocks">
        <details>
          <summary style="font-size:14px;font-weight:900;color:#0f172a;cursor:pointer;padding:12px 0;">✏️ <?= admin_t('edit') ?> <?= admin_t('tab_blocks') ?></summary>
          <div style="display:flex;flex-direction:column;gap:16px;margin-top:12px;">
            <?php foreach ($blocks as $i => $b): ?>
              <div style="background:#f8fafc;border-radius:16px;padding:20px;border:2px solid #e2e8f0;">
                <div style="font-size:12px;font-weight:900;color:#64748b;margin-bottom:12px;">#<?= $i+1 ?> <?= e($blockTypes[$b['type'] ?? ''] ?? $b['type'] ?? '') ?></div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                  <input type="hidden" name="block_type[]" value="<?= e($b['type'] ?? '') ?>">
                  <label><span style="font-size:12px;font-weight:800;color:#64748b;">📝 <?= admin_t('name') ?></span><input name="block_title[]" value="<?= e($b['title'] ?? '') ?>" class="field" placeholder="Заголовок"></label>
                  <label><span style="font-size:12px;font-weight:800;color:#64748b;">📄 Подзаголовок</span><input name="block_subtitle[]" value="<?= e($b['subtitle'] ?? '') ?>" class="field" placeholder="Подзаголовок"></label>
                  <label style="grid-column:1/-1;"><span style="font-size:12px;font-weight:800;color:#64748b;">📝 Содержимое (HTML)</span><textarea name="block_content[]" rows="4" class="field" placeholder="Текст, HTML..."><?= e($b['content'] ?? '') ?></textarea></label>
                  <label><span style="font-size:12px;font-weight:800;color:#64748b;">🔘 Кнопка</span><input name="block_button[]" value="<?= e($b['button_text'] ?? '') ?>" class="field" placeholder="Текст кнопки"></label>
                  <label><span style="font-size:12px;font-weight:800;color:#64748b;">🔗 Ссылка</span><input name="block_link[]" value="<?= e($b['button_link'] ?? '') ?>" class="field" placeholder="index.php"></label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="btn" style="margin-top:12px;">💾 <?= admin_t('save') ?></button>
        </details>
      </form>

      <!-- Add new block -->
      <form method="post" class="card" style="background:#f1f5f9;padding:20px;">
        <input type="hidden" name="action" value="save_blocks">
        <h3 style="font-size:16px;margin-bottom:12px;">➕ <?= admin_t('add') ?> <?= admin_t('block_type') ?></h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
          <?php foreach ($blockTypes as $k => $v): ?>
            <label style="display:flex;align-items:center;gap:8px;padding:12px;background:#fff;border-radius:12px;border:2px solid #e2e8f0;cursor:pointer;transition:all .2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
              <input type="radio" name="block_type[]" value="<?= e($k) ?>" required style="width:18px;height:18px;accent-color:#0f172a;">
              <div>
                <div style="font-weight:900;font-size:14px;"><?= e($v) ?></div>
                <div style="font-size:11px;color:#64748b;font-weight:700;"><?= e($k) ?></div>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <input name="block_title[]" class="field" placeholder="Заголовок">
          <input name="block_subtitle[]" class="field" placeholder="Подзаголовок">
        </div>
        <textarea name="block_content[]" rows="3" class="field" style="margin-top:12px;" placeholder="Содержимое (HTML или текст)"></textarea>
        <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <input name="block_button[]" class="field" placeholder="Текст кнопки">
          <input name="block_link[]" class="field" placeholder="Ссылка кнопки">
        </div>
        <button class="btn btn-dark" style="margin-top:16px;">➕ <?= admin_t('add') ?></button>
      </form>

      <?php foreach ($blocks as $i => $b): ?>
        <form method="post" id="delBlock<?= $i ?>" style="display:none;">
          <input type="hidden" name="action" value="delete_block">
          <input type="hidden" name="block_idx" value="<?= $i ?>">
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
<?php endif; ?>

<script>
function delCat(id){
  if(!confirm('Удалить категорию и все подкатегории?')) return;
  const f=document.createElement('form');f.method='post';
  f.innerHTML='<input type="hidden" name="action" value="delete_category"><input type="hidden" name="category_id" value="'+id+'">';
  document.body.appendChild(f);f.submit();
}
function delSub(catId,subId){
  if(!confirm('Удалить подкатегорию?')) return;
  const f=document.createElement('form');f.method='post';
  f.innerHTML='<input type="hidden" name="action" value="delete_subcategory"><input type="hidden" name="category_id" value="'+catId+'"><input type="hidden" name="subcategory_id" value="'+subId+'">';
  document.body.appendChild(f);f.submit();
}
</script>

</body>
</html>
