<?php
// connect to database
include 'dbconnection.php';

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$products = [];
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($products);

//get a certain product
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No product ID provided']);
}

mysqli_close($conn);
?>