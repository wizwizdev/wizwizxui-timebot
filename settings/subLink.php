<?php
include "../baseInfo.php";

$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
if($connection->connect_error){
    exit("error " . $connection->connect_error);  
}
$connection->set_charset("utf8mb4");
if(isset($_GET['token'])){
$token = $_GET['token'];
    if(preg_match('/[a-zA-Z0-9]{30}/',$token)){
        $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `token` = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        echo base64_encode(implode("\n", json_decode($info['link'])));
        exit();
    }
}
echo "Wrong token";
?>