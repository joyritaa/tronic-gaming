<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_POST['login-submit'])) {
    require 'dbh.inc.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userType = $_POST['user-type']; // 'user' or 'admin'

    if (empty($username) || empty($password)) {
        header("Location: login.html?error=emptyfields");
        exit();
    }

    // Secure the table selection
    $table = ($userType === 'admin') ? 'Admins' : 'Users';

    $sql = "SELECT * FROM $table WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: login.html?error=sqlerror");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Secure password comparison
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = $userType;
            $_SESSION['username'] = $row['username'];

            if ($userType === 'admin') {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['role'] = $row['role'];

                $updateSql = "UPDATE Admins SET last_login = CURRENT_TIMESTAMP WHERE admin_id = ?";
                $updateStmt = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($updateStmt, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "i", $row['admin_id']);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                // Redirect admin to admin_panel.html
                header("Location: admin_panel.html");
                exit();
            } else {
                $_SESSION['user_id'] = $row['user_id'];

                $updateSql = "UPDATE Users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
                $updateStmt = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($updateStmt, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "i", $row['user_id']);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                // Redirect regular user to user_view.html
                header("Location: user_view.html");
                exit();
            }
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: login.html?error=wrongpassword");
            exit();
        }
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: login.html?error=nouser");
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
?>
