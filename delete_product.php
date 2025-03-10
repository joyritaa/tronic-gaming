<?php
// connection
include 'dbconnection.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "DELETE FROM products WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $response = ['success' => true, 'message' => 'Product deleted successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Error deleting product: ' . mysqli_error($conn)];
    }
} else {
    $response = ['success' => false, 'message' => 'No product ID provided'];
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>