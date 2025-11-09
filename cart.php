<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - StyleHub</title>
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
                        <li><a href="products.php">Shop</a></li>
                        <li><a href="cart.php" class="active">Cart <span id="cart-count" class="badge">0</span></a></li>
                        <li><a href="admin/login.php">Admin</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="cart-section">
        <div class="container">
            <h2 class="section-title">Shopping Cart</h2>
            
            <div id="cart-items" class="cart-items">
                <!-- Cart items will be loaded here -->
            </div>

            <div id="cart-summary" class="cart-summary" style="display: none;">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$10.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="total">$0.00</span>
                </div>
                <button class="btn btn-primary btn-block" onclick="checkout()">Proceed to Checkout</button>
            </div>

            <div id="empty-cart" style="display: none; text-align: center; padding: 50px;">
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </section>

    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCheckoutModal()">&times;</span>
            <h2>Checkout</h2>
            <form id="checkout-form" onsubmit="submitOrder(event)">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Place Order</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 StyleHub. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>
