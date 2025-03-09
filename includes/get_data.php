<?php
// Include database connection from includes folder
require_once 'includes/dbh.inc.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check what data is requested
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'products':
        // Query products table
        $sql = "SELECT * FROM Products";
        $result = mysqli_query($conn, $sql);
        
        $products = [];
        while($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        echo json_encode($products);
        break;
        
    case 'users':
        // Query Users table
        $sql = "SELECT * FROM Users";
        $result = mysqli_query($conn, $sql);
        
        $users = [];
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        echo json_encode($users);
        break;
        
    case 'admins':
        // Query Admins table
        $sql = "SELECT * FROM Admins";
        $result = mysqli_query($conn, $sql);
        
        $admins = [];
        while($row = mysqli_fetch_assoc($result)) {
            $admins[] = $row;
        }
        echo json_encode($admins);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>