<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
exit();
}

$user_id = $_SESSION['user_id'];

$latitude =
mysqli_real_escape_string(
$conn,
$_POST['latitude']
);

$longitude =
mysqli_real_escape_string(
$conn,
$_POST['longitude']
);

/* UPDATE ACTIVE LIVE LOCATION */

mysqli_query($conn,

"UPDATE live_locations

SET

latitude='$latitude',
longitude='$longitude'

WHERE sender_id='$user_id'

AND is_active='1'

AND expires_at > NOW()");

echo "updated";