// Replace localStorage with AJAX calls
document.addEventListener('DOMContentLoaded', function() {
    // Check session storage for logged in admin
    const currentUser = JSON.parse(sessionStorage.getItem('currentUser'));
    
    // If no user is logged in or the user is not an admin, redirect to login page
    if (!currentUser || currentUser.role !== 'admin') {
        window.location.href = 'login.html';
        return;
    }
    
    // Update user info in header
    document.getElementById('userInfo').textContent = `Welcome, ${currentUser.username}`;
    
    // Load data from PHP
    Promise.all([
        fetch('get_data.php?action=products').then(res => res.json()),
        fetch('get_data.php?action=users').then(res => res.json()),
        fetch('get_data.php?action=admins').then(res => res.json())
    ])
    .then(([products, users, admins]) => {
        // Update stats based on your new tables
        updateStatsFromDatabase(products, users, admins);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
    
    // Set up event listeners (unchanged)
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

// New function to update stats based on database data
function updateStatsFromDatabase(products, users, admins) {
    // Count total products
    const totalProducts = products.length;
    document.getElementById('totalProductsStat').textContent = totalProducts;
    
    // Count in stock products (assuming your Products table has an inStock field)
    const inStockProducts = products.filter(product => product.inStock == 1).length;
    document.getElementById('inStockStat').textContent = inStockProducts;
    
    // Count out of stock products
    const outOfStockProducts = products.filter(product => product.inStock == 0).length;
    document.getElementById('outOfStockStat').textContent = outOfStockProducts;
    
    // Count total users (including admins)
    const totalUsers = users.length + admins.length;
    document.getElementById('totalUsersStat').textContent = totalUsers;
}