<?php
if(isset($_REQUEST['updateBot'])){
	require "update.php";
	require "../baseInfo.php";
	
	$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
	
	if($connection->connect_error){
	    form("خطای دیتابیس: " . $connection->connect_error);
	    exit();
	}
    
    updateBot();
}
?>
