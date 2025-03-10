<?php
// connect to a database
include 'db_connection.php';

$query = "SELECT id, username, email, role, created_at FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$users = [];
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($users);

//get a single user
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT id, username, email, role FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        header('Content-Type: application/json');
        echo json_encode($user);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No user ID provided']);
}

mysqli_close($conn);
?>