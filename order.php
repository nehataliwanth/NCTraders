<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>
My Orders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">
<button onclick="history.back()"
class="btn btn-dark rounded-pill mb-4">

<i class="fas fa-arrow-left"></i>

Back

</button>

<h1 class="mb-5">

My Orders

</h1>

<div class="card shadow-sm p-4">

<h4>
No Orders Yet
</h4>

<p>
Your purchases will appear here.
</p>

</div>

</div>

</body>
</html>