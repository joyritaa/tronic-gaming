<?php
// add_product.php
session_start();
require_once 'config.php';

 // Allow all users to add products
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.html");
//     exit();
// }


$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prod_name      = trim($_POST['prod_name']);
    $prod_desc      = trim($_POST['prod_desc']);
    $category       = trim($_POST['category']);
    $price          = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $manufacturer   = trim($_POST['manufacturer']);
    $release_date   = trim($_POST['release_date']); // Format: YYYY-MM-DD

    if (empty($prod_name) || empty($category) || empty($price)) {
        $error = "Product name, category, and price are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (prod_name, prod_desc, category, price, stock_quantity, manufacturer, release_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdiss", $prod_name, $prod_desc, $category, $price, $stock_quantity, $manufacturer, $release_date);
        if ($stmt->execute()) {
            header("Location: user_view.html");

            exit();
        } else {
            $error = "Error adding product: " . $conn->error;
        }
    }
}
