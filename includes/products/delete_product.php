<?php
header('Content-Type: application/json');
include 'config.php'; // database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['id'];

// Validate input
if (empty($productId)) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit;
}

// Delete the product from the database
$query = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting product.']);
}

$stmt->close();
$conn->close();
?>
