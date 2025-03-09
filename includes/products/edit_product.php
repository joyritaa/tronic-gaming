<?php
header('Content-Type: application/json');
include 'config.php'; // database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['id'];
$name = $data['name'];
$category = $data['category'];
$price = $data['price'];
$inStock = $data['inStock'];
$quantity = $data['quantity'];

// Validate input
if (empty($productId) || empty($name) || empty($category) || empty($price) || empty($quantity)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Update the product information in the database
$query = "UPDATE products SET name = ?, category = ?, price = ?, inStock = ?, quantity = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssdisi", $name, $category, $price, $inStock, $quantity, $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating product.']);
}

$stmt->close();
$conn->close();

