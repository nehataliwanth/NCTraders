<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$query = mysqli_query($conn,
"SELECT * FROM products WHERE id='$id'");

$product = mysqli_fetch_assoc($query);

$image = $product['product_image'];

unlink("uploads/products/" . $image);

mysqli_query($conn,
"DELETE FROM products WHERE id='$id'");

header("Location: manage-products.php");
?>
