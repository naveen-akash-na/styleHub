let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badges = document.querySelectorAll('#cart-count');
    badges.forEach(badge => badge.textContent = count);
}

async function loadFeaturedProducts() {
    try {
        const response = await fetch('api/products.php?limit=4');
        const products = await response.json();
        displayProducts(products, 'featured-products');
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

async function loadProducts(category = 'all') {
    try {
        const url = category === 'all' 
            ? 'api/products.php' 
            : `api/products.php?category=${category}`;
        const response = await fetch(url);
        const products = await response.json();
        displayProducts(products, 'products-container');
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

function displayProducts(products, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (products.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 50px; color: #666;">No products found.</p>';
        return;
    }

    container.innerHTML = products.map(product => `
        <div class="product-card">
            <div class="product-image">${product.image || '👕'}</div>
            <div class="product-info">
                <div class="product-category">${product.category}</div>
                <h3>${product.name}</h3>
                <p class="product-description">${product.description}</p>
                <p class="product-sizes">Sizes: ${product.sizes}</p>
                <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
                <p class="product-stock ${product.stock <= 0 ? 'out-of-stock' : ''}">
                    ${product.stock > 0 ? `In Stock: ${product.stock}` : 'Out of Stock'}
                </p>
                <button 
                    class="add-to-cart-btn" 
                    onclick="addToCart(${product.id}, '${product.name}', ${product.price}, '${product.image || '👕'}', '${product.sizes}')"
                    ${product.stock <= 0 ? 'disabled' : ''}
                >
                    ${product.stock > 0 ? 'Add to Cart' : 'Out of Stock'}
                </button>
            </div>
        </div>
    `).join('');
}

function addToCart(id, name, price, image, sizes) {
    const size = sizes.split(',')[0].trim();
    
    const existingItem = cart.find(item => item.id === id && item.size === size);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id,
            name,
            price: parseFloat(price),
            image,
            size,
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    
    alert(`${name} added to cart!`);
}

updateCartCount();
