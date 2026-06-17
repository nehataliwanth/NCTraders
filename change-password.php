<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$message = "";

$user_id = $_SESSION['user_id'];

if(isset($_POST['save'])){

$current = $_POST['current_password'];

$new = $_POST['new_password'];

$confirm = $_POST['confirm_password'];

$sql = "SELECT * FROM users
WHERE id='$user_id'";

$result = mysqli_query($conn,$sql);

$user = mysqli_fetch_assoc($result);

if(!password_verify($current,$user['password'])){

$message = "

<div class='alert alert-danger'>

Current password is incorrect.

</div>

";

}
elseif($new != $confirm){

$message = "

<div class='alert alert-danger'>

New passwords do not match.

</div>

";

}
else{

$newPassword = password_hash($new,PASSWORD_DEFAULT);

mysqli_query($conn,

"UPDATE users
SET password='$newPassword'
WHERE id='$user_id'");

$message = "

<div class='alert alert-success'>

Password changed successfully.

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
Change Password
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body class="bg-light">

<?php include 'includes/navbar.php'; ?>

<div class="container py-5"
style="margin-top:120px;">

<button onclick="history.back()"
class="btn btn-dark rounded-pill mb-4">

<i class="fas fa-arrow-left"></i>

Back

</button>

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow border-0 rounded-4">

<div class="card-body p-5">

<h2 class="fw-bold mb-4">

Change Password

</h2>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Current Password

</label>

<input type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

New Password

</label>

<input type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-4">

<label class="form-label">

Confirm New Password

</label>

<input type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button type="submit"
name="save"
class="btn btn-warning w-100 rounded-pill">

Save New Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>