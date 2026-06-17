<?php
session_start();

include '../config/database.php';
include '../includes/security.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

validateAdmin();

$id = intval($_GET['id']);

$product_query = mysqli_query($conn,
"SELECT * FROM products WHERE id='$id'");

$product = mysqli_fetch_assoc($product_query);

$image = $product['product_image'];

unlink("../uploads/products/" . $image);

mysqli_query($conn,
"DELETE FROM products WHERE id='$id'");

header("Location: products.php");
?>
