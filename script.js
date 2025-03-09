document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Default users and admins
    const users = [
        { username: "user1", password: "password1", role: "user" },
        { username: "user2", password: "password2", role: "user" },
        { username: "user3", password: "password3", role: "user" }
    ];
    
    const admins = [
        { username: "admin1", password: "adminpass1", role: "admin" },
        { username: "admin2", password: "adminpass2", role: "admin" }
    ];
    
    // Sample inventory data for demo purposes
    const inventory = [
        { id: 1, name: "PlayStation 5", category: "Console", price: 499.99, inStock: true, quantity: 15 },
        { id: 2, name: "Xbox Series X", category: "Console", price: 499.99, inStock: true, quantity: 10 },
        { id: 3, name: "Nintendo Switch", category: "Console", price: 299.99, inStock: true, quantity: 20 },
        { id: 4, name: "God of War: Ragnarok", category: "Game", price: 69.99, inStock: true, quantity: 30 },
        { id: 5, name: "Elden Ring", category: "Game", price: 59.99, inStock: true, quantity: 25 },
        { id: 6, name: "Call of Duty: Modern Warfare III", category: "Game", price: 69.99, inStock: false, quantity: 0 },
        { id: 7, name: "Gaming Headset Pro", category: "Accessory", price: 149.99, inStock: true, quantity: 12 },
        { id: 8, name: "Elite Controller", category: "Accessory", price: 179.99, inStock: false, quantity: 0 },
        { id: 9, name: "Gaming Chair", category: "Furniture", price: 249.99, inStock: true, quantity: 8 },
        { id: 10, name: "PC Gaming Desktop", category: "Computer", price: 1499.99, inStock: true, quantity: 5 },
    ];
    
    // Store data in localStorage for other pages to access
    localStorage.setItem('inventoryData', JSON.stringify(inventory));
    localStorage.setItem('usersData', JSON.stringify(users));
    localStorage.setItem('adminsData', JSON.stringify(admins));
    
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        // Check if the user exists in users array
        let foundUser = users.find(user => user.username === username && user.password === password);
        let isAdmin = false;
        
        // If not found in users, check in admins array
        if (!foundUser) {
            foundUser = admins.find(admin => admin.username === username && admin.password === password);
            if (foundUser) {
                isAdmin = true;
            }
        }
        
        if (foundUser) {
            // Store login info in session
            sessionStorage.setItem('currentUser', JSON.stringify({
                username: foundUser.username,
                role: foundUser.role
            }));
            
            // Redirect based on role
            if (isAdmin) {
                window.location.href = 'admin_panel.html';
            } else {
                window.location.href = 'user_view.html';
            }
        } else {
            // Show error message
            document.getElementById('errorMessage').style.display = 'block';
            
            // Clear the password field
            document.getElementById('password').value = '';
        }
    });
});






//User view
document.addEventListener('DOMContentLoaded', function() {
    const currentUser = JSON.parse(sessionStorage.getItem('currentUser'));
    
    // If no user is logged in or the user is an admin, redirect to login page
    if (!currentUser || currentUser.role === 'admin') {
        window.location.href = 'login.html';
        return;
    }
    
    // Update user info in header
    document.getElementById('userInfo').textContent = `Welcome, ${currentUser.username}`;
    
    // Get inventory data from localStorage
    const inventory = JSON.parse(localStorage.getItem('inventoryData'));
    
    // Display all products initially
    displayProducts(inventory);
    
    // Set up event listeners for filters
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);
    document.getElementById('stockFilter').addEventListener('change', filterProducts);
    document.getElementById('searchInput').addEventListener('input', filterProducts);
    
    // Logout button handler
    document.getElementById('logoutBtn').addEventListener('click', function() {
        sessionStorage.removeItem('currentUser');
        window.location.href = 'login.html';
    });
});

function displayProducts(products) {
    const inventoryGrid = document.getElementById('inventoryGrid');
    inventoryGrid.innerHTML = '';
    
    if (products.length === 0) {
        inventoryGrid.innerHTML = '<div class="no-results">No products match your search criteria.</div>';
        return;
    }
    
    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        
        // Get emoji based on category
        let categoryEmoji = '🎮';
        switch (product.category) {
            case 'Console': categoryEmoji = '🎮'; break;
            case 'Game': categoryEmoji = '💿'; break;
            case 'Accessory': categoryEmoji = '🎧'; break;
            case 'Furniture': categoryEmoji = '🪑'; break;
            case 'Computer': categoryEmoji = '💻'; break;
        }
        
        productCard.innerHTML = `
            <div class="product-img">${categoryEmoji}</div>
            <div class="product-details">
                <h3 class="product-name">${product.name}</h3>
                <span class="product-category">${product.category}</span>
                <div class="product-price">$${product.price.toFixed(2)}</div>
                <div class="stock-status ${product.inStock ? 'in-stock' : 'out-of-stock'}">
                    ${product.inStock ? 'In Stock' : 'Out of Stock'}
                </div>
                ${product.inStock ? `<div class="quantity-info">Quantity available: ${product.quantity}</div>` : ''}
            </div>
        `;
        
        inventoryGrid.appendChild(productCard);
    });
}

function filterProducts() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    
    // Get inventory data from localStorage
    const inventory = JSON.parse(localStorage.getItem('inventoryData'));
    
    // Apply filters
    const filteredProducts = inventory.filter(product => {
        // Category filter
        if (categoryFilter !== 'all' && product.category !== categoryFilter) {
            return false;
        }
        
        // Stock filter
        if (stockFilter === 'inStock' && !product.inStock) {
            return false;
        }
        if (stockFilter === 'outOfStock' && product.inStock) {
            return false;
        }
        
        // Search filter
        if (searchInput && !product.name.toLowerCase().includes(searchInput) && 
            !product.category.toLowerCase().includes(searchInput)) {
            return false;
        }
        
        return true;
    });
    
    // Display filtered products
    displayProducts(filteredProducts);
}






//Admin panel
  // Check if user is logged in and is an admin
  document.addEventListener('DOMContentLoaded', function() {
    const currentUser = JSON.parse(sessionStorage.getItem('currentUser'));
    
    // If no user is logged in or the user is not an admin, redirect to login page
    if (!currentUser || currentUser.role !== 'admin') {
        window.location.href = 'login.html';
        return;
    }
    
    // Update user info in header
    document.getElementById('userInfo').textContent = `Welcome, ${currentUser.username}`;
    
    // Load inventory and user data
    const inventory = JSON.parse(localStorage.getItem('inventoryData'));
    const users = JSON.parse(localStorage.getItem('usersData'));
    const admins = JSON.parse(localStorage.getItem('adminsData'));
    
    // Update stats
    updateStats(inventory, users, admins);
    
    // Set up event listeners
    document.getElementById('userManagementBtn').addEventListener('click', function() {
        window.location.href = 'user_management.html';
    });
    
    document.getElementById('productManagementBtn').addEventListener('click', function() {
        window.location.href = 'product_management.html';
    });
    
    // Logout button handler
    document.getElementById('logoutBtn').addEventListener('click', function() {
        sessionStorage.removeItem('currentUser');
        window.location.href = 'login.html';
    });
});

function updateStats(inventory, users, admins) {
    // Count total products
    const totalProducts = inventory.length;
    document.getElementById('totalProductsStat').textContent = totalProducts;
    
    // Count in stock products
    const inStockProducts = inventory.filter(product => product.inStock).length;
    document.getElementById('inStockStat').textContent = inStockProducts;
    
    // Count out of stock products
    const outOfStockProducts = inventory.filter(product => !product.inStock).length;
    document.getElementById('outOfStockStat').textContent = outOfStockProducts;
    
    // Count total users (including admins)
    const totalUsers = users.length + admins.length;
    document.getElementById('totalUsersStat').textContent = totalUsers;
}