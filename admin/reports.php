<?php
session_start();

if(
!isset($_SESSION['admin_id'])
||
$_SESSION['role'] != 'admin'
){
    header("Location: admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
</head>
<body>

<h1>Manage Reports</h1>

<p>No reports available yet.</p>

<a href="admin-dashboard.php">
Back to Dashboard
</a>

</body>
</html>