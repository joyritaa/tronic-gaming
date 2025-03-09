<?php
// add_admin.php
session_start();
require_once 'config.php';

// Protect page – only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = trim($_POST['password']); // In production, hash this value.
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $role       = trim($_POST['role']);

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "Username, email, password, and role are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Admins (username, email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $password, $first_name, $last_name, $role);
        if ($stmt->execute()) {
            header("Location: admin_panel.html");
            exit();
        } else {
            $error = "Error adding admin: " . $conn->error;
        }
    }
}
