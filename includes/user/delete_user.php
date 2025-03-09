<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['id'];

// Validate input
if (empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit;
}

// Delete the user from the database
$query = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting user.']);
}

$stmt->close();
$conn->close();
?>
