<?php
session_start();

include 'config/database.php';

$message = "";

if(isset($_POST['reset'])){

$email = mysqli_real_escape_string($conn,$_POST['email']);

$check = mysqli_query($conn,

"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($check) > 0){

$token = md5(rand());

mysqli_query($conn,

"INSERT INTO password_resets(email,token)

VALUES('$email','$token')");

$link = "http://localhost/nctraders/reset-password.php?token=$token";

$message = "

<div class='alert alert-success'>

Password reset link generated:<br><br>

<a href='$link'>$link</a>

</div>

";

}else{

$message = "

<div class='alert alert-danger'>

Email not found.

</div>

";
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Forgot Password
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow border-0 rounded-4">

<div class="card-body p-5">

<h2 class="fw-bold mb-4">

Forgot Password

</h2>

<?php echo $message; ?>

<form method="POST">

<input type="email"
name="email"
class="form-control mb-3"
placeholder="Enter your email"
required>

<button type="submit"
name="reset"
class="btn btn-dark w-100 rounded-pill">

Send Reset Link

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>
</html>