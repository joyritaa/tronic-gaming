<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html?error=unauthorized");
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Get user type from GET parameter
$userType = isset($_GET['type']) ? $_GET['type'] : 'user';
$isAdmin = ($userType === 'admin');

// Prepare SQL statement to fetch users
$table = $isAdmin ? 'Admins' : 'Users';
$idField = $isAdmin ? 'admin_id' : 'user_id';

$sql = "SELECT * FROM $table ORDER BY $idField DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo '<p class="error">Error loading users. Please try again later.</p>';
    exit();
}

// Check if we have users
if (mysqli_num_rows($result) > 0) {
    echo '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Name</th>';
    
    if ($isAdmin) {
        echo '<th>Role</th>';
    } else {
        echo '<th>Phone</th>';
    }
    
    echo    '<th>Actions</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $row[$idField] . '</td>
                <td>' . htmlspecialchars($row['username']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
        
        if ($isAdmin) {
            echo '<td>' . htmlspecialchars($row['role']) . '</td>';
        } else {
            echo '<td>' . htmlspecialchars($row['phone_number']) . '</td>';
        }
        
        echo    '<td>
                    <button onclick="editUser(' . $row[$idField] . ', \'' . $userType . '\')">Edit</button>
                    <button class="delete" onclick="deleteUser(' . $row[$idField] . ', \'' . $userType . '\')">Delete</button>
                </td>
            </tr>';
    }
    
    echo '</tbody></table>';
    
    // Add JavaScript for edit and delete functions
    echo '<script>
        function editUser(id, type) {
            // Redirect to edit page with user ID
            window.location.href = "edit_user.php?id=" + id + "&type=" + type;
        }
        
        function deleteUser(id, type) {
            if (confirm("Are you sure you want to delete this " + type + "?")) {
                // Create form and submit
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "user_management.inc.php";
                
                const actionInput = document.createElement("input");
                actionInput.type = "hidden";
                actionInput.name = "action";
                actionInput.value = "delete";
                form.appendChild(actionInput);
                
                const typeInput = document.createElement("input");
                typeInput.type = "hidden";
                typeInput.name = "user_type";
                typeInput.value = type;
                form.appendChild(typeInput);
                
                const idInput = document.createElement("input");
                idInput.type = "hidden";
                idInput.name = type === "admin" ? "admin_id" : "user_id";
                idInput.value = id;
                form.appendChild(idInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>';
} else {
    echo '<p>No ' . ($isAdmin ? 'admins' : 'users') . ' found.</p>';
}

// Close connection
mysqli_close($conn);
?>