<?php
// connection
include 'dbconnection.php';

// Get  data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Sanitize inputs
$id = isset($data['id']) && !empty($data['id']) ? mysqli_real_escape_string($conn, $data['id']) : null;
$name = mysqli_real_escape_string($conn, $data['name']);
$description = mysqli_real_escape_string($conn, $data['description']);
$price = mysqli_real_escape_string($conn, $data['price']);
$quantity = mysqli_real_escape_string($conn, $data['quantity']);

$response = ['success' => false];

// Update or insert
if ($id) {
    // Update existing product
    $query = "UPDATE products SET 
                name = '$name', 
                description = '$description', 
                price = $price, 
                quantity = $quantity 
              WHERE id = $id";
              
    if (mysqli_query($conn, $query)) {
        $response = ['success' => true, 'message' => 'Product updated successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Error updating product: ' . mysqli_error($conn)];
    }
} else {
    // Insert new product
    $query = "INSERT INTO products (name, description, price, quantity) 
              VALUES ('$name', '$description', $price, $quantity)";
              
    if (mysqli_query($conn, $query)) {
        $response = ['success' => true, 'message' => 'Product added successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Error adding product: ' . mysqli_error($conn)];
    }
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>