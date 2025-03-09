<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html?error=unauthorized");
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Check if ID and type are provided
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: user_management.html?status=error&message=Invalid request");
    exit();
}

$id = $_GET['id'];
$userType = $_GET['type'];
$isAdmin = ($userType === 'admin');

// Fetch user data
$table = $isAdmin ? 'Admins' : 'Users';
$idField = $isAdmin ? 'admin_id' : 'user_id';

$sql = "SELECT * FROM $table WHERE $idField = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: user_management.html?status=error&message=SQL error");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    header("Location: user_management.html?status=error&message=User not found");
    exit();
}

$userData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tronic Shop - Edit <?php echo $isAdmin ? 'Admin' : 'User'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], 
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-buttons">
            <h1>Edit <?php echo $isAdmin ? 'Admin' : 'User'; ?></h1>
            <div>
                <a href="user_management.html#view-users"><button type="button" style="background-color: #6c757d;">Back to User Management</button></a>
            </div>
        </div>
        
        <form action="user_management.inc.php" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_type" value="<?php echo $userType; ?>">
            <input type="hidden" name="<?php echo $idField; ?>" value="<?php echo $userData[$idField]; ?>">
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password (leave empty to keep current password)</label>
                <input type="password" id="password" name="password">
            </div>
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>">
            </div>
            
            <?php if ($isAdmin): ?>
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role">
                    <option value="Store Manager" <?php echo ($userData['role'] == 'Store Manager') ? 'selected' : ''; ?>>Store Manager</option>
                    <option value="Inventory Manager" <?php echo ($userData['role'] == 'Inventory Manager') ? 'selected' : ''; ?>>Inventory Manager</option>
                    <option value="Customer Support" <?php echo ($userData['role'] == 'Customer Support') ? 'selected' : ''; ?>>Customer Support</option>
                    <option value="Technical Support" <?php echo ($userData['role'] == 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                </select>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($userData['phone_number']); ?>">
            </div>
            <?php endif; ?>
            
            <button type="submit">Update <?php echo $isAdmin ? 'Admin' : 'User'; ?></button>
        </form>
    </div>
</body>
</html>
<?php
// Close connection
mysqli_close($conn);
?>