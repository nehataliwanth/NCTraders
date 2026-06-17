<?php
session_start();

include '../config/database.php';
include '../includes/security.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

validateAdmin();

$sql = "SELECT products.*, users.username
        FROM products
        JOIN users
        ON products.seller_id = users.id
        ORDER BY products.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Products | NCTraders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>

.product-image{
    width: 100px;
    height: 100px;
    object-fit: cover;
}

</style>

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<a href="admin-dashboard.php" class="navbar-brand">
NCTraders Admin
</a>

<a href="../logout.php" class="btn btn-warning">
Logout
</a>

</div>

</nav>

<div class="container py-5">

<h1 class="mb-4">
Manage Products
</h1>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered align-middle">

<thead>

<tr>

<th>ID</th>
<th>Image</th>
<th>Product</th>
<th>Seller</th>
<th>Price</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result) > 0){

    while($row = mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<img src="<?php echo $row['image_url']; ?>"
class="product-image rounded">

</td>

<td>

<?php echo $row['product_name']; ?>

</td>

<td>

<?php echo $row['username']; ?>

</td>

<td class="text-success fw-bold">

<?php echo number_format($row['price'],2); ?>

</td>

<td>

<a href="delete-product.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm">

Delete

</a>

</td>

</tr>

<?php

    }

} else {

    echo "<tr><td colspan='6'>No products found.</td></tr>";
}

?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>

