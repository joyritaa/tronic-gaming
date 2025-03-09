<?php
// delete_user.php
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

$user_id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: admin_panel.html");
exit();
