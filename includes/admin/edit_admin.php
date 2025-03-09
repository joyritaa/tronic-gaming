<?php
// edit_admin.php
session_start();
require_once 'config.php';

// Protect page – only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_panel.html");
    exit();
}

$admin_id = intval($_GET['id']);
$error = '';

// Fetch the admin record
$stmt = $conn->prepare("SELECT * FROM Admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    die("Admin not found.");
}
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $role       = trim($_POST['role']);
    $password   = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($role)) {
        $error = "Username, email, and role are required.";
    } else {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE Admins SET username = ?, email = ?, password_hash = ?, first_name = ?, last_name = ?, role = ? WHERE admin_id = ?");
            $stmt->bind_param("ssssssi", $username, $email, $password, $first_name, $last_name, $role, $admin_id);
        } else {
            $stmt = $conn->prepare("UPDATE Admins SET username = ?, email = ?, first_name = ?, last_name = ?, role = ? WHERE admin_id = ?");
            $stmt->bind_param("sssssi", $username, $email, $first_name, $last_name, $role, $admin_id);
        }
        if ($stmt->execute()) {
            header("Location: admin_panel.html");
            exit();
        } else {
            $error = "Error updating admin: " . $conn->error;
        }
    }
}
