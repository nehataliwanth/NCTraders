<?php

session_start();
include 'config/database.php';

$call_id = intval($_GET['call_id']);

$result = mysqli_query($conn,

"SELECT
caller_ice,
receiver_ice

FROM calls

WHERE id='$call_id'");

$data =
mysqli_fetch_assoc($result);

echo json_encode($data);