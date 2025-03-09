<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html?error=unauthorized");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: product_management.html?status=error&message=No product specified");
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Get product data
$productId = $_GET['id'];
$sql = "SELECT * FROM Products WHERE product_id = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: product_management.html?status=error&message=SQL error");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $product = $row;
} else {
    header("Location: product_management.html?status=error&message=Product not found");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Tronic Shop</title>
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
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .button-group {
            margin-top: 20px;
        }
        .cancel-button {
            background-color: #6c757d;
        }
        .cancel-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        
        <form action="product_management.inc.php" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            
            <div class="form-group">
                <label for="prod_name">Product Name *</label>
                <input type="text" id="prod_name" name="prod_name" value="<?php echo htmlspecialchars($product['prod_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="prod_desc">Description</label>
                <textarea id="prod_desc" name="prod_desc"><?php echo htmlspecialchars($product['prod_desc']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Smartphones" <?php if($product['category'] == 'Smartphones') echo 'selected'; ?>>Smartphones</option>
                    <option value="Laptops" <?php if($product['category'] == 'Laptops') echo 'selected'; ?>>Laptops</option>
                    <option value="Tablets" <?php if($product['category'] == 'Tablets') echo 'selected'; ?>>Tablets</option>
                    <option value="Audio" <?php if($product['category'] == 'Audio') echo 'selected'; ?>>Audio</option>
                    <option value="Gaming" <?php if($product['category'] == 'Gaming') echo 'selected'; ?>>Gaming</option>
                    <option value="Accessories" <?php if($product['category'] == 'Accessories') echo 'selected'; ?>>Accessories</option>
                    <option value="Other" <?php if($product['category'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Price (KSh) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="manufacturer">Manufacturer</label>
                <input type="text" id="manufacturer" name="manufacturer" value="<?php echo htmlspecialchars($product['manufacturer']); ?>">
            </div>
            
            <div class="form-group">
                <label for="release_date">Release Date</label>
                <input type="date" id="release_date" name="release_date" value="<?php echo $product['release_date']; ?>">
            </div>
            
            <div class="button-group">
                <button type="submit">Update Product</button>
                <button type="button" class="cancel-button" onclick="window.location.href='product_management.html#view-products'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>