<?php
// edit_user.php
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
$error = '';

// Fetch the user record
$stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    die("User not found.");
}
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $first_name   = trim($_POST['first_name']);
    $last_name    = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);
    $password     = trim($_POST['password']);

    if (empty($username) || empty($email)) {
        $error = "Username and email are required.";
    } else {
        if (!empty($password)) {
            // Update including password
            $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, password_hash = ?, first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssi", $username, $email, $password, $first_name, $last_name, $phone_number, $user_id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?");
            $stmt->bind_param("sssssi", $username, $email, $first_name, $last_name, $phone_number, $user_id);
        }
        if ($stmt->execute()) {
            header("Location: admin_panel.html");
            exit();
        } else {
            $error = "Error updating user: " . $conn->error;
        }
    }
}
