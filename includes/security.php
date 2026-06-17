<?php

function sanitize($data){

    return htmlspecialchars(
        trim($data),
        ENT_QUOTES,
        'UTF-8'
    );
}

function validateSession(){

    if(!isset($_SESSION['user_id'])){

        header("Location: login.php");
        exit();
    }
}

function validateAdmin(){

    if($_SESSION['role'] != 'admin'){

        exit("Access Denied!");
    }
}

?>
