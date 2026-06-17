<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$user_id = $_SESSION['user_id'];

$blocked_id = intval($_GET['id']);

mysqli_query($conn,

"DELETE FROM blocked_users

WHERE

user_id='$user_id'

AND blocked_user_id='$blocked_id'");

header("Location: account.php");
exit();

?>