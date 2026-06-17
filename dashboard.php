<?php

session_start();

?>

<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Dashboard | NC Traders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<span class="navbar-brand fw-bold">

NC Traders Dashboard

</span>

<a href="logout.php"
class="btn btn-warning rounded-pill px-4">

Logout

</a>

</div>

</nav>

<div class="container py-5">

<div class="card shadow border-0 rounded-4 p-5">

<h1 class="fw-bold">

Welcome,
<?php echo $_SESSION['fullname']; ?>

</h1>

<p class="mt-4 fs-5">

You are successfully logged into NC Traders.

</p>

<a href="index.php"
class="btn btn-dark btn-lg rounded-pill mt-4">

<i class="fas fa-house me-2"></i>

Go To Home Page

</a>

</div>

</div>

</body>
</html>