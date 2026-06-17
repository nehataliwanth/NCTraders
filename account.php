<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn,$sql);

$user = mysqli_fetch_assoc($result);

function hideEmail($email){

$parts = explode("@",$email);

$name = substr($parts[0],0,3);

return $name . "******@" . $parts[1];
}

function hidePhone($phone){

$phone = preg_replace('/[^0-9]/', '', $phone);

if(strlen($phone) >= 10){

return substr($phone,0,2)
. "******"
. substr($phone,-2);

}

return "Not Added";

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
My Account | NC Traders
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

<div class="card shadow border-0 rounded-4">

<div class="card-body p-5">

<h1 class="fw-bold mb-5">

My Account

</h1>

<!-- ACCOUNT INFO -->

<div class="mb-5">

<h4 class="fw-bold mb-3">

Manage Account

</h4>
<p>

<strong>Username:</strong>

<?php echo htmlspecialchars($user['username']); ?>

</p>

<p>

<strong>Email:</strong>

<?php echo hideEmail($user['email']); ?>

</p>

<p>

<strong>Phone:</strong>

<?php echo hidePhone($user['phone']); ?>

</p>

<a href="change-password.php"
class="btn btn-warning rounded-pill mt-2">

Change Password

</a>

<button class="btn btn-danger rounded-pill mt-2 ms-2"
data-bs-toggle="modal"
data-bs-target="#deleteModal">

Delete Account

</button>

</div>

<hr>

<!-- BLOCKED USERS -->

<div class="mb-5">

<h4 class="fw-bold mb-3">

Blocked Contacts

</h4>

<?php

$blockedUsers = mysqli_query($conn,

"SELECT users.*

FROM blocked_users

JOIN users
ON blocked_users.blocked_user_id = users.id

WHERE blocked_users.user_id='$user_id'");

?>

<?php if(mysqli_num_rows($blockedUsers) > 0): ?>

<?php while($blocked = mysqli_fetch_assoc($blockedUsers)): ?>

<div class="d-flex justify-content-between align-items-center mb-3">

<div>

<?php echo htmlspecialchars($blocked['username']); ?>

</div>

<a href="unblock-user.php?id=<?php echo $blocked['id']; ?>"
class="btn btn-warning btn-sm rounded-pill">

Unblock

</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>

No blocked contacts.

</p>

<?php endif; ?>

</div>

<hr>

<div class="mb-5">

<h4 class="fw-bold mb-3">

Privacy & Cookie Policy

</h4>

<p>

NC Traders protects your personal information and does not share your private data with third parties without your consent.

Cookies may be used to improve user experience and marketplace functionality.

</p>

</div>

<hr>

<!-- TERMS -->

<div class="mb-5">

<h4 class="fw-bold mb-3">

Terms & Conditions

</h4>

<p>

Users are responsible for arranging safe meetings and payments independently.

NC Traders is not responsible for scams, damages, or payment disputes between buyers and sellers.

Always meet in public places.

</p>

</div>

<hr>

<!-- FEEDBACK -->

<div class="mb-5">

<h4 class="fw-bold mb-3">

Ratings & Feedback

</h4>

<p>

Send your feedback, ratings, and suggestions directly to the NC Traders support team.

</p>

<a href="mailto:nctraders@gmail.com?subject=NC Traders Feedback"
class="btn btn-warning rounded-pill">

<i class="fas fa-envelope me-2"></i>

Send Feedback

</a>

</div>

<hr>

<hr>

<!-- CONTACT -->

<div>

<h4 class="fw-bold mb-3">

Contact Us

</h4>

<p>

<i class="fas fa-envelope me-2"></i>

nctraders@gmail.com

</p>

<p>

<i class="fas fa-phone me-2"></i>

0842600049

</p>

<p>

Open Monday to Friday
9:00 AM - 5:00 PM

</p>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DELETE ACCOUNT MODAL -->

<div class="modal fade"
id="deleteModal"
tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content border-0 rounded-4">

<div class="modal-header border-0">

<h5 class="modal-title fw-bold">

Delete Account

</h5>

<button type="button"
class="btn-close"
data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

Are you sure you want to permanently delete your account?

This action cannot be undone.

</div>

<div class="modal-footer border-0">

<button type="button"
class="btn btn-secondary rounded-pill"
data-bs-dismiss="modal">

Cancel

</button>

<a href="delete-account.php"
class="btn btn-danger rounded-pill">

Yes, Delete

</a>

</div>

</div>

</div>

</div>

</body>
</html>