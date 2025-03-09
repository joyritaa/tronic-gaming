<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    echo "Unauthorized access";
    exit();
}

// Include database connection
require 'dbh.inc.php';

// Get all products
$sql = "SELECT * FROM Products ORDER BY prod_name ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price (KSh)</th>
            <th>Stock</th>
            <th>Manufacturer</th>
            <th>Actions</th>
          </tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['prod_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . $row['stock_quantity'] . "</td>";
        echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
        echo "<td>
                <button onclick=\"editProduct(" . $row['product_id'] . ")\" style=\"background-color: #007bff;\">Edit</button>
                <button onclick=\"deleteProduct(" . $row['product_id'] . ", '" . htmlspecialchars($row['prod_name'], ENT_QUOTES) . "')\" class=\"delete\">Delete</button>
              </td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Add JavaScript for edit/delete functionality
    echo "<script>
        function editProduct(productId) {
            window.location.href = 'edit_product.php?id=' + productId;
        }
        
        function deleteProduct(productId, productName) {
            if (confirm('Are you sure you want to delete \"' + productName + '\"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'product_management.inc.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'product_id';
                idInput.value = productId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>";
} else {
    echo "<p>No products found. Add some products to see them here.</p>";
}

// Close connection
mysqli_close($conn);
?>