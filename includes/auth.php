<?php 
// Include database connection from includes folder
require_once 'dbh.inc.php';

// Set header to return JSON
header('Content-Type: application/json');

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Debug
error_log("Auth action requested: " . $action);

switch($action) {
    case 'admin_login':
        // Get POST data
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Debug
        error_log("Login attempt for admin: " . $username);
        
        // Query admin table with role
        $sql = "SELECT *, 'admin' AS role FROM Admins WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            // Verify password (use password_verify if passwords are hashed)
            if($password == $row['password_hash']) { // In production, use password_verify()
                echo json_encode(['success' => true, 'user' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found']);
        }
        break;
        
    case 'user_login': // Fixed action name (underscore instead of space)
        // Get POST data
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Debug
        error_log("Login attempt for user: " . $username);
        
        // Query users table including role information
        $sql = "SELECT *, 'user' AS role FROM Users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            // Verify password (use password_verify if passwords are hashed)
            if($password == $row['password_hash']) { // In production, use password_verify()
                echo json_encode(['success' => true, 'user' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        break;

    default:
        error_log("Invalid action: " . $action);
        echo json_encode(['error' => 'Invalid action']); 
}
?>