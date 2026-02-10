// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadProducts();
    loadEmployees();
    loadOrders();
    loadCustomers();

    // Navigation click handlers
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Hide all sections
            const sections = document.querySelectorAll('main section');
            sections.forEach(section => section.classList.remove('active'));
            
            // Show selected section
            const sectionId = this.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
        });
    });
});

// Product Modal Functions
function openProductModal(productId = null) {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    const title = document.getElementById('productModalTitle');
    
    form.reset();
    document.getElementById('imagePreview').classList.remove('active');
    
    if (productId) {
        title.textContent = 'Edit Product';
        // Load product data
        fetch(`./api/get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('product_id').value = data.id;
                document.getElementById('product_name').value = data.name;
                document.getElementById('product_description').value = data.description;
                document.getElementById('product_category').value = data.category;
                document.getElementById('product_price').value = data.price;
                document.getElementById('product_quantity').value = data.quantity;
                
                if (data.picture) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = './uploads/products/' + data.picture;
                    preview.classList.add('active');
                }
            });
    } else {
        title.textContent = 'Add New Product';
        document.getElementById('product_id').value = '';
    }
    
    modal.classList.add('active');
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
}

function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.add('active');
        }
        reader.readAsDataURL(file);
    }
}

// Product Form Submission
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productId = document.getElementById('product_id').value;
    const url = productId ? './api/update_product.php' : './api/create_product.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeProductModal();
            loadProducts();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Load Products
function loadProducts() {
    fetch('./api/get_products.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';
            
            data.forEach(product => {
                const row = `
                    <tr>
                        <td>${product.id}</td>
                        <td>
                            ${product.picture ? 
                                `<img src="./uploads/products/${product.picture}" alt="${product.name}" class="product-img">` : 
                                '<img src="./uploads/products/no-image.png" alt="No image" class="product-img">'}
                        </td>
                        <td>${product.name}</td>
                        <td>${product.category}</td>
                        <td>₱${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.quantity}</td>
                        <td class="action-buttons">
                            <button class="action-btn edit" onclick="openProductModal(${product.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteProduct(${product.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch('./api/delete_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadProducts();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function searchProducts() {
    const input = document.getElementById('productSearch');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('productsTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Employee Modal Functions
function openEmployeeModal(employeeId = null) {
    const modal = document.getElementById('employeeModal');
    const form = document.getElementById('employeeForm');
    const title = document.getElementById('employeeModalTitle');
    
    form.reset();
    
    if (employeeId) {
        title.textContent = 'Edit Employee';
        fetch(`./api/get_employee.php?id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('employee_id').value = data.id;
                document.getElementById('employee_name').value = data.full_name;
                document.getElementById('employee_email').value = data.email;
                document.getElementById('employee_phone').value = data.phone;
                document.getElementById('employee_position').value = data.position;
                document.getElementById('employee_salary').value = data.salary;
                document.getElementById('employee_hire_date').value = data.hire_date;
                document.getElementById('employee_status').value = data.status;
            });
    } else {
        title.textContent = 'Add New Employee';
        document.getElementById('employee_id').value = '';
    }
    
    modal.classList.add('active');
}

function closeEmployeeModal() {
    document.getElementById('employeeModal').classList.remove('active');
}

// Employee Form Submission
document.getElementById('employeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const employeeId = document.getElementById('employee_id').value;
    const url = employeeId ? './api/update_employee.php' : './api/create_employee.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeEmployeeModal();
            loadEmployees();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Load Employees
function loadEmployees() {
    fetch('./api/get_employees.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('employeesTableBody');
            tbody.innerHTML = '';
            
            data.forEach(employee => {
                const row = `
                    <tr>
                        <td>${employee.id}</td>
                        <td>${employee.full_name}</td>
                        <td>${employee.email}</td>
                        <td>${employee.phone}</td>
                        <td>${employee.position}</td>
                        <td><span class="status-badge ${employee.status}">${employee.status}</span></td>
                        <td class="action-buttons">
                            <button class="action-btn edit" onclick="openEmployeeModal(${employee.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteEmployee(${employee.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee?')) {
        fetch('./api/delete_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: employeeId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadEmployees();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function searchEmployees() {
    const input = document.getElementById('employeeSearch');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('employeesTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Load Orders
function loadOrders() {
    fetch('./api/get_orders.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = '';
            
            data.forEach(order => {
                const row = `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.customer_name}</td>
                        <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
                        <td><span class="status-badge ${order.status}">${order.status}</span></td>
                        <td>${new Date(order.order_date).toLocaleDateString()}</td>
                        <td class="action-buttons">
                            <button class="action-btn view" onclick="viewOrder(${order.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit" onclick="updateOrderStatus(${order.id})" title="Update Status">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function viewOrder(orderId) {
    alert('View order details for order #' + orderId);
    // Implement order details view
}

function updateOrderStatus(orderId) {
    const newStatus = prompt('Enter new status (pending/processing/completed/cancelled):');
    if (newStatus) {
        fetch('./api/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: orderId, status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadOrders();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function searchOrders() {
    const input = document.getElementById('orderSearch');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('ordersTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Load Customers
function loadCustomers() {
    fetch('./api/get_customers.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('customersTableBody');
            tbody.innerHTML = '';
            
            data.forEach(customer => {
                const row = `
                    <tr>
                        <td>${customer.id}</td>
                        <td>${customer.full_name}</td>
                        <td>${customer.email}</td>
                        <td>${customer.phone}</td>
                        <td>${new Date(customer.created_at).toLocaleDateString()}</td>
                        <td class="action-buttons">
                            <button class="action-btn view" onclick="viewCustomer(${customer.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function viewCustomer(customerId) {
    alert('View customer details for customer #' + customerId);
    // Implement customer details view
}

function searchCustomers() {
    const input = document.getElementById('customerSearch');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('customersTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const productModal = document.getElementById('productModal');
    const employeeModal = document.getElementById('employeeModal');
    
    if (event.target == productModal) {
        closeProductModal();
    }
    if (event.target == employeeModal) {
        closeEmployeeModal();
    }
}
