<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT wishlist.*, products.*
        FROM wishlist
        JOIN products
        ON wishlist.product_id = products.id
        WHERE wishlist.user_id='$user_id'
        ORDER BY wishlist.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Wishlist | NCTraders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

<style>

.wishlist-image{
    width: 100px;
    height: 100px;
    object-fit: cover;
}

</style>

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<a href="index.php" class="navbar-brand">
NCTraders
</a>

<a href="dashboard.php" class="btn btn-warning">
Dashboard
</a>

</div>

</nav>

<div class="container py-5">

<h1 class="mb-4">
My Wishlist
</h1>

<div class="row g-4">

<?php

if(mysqli_num_rows($result) > 0){

    while($row = mysqli_fetch_assoc($result)){

?>

<div class="col-md-4">

<div class="card shadow h-100">

<img src="uploads/products/<?php echo htmlspecialchars($row['product_image'] ?? ''); ?>"
class="card-img-top wishlist-image">

<div class="card-body">

<h5>

<?php echo htmlspecialchars($row['product_name'] ?? 'Product'); ?>

</h5>

<h4 class="text-success">

R<?php echo number_format($row['product_price'] ?? $row['price'] ?? 0, 2); ?>

</h4>

<a href="product.php?id=<?php echo $row['product_id']; ?>"
class="btn btn-dark w-100">

View Product

</a>

</div>

</div>

</div>

<?php

    }

} else {

    echo "<div class='alert alert-info'>Wishlist is empty.</div>";
}

?>

</div>

</div>

</body>
</html>
