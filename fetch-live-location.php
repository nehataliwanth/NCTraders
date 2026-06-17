<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
exit();
}

$user_id = $_SESSION['user_id'];

$sender_id =
intval($_GET['sender_id']);

$query = mysqli_query($conn,

"SELECT *

FROM live_locations

WHERE sender_id='$sender_id'

AND receiver_id='$user_id'

AND is_active='1'

AND expires_at > NOW()

LIMIT 1");

if(mysqli_num_rows($query) > 0){

$data =
mysqli_fetch_assoc($query);

echo json_encode([
'status' => 'active',
'latitude' => $data['latitude'],
'longitude' => $data['longitude'],
'expires_at' => $data['expires_at']
]);

}
else{

echo json_encode([
'status' => 'inactive'
]);

}