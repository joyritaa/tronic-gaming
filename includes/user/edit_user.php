<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$editUserId = $data['editUserId'];
$username = $data['username'];
$password = $data['password'];
$role = $data['role'];

// Validate input
if (empty($editUserId) || empty($username) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Update the user information in the database
$query = "UPDATE users SET username = ?, role = ?" . (!empty($password) ? ", password = ? " : "") . " WHERE id = ?";
$stmt = $conn->prepare($query);

// Bind parameters
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sssi", $username, $role, $hashedPassword, $editUserId);
} else {
    $stmt->bind_param("ssi", $username, $role, $editUserId);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating user.']);
}

$stmt->close();
$conn->close();
?>
