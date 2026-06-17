<?php

session_start();
include 'config/database.php';

$call_id = intval($_POST['call_id']);

$answer =
mysqli_real_escape_string(
$conn,
$_POST['answer']
);

mysqli_query($conn,

"UPDATE calls

SET answer='$answer'

WHERE id='$call_id'");

echo "success";