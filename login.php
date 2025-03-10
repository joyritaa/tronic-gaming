<?php

// connection to database
include 'dbconnection.php';

// Get JSON data
$json = file_get_contents('php://input');
if (empty($json)) {
    $response = ['success' => false, 'message' => 'No input data received'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$data = json_decode($json, true);

// Check if JSON data is valid
if ($data === null || !isset($data['username']) || !isset($data['password'])) {
    $response = ['success' => false, 'message' => 'Invalid input data'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get username and password
$username = mysqli_real_escape_string($conn, $data['username']);
$password = $data['password'];

// Query to get user
$query = "SELECT id, username, password, role FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // Verify password (using password_verify in a real app)
    if (password_verify($password, $user['password'])) {
        // Password is correct
        $response = [
            'success' => true,
            'userId' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
    } else {
        // Password is incorrect
        $response = ['success' => false, 'message' => 'Invalid username or password'];
    }
} else {
    // User not found
    $response = ['success' => false, 'message' => 'Invalid username or password'];
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>
