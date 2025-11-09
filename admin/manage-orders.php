<?php
session_start();
require_once '../config/database.php'; // must define $conn (MySQLi connection)
require_once '../config/csrf.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$csrf_token = generateCSRFToken();

// ✅ Fetch all orders safely using MySQLi
$orders = [];
$query = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
} else {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>Manage Orders - StyleHub Admin</title>
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
                <a href="dashboard.php">Dashboard</a>
                <a href="manage-products.php">Products</a>
                <a href="manage-orders.php" class="active">Orders</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="container">
            <div class="admin-card">
                <h3>Manage Orders</h3>

                <?php if (count($orders) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                            <td><?php echo htmlspecialchars($order['phone']); ?></td>
                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <select onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)" style="padding: 5px; border-radius: 5px;">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                            <td class="action-btns">
                                <button class="btn btn-small btn-edit" onclick='viewOrder(<?php echo json_encode($order); ?>)'>View</button>
                                <button class="btn btn-small btn-delete" onclick="deleteOrder(<?php echo $order['id']; ?>)">Delete</button>
                            </td>
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

    <div id="order-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeOrderModal()">&times;</span>
            <h2>Order Details</h2>
            <div id="order-details"></div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 StyleHub. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function getCSRFToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        }

        function viewOrder(order) {
            let itemsHtml = '';
            try {
                const items = JSON.parse(order.items || '[]');
                itemsHtml = items.map(item => `
                    <div style="padding: 10px; border-bottom: 1px solid #eee;">
                        <strong>${item.name}</strong> - Size: ${item.size} - Qty: ${item.quantity} - $${(item.price * item.quantity).toFixed(2)}
                    </div>
                `).join('');
            } catch (e) {
                itemsHtml = '<p>No items data available</p>';
            }

            document.getElementById('order-details').innerHTML = `
                <div class="form-group"><strong>Order ID:</strong> #${order.id}</div>
                <div class="form-group"><strong>Customer:</strong> ${order.customer_name}</div>
                <div class="form-group"><strong>Email:</strong> ${order.email}</div>
                <div class="form-group"><strong>Phone:</strong> ${order.phone}</div>
                <div class="form-group"><strong>Address:</strong> ${order.address}</div>
                <div class="form-group"><strong>Status:</strong> ${order.status.toUpperCase()}</div>
                <div class="form-group"><strong>Items:</strong>${itemsHtml}</div>
                <div class="form-group"><strong>Total:</strong> $${parseFloat(order.total).toFixed(2)}</div>
                <div class="form-group"><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleString()}</div>
            `;
            document.getElementById('order-modal').style.display = 'block';
        }

        function closeOrderModal() {
            document.getElementById('order-modal').style.display = 'none';
        }

        async function updateOrderStatus(id, status) {
            try {
                const response = await fetch('../api/orders.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': getCSRFToken()
                    },
                    body: JSON.stringify({ id, status })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Order status updated successfully!');
                } else {
                    alert('Error updating order status.');
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating order status.');
                location.reload();
            }
        }

        async function deleteOrder(id) {
            if (!confirm('Are you sure you want to delete this order?')) return;
            
            try {
                const response = await fetch(`../api/orders.php?id=${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-Token': getCSRFToken() }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Order deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting order.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting order.');
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('order-modal');
            if (event.target === modal) closeOrderModal();
        }
    </script>
</body>
</html>
