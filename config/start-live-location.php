<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
exit();
}

$user_id = $_SESSION['user_id'];

$receiver_id =
intval($_POST['receiver_id']);

$duration =
intval($_POST['duration']);

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

/* CALCULATE EXPIRY */

$expires_at =
date(
'Y-m-d H:i:s',
strtotime("+$duration minutes")
);

/* REMOVE OLD ACTIVE TRACKING */

mysqli_query($conn,

"UPDATE live_locations

SET is_active='0'

WHERE sender_id='$user_id'

AND receiver_id='$receiver_id'");

/* CREATE NEW LIVE SESSION */

mysqli_query($conn,

"INSERT INTO live_locations
(
sender_id,
receiver_id,
latitude,
longitude,
expires_at
)

VALUES
(
'$user_id',
'$receiver_id',
'$latitude',
'$longitude',
'$expires_at'
)");

echo "success";