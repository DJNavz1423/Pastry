// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadProducts();
    loadEmployees();
    loadOrders();
    if (typeof isSuperAdmin !== 'undefined' && isSuperAdmin) {
        loadCustomers();
    }

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

            // Refresh archive tables when entering the archive section
            if (sectionId === 'archive') {
                loadArchivedProducts();
                loadArchivedEmployees();
                loadArchivedCustomers();
            }
        });
    });
});

// Category dropdown check
function checkAddCategory(select) {
    if (select.value === '__ADD_NEW__') {
        openCategoryModal();
        // Reset to empty so user must select after adding
        select.value = '';
    }
}

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
        fetch(`./api/product_api/get_product.php?id=${productId}`)
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
    const url = productId ? './api/product_api/update_product.php' : './api/product_api/create_product.php';
    
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
    fetch('./api/product_api/get_products.php')
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
                            <button class="action-btn edit" onclick="openProductModal('${product.id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteProduct('${product.id}')" title="Delete">
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
    if (confirm('Move this product to archive? It can be restored or permanently deleted later.')) {
        fetch('./api/product_api/delete_product.php', {
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
        fetch(`./api/employee_api/get_employee.php?id=${employeeId}`)
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
    
    // Add make_admin checkbox value
    const makeAdminCheckbox = document.getElementById('employee_make_admin');
    if (makeAdminCheckbox) {
        formData.append('employee_make_admin', makeAdminCheckbox.checked ? 'true' : 'false');
    }
    
    const url = employeeId ? './api/employee_api/update_employee.php' : './api/employee_api/create_employee.php';
    
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
    fetch('./api/employee_api/get_employees.php')
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
                            <button class="action-btn edit" onclick="openEmployeeModal('${employee.id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteEmployee('${employee.id}')" title="Delete">
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
    if (confirm('Move this employee to archive? They can be restored or permanently deleted later.')) {
        fetch('./api/employee_api/delete_employee.php', {
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
    fetch('./api/order_api/get_orders.php')
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
        fetch('./api/order_api/update_order_status.php', {
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
    fetch('./api/customer_api/get_customers.php')
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
                            <button class="action-btn edit" onclick="openCustomerModal('${customer.id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteCustomer('${customer.id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function openCustomerModal(customerId) {
    const modal = document.getElementById('customerModal');
    const form = document.getElementById('customerForm');
    
    form.reset();
    
    fetch(`./api/customer_api/get_customer.php?id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('customer_id').value = data.id;
            document.getElementById('customer_name').value = data.full_name;
            document.getElementById('customer_email').value = data.email;
            document.getElementById('customer_phone').value = data.phone;
        });
    
    modal.classList.add('active');
}

function closeCustomerModal() {
    document.getElementById('customerModal').classList.remove('active');
}

function deleteCustomer(customerId) {
    if (confirm('Move this customer to archive? They can be restored or permanently deleted later.')) {
        fetch('./api/customer_api/delete_customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: customerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadCustomers();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

// Customer Form Submission
document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('./api/customer_api/update_customer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCustomerModal();
            loadCustomers();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

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
    const categoryModal = document.getElementById('categoryModal');
    const customerModal = document.getElementById('customerModal');
    
    if (event.target == productModal) {
        closeProductModal();
    }
    if (event.target == employeeModal) {
        closeEmployeeModal();
    }
    if (event.target == categoryModal) {
        closeCategoryModal();
    }
    if (event.target == customerModal) {
        closeCustomerModal();
    }
}

// ============================================================
// CATEGORY MODAL
// ============================================================
function openCategoryModal() {
    const modal = document.getElementById('categoryModal');
    document.getElementById('categoryForm').reset();
    modal.classList.add('active');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.remove('active');
}

// Category Form Submission
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('./api/product_api/create_category.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCategoryModal();
            // Refresh the category dropdown
            refreshCategoryDropdown(data.category_name);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

function refreshCategoryDropdown(selectCategoryName = null) {
    fetch('./api/product_api/get_categories.php')
        .then(response => response.json())
        .then(categories => {
            const select = document.getElementById('product_category');
            const currentValue = select.value;
            
            // Clear and rebuild options
            select.innerHTML = '<option value="">Select Category</option>';
            
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.name;
                option.textContent = cat.name;
                select.appendChild(option);
            });
            
            // Add "Add New Category" option at the end
            const addOption = document.createElement('option');
            addOption.value = '__ADD_NEW__';
            addOption.textContent = '+ Add New Category';
            addOption.style.color = 'var(--red)';
            addOption.style.fontWeight = '600';
            select.appendChild(addOption);
            
            // Select the newly added category or restore previous
            if (selectCategoryName) {
                select.value = selectCategoryName;
            } else if (currentValue && currentValue !== '__ADD_NEW__') {
                select.value = currentValue;
            }
        });
}

// ============================================================
// ARCHIVED CUSTOMERS
// ============================================================
function loadArchivedCustomers() {
    fetch('./api/customer_api/get_archived_customer.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('archivedCustomersTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:30px;">No archived customers.</td></tr>';
                return;
            }

            data.forEach(customer => {
                const archivedDate = customer.archived_at
                    ? new Date(customer.archived_at).toLocaleDateString()
                    : '—';
                const row = `
                    <tr>
                        <td>${customer.id}</td>
                        <td>${customer.full_name}</td>
                        <td>${customer.email}</td>
                        <td>${customer.phone}</td>
                        <td>${archivedDate}</td>
                        <td class="action-buttons">
                            <button class="action-btn restore" onclick="restoreCustomer('${customer.id}')" title="Restore">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="action-btn delete" onclick="permanentlyDeleteCustomer('${customer.id}')" title="Delete Permanently">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function restoreCustomer(customerId) {
    if (confirm('Restore this customer to the active list?')) {
        fetch('./api/customer_api/archived_customer_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: customerId, action: 'restore' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                loadArchivedCustomers();
                loadCustomers();
            }
        });
    }
}

function permanentlyDeleteCustomer(customerId) {
    if (confirm('⚠️ Permanently delete this customer? This CANNOT be undone.')) {
        fetch('./api/customer_api/archived_customer_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: customerId, action: 'delete_permanent' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) loadArchivedCustomers();
        });
    }
}

// ============================================================
// ARCHIVE TAB SWITCHER
// ============================================================
function switchArchiveTab(btn, tabId) {
    document.querySelectorAll('.archive-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.archive-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// ============================================================
// ARCHIVED PRODUCTS
// ============================================================
function loadArchivedProducts() {
    fetch('./api/product_api/get_archived_products.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('archivedProductsTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#aaa;padding:30px;">No archived products.</td></tr>';
                return;
            }

            data.forEach(product => {
                const archivedDate = product.archived_at
                    ? new Date(product.archived_at).toLocaleDateString()
                    : '—';
                const row = `
                    <tr>
                        <td>${product.id}</td>
                        <td>
                            ${product.picture
                                ? `<img src="./uploads/products/${product.picture}" alt="${product.name}" class="product-img">`
                                : '<img src="./uploads/products/no-image.png" alt="No image" class="product-img">'}
                        </td>
                        <td>${product.name}</td>
                        <td>${product.category}</td>
                        <td>₱${parseFloat(product.price).toFixed(2)}</td>
                        <td>${archivedDate}</td>
                        <td class="action-buttons">
                            <button class="action-btn restore" onclick="restoreProduct('${product.id}')" title="Restore">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="action-btn delete" onclick="permanentlyDeleteProduct('${product.id}')" title="Delete Permanently">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function restoreProduct(productId) {
    if (confirm('Restore this product to the active list?')) {
        fetch('./api/product_api/archived_product_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, action: 'restore' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                loadArchivedProducts();
                loadProducts();
            }
        });
    }
}

function permanentlyDeleteProduct(productId) {
    if (confirm('⚠️ Permanently delete this product? This CANNOT be undone and the image will be removed.')) {
        fetch('./api/product_api/archived_product_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, action: 'delete_permanent' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) loadArchivedProducts();
        });
    }
}

// ============================================================
// ARCHIVED EMPLOYEES
// ============================================================
function loadArchivedEmployees() {
    fetch('./api/employee_api/get_archived_employees.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('archivedEmployeesTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:30px;">No archived employees.</td></tr>';
                return;
            }

            data.forEach(employee => {
                const archivedDate = employee.archived_at
                    ? new Date(employee.archived_at).toLocaleDateString()
                    : '—';
                const row = `
                    <tr>
                        <td>${employee.id}</td>
                        <td>${employee.full_name}</td>
                        <td>${employee.email}</td>
                        <td>${employee.position}</td>
                        <td>${archivedDate}</td>
                        <td class="action-buttons">
                            <button class="action-btn restore" onclick="restoreEmployee('${employee.id}')" title="Restore">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button class="action-btn delete" onclick="permanentlyDeleteEmployee('${employee.id}')" title="Delete Permanently">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        });
}

function restoreEmployee(employeeId) {
    if (confirm('Restore this employee to the active list?')) {
        fetch('./api/employee_api/archived_employee_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: employeeId, action: 'restore' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                loadArchivedEmployees();
                loadEmployees();
            }
        });
    }
}

function permanentlyDeleteEmployee(employeeId) {
    if (confirm('⚠️ Permanently delete this employee? This CANNOT be undone.')) {
        fetch('./api/employee_api/archived_employee_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: employeeId, action: 'delete_permanent' })
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) loadArchivedEmployees();
        });
    }
}

// ============================================================
// GENERIC TABLE SEARCH (used by archive tables)
// ============================================================
function searchTable(inputId, tableId) {
    const filter = document.getElementById(inputId).value.toUpperCase();
    const table  = document.getElementById(tableId);
    const rows   = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j] && cells[j].textContent.toUpperCase().includes(filter)) {
                found = true;
                break;
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}