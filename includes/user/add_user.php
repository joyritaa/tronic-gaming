<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$password = $data['password'];
$role = $data['role'];

// Validate input
if (empty($username) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Check if the username already exists
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists.']);
    exit;
}

// Insert the new user into the database
$query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt->bind_param("sss", $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding user.']);
}

$stmt->close();
$conn->close();
?>
