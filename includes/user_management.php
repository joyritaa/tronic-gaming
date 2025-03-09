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

// Function to handle user addition
function addUser($conn, $isAdmin = false) {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // In production, hash this password
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    
    // Additional fields based on user type
    if ($isAdmin) {
        $role = $_POST['role'];
    } else {
        $phoneNumber = $_POST['phone_number'];
    }
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return "Please fill in all required fields (username, email, password).";
    }
    
    // Check if username or email already exists
    $table = $isAdmin ? 'Admins' : 'Users';
    $checkSql = "SELECT * FROM $table WHERE username = ? OR email = ?";
    $checkStmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($checkStmt, $checkSql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($checkStmt, "ss", $username, $email);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return "Username or email already exists.";
    }
    
    // Prepare SQL statement for insertion
    if ($isAdmin) {
        $sql = "INSERT INTO Admins (username, email, password_hash, first_name, last_name, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
    } else {
        $sql = "INSERT INTO Users (username, email, password_hash, first_name, last_name, phone_number) 
                VALUES (?, ?, ?, ?, ?, ?)";
    }
    
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    if ($isAdmin) {
        mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $password, $firstName, $lastName, $role);
    } else {
        mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $password, $firstName, $lastName, $phoneNumber);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to add " . ($isAdmin ? "admin" : "user") . ".";
    }
}

// Function to handle user update
function updateUser($conn, $isAdmin = false) {
    // Get form data
    $id = $isAdmin ? $_POST['admin_id'] : $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    
    // Password is optional for update
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    
    // Additional fields based on user type
    if ($isAdmin) {
        $role = $_POST['role'];
    } else {
        $phoneNumber = $_POST['phone_number'];
    }
    
    // Validate input
    if (empty($id) || empty($username) || empty($email)) {
        return "Please fill in all required fields.";
    }
    
    // Check if username or email already exists for other users
    $table = $isAdmin ? 'Admins' : 'Users';
    $idField = $isAdmin ? 'admin_id' : 'user_id';
    $checkSql = "SELECT * FROM $table WHERE (username = ? OR email = ?) AND $idField != ?";
    $checkStmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($checkStmt, $checkSql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($checkStmt, "ssi", $username, $email, $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return "Username or email already exists.";
    }
    
    // Prepare SQL statement for update
    if ($password) {
        // Update with new password
        if ($isAdmin) {
            $sql = "UPDATE Admins SET username = ?, email = ?, password_hash = ?, 
                    first_name = ?, last_name = ?, role = ? WHERE admin_id = ?";
        } else {
            $sql = "UPDATE Users SET username = ?, email = ?, password_hash = ?, 
                    first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?";
        }
    } else {
        // Update without changing password
        if ($isAdmin) {
            $sql = "UPDATE Admins SET username = ?, email = ?, 
                    first_name = ?, last_name = ?, role = ? WHERE admin_id = ?";
        } else {
            $sql = "UPDATE Users SET username = ?, email = ?, 
                    first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?";
        }
    }
    
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    if ($password) {
        if ($isAdmin) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $username, $email, $password, $firstName, $lastName, $role, $id);
        } else {
            mysqli_stmt_bind_param($stmt, "ssssssi", $username, $email, $password, $firstName, $lastName, $phoneNumber, $id);
        }
    } else {
        if ($isAdmin) {
            mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $firstName, $lastName, $role, $id);
        } else {
            mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $firstName, $lastName, $phoneNumber, $id);
        }
    }
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to update " . ($isAdmin ? "admin" : "user") . ".";
    }
}

// Function to handle user deletion
function deleteUser($conn, $isAdmin = false) {
    // Get ID
    $id = $isAdmin ? $_POST['admin_id'] : $_POST['user_id'];
    
    // Validate input
    if (empty($id)) {
        return "User ID is required.";
    }
    
    // Prepare SQL statement
    $table = $isAdmin ? 'Admins' : 'Users';
    $idField = $isAdmin ? 'admin_id' : 'user_id';
    $sql = "DELETE FROM $table WHERE $idField = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to delete " . ($isAdmin ? "admin" : "user") . ".";
    }
}

// Handle form submissions
$message = "";
$userType = isset($_POST['user_type']) ? $_POST['user_type'] : 'user';
$isAdmin = ($userType === 'admin');

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $message = addUser($conn, $isAdmin);
            break;
        case 'update':
            $message = updateUser($conn, $isAdmin);
            break;
        case 'delete':
            $message = deleteUser($conn, $isAdmin);
            break;
    }
    
    // Redirect back to user management page with message
    if ($message === "success") {
        header("Location: user_management.html?status=success&action=" . $_POST['action'] . "&type=" . $userType);
    } else {
        header("Location: user_management.html?status=error&message=" . urlencode($message) . "&type=" . $userType);
    }
    exit();
}

// Close connection
mysqli_close($conn);
?>