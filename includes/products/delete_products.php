<?php
// delete_product.php
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

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

header("Location:admin_panel.html");
exit();
