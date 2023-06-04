<?php
session_start();
function sessions(){
    if(!isset($_SESSION['username'])){
        header("location: test.php");
        exit();
    }else{}
}
