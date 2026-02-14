-- Users Table (Updated with custom ID and archive)
CREATE TABLE users (
  id VARCHAR(10) PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  is_archived TINYINT(1) NOT NULL DEFAULT 0,
  archived_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table (Updated with role system)
CREATE TABLE admins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  role ENUM('super_admin', 'sub_admin') DEFAULT 'sub_admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table (Updated with custom ID)
CREATE TABLE products (
  id VARCHAR(10) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2) NOT NULL,
  category VARCHAR(50) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  picture VARCHAR(255),
  is_archived TINYINT(1) NOT NULL DEFAULT 0,
  archived_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Employees Table (Updated with custom ID)
CREATE TABLE employees (
  id VARCHAR(10) PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(50) NOT NULL,
  position VARCHAR(50) NOT NULL,
  salary DECIMAL(10, 2),
  hire_date DATE NOT NULL,
  status ENUM('active', 'inactive') DEFAULT 'active',
  is_archived TINYINT(1) NOT NULL DEFAULT 0,
  archived_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table (Updated with custom ID)
CREATE TABLE orders (
  id VARCHAR(10) PRIMARY KEY,
  user_id VARCHAR(10) NOT NULL,
  total_amount DECIMAL(10, 2) NOT NULL,
  status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
  payment_method VARCHAR(50),
  shipping_address TEXT,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id VARCHAR(10) NOT NULL,
  product_id VARCHAR(10) NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Categories Table (Optional for product categories)
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) UNIQUE NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some default categories
INSERT INTO categories (name, description) VALUES
('Cakes', 'Various types of cakes'),
('Breads', 'Fresh baked breads'),
('Pastries', 'Sweet and savory pastries'),
('Cookies', 'Homemade cookies'),
('Desserts', 'Special desserts');

-- Insert a default admin (password: admin123 - hashed with password_hash)
INSERT INTO admins (username, email, password, full_name, role) VALUES
('admin', 'admin@juliesbakery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin');

-- ============================================================
-- ARCHIVE SYSTEM
-- If you already have the tables created without is_archived,
-- run these ALTER statements to add the columns:
-- ============================================================
-- ALTER TABLE products
--   ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN archived_at TIMESTAMP NULL DEFAULT NULL;

-- ALTER TABLE employees
--   ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN archived_at TIMESTAMP NULL DEFAULT NULL;

-- ALTER TABLE users
--   ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN archived_at TIMESTAMP NULL DEFAULT NULL;

-- ALTER TABLE admins
--   ADD COLUMN role ENUM('super_admin', 'sub_admin') DEFAULT 'sub_admin';
--
-- UPDATE admins SET role = 'super_admin' WHERE username = 'admin';
-- ============================================================