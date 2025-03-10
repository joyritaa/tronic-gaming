<?php
// connect to database
include 'db_connection.php';

if (isset($_GET['userId'])) {
    $userId = mysqli_real_escape_string($conn, $_GET['userId']);
    
    // Get only products owned by this user
    $query = "SELECT * FROM products WHERE user_id = $userId ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}


mysqli_close($conn);
?>