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
    $category = $_GET['category'] ?? null;
    $limit = $_GET['limit'] ?? null;
    
    $query = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if ($category && $category !== 'all') {
        $query .= " AND category = ?";
        $params[] = $category;
    }
    
    $query .= " ORDER BY id DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = (int)$limit;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    echo json_encode($products);
}

elseif ($method === 'POST') {
    requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['name']) || !isset($data['price']) || !isset($data['category'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO products (name, description, price, image, category, sizes, stock)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $data['name'],
        $data['description'] ?? '',
        floatval($data['price']),
        $data['image'] ?? '',
        $data['category'],
        $data['sizes'] ?? '',
        intval($data['stock'] ?? 0)
    ]);
    
    echo json_encode(['success' => $result, 'id' => $pdo->lastInsertId()]);
}

elseif ($method === 'PUT') {
    requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id']) || !isset($data['name']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        UPDATE products 
        SET name = ?, description = ?, price = ?, image = ?, category = ?, sizes = ?, stock = ?
        WHERE id = ?
    ");
    
    $result = $stmt->execute([
        $data['name'],
        $data['description'] ?? '',
        floatval($data['price']),
        $data['image'] ?? '',
        $data['category'],
        $data['sizes'] ?? '',
        intval($data['stock'] ?? 0),
        intval($data['id'])
    ]);
    
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
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $result = $stmt->execute([intval($id)]);
    echo json_encode(['success' => $result]);
}
?>
