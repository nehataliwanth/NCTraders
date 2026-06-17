<?php

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
exit();
}

$user_id =
$_SESSION['user_id'];

$receiver_id =
intval($_POST['receiver_id']);

if(isset($_FILES['audio'])){

$file =
$_FILES['audio'];

$fileName =
time() . '.webm';

$target =
'voice_notes/' . $fileName;

move_uploaded_file(
$file['tmp_name'],
$target
);

/* SAVE MESSAGE */

mysqli_query($conn,

"INSERT INTO messages
(
sender_id,
receiver_id,
message,
voice_note
)

VALUES
(
'$user_id',
'$receiver_id',
'',
'$target'
)");

echo "success";

}