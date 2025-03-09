<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Return admin info
$response = [
    'username' => $_SESSION['username'],
    'admin_id' => $_SESSION['admin_id']
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>