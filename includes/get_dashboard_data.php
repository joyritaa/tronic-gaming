<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Get dashboard data
$response = [
    'users' => 0,
    'products' => 0,
    'orders' => 0,
    'revenue' => 0
];

// Count users
$sql = "SELECT COUNT(*) as count FROM Users";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response['users'] = $row['count'];
}

// Count products (assuming you have a Products table)
$sql = "SELECT COUNT(*) as count FROM Products";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response['products'] = $row['count'];
}

// Count orders (assuming you have an Orders table)
$sql = "SELECT COUNT(*) as count FROM Orders";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response['orders'] = $row['count'];
}

// Calculate revenue (assuming Orders table has a total_amount field)
$sql = "SELECT SUM(total_amount) as revenue FROM Orders";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response['revenue'] = $row['revenue'] ? number_format($row['revenue'], 2) : '0.00';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
mysqli_close($conn);
?>