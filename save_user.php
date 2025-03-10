<?php
//connection to database
include "dbconnection.php";

// Set headers for JSON response
header('Content-Type: application/json');

// Get the JSON data from the request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if data is valid
if (!$data || !isset($data['username']) || !isset($data['email']) || !isset($data['role'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data provided'
    ]);
    exit;
}

// Sanitize inputs to prevent SQL injection
$id = isset($data['id']) && !empty($data['id']) ? intval($data['id']) : null;
$username = mysqli_real_escape_string($conn, $data['username']);
$email = mysqli_real_escape_string($conn, $data['email']);
$role = mysqli_real_escape_string($conn, $data['role']);

// Validate inputs
if (empty($username) || empty($email) || empty($role)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Check if username already exists (for new users)
if ($id === null) {
    $check_query = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }
}

// Handle password
if ($id === null || !empty($data['password'])) {
    // New user or password update
    if (empty($data['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Password is required for new users'
        ]);
        exit;
    }
    
    // Hash password
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
} else {
    // Existing user, no password change
    $password = null;
}

// Prepare query based on whether it's an update or new record
if ($id) {
    // Update existing user
    if ($password) {
        $query = "UPDATE users SET username = '$username', email = '$email', 
                  password = '$password', role = '$role', updated_at = NOW() 
                  WHERE id = $id";
    } else {
        $query = "UPDATE users SET username = '$username', email = '$email', 
                  role = '$role', updated_at = NOW() 
                  WHERE id = $id";
    }
} else {
    // Insert new user
    $query = "INSERT INTO users (username, email, password, role, created_at, updated_at) 
              VALUES ('$username', '$email', '$password', '$role', NOW(), NOW())";
}

// Execute query
if (mysqli_query($conn, $query)) {
    if ($id === null) {
        $id = mysqli_insert_id($conn);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'User saved successfully',
        'userId' => $id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
}

// Close connection
mysqli_close($conn);
?>