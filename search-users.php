<?php

include 'config/database.php';

$search =
mysqli_real_escape_string(
$conn,
$_GET['query'] ?? ''
);

if(strlen($search) >= 1){

$result = mysqli_query($conn,

"SELECT id, username

FROM users

WHERE username LIKE '%$search%'

LIMIT 8");

if(mysqli_num_rows($result) > 0){

while($user = mysqli_fetch_assoc($result)){

echo '

<a href="messages.php?user=' .
$user['id'] .
'" class="message-search-item">

' .

htmlspecialchars($user['username'])

. '

</a>

';

}

}
else {

echo '

<div class="message-search-empty">

User not found

</div>

';

}

}
?>