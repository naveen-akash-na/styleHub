<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleHub - Premium Clothing Store</title>
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
                        <li><a href="index.php" class="active">Home</a></li>
                        <li><a href="products.php">Shop</a></li>
                        <li><a href="cart.php">Cart <span id="cart-count" class="badge">0</span></a></li>
                        <li><a href="admin/login.php">Admin</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>New Collection 2024</h2>
                <p>Discover the latest trends in fashion</p>
                <a href="products.php" class="btn btn-primary">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="category-grid">
                <div class="category-card" onclick="window.location.href='products.php?category=men'">
                    <div class="category-icon">👔</div>
                    <h3>Men's Wear</h3>
                    <p>Stylish clothing for men</p>
                </div>
                <div class="category-card" onclick="window.location.href='products.php?category=women'">
                    <div class="category-icon">👗</div>
                    <h3>Women's Wear</h3>
                    <p>Elegant fashion for women</p>
                </div>
                <div class="category-card" onclick="window.location.href='products.php?category=kids'">
                    <div class="category-icon">🧸</div>
                    <h3>Kids Wear</h3>
                    <p>Comfortable clothes for kids</p>
                </div>
                <div class="category-card" onclick="window.location.href='products.php?category=accessories'">
                    <div class="category-icon">👜</div>
                    <h3>Accessories</h3>
                    <p>Complete your look</p>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div id="featured-products" class="product-grid">
                <!-- Products will be loaded here -->
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 StyleHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        loadFeaturedProducts();
    </script>
</body>
</html>
