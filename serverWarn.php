<?php
require 'config.php';
require 'core.php';

$hourLeft = 24;
$mbLeft = 1024;

if(file_exists("info.json")){
    $info = json_decode(file_get_contents("info.json"),true);
    
    $serverOffset = $info['serverOffset'];
    $usersOffset = $info['usersOffset'];
    $usersInfo = $info['usersInfo'];
}else{
    $info = array();
    $serverOffset = 0;
    $usersOffset = 0;
    $usersInfo = array();
}
$stmt = $connection->prepare("SELECT * FROM `servers` LIMIT 1 OFFSET ? ");
$stmt->bind_param("i", $serverOffset);
$stmt->execute();
$servers = $stmt->get_result();
while($row = $servers->fetch_assoc()){
    $serverIp = $row['server_ip'];
    $serverName = $row['user_name'];
    $serverPass = $row['password'];
    $serverType = $row['type'];
    $response = getJson($serverIp, $serverName, $serverPass, $userId);
    $row = 0;
    if($response['success']){
        $list = $response['obj'];
        if(!isset($list[0]['clientStats'])){
            foreach($list as $keys=>$packageInfo){
                $row++;
                if($row < $usersOffset){
                    continue;
                }
                
                $upload = $packageInfo['up'];
                $download = $packageInfo['down'];
                $totalUsed = $packageInfo['up'] + $packageInfo['down'];
                $total = $packageInfo['total'];
                $expiryTime = substr($packageInfo['expiryTime'],0,-3);
                $remark = $packageInfo['remark'];
                $port = $packageInfo['port'];                        
                
                preg_match('/[\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12}/',$packageInfo['settings'],$uid);
                $uuid = $uid[0];

                
                if($totalUsed >= $total - 1024 && $total != 0){
                    if(!isset($usersInfo[$uuid])){
                        $info['usersInfo'][$uuid] = "";
                        sendMessage($Config['report_channel'], "");
                    }
                }elseif($expiryTime - time() <= (24 * 60 * 60) && $expiryTime != 0){
                    if(!isset($usersInfo[$uuid])){
                        $info['usersInfo'][$uuid] = "";
                        sendMessage($Config['report_channel'], "");
                    }
                }else{
                    unset($arr['userInfo']['ryan']);
                }

            	
            	if($row >= $row + $usersOffset){
            	    break;
            	}
            }
            if(count($list) > $row + $usersOffset){
                $info['usersOffset'] = $row + $usersOffset;
                file_put_contents("info.json", json_encode($info));
            }else{
                $info['usersOffset'] = 0;
                $nextServer = true;
            }
        }else{
            $settings = array_column($list,'settings');
            foreach($settings as $key => $value){
                $row++;
                if($row < $usersOffset){
                    continue;
                }
        		$keys = $key;

        		
	            $clientsSettings = json_decode($list[$keys]['settings'],true)['clients'];

                foreach($clientsSettings as $clients => $client){
                    $email = $client['email'];
                    $uuid = $client['id'];
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
                    
                    if($total - $totalUsed <= ($mbLeft * 1024 * 1024) && $total != 0){
                        if(!isset($usersInfo[$uuid])){
                            $info['usersInfo'][$uuid] = "";
                            sendMessage($Config['report_channel'], "
❌ | ☑️ کاربر زیر 85 درصد از حجم خورد را مصرف کرده است :

🔅کلید کاربر:  $uuid
🔅نام کاربر:  $remark
👽 پورت کاربر :  $port

🆔 @wizwizdev

");
                        }
                    }elseif($expiryTime - time() <= ($hourLeft * 60 * 60) && $expiryTime != 0){
                        if(!isset($usersInfo[$uuid])){
                            $info['usersInfo'][$uuid] = "";
                            sendMessage($Config['report_channel'], "
⏰ | 🔌 زمان بسته کاربر رو به پایان است:

🔑کلید کاربر:  $uuid
🧑‍💼نام کاربر:  $remark
💡 پورت کاربر :  $port

🆔 @wizwizdev

");
                        }
                    }else{
                        unset($arr['userInfo']['ryan']);
                    }
                }
 
            	if($row >= $row + $usersOffset){
            	    break;
            	}
            }
            if(count($settings) > $row + $usersOffset){
                $info['usersOffset'] = $row + $usersOffset;
                file_put_contents("info.json", json_encode($info));
            }else{
                $info['usersOffset'] = 0;
                $nextServer = true;
            }
        }        
    }
}


if(mysqli_num_rows($servers) > 0){
    if($nextServer = true){
        $info['serverOffset'] = $serverOffset +1;
        file_put_contents("info.json", json_encode($info));
    }
}else{
    $info['usersOffset'] = 0;
    $info['serverOffset'] = 0;
    file_put_contents("info.json", json_encode($info));
}

//-----------------------------//
unlink("error_log");
?>
