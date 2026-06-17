<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/db.php";

if(
!isset($_SESSION['admin_id'])
||
$_SESSION['role'] != 'admin'
){

header("Location: admin-login.php");

exit();

}

/* TOTAL USERS */

$usersQuery = mysqli_query(
$conn,
"SELECT COUNT(*) AS total FROM users"
);

if(!$usersQuery){
    die(mysqli_error($conn));
}

$totalUsers = mysqli_fetch_assoc($usersQuery)['total'];

/* TOTAL PRODUCTS */

$productsQuery = mysqli_query(
$conn,
"SELECT COUNT(*) AS total FROM products"
);

if(!$productsQuery){
    die(mysqli_error($conn));
}

$totalProducts = mysqli_fetch_assoc($productsQuery)['total'];

/* TOTAL MESSAGES */

$messagesQuery = mysqli_query(
$conn,
"SELECT COUNT(*) AS total FROM messages"
);

if(!$messagesQuery){
    die(mysqli_error($conn));
}

$totalMessages = mysqli_fetch_assoc($messagesQuery)['total'];

?>

<!DOCTYPE html>
<html>

<head>

<title>
Admin Dashboard
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<span class="navbar-brand">

NC Traders Admin

</span>

<a href="../logout.php"
class="btn btn-warning">

Logout

</a>

</div>

</nav>

<div class="container py-5">

<h1 class="mb-4">

Welcome,
<?php echo $_SESSION['admin_name']; ?>

</h1>

<div class="row">

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<h5>Total Users</h5>

<h2>

<?php echo $totalUsers; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<h5>Total Products</h5>

<h2>

<?php echo $totalProducts; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<h5>Total Messages</h5>

<h2>

<?php echo $totalMessages; ?>

</h2>

</div>

</div>

</div>

</div>

<hr class="my-5">

<h3>

Administrative Controls

</h3>

<div class="mt-4">

<a href="users.php"
class="btn btn-primary me-2">
Manage Users
</a>

<a href="products.php"
class="btn btn-success me-2">
Manage Products
</a>

<a href="reports.php"
class="btn btn-danger">
Manage Reports
</a>

</div>

</div>

</body>

</html>