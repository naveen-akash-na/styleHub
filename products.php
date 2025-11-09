<?php
session_start();
require_once 'config/database.php'; // Must define $conn = new mysqli(...)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - StyleHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>StyleHub</h1>
                    <p class="tagline">Premium Fashion</p>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php" class="active">Shop</a></li>
                        <li><a href="cart.php">Cart <span id="cart-count" class="badge">0</span></a></li>
                        <li><a href="admin/login.php">Admin</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="products-section">
        <div class="container">
            <h2 class="section-title">All Products</h2>
            
            <div class="filter-section">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="men">Men</button>
                <button class="filter-btn" data-category="women">Women</button>
                <button class="filter-btn" data-category="kids">Kids</button>
                <button class="filter-btn" data-category="accessories">Accessories</button>
            </div>

            <div id="products-container" class="product-grid">
                <?php
                // Fetch all products using MySQLi
                $result = $conn->query("SELECT * FROM products ORDER BY 1 DESC");

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <div class="product-card" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                            <h1 class="category-icon"><?php echo $row['image']; ?></h1>
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                            <button class="btn btn-primary add-to-cart" data-id="<?php echo $row[1]; ?>">Add to Cart</button>
                        </div>
                <?php
                    endwhile;
                else:
                    echo "<p style='text-align:center; color:#666;'>No products found.</p>";
                endif;
                ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; StyleHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        // Product filtering
        const buttons = document.querySelectorAll('.filter-btn');
        const products = document.querySelectorAll('.product-card');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const category = btn.dataset.category;

                products.forEach(product => {
                    if (category === 'all' || product.dataset.category === category) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
