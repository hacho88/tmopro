<?php
require_once __DIR__ . '/db.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Введите email и пароль';
    } else {
        $pdo = tmopro_db();
        if (!$pdo) {
            $error = 'Система временно недоступна. Попробуйте позже.';
        } else {
            $stmt = $pdo->prepare('SELECT u.*, a.company_name, a.price_tier FROM b2b_users u JOIN b2b_accounts a ON u.account_id = a.id WHERE u.email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_start();
                $_SESSION['b2b_user_id'] = $user['id'];
                $_SESSION['b2b_account_id'] = $user['account_id'];
                $_SESSION['b2b_user_name'] = $user['name'];
                $_SESSION['b2b_company'] = $user['company_name'];
                $_SESSION['b2b_price_tier'] = $user['price_tier'];
                header('Location: profile.php');
                exit;
            } else {
                $error = 'Неверный email или пароль';
            }
        }
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $company = trim((string)($_POST['company_name'] ?? ''));
    $inn = trim((string)($_POST['inn'] ?? ''));
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');

    if ($company === '' || $name === '' || $email === '' || $password === '') {
        $error = 'Заполните все обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email';
    } elseif ($password !== $password2) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $pdo = tmopro_db();
        if (!$pdo) {
            $error = 'Система временно недоступна. Попробуйте позже.';
        } else {
            try {
                $pdo->beginTransaction();

                // Create account
                $stmt = $pdo->prepare('INSERT INTO b2b_accounts (company_name, inn, email, phone, price_tier) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$company, $inn, $email, $phone, 'default']);
                $accountId = (int)$pdo->lastInsertId();

                // Create user
                $stmt = $pdo->prepare('INSERT INTO b2b_users (account_id, name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$accountId, $name, $email, password_hash($password, PASSWORD_DEFAULT), 'buyer']);

                $pdo->commit();
                $success = 'Регистрация успешна! Теперь вы можете войти.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                if ($e->getCode() == 23000) {
                    $error = 'Пользователь с таким email уже существует';
                } else {
                    $error = 'Ошибка при регистрации. Попробуйте позже.';
                }
            }
        }
    }
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
  <title>Вход — <?= e($siteName) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=lux-gold-f">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, Segoe UI, Arial; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); min-height: 100vh; }
    .auth-card { background: #fff; border-radius: 32px; box-shadow: 0 24px 80px rgba(15,23,42,.12); border: 1px solid rgba(15,23,42,0.06); }
    .auth-input { width: 100%; padding: 14px 18px; border-radius: 16px; border: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; color: #0f172a; background: #f8fafc; transition: all .2s; }
    .auth-input:focus { outline: none; border-color: #008A4E; background: #fff; box-shadow: 0 0 0 3px rgba(0,138,78,0.1); }
    .auth-label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
    .auth-btn { width: 100%; padding: 16px; border-radius: 20px; font-size: 16px; font-weight: 900; border: none; cursor: pointer; transition: all .2s; color: #fff; }
    .auth-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 48px rgba(5,150,105,.35); }
    .auth-tab { padding: 12px 24px; font-size: 14px; font-weight: 900; border-radius: 16px; cursor: pointer; border: none; background: transparent; color: #64748b; transition: all .2s; }
    .auth-tab.active { background: #f1f5f9; color: #0f172a; }
    .auth-tab:hover { color: #0f172a; }
    .auth-alert { padding: 14px 18px; border-radius: 16px; font-size: 14px; font-weight: 800; }
    .auth-alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .auth-alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
  </style>
</head>
<body class="flex items-center justify-center p-6">

  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <a href="index.php" class="text-2xl font-black tracking-tight text-gray-900" style="text-decoration:none;"><?= e($settings['site_short_name'] ?? 'TMOPRO') ?></a>
      <p class="mt-2 text-sm font-bold text-gray-400">B2B портал для оптовых клиентов</p>
    </div>

    <div class="auth-card p-8">
      <div class="flex gap-2 mb-8 bg-gray-50 rounded-2xl p-1">
        <button type="button" onclick="showTab('login')" id="tab-login" class="auth-tab active flex-1">Вход</button>
        <button type="button" onclick="showTab('register')" id="tab-register" class="auth-tab flex-1">Регистрация</button>
      </div>

      <?php if ($error): ?>
        <div class="auth-alert auth-alert-error mb-6"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="auth-alert auth-alert-success mb-6"><?= e($success) ?></div>
      <?php endif; ?>

      <!-- Login Form -->
      <form id="form-login" method="post" action="" class="space-y-5">
        <input type="hidden" name="action" value="login">
        <div>
          <label class="auth-label">Email</label>
          <input type="email" name="email" class="auth-input" placeholder="your@company.ru" required>
        </div>
        <div>
          <label class="auth-label">Пароль</label>
          <input type="password" name="password" class="auth-input" placeholder="••••••" required>
        </div>
        <button type="submit" class="auth-btn <?= e($accentBg) ?>">Войти</button>
      </form>

      <!-- Register Form -->
      <form id="form-register" method="post" action="" class="space-y-5" style="display:none;">
        <input type="hidden" name="action" value="register">
        <div>
          <label class="auth-label">Название компании *</label>
          <input type="text" name="company_name" class="auth-input" placeholder="ООО Стройка" required>
        </div>
        <div>
          <label class="auth-label">ИНН</label>
          <input type="text" name="inn" class="auth-input" placeholder="7701234567">
        </div>
        <div>
          <label class="auth-label">Контактное лицо *</label>
          <input type="text" name="name" class="auth-input" placeholder="Иванов Иван" required>
        </div>
        <div>
          <label class="auth-label">Email *</label>
          <input type="email" name="email" class="auth-input" placeholder="your@company.ru" required>
        </div>
        <div>
          <label class="auth-label">Телефон</label>
          <input type="tel" name="phone" class="auth-input" placeholder="+7 (900) 123-45-67">
        </div>
        <div>
          <label class="auth-label">Пароль *</label>
          <input type="password" name="password" class="auth-input" placeholder="Минимум 6 символов" required minlength="6">
        </div>
        <div>
          <label class="auth-label">Повторите пароль *</label>
          <input type="password" name="password2" class="auth-input" placeholder="••••••" required>
        </div>
        <button type="submit" class="auth-btn <?= e($accentBg) ?>">Зарегистрироваться</button>
      </form>
    </div>

    <p class="text-center mt-6 text-sm font-bold text-gray-400">
      <a href="index.php" style="color:#64748b;text-decoration:none;">← Вернуться в каталог</a>
    </p>
  </div>

  <script>
    function showTab(tab) {
      document.getElementById('form-login').style.display = tab === 'login' ? 'block' : 'none';
      document.getElementById('form-register').style.display = tab === 'register' ? 'block' : 'none';
      document.getElementById('tab-login').classList.toggle('active', tab === 'login');
      document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    }
    <?php if ($success): ?>showTab('login');<?php endif; ?>
  </script>

</body>
</html>
