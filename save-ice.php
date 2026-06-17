<?php

session_start();
include 'config/database.php';

$call_id = intval($_POST['call_id']);

$type =
$_POST['type'];

$ice =
mysqli_real_escape_string(
$conn,
$_POST['ice']
);

if($type === 'caller'){

mysqli_query($conn,

"UPDATE calls

SET caller_ice='$ice'

WHERE id='$call_id'");

}
else{

mysqli_query($conn,

"UPDATE calls

SET receiver_ice='$ice'

WHERE id='$call_id'");

}

echo "success";