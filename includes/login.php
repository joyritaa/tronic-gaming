<?php
// login.php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // First check in Admins table
    $stmt = $conn->prepare("SELECT * FROM Admins WHERE username=? AND password_hash=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows === 1) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        header("Location: admin_panel.php");
        exit();
    }

    // Then check in Users table
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username=? AND password_hash=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows === 1) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';
        header("Location: user_view.html");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}