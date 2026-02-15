<?php
session_start();

if(!isset($_SESSION['email'])){
  header('Location: login_page.html');
  exit;
}

require './database/db.php';

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM users where email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
  $user = $result->fetch_assoc();

  $fullName = $user['full_name'];
  $firstName = explode(' ', $fullName)[0];

  $_SESSION['user_id'] = $user['id'];
  $_SESSION['full_name'] = $fullName;
  $_SESSION['first_name'] = $firstName;
  $_SESSION['phone'] = $user['phone'];
} else{
  session_destroy();
  header('Location: login_page.html');
  exit;
}

// Get cart count
$cart_count_query = "SELECT COUNT(*) as count FROM cart WHERE user_id = '{$user['id']}'";
$cart_count = $conn->query($cart_count_query)->fetch_assoc()['count'] ?? 0;

// Get favorites count
$fav_count_query = "SELECT COUNT(*) as count FROM favorites WHERE user_id = '{$user['id']}'";
$fav_count = $conn->query($fav_count_query)->fetch_assoc()['count'] ?? 0;

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Merienda Font-->
  <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">
  <!-- Lato Font -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Merienda:wght@300..900&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">
  <!-- Open Sans Font -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Merienda:wght@300..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">

  <!-- Google Icons -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <!-- Boxicons CSS -->
   <!-- Basic Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
<!-- Filled Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
<!-- Brand Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">

  <!-- Font Awesome CSS -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- CSS Stylesheet -->
  <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./css/user-dashboard.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./css/admin-dashboard.css?v=<?php echo time(); ?>">
  
  <title>Julie's Bakery | User Dashboard</title>
</head>

<body>
  <aside class="sidebar">
    <nav>
      <a href="#home" class="sidebar-header"><i class='bx bx-cupcake'></i><h1>Julie's Bakery</h1></a>

      <ul>
        <h2><span>Main Menu</span><div class="menu-divider"></div></h2>

        <li><a href="#" class="nav-link active" data-section="dashboard"><span class="material-symbols-outlined">dashboard</span>Dashboard</a></li>
        <li><a href="#" class="nav-link" data-section="shop"><span class="material-symbols-outlined">shopping_basket</span>Shop</a></li>
        <li><a href="#" class="nav-link" data-section="favorites"><span class="material-symbols-outlined">favorite</span>Favorites <span class="badge"><?php echo $fav_count; ?></span></a></li>
        <li><a href="#" class="nav-link" data-section="cart"><span class="material-symbols-outlined">shopping_bag</span>Your Cart <span class="badge"><?php echo $cart_count; ?></span></a></li>

        <h2><span>Account</span><div class="menu-divider"></div></h2>
        <li><a href="#" class="nav-link" data-section="profile"><span class="material-symbols-outlined">account_circle</span>Profile</a></li>
        <li><a href="#" class="nav-link" data-section="settings"><span class="material-symbols-outlined">settings</span>Settings</a></li>
        <li><form action="./formHandlers/logout_handler.php" method="post"><button id="logout_button"><a href="./formHandlers/logout_handler.php"><span class="material-symbols-outlined">logout</span>Logout</a></button></form></li>
      </ul>

      <div class="user-account">
        <div class="user-profile">
          <img src="./images/blank-pfp1.jpg" alt="Profile Image">
          <div class="user-details">
            <h3><?php echo htmlspecialchars($firstName); ?></h3>
            <h4><?php echo htmlspecialchars($user['email']); ?></h4>
          </div>
        </div>
      </div>
    </nav>
  </aside>

  <div class="grid-column-2">
  <main>
    <!-- Dashboard Section -->
    <section id="dashboard" class="active">
      <div class="dashboard-hero-section">
        <div class="dashboard-hero-content">
          <div class="hero-content-col-1">
            <p>Deal of the weekend</p>
            <h1>Hello, <?php echo htmlspecialchars($firstName); ?>!</h1>
            <p>Get FREE delivery on every weekend</p>
            <a href="#" onclick="switchSection('shop')"><button type="button" class="cta-button">Check Menu</button></a>
          </div>

          <div class="hero-content-col-2">
            <figure>
              <img src="./images/blueberry-cheesecake.jpg" alt="blueberry-cheesecake">
            </figure>
          </div>
        </div>
      </div>

      <div class="dashboard-product-section sub-section">
        <div class="product-subsection">
          <!-- Categories -->
          <div class="dashboard-products-container">
            <div class="heading-nav">
              <h2>Categories</h2>
              <a href="#" onclick="switchSection('shop')">View All</a>
            </div>

            <div class="category-container" id="dashboardCategories">
              <!-- Categories loaded via AJAX -->
            </div>
          </div>

          <!-- Best Selling -->
          <div class="dashboard-products-container">
            <div class="heading-nav">
              <h2>Best Selling Products</h2>
              <a href="#" onclick="switchSection('shop')">View All</a>
            </div>

            <div class="trending-container" id="bestSellingProducts">
              <!-- Products loaded via AJAX -->
            </div>
          </div>
        </div>

        <!-- Cart Preview -->
        <div class="cart-subsection">
          <div class="heading-nav">
            <h2>My Cart</h2>
            <a href="#" onclick="switchSection('cart')">View All</a>
          </div>

          <div class="cart-product-container" id="dashboardCart">
            <!-- Cart items loaded via AJAX -->
          </div>
        </div>
      </div>
    </section>

    <!-- Shop Section -->
    <section id="shop" class="sub-section">
      <div class="section-header">
        <h2>Shop</h2>
      </div>

      <div class="category-tabs">
        <button class="category-tab active" data-category="all" onclick="filterProducts('all')">
          <i class="fas fa-th"></i> All
        </button>
        <!-- Category tabs loaded via AJAX -->
      </div>

      <div class="products-grid" id="shopProducts">
        <!-- Products loaded via AJAX -->
      </div>
    </section>

    <!-- Favorites Section -->
    <section id="favorites" class="sub-section">
      <div class="section-header">
        <h2>Your Favorites</h2>
      </div>

      <div class="category-tabs">
        <button class="category-tab active" data-category="all" onclick="filterFavorites('all')">
          <i class="fas fa-heart"></i> All
        </button>
        <!-- Category tabs loaded via AJAX -->
      </div>

      <div class="products-grid" id="favoriteProducts">
        <!-- Favorites loaded via AJAX -->
      </div>
    </section>

    <!-- Cart Section -->
    <section id="cart" class="sub-section">
      <div class="section-header">
        <h2>Your Shopping Cart</h2>
      </div>

      <div class="cart-full-container">
        <div class="cart-items-section" id="cartItems">
          <!-- Cart items loaded via AJAX -->
        </div>

        <div class="cart-summary">
          <h3>Order Summary</h3>
          <div class="summary-row">
            <span>Subtotal:</span>
            <span id="cartSubtotal">₱0.00</span>
          </div>
          <div class="summary-row">
            <span>Delivery Fee:</span>
            <span id="deliveryFee">₱50.00</span>
          </div>
          <div class="summary-row total">
            <span>Total:</span>
            <span id="cartTotal">₱0.00</span>
          </div>
          <button class="btn btn-primary btn-block" onclick="proceedToCheckout()">
            <i class="fas fa-shopping-cart"></i> Proceed to Checkout
          </button>
        </div>
      </div>
    </section>

    <!-- Profile Section -->
    <section id="profile" class="sub-section">
      <div class="section-header">
        <h2>Your Profile</h2>
      </div>
      <div class="data-table-container">
        <form id="userProfileForm">
          <div class="modal-body">
            <input type="hidden" id="user_id" value="<?php echo $user['id']; ?>">
            
            <div class="form-group">
              <label for="user_fullname">Full Name *</label>
              <input type="text" id="user_fullname" name="user_fullname" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="form-group">
              <label for="user_email">Email *</label>
              <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
              <label for="user_phone">Phone *</label>
              <input type="text" id="user_phone" name="user_phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
              <label for="user_address">Delivery Address</label>
              <textarea id="user_address" name="user_address" placeholder="Enter your complete address for delivery"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
              <label>Password</label>
              <button type="button" class="btn btn-secondary" onclick="openUserPasswordModal()">
                <i class="fas fa-key"></i> Change Password
              </button>
            </div>

            <div class="form-group" style="margin-top: 20px;">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Profile Changes
              </button>
            </div>
          </div>
        </form>
      </div>
    </section>

    <!-- Settings Section -->
    <section id="settings" class="sub-section">
      <div class="section-header">
        <h2>Settings</h2>
      </div>
      <div class="data-table-container">
        <div class="modal-body">
          <h3>Application Settings</h3>
          <p>Settings management coming soon...</p>
        </div>
      </div>
    </section>
  </main>
</div>

  <!-- Checkout Modal -->
  <div id="checkoutModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Checkout</h2>
        <button class="close-modal" onclick="closeCheckoutModal()">&times;</button>
      </div>
      <form id="checkoutForm">
        <div class="modal-body">
          <h3>Delivery Information</h3>
          <div class="form-group">
            <label for="checkout_address">Delivery Address *</label>
            <textarea id="checkout_address" name="checkout_address" required placeholder="Enter your complete delivery address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label for="checkout_phone">Contact Number *</label>
            <input type="text" id="checkout_phone" name="checkout_phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
          </div>

          <h3 style="margin-top: 30px;">Payment Method</h3>
          <div class="payment-methods">
            <label class="payment-option">
              <input type="radio" name="payment_method" value="cod" checked>
              <span class="payment-label">
                <i class="fas fa-money-bill-wave"></i>
                Cash on Delivery
              </span>
            </label>
            <label class="payment-option">
              <input type="radio" name="payment_method" value="gcash">
              <span class="payment-label">
                <i class="fas fa-mobile-alt"></i>
                GCash
              </span>
            </label>
          </div>

          <div class="checkout-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
              <span>Subtotal:</span>
              <span id="checkoutSubtotal">₱0.00</span>
            </div>
            <div class="summary-row">
              <span>Delivery Fee:</span>
              <span>₱50.00</span>
            </div>
            <div class="summary-row total">
              <span>Total Amount:</span>
              <span id="checkoutTotal">₱0.00</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeCheckoutModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Place Order</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div id="userPasswordModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Change Password</h2>
        <button class="close-modal" onclick="closeUserPasswordModal()">&times;</button>
      </div>
      <form id="userPasswordForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="user_current_password">Current Password *</label>
            <input type="password" id="user_current_password" name="current_password" required>
          </div>

          <div class="form-group">
            <label for="user_new_password">New Password *</label>
            <input type="password" id="user_new_password" name="new_password" required minlength="6">
            <small style="color: var(--gray-light);">Minimum 6 characters</small>
          </div>

          <div class="form-group">
            <label for="user_confirm_password">Confirm New Password *</label>
            <input type="password" id="user_confirm_password" name="confirm_password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeUserPasswordModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const userId = '<?php echo $user['id']; ?>';
  </script>
  <script src="./js/user-dashboard.js?v=<?php echo time(); ?>"></script>
</body>
</html>
<?php $conn->close(); ?>