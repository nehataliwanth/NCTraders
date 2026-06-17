<?php

session_start();

include "../config/database.php";

if(
    !isset($_SESSION['admin_id'])
    ||
    $_SESSION['role'] != 'admin'
){
    header("Location: admin-login.php");
    exit();
}

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    mysqli_query(
        $conn,
        "DELETE FROM users WHERE id = $id"
    );

}

header("Location: users.php");
exit();

?>