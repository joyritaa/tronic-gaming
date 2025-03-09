<?php
// add_user.php
session_start();
require_once 'config.php';

// Protect page – only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $password     = trim($_POST['password']); // In production, hash this value.
    $first_name   = trim($_POST['first_name']);
    $last_name    = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Username, email, and password are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, first_name, last_name, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $password, $first_name, $last_name, $phone_number);
        if ($stmt->execute()) {
            header("Location: admin_panel.html");
            exit();
        } else {
            $error = "Error adding user: " . $conn->error;
        }
    }
}
