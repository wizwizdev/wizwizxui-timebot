<?php
function session_notif_wizwiz(){
    if (isset($_SESSION['flash_message'])) {
        echo $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
    }
}

function session_login_wizwiz(){
    if (!isset($_SESSION['username'])) {
//    if (!isset($_COOKIE['cookie_username'])) {
        header("location: login.php");
        exit();
    } else {
    }
}

?>