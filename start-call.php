<?php

session_start();
include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    exit;
}

$caller_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$call_type = $_POST['call_type'];

mysqli_query($conn,

"INSERT INTO calls
(caller_id, receiver_id, call_type, status)

VALUES
('$caller_id','$receiver_id','$call_type','ringing')"

);

echo "success";