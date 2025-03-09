// login.js - Form validation and redirection

document.addEventListener('DOMContentLoaded', function() {
    // Get the login form
    const loginForm = document.getElementById('loginForm');
    
    // Add event listener for form submission
    loginForm.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();
        
        // Get form values
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const userType = document.querySelector('input[name="user-type"]:checked').value;
        
        // Basic validation
        if (username.trim() === '' || password.trim() === '') {
            showError('Username and password are required!');
            return;
        }
        
        // In a real application, you would send these credentials to a server
        // for authentication. Here we're simulating the authentication process.
        authenticateUser(username, password, userType);
    });
    
    // Function to show error messages
    function showError(message) {
        const errorDiv = document.getElementById('error-message');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        // Hide error after 3 seconds
        setTimeout(function() {
            errorDiv.style.display = 'none';
        }, 3000);
    }
    
    // Function to authenticate user (simulation)
    function authenticateUser(username, password, userType) {
        // This is where you would normally make an AJAX call to your server
        // For demonstration, we're just doing a simple check
        
        // Simulate server request with a timeout
        document.getElementById('login-button').disabled = true;
        document.getElementById('login-button').textContent = 'Logging in...';
        
        setTimeout(function() {
            // For demo purposes:
            // In a real application, this logic would happen on the server
            if (username.length < 3) {
                showError('Invalid username format');
                resetButton();
                return;
            }
            
            if (password.length < 6) {
                showError('Password too short');
                resetButton();
                return;
            }
            
            // Successful login simulation
            // Redirect based on user type
            if (userType === 'admin') {
                // Store user info in session storage
                sessionStorage.setItem('loggedIn', 'true');
                sessionStorage.setItem('userType', 'admin');
                sessionStorage.setItem('username', username);
                
                // Redirect to admin panel
                window.location.href = 'admin_panel.html';
            } else {
                // Store user info in session storage
                sessionStorage.setItem('loggedIn', 'true');
                sessionStorage.setItem('userType', 'user');
                sessionStorage.setItem('username', username);
                
                // Redirect to user view
                window.location.href = 'user_view.html';
            }
        }, 1000); // Simulate network delay
    }
    
    function resetButton() {
        document.getElementById('login-button').disabled = false;
        document.getElementById('login-button').textContent = 'Login';
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

// logout.js - Handles user logout functionality

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const isLoggedIn = sessionStorage.getItem('loggedIn') === 'true';
    const userType = sessionStorage.getItem('userType');
    const username = sessionStorage.getItem('username');
    
    // Get the logout button
    const logoutButton = document.getElementById('logout-button');
    
    // Get the user info display element (if it exists)
    const userInfoElement = document.getElementById('user-info');
    
    // If user is not logged in, redirect to login page
    if (!isLoggedIn) {
        window.location.href = 'login.html';
        return;
    }
    
    // Display username and user type if the element exists
    if (userInfoElement) {
        userInfoElement.textContent = `Logged in as: ${username} (${userType})`;
    }
    
    // Add event listener to logout button
    if (logoutButton) {
        logoutButton.addEventListener('click', function(event) {
            // Prevent default button behavior
            event.preventDefault();
            
            // Show logout confirmation
            const confirmLogout = confirm('Are you sure you want to log out?');
            
            if (confirmLogout) {
                // Clear session data
                sessionStorage.removeItem('loggedIn');
                sessionStorage.removeItem('userType');
                sessionStorage.removeItem('username');
                
                // You can also clear all session storage if preferred
                // sessionStorage.clear();
                
                // Show logout message
                alert('You have been successfully logged out');
                
                // Redirect to login page
                window.location.href = 'login.html';
            }
        });
    }
    
    // Add a function to check if session is still valid (can be called periodically)
    function checkSession() {
        if (sessionStorage.getItem('loggedIn') !== 'true') {
            // Session expired or was cleared
            window.location.href = 'login.html?error=session_expired';
        }
    }
    
    // Optional: Check session every minute (uncomment if needed)
    // setInterval(checkSession, 60000);
});