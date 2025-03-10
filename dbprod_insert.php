<?php
#establish connection
include 'dbconnection.php';
#collect values from the form
include 'dbproducts.php';

#define the query string
$sql = "INSERT INTO products (prod_name, prod_desc, category, price, stock_quantity, manufacturer, release_date) VALUES ('$prod_name', '$prod_desc', '$category', '$price', '$stock_quantity', '$manufacturer', '$release_date')";

#test the query
if (mysqli_query($conn, $sql)) {
  echo "A new record has been created successfully";
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
#disconnect
mysqli_close($conn);
?>