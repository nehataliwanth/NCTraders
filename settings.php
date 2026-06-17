<?php
session_start();

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Settings
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
background:#f5f5f5;
font-family:Arial,sans-serif;
}

.settings-container{
max-width:600px;
margin:50px auto;
background:white;
border-radius:20px;
padding:30px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.settings-title{
font-size:30px;
font-weight:700;
margin-bottom:30px;
}

.setting-item{
display:flex;
align-items:center;
justify-content:space-between;
padding:18px 20px;
border-radius:14px;
margin-bottom:15px;
background:#f8f8f8;
text-decoration:none;
color:#111;
transition:0.2s;
}

.setting-item:hover{
background:#ececec;
}

.setting-left{
display:flex;
align-items:center;
gap:15px;
font-size:17px;
font-weight:600;
}

.setting-icon{
width:45px;
height:45px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
background:#162331;
color:white;
font-size:18px;
}

.logout-btn{
background:#e63946 !important;
color:white !important;
}

</style>

</head>

<body>

<div class="settings-container">

<div class="settings-title">

Settings

</div>

<a href="manage_account.php"
class="setting-item">

<div class="setting-left">

<div class="setting-icon">
<i class="fas fa-user"></i>
</div>

Manage Account

</div>

<i class="fas fa-chevron-right"></i>

</a>

<a href="logout.php"
class="setting-item logout-btn">

<div class="setting-left">

<div class="setting-icon">
<i class="fas fa-sign-out-alt"></i>
</div>

Sign Out

</div>

<i class="fas fa-chevron-right"></i>

</a>

</div>

</body>

</html>