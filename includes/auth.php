<?php
// Include database connection from includes folder
require_once 'includes/dbh.inc.php';

// Set header to return JSON
header('Content-Type: application/json');

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'admin_login':
        // Get POST data
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Query admin
        $sql = "SELECT * FROM Admins WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            // Verify password (use password_verify if passwords are hashed)
            if($password == $row['password']) { // In production, use password_verify()
                // Add role field for consistency
                $row['role'] = 'admin';
                echo json_encode(['success' => true, 'user' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>