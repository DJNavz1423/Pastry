// User Dashboard JavaScript

let currentCategory = 'all';
let currentFavoriteCategory = 'all';

document.addEventListener('DOMContentLoaded', function() {
    // Load initial dashboard data
    loadDashboardCategories();
    loadBestSellingProducts();
    loadDashboardCart();
    
    // Navigation handlers
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sectionId = this.getAttribute('data-section');
            switchSection(sectionId);
        });
    });

    // Search functionality
    const searchBar = document.getElementById('search-bar');
    if (searchBar) {
        searchBar.addEventListener('input', debounce(function(e) {
            const searchTerm = e.target.value.trim();
            if (searchTerm.length >= 2) {
                searchProducts(searchTerm);
            } else if (searchTerm.length === 0) {
                // Reset to current view
                const activeSection = document.querySelector('main section.active').id;
                if (activeSection === 'shop') {
                    loadShopProducts(currentCategory);
                }
            }
        }, 300));
    }
});

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search products
function searchProducts(searchTerm) {
    // Switch to shop section if not already there
    if (!document.getElementById('shop').classList.contains('active')) {
        switchSection('shop');
    }
    
    fetch(`./api/user_api/search_products.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('shopProducts');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try searching with different keywords</p>
                    </div>
                `;
                return;
            }
            
            // Display search results using same product card template
            products.forEach(product => {
                const favoriteIcon = product.is_favorite ? 'fas fa-heart' : 'far fa-heart';
                const favoriteClass = product.is_favorite ? 'active' : '';
                const inCartClass = product.in_cart ? 'in-cart' : '';
                const cartText = product.in_cart ? 'In Cart' : 'Add to Cart';
                
                const productCard = `
                    <div class="product-card">
                        <div class="product-card-image">
                            <img src="./uploads/products/${product.picture || 'no-image.png'}" alt="${product.name}">
                            <button class="favorite-btn ${favoriteClass}" onclick="toggleFavorite('${product.id}', this)">
                                <i class="${favoriteIcon}"></i>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <span class="product-category">${product.category}</span>
                            <h3>${product.name}</h3>
                            <div class="product-price">₱${parseFloat(product.price).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn-add-cart ${inCartClass}" onclick="addToCart('${product.id}', this)">
                                    <i class="fas fa-shopping-cart"></i> ${cartText}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += productCard;
            });
        });
}

// Switch between sections
function switchSection(sectionId) {
    // Remove active from all nav links
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    // Add active to clicked nav link
    const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
    if (activeLink) activeLink.classList.add('active');
    
    // Hide all sections
    document.querySelectorAll('main section').forEach(section => section.classList.remove('active'));
    
    // Show selected section
    document.getElementById(sectionId).classList.add('active');
    
    // Load section-specific data
    if (sectionId === 'shop') {
        loadShopCategories();
        loadShopProducts(currentCategory);
    } else if (sectionId === 'favorites') {
        loadFavoritesCategories();
        loadFavorites(currentFavoriteCategory);
    } else if (sectionId === 'cart') {
        loadFullCart();
    }
}

// ============================================================
// DASHBOARD SECTION
// ============================================================

function loadDashboardCategories() {
    fetch('./api/user_api/get_categories.php')
        .then(response => response.json())
        .then(categories => {
            const container = document.getElementById('dashboardCategories');
            container.innerHTML = '';
            
            categories.slice(0, 4).forEach(cat => {
                const categoryEl = `
                    <div class="category" onclick="switchToCategory('${cat.name}')">
                        <figure>
                            <img src="./images/category_img/${cat.name.toLowerCase()}.jpg" alt="${cat.name}" 
                                 onerror="this.src='./images/default-category.jpg'">
                            <figcaption>${cat.name}</figcaption>
                        </figure>
                    </div>
                `;
                container.innerHTML += categoryEl;
            });
        });
}

function switchToCategory(categoryName) {
    currentCategory = categoryName.toLowerCase();
    switchSection('shop');
    filterProducts(currentCategory);
}

function loadBestSellingProducts() {
    fetch('./api/user_api/get_products.php?limit=6&sort=bestselling')
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('bestSellingProducts');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = '<p style="text-align:center;color:var(--gray-light);">No products available</p>';
                return;
            }
            
            products.forEach(product => {
                const isFavorite = product.is_favorite ? 'heart_check' : 'heart_plus';
                const productEl = `
                    <div class="trending wide-container">
                        <figure style="position:relative;">
                            <span class="img-container">
                                <img src="./uploads/products/${product.picture || 'no-image.png'}" alt="${product.name}">
                            </span>
                            <span class="material-symbols-outlined heart_plus" onclick="toggleFavorite('${product.id}', this)">
                                ${isFavorite}
                            </span>
                            <figcaption>
                                <h3>${product.name}</h3>
                                <data><span class="php-symbol">&#8369;</span>${parseFloat(product.price).toFixed(2)}</data>
                                <div class="buttons-wrapper">
                                    <button type="button" class="cta-button add-cart-btn" onclick="addToCart('${product.id}')">
                                        <span class="material-symbols-outlined">add_shopping_cart</span> Add to Cart
                                    </button>
                                </div>
                            </figcaption>
                        </figure>
                    </div>
                `;
                container.innerHTML += productEl;
            });
        });
}

function loadDashboardCart() {
    fetch('./api/user_api/get_cart.php?limit=5')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('dashboardCart');
            
            if (data.items.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some delicious items!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = '';
            data.items.forEach(item => {
                const itemEl = `
                    <div class="cart-container wide-container">
                        <figure>
                            <span class="img-container">
                                <img src="./uploads/products/${item.picture || 'no-image.png'}" alt="${item.name}">
                            </span>
                            <figcaption>
                                <h3>${item.name}</h3>
                                <data><span class="php-symbol">&#8369;</span>${parseFloat(item.price * item.quantity).toFixed(2)}</data>
                            </figcaption>
                        </figure>
                    </div>
                `;
                container.innerHTML += itemEl;
            });
            
            container.innerHTML += `
                <button class="cta-button" onclick="switchSection('cart')">Checkout</button>
            `;
        });
}

// ============================================================
// SHOP SECTION
// ============================================================

function loadShopCategories() {
    fetch('./api/user_api/get_categories.php')
        .then(response => response.json())
        .then(categories => {
            const container = document.querySelector('#shop .category-tabs');
            
            // Keep the All button, add category buttons
            let html = `
                <button class="category-tab active" data-category="all" onclick="filterProducts('all')">
                    <i class="fas fa-th"></i> All
                </button>
            `;
            
            categories.forEach(cat => {
                html += `
                    <button class="category-tab" data-category="${cat.name.toLowerCase()}" onclick="filterProducts('${cat.name.toLowerCase()}')">
                        ${cat.name}
                    </button>
                `;
            });
            
            container.innerHTML = html;
        });
}

function filterProducts(category) {
    currentCategory = category;
    
    // Update active tab
    document.querySelectorAll('#shop .category-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.getAttribute('data-category') === category) {
            tab.classList.add('active');
        }
    });
    
    loadShopProducts(category);
}

function loadShopProducts(category = 'all') {
    const url = category === 'all' 
        ? './api/user_api/get_products.php'
        : `./api/user/get_products.php?category=${category}`;
    
    fetch(url)
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('shopProducts');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No products found</h3>
                        <p>Check back later for new items!</p>
                    </div>
                `;
                return;
            }
            
            products.forEach(product => {
                const favoriteIcon = product.is_favorite ? 'fas fa-heart' : 'far fa-heart';
                const favoriteClass = product.is_favorite ? 'active' : '';
                const inCartClass = product.in_cart ? 'in-cart' : '';
                const cartText = product.in_cart ? 'In Cart' : 'Add to Cart';
                
                const productCard = `
                    <div class="product-card">
                        <div class="product-card-image">
                            <img src="./uploads/products/${product.picture || 'no-image.png'}" alt="${product.name}">
                            <button class="favorite-btn ${favoriteClass}" onclick="toggleFavorite('${product.id}', this)">
                                <i class="${favoriteIcon}"></i>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <span class="product-category">${product.category}</span>
                            <h3>${product.name}</h3>
                            <div class="product-price">₱${parseFloat(product.price).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn-add-cart ${inCartClass}" onclick="addToCart('${product.id}', this)">
                                    <i class="fas fa-shopping-cart"></i> ${cartText}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += productCard;
            });
        });
}

// ============================================================
// FAVORITES SECTION
// ============================================================

function loadFavoritesCategories() {
    fetch('./api/user_api/get_categories.php')
        .then(response => response.json())
        .then(categories => {
            const container = document.querySelector('#favorites .category-tabs');
            
            let html = `
                <button class="category-tab active" data-category="all" onclick="filterFavorites('all')">
                    <i class="fas fa-heart"></i> All
                </button>
            `;
            
            categories.forEach(cat => {
                html += `
                    <button class="category-tab" data-category="${cat.name.toLowerCase()}" onclick="filterFavorites('${cat.name.toLowerCase()}')">
                        ${cat.name}
                    </button>
                `;
            });
            
            container.innerHTML = html;
        });
}

function filterFavorites(category) {
    currentFavoriteCategory = category;
    
    // Update active tab
    document.querySelectorAll('#favorites .category-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.getAttribute('data-category') === category) {
            tab.classList.add('active');
        }
    });
    
    loadFavorites(category);
}

function loadFavorites(category = 'all') {
    const url = category === 'all'
        ? './api/user_api/get_favorites.php'
        : `./api/user/get_favorites.php?category=${category}`;
    
    fetch(url)
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('favoriteProducts');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="far fa-heart"></i>
                        <h3>No favorites yet</h3>
                        <p>Start adding your favorite items!</p>
                    </div>
                `;
                return;
            }
            
            products.forEach(product => {
                const inCartClass = product.in_cart ? 'in-cart' : '';
                const cartText = product.in_cart ? 'In Cart' : 'Add to Cart';
                
                const productCard = `
                    <div class="product-card">
                        <div class="product-card-image">
                            <img src="./uploads/products/${product.picture || 'no-image.png'}" alt="${product.name}">
                            <button class="favorite-btn active" onclick="toggleFavorite('${product.id}', this)">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-card-body">
                            <span class="product-category">${product.category}</span>
                            <h3>${product.name}</h3>
                            <div class="product-price">₱${parseFloat(product.price).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn-add-cart ${inCartClass}" onclick="addToCart('${product.id}', this)">
                                    <i class="fas fa-shopping-cart"></i> ${cartText}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += productCard;
            });
        });
}

function toggleFavorite(productId, element) {
    fetch('./api/user_api/toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update icon
            if (data.action === 'added') {
                if (element.tagName === 'SPAN') {
                    element.textContent = 'heart_check';
                } else {
                    element.classList.add('active');
                    element.querySelector('i').className = 'fas fa-heart';
                }
            } else {
                if (element.tagName === 'SPAN') {
                    element.textContent = 'heart_plus';
                } else {
                    element.classList.remove('active');
                    element.querySelector('i').className = 'far fa-heart';
                }
            }
            
            // Update badge count
            updateBadges();
            
            // If we're in favorites view and item was removed, reload
            if (data.action === 'removed' && document.getElementById('favorites').classList.contains('active')) {
                loadFavorites(currentFavoriteCategory);
            }
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// ============================================================
// CART SECTION
// ============================================================

function addToCart(productId, buttonElement = null) {
    fetch('./api/user_api/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Added to cart!');
            
            // Update button if provided
            if (buttonElement) {
                buttonElement.classList.add('in-cart');
                buttonElement.innerHTML = '<i class="fas fa-shopping-cart"></i> In Cart';
            }
            
            // Update badge and reload cart views
            updateBadges();
            if (document.getElementById('dashboard').classList.contains('active')) {
                loadDashboardCart();
            }
            if (document.getElementById('cart').classList.contains('active')) {
                loadFullCart();
            }
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function loadFullCart() {
    fetch('./api/user_api/get_cart.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('cartItems');
            
            if (data.items.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Start shopping to add items to your cart!</p>
                        <button class="btn btn-primary" onclick="switchSection('shop')">
                            <i class="fas fa-shopping-bag"></i> Browse Products
                        </button>
                    </div>
                `;
                updateCartSummary(0);
                return;
            }
            
            container.innerHTML = '';
            data.items.forEach(item => {
                const itemEl = `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="./uploads/products/${item.picture || 'no-image.png'}" alt="${item.name}">
                        </div>
                        <div class="cart-item-details">
                            <h3>${item.name}</h3>
                            <div class="cart-item-price">₱${parseFloat(item.price).toFixed(2)}</div>
                            <div class="cart-item-actions">
                                <div class="quantity-control">
                                    <button onclick="updateCartQuantity('${item.product_id}', ${item.quantity - 1})">-</button>
                                    <span>${item.quantity}</span>
                                    <button onclick="updateCartQuantity('${item.product_id}', ${item.quantity + 1})">+</button>
                                </div>
                                <button class="btn-remove" onclick="removeFromCart('${item.product_id}')">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += itemEl;
            });
            
            updateCartSummary(data.total);
        });
}

function updateCartQuantity(productId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    fetch('./api/user_api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFullCart();
            loadDashboardCart();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;
    
    fetch('./api/user_api/remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFullCart();
            loadDashboardCart();
            updateBadges();
            
            // Reload shop/favorites if active to update button states
            if (document.getElementById('shop').classList.contains('active')) {
                loadShopProducts(currentCategory);
            }
            if (document.getElementById('favorites').classList.contains('active')) {
                loadFavorites(currentFavoriteCategory);
            }
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function updateCartSummary(subtotal) {
    const deliveryFee = subtotal > 0 ? 50 : 0;
    const total = subtotal + deliveryFee;
    
    document.getElementById('cartSubtotal').textContent = '₱' + subtotal.toFixed(2);
    document.getElementById('deliveryFee').textContent = '₱' + deliveryFee.toFixed(2);
    document.getElementById('cartTotal').textContent = '₱' + total.toFixed(2);
}

// ============================================================
// CHECKOUT
// ============================================================

function proceedToCheckout() {
    fetch('./api/user_api/get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.items.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            const deliveryFee = 50;
            const total = data.total + deliveryFee;
            
            document.getElementById('checkoutSubtotal').textContent = '₱' + data.total.toFixed(2);
            document.getElementById('checkoutTotal').textContent = '₱' + total.toFixed(2);
            
            openCheckoutModal();
        });
}

function openCheckoutModal() {
    document.getElementById('checkoutModal').classList.add('active');
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.remove('active');
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        address: formData.get('checkout_address'),
        phone: formData.get('checkout_phone'),
        payment_method: formData.get('payment_method')
    };
    
    if (!data.address.trim()) {
        alert('Please enter your delivery address');
        return;
    }
    
    fetch('./api/user_api/place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Order placed successfully! Order ID: ' + result.order_id);
            closeCheckoutModal();
            loadFullCart();
            loadDashboardCart();
            updateBadges();
            switchSection('dashboard');
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// ============================================================
// PROFILE SECTION
// ============================================================

document.getElementById('userProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('./api/user_api/update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

function openUserPasswordModal() {
    document.getElementById('userPasswordModal').classList.add('active');
}

function closeUserPasswordModal() {
    document.getElementById('userPasswordModal').classList.remove('active');
}

document.getElementById('userPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        current_password: formData.get('current_password'),
        new_password: formData.get('new_password'),
        confirm_password: formData.get('confirm_password')
    };
    
    if (data.new_password !== data.confirm_password) {
        alert('New passwords do not match!');
        return;
    }
    
    if (data.new_password.length < 6) {
        alert('Password must be at least 6 characters long!');
        return;
    }
    
    fetch('./api/user_api/change_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            closeUserPasswordModal();
            document.getElementById('userPasswordForm').reset();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

function updateBadges() {
    fetch('./api/user_api/get_counts.php')
        .then(response => response.json())
        .then(data => {
            // Update cart badge
            const cartBadges = document.querySelectorAll('.nav-link[data-section="cart"] .badge');
            cartBadges.forEach(badge => badge.textContent = data.cart_count);
            
            // Update favorites badge
            const favBadges = document.querySelectorAll('.nav-link[data-section="favorites"] .badge');
            favBadges.forEach(badge => badge.textContent = data.favorites_count);
        });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const checkoutModal = document.getElementById('checkoutModal');
    const passwordModal = document.getElementById('userPasswordModal');
    
    if (event.target == checkoutModal) {
        closeCheckoutModal();
    }
    if (event.target == passwordModal) {
        closeUserPasswordModal();
    }
}