document.addEventListener('DOMContentLoaded', function() {
    // Fetch dashboard statistics
    fetchDashboardStats();
    
    // Set up logout functionality
    document.getElementById('logoutBtn').addEventListener('click', function() {
        //  logout 
        window.location.href = 'login.html';
    });
});

// Function to get dashboard statistics
function fetchDashboardStats() {
    fetch('get_stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalProductsStat').textContent = data.totalProducts;
            document.getElementById('inStockStat').textContent = data.inStock;
            document.getElementById('outOfStockStat').textContent = data.outOfStock;
            document.getElementById('totalUsersStat').textContent = data.totalUsers;
        })
        .catch(error => console.error('Error fetching stats:', error));
}






// product_script.js
document.addEventListener('DOMContentLoaded', function() {
    // Load all products
    loadProducts();
    
    // Set up form submission
    document.getElementById('productFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        saveProduct();
    });
    
    // Set up add product button
    document.getElementById('addProductBtn').addEventListener('click', function() {
        showProductForm();
    });
    
    // Set up cancel button
    document.getElementById('cancelProductBtn').addEventListener('click', function() {
        hideProductForm();
    });
    
    // Set up logout
    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = 'login.html';
    });
});

function loadProducts() {
    fetch('get_products.php')
        .then(response => response.json())
        .then(data => {
            const productsList = document.getElementById('productsList');
            
            if (data.length === 0) {
                productsList.innerHTML = '<p>No products found.</p>';
                return;
            }
            
            let html = '<table class="data-table">';
            html += '<thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            data.forEach(product => {
                html += `<tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.quantity}</td>
                    <td>
                        <button onclick="editProduct(${product.id})">Edit</button>
                        <button onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            productsList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('productsList').innerHTML = '<p>Error loading products.</p>';
        });
}

function showProductForm(product = null) {
    const form = document.getElementById('productForm');
    form.style.display = 'block';
    
    // Clear form
    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productDescription').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productQuantity').value = '';
    
    // If editing, fill form with product data
    if (product) {
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productDescription').value = product.description;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productQuantity').value = product.quantity;
    }
}

function hideProductForm() {
    document.getElementById('productForm').style.display = 'none';
}

function saveProduct() {
    const productId = document.getElementById('productId').value;
    const productData = {
        id: productId,
        name: document.getElementById('productName').value,
        description: document.getElementById('productDescription').value,
        price: document.getElementById('productPrice').value,
        quantity: document.getElementById('productQuantity').value
    };
    
    fetch('save_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideProductForm();
            loadProducts();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving product');
    });
}

function editProduct(id) {
    fetch(`get_products.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            showProductForm(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product data');
        });
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch(`delete_product.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadProducts();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting product');
            });
    }
}






// user_script.js
document.addEventListener('DOMContentLoaded', function() {
    // Load all users
    loadUsers();
    
    // Set up form submission
    document.getElementById('userFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    // Set up add user button
    document.getElementById('addUserBtn').addEventListener('click', function() {
        showUserForm();
    });
    
    // Set up cancel button
    document.getElementById('cancelUserBtn').addEventListener('click', function() {
        hideUserForm();
    });
    
    // Set up logout
    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = 'login.html';
    });
});

function loadUsers() {
    fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            const usersList = document.getElementById('usersList');
            
            if (data.length === 0) {
                usersList.innerHTML = '<p>No users found.</p>';
                return;
            }
            
            let html = '<table class="data-table">';
            html += '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            data.forEach(user => {
                html += `<tr>
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.created_at}</td>
                    <td>
                        <button onclick="editUser(${user.id})">Edit</button>
                        <button onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            usersList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('usersList').innerHTML = '<p>Error loading users.</p>';
        });
}

function showUserForm(user = null) {
    const form = document.getElementById('userForm');
    form.style.display = 'block';
    
    // Clear form
    document.getElementById('userId').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('role').value = 'user';
    
    // If editing, fill form with user data
    if (user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        // Don't fill password - it should be empty when editing
    }
}

function hideUserForm() {
    document.getElementById('userForm').style.display = 'none';
}

function saveUser() {
    const userId = document.getElementById('userId').value;
    const userData = {
        id: userId,
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value
    };
    
    fetch('save_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideUserForm();
            loadUsers();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving user');
    });
}

function editUser(id) {
    fetch(`get_users.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            showUserForm(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user data');
        });
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`delete_user.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUsers();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting user');
            });
    }
}





//user_productjs
document.addEventListener('DOMContentLoaded', function() {
    // Get logged in user ID from session or localStorage we'll simulate with a fixed ID
    const currentUserId = getUserIdFromSession(); // get the ID from your auth system
    
    // Set the user ID field value
    document.getElementById('userId').value = currentUserId;
    
    // Display username in header
    fetchUserInfo(currentUserId);
    
    // Load products for this user
    loadUserProducts(currentUserId);
    
    // Set up form submission
    document.getElementById('productFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        saveProduct(currentUserId);
    });
    
    // Set up add product button
    document.getElementById('addProductBtn').addEventListener('click', function() {
        showProductForm();
    });
    
    // Set up cancel button
    document.getElementById('cancelProductBtn').addEventListener('click', function() {
        hideProductForm();
    });
    
    // Set up refresh button
    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadUserProducts(currentUserId);
    });
    
    // Set up logout
    document.getElementById('logoutBtn').addEventListener('click', function() {
        // Clear user session/local storage
        localStorage.removeItem('userId');
        window.location.href = 'login.html';
    });
});

// This is a placeholder based on the auth system
function getUserIdFromSession() {
    // In a real app, get from session or localStorage
    return localStorage.getItem('userId') || 1; // Fallback to ID 1 for testing
}

function fetchUserInfo(userId) {
    fetch(`get_user_info.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('userInfo').textContent = `Welcome, ${data.username}`;
            }
        })
        .catch(error => {
            console.error('Error fetching user info:', error);
        });
}

function loadUserProducts(userId) {
    fetch(`get_user_products.php?userId=${userId}`)
        .then(response => response.json())
        .then(data => {
            const productsList = document.getElementById('productsList');
            
            if (data.length === 0) {
                productsList.innerHTML = '<p>You haven\'t added any products yet.</p>';
                return;
            }
            
            let html = '<table class="data-table">';
            html += '<thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            data.forEach(product => {
                html += `<tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.quantity}</td>
                    <td>
                        <button onclick="editProduct(${product.id})">Edit</button>
                        <button onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            productsList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('productsList').innerHTML = '<p>Error loading products.</p>';
        });
}

function showProductForm(product = null) {
    const form = document.getElementById('productForm');
    form.style.display = 'block';
    
    // Clear form
    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productDescription').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productQuantity').value = '';
    
    // If editing, fill form with product data
    if (product) {
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productDescription').value = product.description;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productQuantity').value = product.quantity;
    }
}

function hideProductForm() {
    document.getElementById('productForm').style.display = 'none';
}

function saveProduct(userId) {
    const productId = document.getElementById('productId').value;
    const productData = {
        id: productId,
        userId: userId,
        name: document.getElementById('productName').value,
        description: document.getElementById('productDescription').value,
        price: document.getElementById('productPrice').value,
        quantity: document.getElementById('productQuantity').value
    };
    
    fetch('save_user_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideProductForm();
            loadUserProducts(userId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving product');
    });
}

function editProduct(id) {
    fetch(`get_products.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Check if this user owns this product
            const currentUserId = getUserIdFromSession();
            if (data.user_id != currentUserId) {
                alert('You can only edit your own products');
                return;
            }
            
            showProductForm(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product data');
        });
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const userId = getUserIdFromSession();
        
        fetch(`delete_user_product.php?id=${id}&userId=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadUserProducts(userId);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting product');
            });
    }
}