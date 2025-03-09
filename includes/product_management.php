<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html?error=unauthorized");
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Function to handle product addition
function addProduct($conn) {
    // Get form data
    $productName = $_POST['prod_name'];
    $productDesc = $_POST['prod_desc'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stockQuantity = $_POST['stock_quantity'];
    $manufacturer = $_POST['manufacturer'];
    $releaseDate = $_POST['release_date'];
    
    // Validate input
    if (empty($productName) || empty($category) || empty($price)) {
        return "Please fill in all required fields (name, category, price).";
    }
    
    // Prepare SQL statement
    $sql = "INSERT INTO Products (prod_name, prod_desc, category, price, stock_quantity, manufacturer, release_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($stmt, "sssdisd", $productName, $productDesc, $category, $price, $stockQuantity, $manufacturer, $releaseDate);
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to add product.";
    }
}

// Function to handle product update
function updateProduct($conn) {
    // Get form data
    $productId = $_POST['product_id'];
    $productName = $_POST['prod_name'];
    $productDesc = $_POST['prod_desc'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stockQuantity = $_POST['stock_quantity'];
    $manufacturer = $_POST['manufacturer'];
    $releaseDate = $_POST['release_date'];
    
    // Validate input
    if (empty($productId) || empty($productName) || empty($category) || empty($price)) {
        return "Please fill in all required fields.";
    }
    
    // Prepare SQL statement
    $sql = "UPDATE Products 
            SET prod_name = ?, prod_desc = ?, category = ?, price = ?, 
                stock_quantity = ?, manufacturer = ?, release_date = ? 
            WHERE product_id = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($stmt, "sssdiisi", $productName, $productDesc, $category, $price, $stockQuantity, $manufacturer, $releaseDate, $productId);
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to update product.";
    }
}

// Function to handle product deletion
function deleteProduct($conn) {
    // Get product ID
    $productId = $_POST['product_id'];
    
    // Validate input
    if (empty($productId)) {
        return "Product ID is required.";
    }
    
    // Prepare SQL statement
    $sql = "DELETE FROM Products WHERE product_id = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return "SQL error occurred.";
    }
    
    mysqli_stmt_bind_param($stmt, "i", $productId);
    
    if (mysqli_stmt_execute($stmt)) {
        return "success";
    } else {
        return "Failed to delete product.";
    }
}

// Function to get all products
function getAllProducts($conn) {
    $sql = "SELECT * FROM Products ORDER BY prod_name ASC";
    $result = mysqli_query($conn, $sql);
    
    $products = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Handle form submissions
$message = "";
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $message = addProduct($conn);
            break;
        case 'update':
            $message = updateProduct($conn);
            break;
        case 'delete':
            $message = deleteProduct($conn);
            break;
    }
    
    // Redirect back to product management page with message
    if ($message === "success") {
        header("Location: product_management.html?status=success&action=" . $_POST['action']);
    } else {
        header("Location: product_management.html?status=error&message=" . urlencode($message));
    }
    exit();
}

// Close connection
mysqli_close($conn);
?>