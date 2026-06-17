<?php

session_start();

session_start();

include 'config/database.php';

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$user_id = $_SESSION['user_id'];

/* SELECT CHAT USER */

$chat_user =
intval($_GET['user'] ?? 0);

$product_id =
intval($_GET['product'] ?? 0);

/* SEND MESSAGE */

$blockedCheck = mysqli_query($conn,

"SELECT *

FROM blocked_users

WHERE

(user_id='$chat_user'
AND blocked_user_id='$user_id')
OR

(user_id='$user_id'
AND blocked_user_id='$chat_user')");


if(isset($_POST['send_message'])){

$message = mysqli_real_escape_string(
$conn,
$_POST['message']
);

$attachment = NULL;
$attachment_type = NULL;

/* PHOTO OR VIDEO */

if(isset($_FILES['photo']) &&
$_FILES['photo']['error'] == 0){

$fileName =
time().'_'.
basename($_FILES['photo']['name']);

$target =
'uploads/messages/'.
$fileName;

move_uploaded_file(
$_FILES['photo']['tmp_name'],
$target
);

$attachment = $target;

$extension =
strtolower(
pathinfo(
$fileName,
PATHINFO_EXTENSION
)
);

if(
in_array(
$extension,
['jpg','jpeg','png','gif','webp']
)
){

$attachment_type = 'image';

}
elseif(
in_array(
$extension,
['mp4','mov','avi','webm']
)
){

$attachment_type = 'video';

}

}

/* DOCUMENT */

if(isset($_FILES['document']) &&
$_FILES['document']['error'] == 0){

$fileName =
time().'_'.
basename($_FILES['document']['name']);

$target =
'uploads/messages/'.
$fileName;

move_uploaded_file(
$_FILES['document']['tmp_name'],
$target
);

$attachment = $target;

$attachment_type = 'document';

}

/* SEND MESSAGE */

if(
$message != '' ||
$attachment != NULL
){


/* INSERT MESSAGE */

mysqli_query($conn,

"INSERT INTO messages
(
sender_id,
receiver_id,
product_id,
message,
attachment,
attachment_type
)

VALUES
(
'$user_id',
'$chat_user',
'$product_id',
'$message',
'$attachment',
'$attachment_type'
)"

);

}
header(
"Location: messages.php?user=$chat_user"
);

exit();

}

/* DELETE CHAT */

if(isset($_GET['delete'])){

$delete_user = intval($_GET['delete']);

/* HIDE CHAT FOR CURRENT USER ONLY */

$checkDeleted = mysqli_query($conn,

"SELECT id

FROM deleted_conversations

WHERE

user_id='$user_id'

AND deleted_user_id='$delete_user'");

if(mysqli_num_rows($checkDeleted) == 0){

/* GET LAST MESSAGE ID */

$lastMsgQuery = mysqli_query($conn,

"SELECT MAX(id) AS last_id

FROM messages

WHERE

(sender_id='$user_id'
AND receiver_id='$delete_user')

OR

(sender_id='$delete_user'
AND receiver_id='$user_id')");

$lastMsg =
mysqli_fetch_assoc($lastMsgQuery);

$lastMessageId =
$lastMsg['last_id'] ?? 0;

/* SAVE DELETE POINT */

mysqli_query($conn,

"INSERT INTO deleted_conversations
(
user_id,
deleted_user_id,
last_message_id
)

VALUES
(
'$user_id',
'$delete_user',
'$lastMessageId'
)");

}

/* REMOVE BLOCKS */

mysqli_query($conn,

"DELETE FROM blocked_users

WHERE

(user_id='$user_id'
AND blocked_user_id='$delete_user')

OR

(user_id='$delete_user'
AND blocked_user_id='$user_id')");


header("Location: messages.php");
exit();

}

/* BLOCK USER */

if(isset($_GET['block'])){

$block_user = intval($_GET['block']);

$check = mysqli_query($conn,

"SELECT *

FROM blocked_users

WHERE

user_id='$user_id'

AND blocked_user_id='$block_user'");

if(mysqli_num_rows($check) == 0){

mysqli_query($conn,

"INSERT INTO blocked_users
(user_id, blocked_user_id)

VALUES
('$user_id','$block_user')"

);

}

header("Location: messages.php");
exit();

}

/* REPORT USER */

if(isset($_POST['report_user'])){

$reported_user =
intval($_POST['reported_user']);

$reason = mysqli_real_escape_string(
$conn,
$_POST['reason']
);

mysqli_query($conn,

"INSERT INTO reports
(reporter_id, reported_user_id, reason)

VALUES
('$user_id','$reported_user','$reason')"

);

}

/* CONVERSATIONS */

$userSearchResults = null;

if(isset($_GET['search_user'])){

$search =
mysqli_real_escape_string(
$conn,
$_GET['search_user']
);

$userSearchResults = mysqli_query($conn,

"SELECT id, username

FROM users

WHERE username LIKE '%$search%'

AND id != '$user_id'

LIMIT 10");

}

$conversations = mysqli_query($conn,

"SELECT

u.id AS chat_user_id,
u.username,

MAX(m.created_at) AS latest_time

FROM users u

JOIN messages m

ON

(
m.sender_id = u.id
AND m.receiver_id = '$user_id'
)

OR

(
m.receiver_id = u.id
AND m.sender_id = '$user_id'
)

WHERE
u.id != '$user_id'

GROUP BY u.id, u.username

HAVING COUNT(m.id) > 0

ORDER BY latest_time DESC"

);

/* GET CURRENT CHAT */

$currentChat = null;
$isBlocked = false;

if($chat_user > 0){
$blockedCheck = mysqli_query($conn,

"SELECT *

FROM blocked_users

WHERE

(user_id='$user_id'
AND blocked_user_id='$chat_user')

OR

(user_id='$chat_user'
AND blocked_user_id='$user_id')");

if(mysqli_num_rows($blockedCheck) > 0){

$isBlocked = true;

}

$currentChatQuery = mysqli_query($conn,

"SELECT *

FROM users

WHERE id='$chat_user'");

$currentChat =
mysqli_fetch_assoc($currentChatQuery);
$deleteRecord = mysqli_query($conn,

"SELECT last_message_id

FROM deleted_conversations

WHERE

user_id='$user_id'

AND deleted_user_id='$chat_user'

LIMIT 1");

$deleteData =
mysqli_fetch_assoc($deleteRecord);

$lastDeletedMessageId =
$deleteData['last_message_id'] ?? 0;

}

/* GET MESSAGES */

$messages = null;

if($chat_user > 0){

/* GET DELETE POINT */

$deleteRecord = mysqli_query($conn,

"SELECT last_message_id

FROM deleted_conversations

WHERE

user_id='$user_id'

AND deleted_user_id='$chat_user'

LIMIT 1");

$deleteData =
mysqli_fetch_assoc($deleteRecord);

$lastDeletedMessageId =
$deleteData['last_message_id'] ?? 0;

/* LOAD ONLY NEWER MESSAGES */

$messages = mysqli_query($conn,

"SELECT messages.*,
users.username

FROM messages

JOIN users
ON messages.sender_id = users.id

WHERE

(
(sender_id='$user_id'
AND receiver_id='$chat_user')

OR

(sender_id='$chat_user'
AND receiver_id='$user_id')
)

AND messages.id >

COALESCE(

(
SELECT last_message_id

FROM deleted_conversations

WHERE

user_id='$user_id'

AND deleted_user_id='$chat_user'

LIMIT 1

),

0

)

ORDER BY created_at ASC"
);
$isDeletedConversation =

(
mysqli_num_rows($messages) == 0
&&
$lastDeletedMessageId > 0
);

}

/* MARK READ */

if($chat_user > 0){

mysqli_query($conn,

"UPDATE messages

SET is_read='1'

WHERE receiver_id='$user_id'

AND sender_id='$chat_user'");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Messages | NC Traders
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet"
href="assets/css/style.css">

<style>

body{
background:#ece5dd;
margin:0;
padding:0;
overflow:hidden;
overflow-x:hidden;
}

.messaging-layout{
display:flex;
height:100dvh;
padding-top:80px;
overflow:hidden;
}

.sidebar{
width:380px;
max-width:100%;
background:white;
border-right:1px solid #eee;
padding:20px;
overflow-y:auto;
}

.sidebar-header{
padding:20px;
font-weight:bold;
font-size:22px;
border-bottom:1px solid #eee;
}

.chat-user{
padding:18px 20px;
border-bottom:1px solid #f2f2f2;
cursor:pointer;
transition:0.2s;
display:block;
text-decoration:none;
color:black;
}

.chat-user:hover{
background:#f5f5f5;
}

.chat-area{
flex:1;
display:flex;
flex-direction:column;
overflow:hidden;
height:100%;
}
.chat-top{
background:#162331;
margin-top:5px;
color:white;
padding:15px 25px;
font-weight:600;
display:flex;
justify-content:space-between;
align-items:center;
min-height:65px;
flex-shrink:0;
}

.chat-box{
flex:1;
overflow-y:auto;
padding:25px;
display:flex;
flex-direction:column;
gap:15px;
background:#e5ddd5;
min-height:0;
}
.message-row{
display:flex;
width:100%;
}

.sent{
justify-content:flex-end;
}

.received{
justify-content:flex-start;
}

.message-bubble{
max-width:70%;
padding:14px 18px;
border-radius:18px;
box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.sent .message-bubble{
background:#d9fdd3;
border-bottom-right-radius:5px;
}

.received .message-bubble{
background:white;
border-bottom-left-radius:5px;
}

.message-time{
font-size:11px;
margin-top:8px;
text-align:right;
color:#666;
}

.chat-form{
background:#f0f2f5;
padding:12px 15px;
display:flex;
gap:10px;
align-items:center;
flex-shrink:0;
border-top:1px solid #ddd;
margin-top:auto;
}

.chat-input{
flex:1;
border:none;
border-radius:30px;
padding:14px 20px;
outline:none;
}

.send-btn{
border:none;
background:#00a884;
color:white;
width:55px;
height:55px;
border-radius:50%;
font-size:20px;
flex-shrink:0;
display:flex;
justify-content:center;
align-items:center;
}

.action-buttons a{
margin-left:10px;
}

.empty-chat{
flex:1;
display:flex;
justify-content:center;
align-items:center;
flex-direction:column;
background:#e5ddd5;
padding:40px;
text-align:center;
color:#777;
}

</style>

</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="messaging-layout">

<!-- SIDEBAR -->

<div class="sidebar">

<div class="sidebar-header">

<div class="fw-bold mb-3">

Messages

</div>

<div class="search-form position-relative">

<div class="search-wrapper">

<input class="search-input"
type="search"
id="messageSearch"
placeholder="Search username..."
autocomplete="off">

<button class="search-btn"
type="button">

<i class="fas fa-search"></i>

</button>

</div>

<div id="messageSearchResults"
class="message-search-results d-none">
</div>



</div>

</div>



<?php

if(
isset($_GET['search_user'])
&&
$userSearchResults
&&
mysqli_num_rows($userSearchResults) == 0
):

?>

<div class="text-center p-3 text-muted">

User not found

</div>

<?php endif; ?>

<?php while($chat = mysqli_fetch_assoc($conversations)): ?>

<?php

/* UNREAD COUNT */

$unreadQuery = mysqli_query($conn,

"SELECT COUNT(*) AS total

FROM messages

WHERE sender_id='{$chat['chat_user_id']}'

AND receiver_id='$user_id'

AND is_read='0'");
$unreadData =
mysqli_fetch_assoc($unreadQuery);

$unreadCount =
$unreadData['total'];

/* LAST MESSAGE */
$deleteCheck = mysqli_query($conn,

"SELECT last_message_id

FROM deleted_conversations

WHERE

user_id='$user_id'

AND deleted_user_id='{$chat['chat_user_id']}'

LIMIT 1");

$deleteData =
mysqli_fetch_assoc($deleteCheck);

$previewDeletePoint =
$deleteData['last_message_id'] ?? 0;

$lastMessageQuery = mysqli_query($conn,

"SELECT
message,
voice_note,
attachment,
attachment_type,
created_at

FROM messages

WHERE

(
(sender_id='{$chat['chat_user_id']}'
AND receiver_id='$user_id')

OR

(sender_id='$user_id'
AND receiver_id='{$chat['chat_user_id']}')
)

AND id > '$previewDeletePoint'

ORDER BY created_at DESC

LIMIT 1");

$lastMessage =
mysqli_fetch_assoc($lastMessageQuery);
if(!$lastMessage){

continue;

}

/* PREVIEW TEXT */

if(!empty($lastMessage['voice_note'])){

    $previewText = '🎤 Voice Note';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'image'
){

    $previewText = '📷 Photo';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'video'
){

    $previewText = '🎥 Video';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'document'
){

    $previewText = '📄 Document';

}
else{

    $previewText =
    htmlspecialchars(
        $lastMessage['message']
    );

}
$messageTime =
strtotime($lastMessage['created_at']);

$today =
date('Y-m-d');

$messageDate =
date('Y-m-d', $messageTime);

$yesterday =
date('Y-m-d', strtotime('-1 day'));

if($messageDate == $today){

$displayTime =
date('h:i A', $messageTime);

}
elseif($messageDate == $yesterday){

$displayTime = 'Yesterday';

}
else{

$displayTime =
date('d M', $messageTime);

}

?>

<?php

$blockedSidebar = mysqli_query($conn,

"SELECT *

FROM blocked_users

WHERE

(user_id='$user_id'
AND blocked_user_id='{$chat['chat_user_id']}')

OR

(user_id='{$chat['chat_user_id']}'
AND blocked_user_id='$user_id')");

$isSidebarBlocked =
mysqli_num_rows($blockedSidebar) > 0;

?>

<a href="messages.php?user=<?php echo $chat['chat_user_id']; ?>"
class="chat-user">

<div class="fw-bold">

<?php

if($isSidebarBlocked){

echo 'User Unavailable';

}
else {

echo htmlspecialchars(
$chat['username']
);

}

?>

</div>


<?php if(!$isSidebarBlocked): ?>

<?php

$lastMessageQuery = mysqli_query($conn,

"SELECT message,
voice_note,
attachment,
attachment_type
FROM messages

WHERE

(sender_id='{$chat['chat_user_id']}'
AND receiver_id='$user_id')

OR

(sender_id='$user_id'
AND receiver_id='{$chat['chat_user_id']}')

ORDER BY created_at DESC

LIMIT 1");

$lastMessage =
mysqli_fetch_assoc($lastMessageQuery);

?>

<div style="
display:flex;
justify-content:space-between;
align-items:center;
margin-top:6px;
width:100%;
">

<div style="flex:1;">

<div style="<?php

echo $unreadCount > 0

? 'font-weight:700;color:#000;font-size:17px;line-height:1.2;'

: 'color:#777;font-size:17px;line-height:1.2;';

?>">

<?php

if(!empty($lastMessage['voice_note'])){

    echo '🎤 Voice Note';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'image'
){

    echo '📷 Photo';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'video'
){

    echo '🎥 Video';

}
elseif(
    !empty($lastMessage['attachment']) &&
    $lastMessage['attachment_type'] == 'document'
){

    echo '📄 Document';

}
elseif(
    strpos(
        $lastMessage['message'] ?? '',
        '📡 Sharing Live Location'
    ) !== false
){

    echo '📡 Live Location';

}
else{

    echo htmlspecialchars(
        substr(
            $lastMessage['message'] ?? '',
            0,
            35
        )
    );

}

?>

</div>

<?php if($unreadCount > 0): ?>

<div style="
font-size:14px;
font-weight:700;
color:#666;
margin-top:4px;
">

<?php echo $unreadCount; ?>

new message<?php
if($unreadCount > 1){
echo 's';
}
?>

</div>

<?php endif; ?>

</div>

<div style="
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
margin-left:15px;
min-width:70px;
">

<div style="
font-size:13px;
font-weight:600;
color:#666;
margin-bottom:6px;
">

<?php echo $displayTime; ?>

</div>

<span style="
width:14px;
height:14px;
border-radius:50%;
display:inline-block;
background:<?php

echo $unreadCount > 0
? '#1877f2'
: '#8e8e93';

?>;
"></span>

</div>

</div>

<?php endif; ?>

</a>

<?php endwhile; ?>

</div>

<!-- CHAT -->

<div class="chat-area">

<?php if($chat_user > 0 && $currentChat): ?>

<div class="chat-top">

    <div>
        <?php echo htmlspecialchars($currentChat['username']); ?>
    </div>

    <div style="display:flex;align-items:center;gap:15px;">

        <div class="chat-menu-wrapper">

<button type="button"
class="chat-menu-btn"
id="openChatMenu">
    <i class="fas fa-ellipsis-vertical"></i>
</button>

<div class="chat-menu d-none"
id="chatMenu">

    <div class="chat-menu-item"
    id="openSearchChat">

        <i class="fas fa-search"></i>

        <span>Search Messages</span>

    </div>

    <div class="chat-menu-item"
    id="confirmBlockBtn">

        <i class="fas fa-ban"></i>

        <span>Block User</span>

    </div>

    <div class="chat-menu-item"
    id="confirmDeleteBtn">

        <i class="fas fa-trash"></i>

        <span>Delete Chat</span>

    </div>

</div>
        </div>

    </div>

</div>

<div class="chat-search-bar d-none"
id="chatSearchBar">

<input type="text"
id="searchChatInput"
placeholder="Search messages...">

</div>

<?php if($isBlocked): ?>

<div class="empty-chat">

<div class="text-center">

<h3 class="mb-3">

User Unavailable

</h3>

<p class="text-muted mb-4">

This conversation is unavailable.

</p>

<a href="messages.php?block=<?php echo $chat_user; ?>"
class="btn btn-warning rounded-pill me-2">

Block Back

</a>

<a href="messages.php?delete=<?php echo $chat_user; ?>"
class="btn btn-danger rounded-pill">

Delete Chat

</a>

</div>

</div>

<?php else: ?>

<div class="chat-box"
id="chatBox">

<?php if($isDeletedConversation): ?>

<div class="text-center text-muted">

No messages yet.

</div>

<?php elseif(mysqli_num_rows($messages) > 0): ?>
<?php while($msg = mysqli_fetch_assoc($messages)):

$isSender =
$msg['sender_id'] == $user_id;

?>

<div class="message-row <?php echo $isSender ? 'sent' : 'received'; ?>">

<div class="message-bubble">

<div>

<?php

/* VOICE NOTE */

if(!empty($msg['voice_note'])){

?>

<div class="voice-note-player">

<audio controls>

<source
src="<?php echo $msg['voice_note']; ?>"
type="audio/webm">

</audio>

</div>

<?php

}

/* IMAGE */

/* IMAGE */

elseif(
!empty($msg['attachment']) &&
$msg['attachment_type'] == 'image'
){

?>

<img
src="<?php echo htmlspecialchars($msg['attachment']); ?>"
style="
max-width:250px;
border-radius:12px;
display:block;
cursor:pointer;
"
onclick="
window.open(
this.src,
'_blank'
);
">

<?php

}


/* VIDEO */

elseif(
!empty($msg['attachment']) &&
$msg['attachment_type'] == 'video'
){

?>

<video
controls
style="
max-width:280px;
border-radius:12px;
">

<source
src="<?php echo $msg['attachment']; ?>">

</video>

<?php

}

/* DOCUMENT */

elseif(
!empty($msg['attachment']) &&
$msg['attachment_type'] == 'document'
){

$fileName =
basename(
$msg['attachment']
);

?>

<a
href="<?php echo $msg['attachment']; ?>"
target="_blank"
download
style="
display:flex;
align-items:center;
gap:10px;
padding:12px;
background:#f3f3f3;
border-radius:12px;
text-decoration:none;
font-weight:600;
">

📄 <?php echo $fileName; ?>

</a>

<?php

}

/* LIVE LOCATION */

elseif(
strpos(
$msg['message'],
'📡 Sharing Live Location'
) !== false
){

?>

<div onclick="
openLiveLocation(
<?php echo $msg['sender_id']; ?>
)
"
style="
cursor:pointer;
padding:12px;
background:#e7f3ff;
border-radius:12px;
font-weight:600;
">

📡 View Live Location

</div>

<?php

}

/* NORMAL MESSAGE */

else{

echo nl2br(
htmlspecialchars($msg['message'])
);

}

?>

</div>

<div class="message-time">

<?php echo date(
'h:i A',
strtotime($msg['created_at'])
); ?>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="text-center text-muted">

No messages yet.

</div>

<?php endif; ?>

</div>

<form method="POST"
class="chat-form"
autocomplete="off"
enctype="multipart/form-data">

<!-- PLUS BUTTON -->

<div class="attachment-wrapper">

<button type="button"
class="plus-btn"
id="toggleAttachmentMenu">

<i class="fas fa-plus"></i>

</button>

<!-- ATTACHMENT MENU -->

<div class="attachment-menu d-none"
id="attachmentMenu">

<label class="attachment-item">

<i class="fas fa-image text-primary"></i>

<span>Photos</span>

<input type="file"
name="photo"
hidden
accept="image/*,video/*">

</label>

<div class="attachment-item"
id="openCamera">

<i class="fas fa-camera"></i>

<span>Camera</span>

</div>

<div class="attachment-item"
id="openLocationMenu">

<i class="fas fa-location-dot"></i>

<span>Location</span>

</div>

<label class="attachment-item">

<i class="fas fa-file text-warning"></i>

<span>Document</span>

<input type="file"
name="document"
hidden>

</label>

</div>

</div>

<!-- EMOJI -->

<button type="button"
class="emoji-btn"
id="emojiToggle">

<i class="far fa-face-smile"></i>

</button>

<div class="emoji-picker d-none"
id="emojiPicker">

<span class="emoji">😀</span>
<span class="emoji">😂</span>
<span class="emoji">😍</span>
<span class="emoji">😭</span>
<span class="emoji">🥺</span>
<span class="emoji">🔥</span>
<span class="emoji">❤️</span>
<span class="emoji">👍</span>
<span class="emoji">🙏</span>
<span class="emoji">😎</span>

</div>

<!-- FILE PREVIEW -->

<div id="selectedFilePreview"
style="
display:none;
width:100%;
padding:8px 12px;
background:#fff;
border-radius:10px;
margin-bottom:8px;
font-size:14px;
">
</div>

<!-- MESSAGE INPUT -->

<input type="text"
name="message"
class="chat-input"
id="messageInput"
placeholder="Type a message..."
autocomplete="off">

<!-- VOICE -->

<button type="button"
class="voice-btn"
id="recordVoice">

<i class="fas fa-microphone"></i>

</button>

<!-- SEND -->

<button type="submit"
name="send_message"
class="send-btn">

<i class="fas fa-paper-plane"></i>

</button>

</form>
<?php endif; ?>

<?php else: ?>

<div class="empty-chat">

Select a conversation to start chatting

</div>

<?php endif; ?>

</div>

</div>
<div class="location-popup d-none"
id="locationPopup">

<div class="location-option"
id="sendCurrentLocation">

📍 Send Current Location

</div>

<div class="location-title">

Share Live Location

</div>

<div class="location-option live-location"
data-duration="15">

15 Minutes

</div>

<div class="location-option live-location"
data-duration="60">

1 Hour

</div>

<div class="location-option live-location"
data-duration="480">

8 Hours

</div>

<div class="location-option live-location"
data-duration="1440">

24 Hours

</div>

</div>
<!-- CONFIRM MODAL -->

<div class="confirm-modal d-none"
id="confirmModal">

<div class="confirm-box">

<h4 id="confirmTitle">
Confirm Action
</h4>

<p id="confirmText">
Are you sure?
</p>

<div class="confirm-actions">

<button type="button"
class="confirm-cancel"
id="cancelConfirm">

Cancel

</button>

<a href="#"
class="confirm-yes"
id="confirmYesBtn">

Yes

</a>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* LOCATION SYSTEM */

const openLocationMenu =
document.getElementById(
'openLocationMenu'
);

const locationPopup =
document.getElementById(
'locationPopup'
);
console.log(locationPopup);

const sendCurrentLocation =
document.getElementById(
'sendCurrentLocation'
);

/* OPEN MENU */

if(openLocationMenu){

openLocationMenu.addEventListener(
'click',
function(e){

e.stopPropagation();

locationPopup.classList.toggle(
'd-none'
);

});

}

/* CURRENT LOCATION */

if(sendCurrentLocation){

sendCurrentLocation.addEventListener(
'click',
function(){

navigator.geolocation.getCurrentPosition(

function(position){

const lat =
position.coords.latitude;

const lng =
position.coords.longitude;

messageInput.value =
`📍 https://maps.google.com/?q=${lat},${lng}`;

locationPopup.classList.add(
'd-none'
);

},

function(){

alert(
'Location permission denied'
);

}

);

});

}

/* LIVE LOCATION */

let liveTrackingId;

document.querySelectorAll('.live-location')
.forEach(function(item){

item.addEventListener(
'click',
function(){

const duration =
this.dataset.duration;

/* START LIVE TRACKING */

navigator.geolocation.watchPosition(

function(position){

const lat =
position.coords.latitude;

const lng =
position.coords.longitude;

/* FIRST MESSAGE */

messageInput.value =
`📡 Sharing Live Location (${duration} mins)`;

/* SAVE TO DATABASE */

fetch(
'start-live-location.php',
{
method:'POST',
headers:{
'Content-Type':
'application/x-www-form-urlencoded'
},
body:
`receiver_id=<?php echo $chat_user; ?>&duration=${duration}&latitude=${lat}&longitude=${lng}`
}
);

/* CONTINUOUS UPDATES */

fetch(
'update-live-location.php',
{
method:'POST',
headers:{
'Content-Type':
'application/x-www-form-urlencoded'
},
body:
`latitude=${lat}&longitude=${lng}`
}
);

locationPopup.classList.add(
'd-none'
);

},

function(error){

alert(
'Location permission denied'
);

},

{
enableHighAccuracy:true,
maximumAge:0,
timeout:5000
}

);

});

});



/* CLOSE LOCATION MENU */

document.addEventListener(
'click',
function(e){

if(
!e.target.closest('#locationPopup')
&&
!e.target.closest('#openLocationMenu')
){

locationPopup.classList.add(
'd-none'
);

}

});

/* CAMERA SYSTEM */

const openCamera =
document.getElementById(
'openCamera'
);

const cameraModal =
document.getElementById(
'cameraModal'
);

const cameraPreview =
document.getElementById(
'cameraPreview'
);

const closeCamera =
document.getElementById(
'closeCamera'
);

const capturePhoto =
document.getElementById(
'capturePhoto'
);

let cameraStream;

/* OPEN CAMERA */

if(openCamera){

openCamera.addEventListener(
'click',
async function(){

try{

cameraStream =
await navigator.mediaDevices.getUserMedia({
video:true
});

cameraPreview.srcObject =
cameraStream;

cameraModal.classList.remove(
'd-none'
);

}catch(error){

alert(
'Camera permission denied'
);

}

});

}

/* CLOSE CAMERA */

if(closeCamera){

closeCamera.addEventListener(
'click',
function(){

cameraModal.classList.add(
'd-none'
);

if(cameraStream){

cameraStream
.getTracks()
.forEach(track => track.stop());

}

});

}

/* CAPTURE */

if(capturePhoto){

capturePhoto.addEventListener(
'click',
function(){

alert(
'Photo captured successfully'
);

});

}

/* MESSAGE SEARCH */

const messageSearch =
document.getElementById(
'messageSearch'
);

const messageResults =
document.getElementById(
'messageSearchResults'
);

if(messageSearch){

messageSearch.addEventListener(
'keyup',
function(){

const query =
this.value.trim();

if(query.length < 1){

messageResults.classList.add(
'd-none'
);

return;

}
console.log("Searching:", query);
fetch(
'search-users.php?query='
+
query
)

.then(response => response.text())

.then(data => {

messageResults.innerHTML = data;
messageResults.classList.remove('d-none');

});

});

}

/* ATTACHMENT MENU */

const toggleAttachmentMenu =
document.getElementById(
'toggleAttachmentMenu'
);

const attachmentMenu =
document.getElementById(
'attachmentMenu'
);

if(toggleAttachmentMenu){

toggleAttachmentMenu.addEventListener(
'click',
function(e){

e.stopPropagation();

attachmentMenu.classList.toggle(
'd-none'
);

});

}

/* EMOJI PICKER */

const emojiBtn =
document.getElementById(
'emojiToggle'
);

const emojiPicker =
document.getElementById(
'emojiPicker'
);

const messageInput =
document.getElementById(
'messageInput'
);

if(emojiBtn){

emojiBtn.addEventListener(
'click',
function(e){

e.stopPropagation();

emojiPicker.classList.toggle(
'd-none'
);

});

}

/* INSERT EMOJI */

document.querySelectorAll('.emoji')
.forEach(function(emoji){

emoji.addEventListener(
'click',
function(){

messageInput.value +=
this.innerText;

messageInput.focus();

});

});

/* VOICE BUTTON */

const voiceBtn =
document.getElementById(
'voiceRecordBtn'
);

if(voiceBtn){

voiceBtn.addEventListener(
'click',
function(){

alert(
'Voice notes coming soon'
);

});

}

/* CLOSE POPUPS */

document.addEventListener(
'click',
function(e){

if(
!e.target.closest('.attachment-wrapper')
){

attachmentMenu.classList.add(
'd-none'
);

}

if(
!e.target.closest('.emoji-picker')
&&
!e.target.closest('#emojiToggle')
){

emojiPicker.classList.add(
'd-none'
);

}

});

/* LIVE MAP VIEWER */

const liveMapModal =
document.getElementById(
'liveMapModal'
);

const liveMapFrame =
document.getElementById(
'liveMapFrame'
);

const closeLiveMap =
document.getElementById(
'closeLiveMap'
);

let liveMapInterval;

/* OPEN LIVE MAP */

function openLiveLocation(senderId){

liveMapModal.classList.remove(
'd-none'
);

/* FETCH LOCATION */

function fetchLiveLocation(){

fetch(
`fetch-live-location.php?sender_id=${senderId}`
)

.then(response => response.json())

.then(data => {

if(data.status === 'active'){

liveMapFrame.src =
`https://maps.google.com/maps?q=${data.latitude},${data.longitude}&z=15&output=embed`;

}
else{

alert(
'Live location ended'
);

clearInterval(
liveMapInterval
);

liveMapModal.classList.add(
'd-none'
);

}

});

}

/* INITIAL LOAD */

fetchLiveLocation();

/* REFRESH EVERY 5 SECONDS */

liveMapInterval =
setInterval(
fetchLiveLocation,
5000
);

}

/* CLOSE MAP */

if(closeLiveMap){

closeLiveMap.addEventListener(
'click',
function(){

liveMapModal.classList.add(
'd-none'
);

clearInterval(
liveMapInterval
);

});

}

/* VOICE NOTES */

const recordVoice =
document.getElementById(
'recordVoice'
);

let mediaRecorder;

let audioChunks = [];

let isRecording = false;

/* RECORD BUTTON */

if(recordVoice){

recordVoice.addEventListener(
'click',
async function(){

/* START RECORDING */

if(!isRecording){

try{

const stream =
await navigator.mediaDevices.getUserMedia({
audio:true
});

mediaRecorder =
new MediaRecorder(stream);

audioChunks = [];

mediaRecorder.start();

isRecording = true;

recordVoice.innerHTML =
'<i class="fas fa-stop"></i>';

mediaRecorder.addEventListener(
'dataavailable',
function(event){

audioChunks.push(
event.data
);

});

mediaRecorder.addEventListener(
'stop',
async function(){

const audioBlob =
new Blob(
audioChunks,
{
type:'audio/webm'
}
);

const formData =
new FormData();

formData.append(
'audio',
audioBlob,
'voice-note.webm'
);

formData.append(
'receiver_id',
'<?php echo $chat_user; ?>'
);

/* UPLOAD */

fetch(
'upload-voice-note.php',
{
method:'POST',
body:formData
}
)

.then(response => response.text())

.then(data => {

location.reload();

});

});

}catch(error){

alert(
'Microphone permission denied'
);

}

/* STOP RECORDING */

}else{

mediaRecorder.stop();

isRecording = false;

recordVoice.innerHTML =
'<i class="fas fa-microphone"></i>';

}

});

}
/* AUTO SCROLL CHAT */

const chatBox =
document.getElementById(
'chatBox'
);

/* SCROLL TO BOTTOM */

function scrollToBottom(){

chatBox.scrollTop =
chatBox.scrollHeight;

}

/* INITIAL LOAD */

if(chatBox){

scrollToBottom();

}

/* AUTO SCROLL ON NEW MESSAGE */

const observer =
new MutationObserver(function(){

const isNearBottom =

chatBox.scrollHeight
-
chatBox.scrollTop
-
chatBox.clientHeight

< 200;

if(isNearBottom){

scrollToBottom();

}

});

if(chatBox){

observer.observe(
chatBox,
{
childList:true,
subtree:true
}
);

}

/* CHAT MENU */

const openChatMenu =
document.getElementById(
'openChatMenu'
);

const chatMenu =
document.getElementById(
'chatMenu'
);

if(openChatMenu){

openChatMenu.addEventListener(
'click',
function(e){

e.stopPropagation();

chatMenu.classList.toggle(
'd-none'
);

});

}

/* SEARCH BAR */

const openSearchChat =
document.getElementById(
'openSearchChat'
);

const chatSearchBar =
document.getElementById(
'chatSearchBar'
);

const searchChatInput =
document.getElementById(
'searchChatInput'
);

if(openSearchChat){

openSearchChat.addEventListener(
'click',
function(){

chatSearchBar.classList.toggle(
'd-none'
);

chatMenu.classList.add(
'd-none'
);

searchChatInput.focus();

});

}

/* WHATSAPP STYLE SEARCH */

let currentSearchIndex = 0;

let matchedMessages = [];

/* SEARCH UI */

const searchNavigation = document.createElement('div');

searchNavigation.className =
'search-navigation d-none';

searchNavigation.innerHTML = `

<button type="button"
id="searchUp">

<i class="fas fa-chevron-up"></i>

</button>

<div id="searchCount">
0 results
</div>

<button type="button"
id="searchDown">

<i class="fas fa-chevron-down"></i>

</button>

<button type="button"
id="closeSearchChat">

<i class="fas fa-times"></i>

</button>

`;

if(chatSearchBar){

chatSearchBar.appendChild(
searchNavigation
);

}

/* SEARCH */

if(searchChatInput){

searchChatInput.addEventListener(
'keyup',
function(){

const value =
this.value.trim().toLowerCase();

matchedMessages = [];

currentSearchIndex = 0;

/* REMOVE OLD HIGHLIGHTS */

document.querySelectorAll(
'.message-bubble'
).forEach(function(msg){

msg.innerHTML =
msg.innerHTML.replaceAll(
'<span class="search-highlight">',
''
);

msg.innerHTML =
msg.innerHTML.replaceAll(
'</span>',
''
);

});

/* EMPTY SEARCH */

if(value === ''){

searchNavigation.classList.add(
'd-none'
);

return;

}

/* FIND MATCHES */

const messages =
document.querySelectorAll(
'.message-bubble div:first-child'
);

messages.forEach(function(msg){

const originalText =
msg.textContent;

if(
originalText
.toLowerCase()
.includes(value)
){

/* HIGHLIGHT ALL */

const regex =
new RegExp(
`(${value})`,
'gi'
);

if(
msg.querySelector('img') ||
msg.querySelector('video') ||
msg.querySelector('audio') ||
msg.querySelector('a')
){
    return;
}

msg.innerHTML =
msg.innerHTML.replace(
regex,
'<span class="search-highlight">$1</span>'
);

matchedMessages.push(msg);

}

});

/* NO RESULTS */

if(matchedMessages.length === 0){

searchNavigation.classList.remove(
'd-none'
);

document.getElementById(
'searchCount'
).innerText =
'No results';

return;

}

/* SHOW RESULTS */

searchNavigation.classList.remove(
'd-none'
);

document.getElementById(
'searchCount'
).innerText =

`${currentSearchIndex + 1}
of
${matchedMessages.length}`;

/* GO TO FIRST */

matchedMessages[0].scrollIntoView({
behavior:'smooth',
block:'center'
});

});

/* DOWN BUTTON */

document.getElementById(
'searchDown'
).addEventListener(
'click',
function(){

if(matchedMessages.length === 0){
return;
}

currentSearchIndex++;

if(
currentSearchIndex
>=
matchedMessages.length
){

currentSearchIndex = 0;

}

matchedMessages[
currentSearchIndex
].scrollIntoView({
behavior:'smooth',
block:'center'
});

document.getElementById(
'searchCount'
).innerText =

`${currentSearchIndex + 1}
of
${matchedMessages.length}`;

});

/* UP BUTTON */
/* CLOSE SEARCH */

document.getElementById(
'closeSearchChat'
).addEventListener(
'click',
function(){

/* HIDE SEARCH BAR */

chatSearchBar.classList.add(
'd-none'
);

/* CLEAR INPUT */

searchChatInput.value = '';

/* HIDE NAVIGATION */

searchNavigation.classList.add(
'd-none'
);

/* REMOVE HIGHLIGHTS */

document.querySelectorAll(
'.message-bubble'
).forEach(function(msg){

msg.innerHTML =
msg.innerHTML.replaceAll(
'<span class="search-highlight">',
''
);

msg.innerHTML =
msg.innerHTML.replaceAll(
'</span>',
''
);

});

});

document.getElementById(
'searchUp'
).addEventListener(
'click',
function(){

if(matchedMessages.length === 0){
return;
}

currentSearchIndex--;

if(currentSearchIndex < 0){

currentSearchIndex =
matchedMessages.length - 1;

}

matchedMessages[
currentSearchIndex
].scrollIntoView({
behavior:'smooth',
block:'center'
});

document.getElementById(
'searchCount'
).innerText =

`${currentSearchIndex + 1}
of
${matchedMessages.length}`;

});

}

/* CLOSE MENU */

document.addEventListener(
'click',
function(e){

/* IGNORE NAVBAR DROPDOWNS */

if(
e.target.closest('.dropdown')
||
e.target.closest('.dropdown-menu')
){
return;
}

/* CHAT MENU */

if(
chatMenu
&&
!e.target.closest('.chat-menu-wrapper')
){

chatMenu.classList.add(
'd-none'
);

}

/* ATTACHMENTS */

if(
attachmentMenu
&&
!e.target.closest('.attachment-wrapper')
){

attachmentMenu.classList.add(
'd-none'
);

}

/* EMOJI */

if(
emojiPicker
&&
!e.target.closest('.emoji-picker')
&&
!e.target.closest('#emojiToggle')
){

emojiPicker.classList.add(
'd-none'
);

}

});

/* CONFIRM MODAL */

const confirmModal =
document.getElementById(
'confirmModal'
);

const confirmTitle =
document.getElementById(
'confirmTitle'
);

const confirmText =
document.getElementById(
'confirmText'
);

const confirmYesBtn =
document.getElementById(
'confirmYesBtn'
);

const cancelConfirm =
document.getElementById(
'cancelConfirm'
);

const confirmBlockBtn =
document.getElementById(
'confirmBlockBtn'
);

const confirmDeleteBtn =
document.getElementById(
'confirmDeleteBtn'
);

/* BLOCK */

if(confirmBlockBtn && confirmModal){

confirmBlockBtn.addEventListener(
'click',
function(){

confirmModal.classList.remove(
'd-none'
);

if(confirmTitle){
confirmTitle.innerText =
'Block User';
}

if(confirmText){
confirmText.innerText =
'Are you sure you want to block this user?';
}

if(confirmYesBtn){
confirmYesBtn.href =
'messages.php?block=<?php echo $chat_user; ?>';
}

});

}

/* DELETE */

if(confirmDeleteBtn && confirmModal){

confirmDeleteBtn.addEventListener(
'click',
function(){

confirmModal.classList.remove(
'd-none'
);

if(confirmTitle){
confirmTitle.innerText =
'Delete Chat';
}

if(confirmText){
confirmText.innerText =
'Are you sure you want to permanently delete this chat?';
}

if(confirmYesBtn){
confirmYesBtn.href =
'messages.php?delete=<?php echo $chat_user; ?>';
}

});

}

/* CANCEL */

if(cancelConfirm){

cancelConfirm.addEventListener(
'click',
function(){

confirmModal.classList.add(
'd-none'
);

});

}

/* CLOSE OUTSIDE */

if(confirmModal){

confirmModal.addEventListener(
'click',
function(e){

if(e.target === confirmModal){

confirmModal.classList.add(
'd-none'
);

}

});

}
/* FILE PREVIEW */

const photoInput =
document.querySelector('input[name="photo"]');

const documentInput =
document.querySelector('input[name="document"]');

const filePreview =
document.getElementById('selectedFilePreview');

function showFileName(file){

    if(!file) return;

    filePreview.style.display = 'block';

    filePreview.innerHTML =
    '📎 ' + file.name;

}

if(photoInput){

    photoInput.addEventListener(
    'change',
    function(){

        showFileName(this.files[0]);

    });

}

if(documentInput){

    documentInput.addEventListener(
    'change',
    function(){

        showFileName(this.files[0]);

    });

}

</script>
<div class="camera-modal d-none"
id="cameraModal">

<video id="cameraPreview"
autoplay
playsinline></video>

<div class="camera-actions">

<button type="button"
id="capturePhoto">

Capture

</button>

<button type="button"
id="closeCamera">

Close

</button>

</div>

</div>
<div class="live-map-modal d-none"
id="liveMapModal">

<div class="live-map-header">

<div>

📡 Live Location

</div>

<button type="button"
id="closeLiveMap">

✕

</button>

</div>

<iframe
id="liveMapFrame"
width="100%"
height="100%"
style="border:none;">
</iframe>

</div>
</body>
</html>