<?php
session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];

$seller_id = $_GET['seller_id'];

$message_status = "";

if(isset($_POST['send_message'])){

    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO messages
    (sender_id, receiver_id, message)

    VALUES
    ('$sender_id','$seller_id','$message')";

    if(mysqli_query($conn, $sql)){

        $message_status = "Message Sent Successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Message Seller | NCTraders</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<a href="index.php" class="navbar-brand">
NCTraders
</a>

<a href="messages.php" class="btn btn-warning">
Inbox
</a>

</div>

</nav>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-body p-5">

<h2 class="mb-4 text-center">

Message Seller

</h2>

<?php if($message_status != ""){ ?>

<div class="alert alert-success">

<?php echo $message_status; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label>Your Message</label>

<textarea name="message"
class="form-control"
rows="6"
required></textarea>

</div>

<button type="submit"
name="send_message"
class="btn btn-dark w-100">

Send Message

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>
</html>
