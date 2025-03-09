<?php
// delete_admin.php
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

$admin_id = intval($_GET['id']);

// Check if the admin's role is "Store Manager" – if so, do not allow deletion.
$stmt = $conn->prepare("SELECT role FROM Admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    die("Admin not found.");
}
$row = $result->fetch_assoc();
if ($row['role'] === 'Store Manager') {
    die("Cannot delete a Store Manager.");
}

// Proceed to delete
$stmt = $conn->prepare("DELETE FROM Admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();

header("Location: admin_panel.html");
exit();

