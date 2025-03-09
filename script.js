document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    // Login API request
    fetch('http://127.0.0.1:5000/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: 'Mark_grayson', password: 'invincible'})
    })
    .then(response => response.json())
    .then(data => {
        if (data.role) {
            localStorage.setItem('role', data.role);
            window.location.href = data.direct; // Redirect to the user panel
        } else {
            alert('Login failed: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
    
});

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const username = document.getElementById('regUsername').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;
    const role = document.getElementById('regRole').value;
    // Registration API request
    fetch('http://127.0.0.1:5000/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, email, password, role })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert('Registration successful');
        } else {
            alert('Registration failed: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

async function loadInventory() {
    const response = await fetch('/inventory');
    const data = await response.json();
    const tableBody = document.querySelector('#inventoryTable tbody');
    tableBody.innerHTML = data.map(item => `
        <tr>
            <td>${item.name}</td>
            <td>${item.stock}</td>
            <td>${item.price}</td>
        </tr>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadInventory);