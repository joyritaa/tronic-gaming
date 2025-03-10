<?php
// connect to database
include 'db_connection.php';

if (isset($_GET['id']) && isset($_GET['userId'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $userId = mysqli_real_escape_string($conn, $_GET['userId']);
    
    // Verify user owns this product
    $checkQuery = "SELECT id FROM products WHERE id = $id AND user_id = $userId";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $query = "DELETE FROM products WHERE id = $id AND user_id = $userId";
        
        if (mysqli_query($conn, $query)) {
            $response = ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Error deleting product: ' . mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'You can only delete your own products'];
    }
} else {
    $response = ['success' => false, 'message' => 'Missing product ID or user ID'];
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>