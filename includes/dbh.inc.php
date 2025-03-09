<?php
// Database connection parameters
$serverName = "localhost";
$dbUsername = "root";  
$dbPassword = "";      
$dbName = "tronic_inventory";

// Create connection
$conn = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>