<?php
//connect to database
include 'dbconnection.php';

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Sanitize inputs
$id = isset($data['id']) && !empty($data['id']) ? mysqli_real_escape_string($conn, $data['id']) : null;
$userId = isset($data['userId']) && !empty($data['userId']) ? mysqli_real_escape_string($conn, $data['userId']) : null;
$name = isset($data['name']) && !empty($data['name']) ? mysqli_real_escape_string($conn, $data['name']) : null;
$description = isset($data['description']) && !empty($data['description']) ? mysqli_real_escape_string($conn, $data['description']) : null;
$price = isset($data['price']) && !empty($data['price']) ? mysqli_real_escape_string($conn, $data['price']) : null;
$quantity = isset($data['quantity']) && !empty($data['quantity']) ? mysqli_real_escape_string($conn, $data['quantity']) : null;

$response = ['success' => false];

// If editing, verify user owns this product
if ($id) {
    $checkQuery = "SELECT user_id FROM products WHERE id = $id";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $product = mysqli_fetch_assoc($checkResult);
        
        if ($product['user_id'] != $userId) {
            $response = ['success' => false, 'message' => 'You can only edit your own products'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Update existing product
        $query = "UPDATE products SET 
                    name = '$name', 
                    description = '$description', 
                    price = $price, 
                    quantity = $quantity 
                  WHERE id = $id AND user_id = $userId";
                  
        if (mysqli_query($conn, $query)) {
            $response = ['success' => true, 'message' => 'Product updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Error updating product: ' . mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'Product not found'];
    }
} else {
    // Insert new product
    $query = "INSERT INTO products (name, description, price, quantity, user_id) 
              VALUES ('$name', '$description', $price, $quantity, $userId)";
              
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