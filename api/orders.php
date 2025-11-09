<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/csrf.php';

function requireAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    requireCSRF();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();
    
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
    $orders = $stmt->fetchAll();
    
    foreach ($orders as &$order) {
        $order['items'] = json_decode($order['items'], true);
    }
    
    echo json_encode($orders);
}

elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['customer_name']) || !isset($data['items']) || empty($data['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid order data']);
        exit;
    }
    
    $calculatedTotal = 0;
    $validatedItems = [];
    
    foreach ($data['items'] as $item) {
        if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price'])) {
            continue;
        }
        
        $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
        $stmt->execute([intval($item['id'])]);
        $product = $stmt->fetch();
        
        if (!$product) {
            continue;
        }
        
        if ($product['stock'] < intval($item['quantity'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Insufficient stock for ' . ($item['name'] ?? 'product')]);
            exit;
        }
        
        $actualPrice = floatval($product['price']);
        $quantity = intval($item['quantity']);
        
        $validatedItems[] = [
            'id' => intval($item['id']),
            'name' => $item['name'] ?? '',
            'price' => $actualPrice,
            'quantity' => $quantity,
            'size' => $item['size'] ?? '',
            'image' => $item['image'] ?? ''
        ];
        
        $calculatedTotal += $actualPrice * $quantity;
    }
    
    if (empty($validatedItems)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No valid items in order']);
        exit;
    }
    
    $shippingCost = 10.00;
    $finalTotal = $calculatedTotal + $shippingCost;
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_name, email, phone, address, items, total, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $result = $stmt->execute([
        $data['customer_name'],
        $data['email'] ?? '',
        $data['phone'] ?? '',
        $data['address'] ?? '',
        json_encode($validatedItems),
        $finalTotal
    ]);
    
    echo json_encode([
        'success' => $result,
        'order_id' => $pdo->lastInsertId(),
        'total' => $finalTotal
    ]);
}

elseif ($method === 'PUT') {
    requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($data['status'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $result = $stmt->execute([$data['status'], intval($data['id'])]);
    
    echo json_encode(['success' => $result]);
}

elseif ($method === 'DELETE') {
    requireAdmin();
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No ID provided']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $result = $stmt->execute([intval($id)]);
    echo json_encode(['success' => $result]);
}
?>
