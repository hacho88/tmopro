<?php
/**
 * TMOPRO REST API
 * Methods: GET (read), POST (create)
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$resource = trim((string)($_GET['resource'] ?? ''));
$method = strtoupper($_SERVER['REQUEST_METHOD']);

function json_error($code, $msg) {
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}
function json_ok($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function load_json($path) {
    if (!file_exists($path)) return [];
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

switch ($resource) {
    case 'products':
        $products = load_json(__DIR__ . '/products.json');
        $id = trim((string)($_GET['id'] ?? ''));
        if ($id !== '') {
            $product = array_values(array_filter($products, fn($p) => (string)($p['id'] ?? '') === $id))[0] ?? null;
            if (!$product) json_error(404, 'Product not found');
            json_ok($product);
        }
        // Filters
        $category = trim((string)($_GET['category'] ?? ''));
        $brand = trim((string)($_GET['brand'] ?? ''));
        $search = mb_strtolower(trim((string)($_GET['search'] ?? '')));
        $in_stock = isset($_GET['in_stock']) ? true : false;
        $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

        $filtered = array_filter($products, function($p) use ($category, $brand, $search, $in_stock, $min_price, $max_price) {
            if ($category && ($p['category'] ?? '') !== $category) return false;
            if ($brand && ($p['brand'] ?? '') !== $brand) return false;
            if ($search) {
                $name = mb_strtolower((string)($p['name'] ?? ''));
                $article = mb_strtolower((string)($p['article'] ?? ''));
                if (mb_strpos($name, $search) === false && mb_strpos($article, $search) === false) return false;
            }
            if ($in_stock && (float)($p['stock'] ?? 0) <= 0) return false;
            $price = (float)($p['price_base'] ?? 0);
            if ($min_price !== null && $price < $min_price) return false;
            if ($max_price !== null && $price > $max_price) return false;
            return true;
        });
        $total = count($filtered);
        $items = array_slice(array_values($filtered), $offset, $limit);
        json_ok(['total' => $total, 'offset' => $offset, 'limit' => $limit, 'items' => $items]);

    case 'categories':
        $cats = load_json(__DIR__ . '/categories.json');
        json_ok($cats);

    case 'coupons':
        $coupons = load_json(__DIR__ . '/coupons.json');
        json_ok($coupons);

    case 'orders':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) json_error(400, 'Invalid JSON body');
            $company = trim((string)($input['company'] ?? ''));
            $cart = (array)($input['cart'] ?? []);
            if ($company === '' || empty($cart)) json_error(400, 'company and cart are required');
            // Save to JSON
            $ordersPath = __DIR__ . '/orders_api.json';
            $orders = load_json($ordersPath);
            $orderNumber = 'API-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $order = [
                'id' => count($orders) + 1,
                'order_number' => $orderNumber,
                'company' => $company,
                'inn' => $input['inn'] ?? '',
                'contact_person' => $input['contact_person'] ?? '',
                'phone' => $input['phone'] ?? '',
                'email' => $input['email'] ?? '',
                'cart' => $cart,
                'coupon_code' => $input['coupon_code'] ?? '',
                'city' => $input['city'] ?? '',
                'address' => $input['address'] ?? '',
                'zip' => $input['zip'] ?? '',
                'delivery_note' => $input['delivery_note'] ?? '',
                'total' => (float)($input['total'] ?? 0),
                'status' => 'new',
                'created_at' => date('c'),
                'source' => 'api'
            ];
            $orders[] = $order;
            file_put_contents($ordersPath, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            http_response_code(201);
            json_ok(['order_number' => $orderNumber, 'status' => 'new', 'id' => $order['id']]);
        }
        json_error(405, 'Method not allowed. Use POST to create orders.');

    case 'settings':
        $settings = load_json(__DIR__ . '/settings.json');
        // Hide sensitive fields
        unset($settings['admin_login'], $settings['admin_password']);
        json_ok($settings);

    default:
        json_error(400, 'Unknown resource. Available: products, categories, coupons, orders, settings');
}
