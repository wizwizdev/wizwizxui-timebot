<?php
require 'config.php';
require 'core.php';

$hourLeft = 24;
$mbLeft = 1000;

$offset = 0;
if(file_exists("offset.txt")){
    $offset = file_get_contents("offset.txt");
}
$usersList = $connection->query("SELECT * FROM `user` WHERE `uuid` IS NOT NULL AND (UNIX_TIMESTAMP() > `warned` OR `warned` IS NULL) LIMIT 30 OFFSET $offset ");

while($rowUser = $usersList->fetch_assoc()){
    $userId = $rowUser['id'];
    $uuid = $rowUser['uuid'];
    $warned = $rowUser['warned'];
    
    if($uuid != null && $warned != "true"){
        $server = $rowUser['sub_server'];
        
        $serversList = $connection->query("SELECT * FROM `servers` WHERE `server_ip` = '{$server}'");
        $row = $serversList->fetch_assoc();
        $serverIp = $row['server_ip'];
        $serverName = $row['user_name'];
        $serverPass = $row['password'];

        $response = getJson($serverIp, $serverName, $serverPass, $userId);
        if($response['success']){
            $list = $response['obj'];
            if($uuid != null){
                if(!isset($list[0]['clientStats'])){
                    foreach($list as $keys=>$packageInfo){
                    	if(strpos($packageInfo['settings'], $uuid)!=false){
                            $upload = $packageInfo['up'];
                            $download = $packageInfo['down'];
                            $totalUsed = $packageInfo['up'] + $packageInfo['down'];
                            $total = $packageInfo['total'];
                            $expiryTime = substr($packageInfo['expiryTime'],0,-3);
                            $remark = $packageInfo['remark'];
                            $port = $packageInfo['port'];                        
                    	}
                    }
                }else{
                    $keys = -1;
                    $settings = array_column($list,'settings');
                    foreach($settings as $key => $value){
                    	if(strpos($value, $uuid)!= false){
                    		$keys = $key;
                    		break;
                    	}
                    }
                    $clientsSettings = json_decode($list[$keys]['settings'],true)['clients'];
                    if(!is_array($clientsSettings)){
                        exit();
                    }
                    $settingsId = array_column($clientsSettings,'id');
                    $settingKey = array_search($uuid,$settingsId);
                    
                    $email = $clientsSettings[$settingKey]['email'];
                    $remark = $email;
                    $port = $list[$keys]['port'];
                    
                    $clientState = $list[$keys]['clientStats'];
                    $emails = array_column($clientState,'email');
                    $emailKey = array_search($email,$emails);
        
                    if($clientState[$emailKey]['total'] != 0 || $clientState[$emailKey]['up'] != 0  ||  $clientState[$emailKey]['down'] != 0 || $clientState[$emailKey]['expiryTime'] != 0){
                        $upload = $clientState[$emailKey]['up'];
                        $download = $clientState[$emailKey]['down'];
                        $totalUsed = $clientState[$emailKey]['up'] + $clientState[$emailKey]['down'];
                        $total = $clientState[$emailKey]['total'];
                        $expiryTime = substr($clientState[$emailKey]['expiryTime'],0,-3);
                    }
                    elseif($list[$keys]['total'] != 0 || $list[$keys]['up'] != 0  ||  $list[$keys]['down'] != 0 || $list[$keys]['expiryTime'] != 0){
                        $upload = $clientState[$emailKey]['up'];
                        $download = $clientState[$emailKey]['down'];
                        $totalUsed = $clientState[$emailKey]['up'] + $clientState[$emailKey]['down'];
                        $total = $clientState[$emailKey]['total'];
                        $expiryTime = substr($clientState[$emailKey]['expiryTime'],0,-3);

                        $upload = $list[$keys]['up'];
                        $download = $list[$keys]['down'];
                        $totalUsed = $list[$keys]['up'] + $list[$keys]['down'];
                        $total = $list[$keys]['total'];
                        $expiryTime = substr($list[$keys]['expiryTime'],0,-3);
                    }
                }

                if($total - $totalUsed <  ($mbLeft * 1024 * 1024) && $total != 0){
                    $subLeft = "حجم بسته ی اشتراک شما رو به اتمام است، لطفا برای تمدید اشتراک خو اقدام کنید";

                    sendMessage($userId,$subLeft);
                    sendMessage($Config['report_channel'], "حجم بسته ی کاربر $userId رو به اتمام است\nآیپی سرور: $serverIp\nuuid: $uuid");
                    setUser('warned','true',$userId);
                }elseif($expiryTime - time() <= ($hourLeft * 60 * 60) && $expiryTime != 0){
                    $subLeft = "زمان اشتراک شما رو به پایان است، لطفا برای تمدید اشتراک خو اقدام کنید";
                    
                    sendMessage($userId,$subLeft);
                    sendMessage($Config['report_channel'], "زمان بسته ی کاربر $userId رو به اتمام است\nآیپی سرور: $serverIp\nuuid: $uuid");
                    setUser('warned','true',$userId);
                }
    
            }
        }
    }
}

if(mysqli_num_rows($usersList) >= $offset + 30){
    file_put_contents("offset.txt",$offset+30);
}else{
    unlink("offset.txt");
}

//-----------------------------//
unlink("error_log");
?>
