# Pastry - Bakery Management System

A school project for a simple bakery management system built with PHP and MySQL.

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Endpoints](#api-endpoints)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Security Features](#security-features)

## 🎯 Project Overview

Pastry is a full-stack web application built with PHP and MySQL that enables bakery businesses to:
- Manage product catalogs and inventory
- Process customer orders seamlessly
- Handle employee and staff management
- Provide administrative dashboard for business oversight
- Enable customers to browse and purchase products online

## ✨ Features

### Customer Features
- Browse available pastry products
- Place orders online
- User account management
- Order tracking

### Employee Features
- Access employee dashboard
- View assigned tasks
- Manage orders
- Track inventory items

### Admin Features
- Complete administrative dashboard
- User and employee management
- Product and inventory management
- Order management and tracking
- Analytics and reporting
- Admin authentication system

### Technical Features
- Secure user authentication
- Input sanitization and validation
- RESTful API endpoints
- Responsive web interface
- MySQL database with relational schema

## 🛠 Tech Stack

- **Backend**: PHP 7.0+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP)
- **Architecture**: MVC-inspired REST API

## 📦 Installation

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- MySQL Server running
- Web browser

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Pastry
   ```

2. **Place files in XAMPP directory**
   ```bash
   cp -r Pastry C:\xampp\htdocs\
   ```

3. **Create the database**
   ```bash
   mysql -u root -p < database/database.sql
   ```

4. **Configure database connection**
   - Update `database/db.php` with your database credentials if needed:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "pastry";
   ```

5. **Start XAMPP Services**
   - Start Apache and MySQL from XAMPP Control Panel

6. **Access the application**
   - Customer portal: `http://localhost/Pastry/index.html`
   - Admin login: `http://localhost/Pastry/admin_login.php`

## ⚙️ Configuration

### Database Configuration
Edit `database/db.php` to set your database credentials:
- `$servername`: Database host (default: localhost)
- `$username`: Database user (default: root)
- `$password`: Database password
- `$dbname`: Database name (default: pastry)

### File Permissions
Ensure the `uploads/` directory is writable:
```bash
chmod 755 uploads/
```

## 🔌 API Endpoints

The application provides RESTful API endpoints organized by module:

- **Admin API** (`api/admin_api/`) - Admin management operations
- **Customer API** (`api/customer_api/`) - Customer-related operations
- **Employee API** (`api/employee_api/`) - Employee management
- **Order API** (`api/order_api/`) - Order processing
- **Product API** (`api/product_api/`) - Product management
- **User API** (`api/user_api/`) - User management and authentication

## 💻 Usage

### For Customers
1. Visit `index.html` to browse products
2. Create an account or login
3. Add items to cart and place orders
4. Track order status

### For Admin
1. Navigate to `admin_login.php`
2. Login with admin credentials
3. Access `admin.php` dashboard
4. Manage products, orders, employees, and users

### For Employees
1. Login through customer portal with employee credentials
2. Access employee dashboard
3. View and manage assigned tasks

## 🗄️ Database Schema

The application uses a relational MySQL database with tables for:
- `users` - Customer accounts
- `admins` - Admin accounts
- `employees` - Staff management
- `products` - Bakery items
- `orders` - Customer orders
- `order_items` - Order details
- `inventory` - Stock management

For detailed schema, see `database/database.sql`

## Notes

This is a student project and has some bugs. Not recommended for production use.
