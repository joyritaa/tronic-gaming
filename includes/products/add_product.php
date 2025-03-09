<?php
header('Content-Type: application/json');
include 'config.php'; // database connection file

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);
$name = $data['name'];
$category = $data['category'];
$price = $data['price'];
$inStock = $data['inStock'];
$quantity = $data['quantity'];

// Validate input
if (empty($name) || empty($category) || empty($price) || empty($quantity)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Insert the new product into the database
$query = "INSERT INTO products (name, category, price, inStock, quantity) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssdis", $name, $category, $price, $inStock, $quantity);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding product.']);
}

$stmt->close();
$conn->close();

