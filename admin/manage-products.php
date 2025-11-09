<?php
session_start();
require_once '../config/database.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$csrf_token = generateCSRFToken();

// Fetch products
$result = []; // $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>Manage Products - StyleHub Admin</title>
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
                <a href="manage-products.php" class="active">Products</a>
                <a href="manage-orders.php">Orders</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="container">
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Manage Products</h3>
                    <button class="btn btn-primary" onclick="showAddProduct()">Add New Product</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo ucfirst($product['category']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td class="action-btns">
                                <button class="btn btn-small btn-edit" onclick='editProduct(<?php echo json_encode($product); ?>)'>Edit</button>
                                <button class="btn btn-small btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProductModal()">&times;</span>
            <h2 id="modal-title">Add New Product</h2>
            <form id="product-form" onsubmit="saveProduct(event)">
                <input type="hidden" id="product-id" name="id">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="product-name" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="product-description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="product-category" name="category" required>
                        <option value="men">Men</option>
                        <option value="women">Women</option>
                        <option value="kids">Kids</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sizes (comma separated)</label>
                    <input type="text" id="product-sizes" name="sizes" placeholder="S, M, L, XL" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" id="product-stock" name="stock" min="0" required>
                </div>
                <div class="form-group">
                    <label>Image (emoji or icon)</label>
                    <input type="text" id="product-image" name="image" placeholder="👕">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Save Product</button>
            </form>
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

        function showAddProduct() {
            document.getElementById('modal-title').textContent = 'Add New Product';
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-modal').style.display = 'block';
        }

        function editProduct(product) {
            document.getElementById('modal-title').textContent = 'Edit Product';
            document.getElementById('product-id').value = product.id;
            document.getElementById('product-name').value = product.name;
            document.getElementById('product-description').value = product.description;
            document.getElementById('product-price').value = product.price;
            document.getElementById('product-category').value = product.category;
            document.getElementById('product-sizes').value = product.sizes;
            document.getElementById('product-stock').value = product.stock;
            document.getElementById('product-image').value = product.image;
            document.getElementById('product-modal').style.display = 'block';
        }

        function closeProductModal() {
            document.getElementById('product-modal').style.display = 'none';
        }

        async function saveProduct(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            const isEdit = data.id !== '';
            const method = isEdit ? 'PUT' : 'POST';
            const url = '../api/products.php';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': getCSRFToken()
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    alert(isEdit ? 'Product updated successfully!' : 'Product added successfully!');
                    location.reload();
                } else {
                    alert('Error saving product.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving product.');
            }
        }

        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;
            try {
                const response = await fetch(`../api/products.php?id=${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-Token': getCSRFToken() }
                });
                const result = await response.json();
                if (result.success) {
                    alert('Product deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting product.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting product.');
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('product-modal');
            if (event.target === modal) closeProductModal();
        }
    </script>
</body>
</html>
