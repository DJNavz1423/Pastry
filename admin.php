<?php
require_once './database/db.php';
require_admin_login();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admins WHERE id = $admin_id";
$admin_result = $conn->query($admin_query);
$admin = $admin_result->fetch_assoc();
$is_super_admin = ($admin['role'] === 'super_admin');

// Get statistics for dashboard
$total_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_archived = 0")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_customers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_employees = $conn->query("SELECT COUNT(*) as count FROM employees WHERE is_archived = 0")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/admin.css?v=<?php echo time(); ?>">

    <title>Julie's Bakery | Admin Dashboard</title>
</head>

<body>
    <aside class="sidebar">
        <nav>
            <a href="#home" class="sidebar-header"><i class='bx bx-cupcake'></i>
                <h1>Julie's Bakery</h1>
            </a>

            <ul>
                <h2><span>Main Menu</span>
                    <div class="menu-divider"></div>
                </h2>

                <li><a href="#" class="nav-link active" data-section="dashboard"><span class="material-symbols-outlined">dashboard</span>Dashboard</a></li>
                <li><a href="#" class="nav-link" data-section="products-section"><span class="material-symbols-outlined">inventory</span>Products</a></li>
                <li><a href="#" class="nav-link" data-section="employees"><span class="material-symbols-outlined">person</span>Employees</a></li>
                <li><a href="#" class="nav-link" data-section="orders"><span class="material-symbols-outlined">orders</span>Orders</a></li>
                <?php if ($is_super_admin): ?>
                <li><a href="#" class="nav-link" data-section="customers"><span class="material-symbols-outlined">group</span>Customers</a></li>
                <li><a href="#" class="nav-link" data-section="archive"><span class="material-symbols-outlined">archive</span>Archive</a></li>
                <?php endif; ?>

                <h2><span>Account</span>
                    <div class="menu-divider"></div>
                </h2>
                <li><a href="#" class="nav-link" data-section="profile"><span class="material-symbols-outlined">account_circle</span>Profile</a></li>
                <?php if ($is_super_admin): ?>
                <li><a href="#" class="nav-link" data-section="settings"><span class="material-symbols-outlined">settings</span>Settings</a></li>
                <?php endif; ?>
                <li>
                    <form action="./formHandlers/logout_handler.php" method="post"><button id="logout_button"><a href="./formHandlers/logout_handler.php"><span class="material-symbols-outlined">logout</span>Logout</a></button></form>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="grid-column-2">
        <main>
            <!-- Dashboard Section -->
            <section id="dashboard" class="active">
                <div class="dashboard-header">
                    <h1>Dashboard Overview</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($admin['full_name']); ?>!</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3>Total Revenue</h3>
                            <div class="stat-icon red">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="stat-value">₱<?php echo number_format($total_revenue, 2); ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 12% from last month
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3>Total Orders</h3>
                            <div class="stat-icon blue">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $total_orders; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 8% from last month
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3>Total Customers</h3>
                            <div class="stat-icon green">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $total_customers; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> 15% from last month
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3>Total Products</h3>
                            <div class="stat-icon orange">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $total_products; ?></div>
                        <div class="stat-change negative">
                            <i class="fas fa-arrow-down"></i> 3% from last month
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="activity-details">
                            <h4>New order received</h4>
                            <p>Order #1234 - 2 hours ago</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <h4>New customer registered</h4>
                            <p>John Doe - 5 hours ago</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="activity-details">
                            <h4>Product stock updated</h4>
                            <p>Chocolate Cake - 1 day ago</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Products Section -->
            <section id="products-section">
                <div class="section-header">
                    <h2>Products Management</h2>
                    <button class="btn btn-primary" onclick="openProductModal()">
                        <i class="fas fa-plus"></i> Add New Product
                    </button>
                </div>

                <div class="data-table-container">
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="productSearch" placeholder="Search products..." onkeyup="searchProducts()">
                        </div>
                    </div>
                    <table class="data-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Products will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Employees Section -->
            <section id="employees">
                <div class="section-header">
                    <h2>Employees Management</h2>
                    <button class="btn btn-primary" onclick="openEmployeeModal()">
                        <i class="fas fa-plus"></i> Add New Employee
                    </button>
                </div>

                <div class="data-table-container">
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="employeeSearch" placeholder="Search employees..." onkeyup="searchEmployees()">
                        </div>
                    </div>
                    <table class="data-table" id="employeesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            <!-- Employees will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Orders Section -->
            <section id="orders">
                <div class="section-header">
                    <h2>Orders Management</h2>
                </div>

                <div class="data-table-container">
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="orderSearch" placeholder="Search orders..." onkeyup="searchOrders()">
                        </div>
                    </div>
                    <table class="data-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <!-- Orders will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Customers Section -->
            <section id="customers">
                <div class="section-header">
                    <h2>Customers Management</h2>
                </div>

                <div class="data-table-container">
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="customerSearch" placeholder="Search customers..." onkeyup="searchCustomers()">
                        </div>
                    </div>
                    <table class="data-table" id="customersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <!-- Customers will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Archive Section -->
            <section id="archive">
                <div class="section-header">
                    <h2>Archive</h2>
                </div>

                <!-- Archive Tabs -->
                <div class="archive-tabs">
                    <button class="archive-tab active" data-tab="archive-products" onclick="switchArchiveTab(this, 'archive-products')">
                        <i class="fas fa-box"></i> Products
                    </button>
                    <button class="archive-tab" data-tab="archive-employees" onclick="switchArchiveTab(this, 'archive-employees')">
                        <i class="fas fa-user"></i> Employees
                    </button>
                    <button class="archive-tab" data-tab="archive-customers" onclick="switchArchiveTab(this, 'archive-customers')">
                        <i class="fas fa-users"></i> Customers
                    </button>
                </div>

                <!-- Archived Products Table -->
                <div id="archive-products" class="archive-panel active">
                    <div class="data-table-container" style="margin-top:20px;">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="archivedProductSearch" placeholder="Search archived products..." onkeyup="searchTable('archivedProductSearch','archivedProductsTable')">
                            </div>
                        </div>
                        <table class="data-table" id="archivedProductsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Archived On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archivedProductsTableBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Archived Employees Table -->
                <div id="archive-employees" class="archive-panel">
                    <div class="data-table-container" style="margin-top:20px;">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="archivedEmployeeSearch" placeholder="Search archived employees..." onkeyup="searchTable('archivedEmployeeSearch','archivedEmployeesTable')">
                            </div>
                        </div>
                        <table class="data-table" id="archivedEmployeesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Archived On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archivedEmployeesTableBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Archived Customers Table -->
                <div id="archive-customers" class="archive-panel">
                    <div class="data-table-container" style="margin-top:20px;">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="archivedCustomerSearch" placeholder="Search archived customers..." onkeyup="searchTable('archivedCustomerSearch','archivedCustomersTable')">
                            </div>
                        </div>
                        <table class="data-table" id="archivedCustomersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Archived On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archivedCustomersTableBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Profile Section -->
            <section id="profile">
                <div class="section-header">
                    <h2>Admin Profile</h2>
                </div>
                <div class="data-table-container">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($admin['full_name']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($admin['email']); ?>" readonly>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings">
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

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="productModalTitle">Add New Product</h2>
                <button class="close-modal" onclick="closeProductModal()">&times;</button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <div class="form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" id="product_name" name="product_name" required>
                    </div>

                    <div class="form-group">
                        <label for="product_description">Description</label>
                        <textarea id="product_description" name="product_description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_category">Category *</label>
                        <select id="product_category" name="product_category" required onchange="checkAddCategory(this)">
                            <option value="">Select Category</option>
                            <?php
                            $categories = $conn->query("SELECT * FROM categories ORDER BY name");
                            while($cat = $categories->fetch_assoc()) {
                                echo "<option value='{$cat['name']}'>{$cat['name']}</option>";
                            }
                            ?>
                            <option value="__ADD_NEW__" style="color: var(--red); font-weight: 600;">+ Add New Category</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_price">Price (₱) *</label>
                        <input type="number" id="product_price" name="product_price" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="product_quantity">Quantity *</label>
                        <input type="number" id="product_quantity" name="product_quantity" required>
                    </div>

                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="file-upload" onclick="document.getElementById('product_picture').click()">
                            <input type="file" id="product_picture" name="product_picture" accept="image/*" onchange="previewImage(event)">
                            <label class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload image</p>
                                <p style="font-size: 12px;">PNG, JPG, GIF up to 5MB</p>
                            </label>
                        </div>
                        <img id="imagePreview" class="image-preview" alt="Preview">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Employee Modal -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="employeeModalTitle">Add New Employee</h2>
                <button class="close-modal" onclick="closeEmployeeModal()">&times;</button>
            </div>
            <form id="employeeForm">
                <div class="modal-body">
                    <input type="hidden" id="employee_id" name="employee_id">
                    
                    <div class="form-group">
                        <label for="employee_name">Full Name *</label>
                        <input type="text" id="employee_name" name="employee_name" required>
                    </div>

                    <div class="form-group">
                        <label for="employee_email">Email *</label>
                        <input type="email" id="employee_email" name="employee_email" required>
                    </div>

                    <div class="form-group">
                        <label for="employee_phone">Phone *</label>
                        <input type="text" id="employee_phone" name="employee_phone" required>
                    </div>

                    <div class="form-group">
                        <label for="employee_position">Position *</label>
                        <input type="text" id="employee_position" name="employee_position" required>
                    </div>

                    <div class="form-group">
                        <label for="employee_salary">Salary (₱)</label>
                        <input type="number" id="employee_salary" name="employee_salary" step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="employee_hire_date">Hire Date *</label>
                        <input type="date" id="employee_hire_date" name="employee_hire_date" required>
                    </div>

                    <div class="form-group">
                        <label for="employee_status">Status *</label>
                        <select id="employee_status" name="employee_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <?php if ($is_super_admin): ?>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="employee_make_admin" name="employee_make_admin" style="width: auto; cursor: pointer;">
                            <span>Make this employee a Sub Admin (limited dashboard access)</span>
                        </label>
                        <small style="color: var(--gray-light); display: block; margin-top: 5px;">
                            Sub admins can access: Dashboard, Products, Orders, and Employees only.
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEmployeeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Category</h2>
                <button class="close-modal" onclick="closeCategoryModal()">&times;</button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_name">Category Name *</label>
                        <input type="text" id="category_name" name="category_name" required placeholder="e.g., Cupcakes">
                    </div>

                    <div class="form-group">
                        <label for="category_description">Description</label>
                        <textarea id="category_description" name="category_description" placeholder="Optional description for this category"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Edit Modal -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="customerModalTitle">Edit Customer</h2>
                <button class="close-modal" onclick="closeCustomerModal()">&times;</button>
            </div>
            <form id="customerForm">
                <div class="modal-body">
                    <input type="hidden" id="customer_id" name="customer_id">
                    
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email *</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Phone *</label>
                        <input type="text" id="customer_phone" name="customer_phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass PHP variable to JavaScript
        const isSuperAdmin = <?php echo $is_super_admin ? 'true' : 'false'; ?>;
    </script>
    <script src="./js/admin-dashboard.js"></script>
</body>
</html>