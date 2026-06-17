<?php
session_start();

include 'config/database.php';

$message = "";

$token = $_GET['token'] ?? '';

$check = mysqli_query($conn,

"SELECT * FROM password_resets

WHERE token='$token'");

if(mysqli_num_rows($check) == 0){

die("Invalid token.");
}

$data = mysqli_fetch_assoc($check);

$email = $data['email'];

if(isset($_POST['save'])){

$password = $_POST['password'];

$confirm = $_POST['confirm_password'];

if($password != $confirm){

$message = "

<div class='alert alert-danger'>

Passwords do not match.

</div>

";

}else{

$hashed = password_hash($password,PASSWORD_DEFAULT);

mysqli_query($conn,

"UPDATE users

SET password='$hashed'

WHERE email='$email'");

mysqli_query($conn,

"DELETE FROM password_resets

WHERE email='$email'");

$message = "

<div class='alert alert-success'>

Password updated successfully.

<a href='login.php'>

Login

</a>

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
Reset Password
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

Reset Password

</h2>

<?php echo $message; ?>

<form method="POST">

<input type="password"
name="password"
class="form-control mb-3"
placeholder="New Password"
required>

<input type="password"
name="confirm_password"
class="form-control mb-3"
placeholder="Confirm New Password"
required>

<button type="submit"
name="save"
class="btn btn-dark w-100 rounded-pill">

Save Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>
</html>