<?php
session_start();
require_once '../config/database.php'; // defines $conn

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// ✅ Helper function to safely get a single value from DB
function getSingleValue($conn, $query) {
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_row()) {
        return $row[0];
    }
    return 0;
}

// ✅ Dashboard statistics
$stats = [
    'total_products' => getSingleValue($conn, "SELECT COUNT(*) FROM products"),
    'total_orders' => getSingleValue($conn, "SELECT COUNT(*) FROM orders"),
    'pending_orders' => getSingleValue($conn, "SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
    'total_revenue' => getSingleValue($conn, "SELECT SUM(total) FROM orders") ?? 0
];

// ✅ Recent orders
$recentOrders = [];
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StyleHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>StyleHub Admin</h1>
                    <p class="tagline">Management Portal</p>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="manage-products.php">Products</a>
                <a href="manage-orders.php">Orders</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="container">
            <h2 class="section-title">Dashboard</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h4><?php echo $stats['total_products']; ?></h4>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $stats['total_orders']; ?></h4>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $stats['pending_orders']; ?></h4>
                    <p>Pending Orders</p>
                </div>
                <div class="stat-card">
                    <h4>$<?php echo number_format($stats['total_revenue'], 2); ?></h4>
                    <p>Total Revenue</p>
                </div>
            </div>

            <div class="admin-card">
                <h3>Recent Orders</h3>
                <?php if (count($recentOrders) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <span style="color: <?php echo $order['status'] === 'pending' ? '#ffc107' : '#28a745'; ?>; font-weight: 600;">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; padding: 2rem; color: #666;">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 StyleHub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
