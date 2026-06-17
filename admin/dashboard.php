<?php
session_start();

include '../config/database.php';
include '../includes/security.php';

if(!isset($_SESSION['user_id'])){

    header("Location: ../login.php");
    exit();
}

validateAdmin();

$users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));

$products = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));

$orders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));

$messages = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM messages"));
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard | NCTraders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<a class="navbar-brand">
NCTraders Admin
</a>

<a href="../logout.php" class="btn btn-warning">
Logout
</a>

</div>

</nav>

<div class="container py-5">

<h1 class="mb-5">
Admin Dashboard
</h1>

<div class="row g-4">

<div class="col-md-3">

<div class="card shadow text-center p-4">

<h2>

<?php echo $users; ?>

</h2>

<p>Total Users</p>

</div>

</div>

<div class="col-md-3">

<div class="card shadow text-center p-4">

<h2>

<?php echo $products; ?>

</h2>

<p>Total Products</p>

</div>

</div>

<div class="col-md-3">

<div class="card shadow text-center p-4">

<h2>

<?php echo $orders; ?>

</h2>

<p>Total Orders</p>

</div>

</div>

<div class="col-md-3">

<div class="card shadow text-center p-4">

<h2>

<?php echo $messages; ?>

</h2>

<p>Total Messages</p>

</div>

</div>

</div>

<div class="mt-5">

<a href="users.php" class="btn btn-dark me-3">

Manage Users

</a>

<a href="products.php" class="btn btn-dark">

Manage Products

</a>

</div>

</div>

</body>
</html>

