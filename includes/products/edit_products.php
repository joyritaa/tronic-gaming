<?php
// edit_product.php
session_start();
require_once 'config.php';

// Protect page – only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_panel.html");
    exit();
}

$product_id = intval($_GET['id']);
$error = '';

// Fetch the product record
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    die("Product not found.");
}
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prod_name      = trim($_POST['prod_name']);
    $prod_desc      = trim($_POST['prod_desc']);
    $category       = trim($_POST['category']);
    $price          = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $manufacturer   = trim($_POST['manufacturer']);
    $release_date   = trim($_POST['release_date']);

    if (empty($prod_name) || empty($category) || empty($price)) {
        $error = "Product name, category, and price are required.";
    } else {
        $stmt = $conn->prepare("UPDATE products SET prod_name = ?, prod_desc = ?, category = ?, price = ?, stock_quantity = ?, manufacturer = ?, release_date = ? WHERE product_id = ?");
        $stmt->bind_param("sssdissi", $prod_name, $prod_desc, $category, $price, $stock_quantity, $manufacturer, $release_date, $product_id);
        if ($stmt->execute()) {
            header("Location: admin_panel.html");
            exit();
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}