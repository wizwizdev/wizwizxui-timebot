<?php
include_once '../baseInfo.php';
include_once '../config.php';
$time = time();

if(file_exists("warnOffset.txt")) $warnOffset = file_get_contents("warnOffset.txt");
else $warnOffset = 0;
$limit = 50;

$stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `notif`=0 ORDER BY `id` ASC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $warnOffset);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

if($orders){
    if($orders->num_rows>0){
        while ($order = $orders->fetch_assoc()){
            $send = false;
    	    $from_id = $order['userid'];
    	    $token = $order['token'];
            $remark = $order['remark'];
            $uuid = $order['uuid']??"0";
            $server_id = $order['server_id'];
            $inbound_id = $order['inbound_id'];
            $links_list = $order['link']; 
            $notif = $order['notif'];
            $expiryTime = "";
            $found = false;
            
        	$stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id` = ?");
        	$stmt->bind_param('i', $server_id);
        	$stmt->execute();
        	$serverConfig = $stmt->get_result()->fetch_assoc();
        	$stmt->close();
        	$serverType = $serverConfig['type'];
            $panel_url = $serverConfig['panel_url'];

            if($serverType == "marzban"){
                $info = getMarzbanUser($server_id, $remark);
                if(isset($info->username)){
                    $found = true;
                    $total = $info->data_limit;
                    $totalLeft = $total - $info->used_traffic;
                    $expiryTime = $info->expire;
                    $enable = $info->status == "active"?true:false;
                }
            }else{
                $response = getJson($server_id)->obj; 
                foreach($response as $row){
                    if($inbound_id == 0) { 
                        $clients = json_decode($row->settings)->clients;
                        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
                            $found = true;
                            $total = $row->total;
                            $up = $row->up;
                            $down = $row->down;
                            $expiryTime = substr($row->expiryTime, 0, -3);
                            break;
                        }
                    }else{
                        if($row->id == $inbound_id) {
                            $settings = json_decode($row->settings, true); 
                            $clients = $settings['clients'];
                            
                            $clientsStates = $row->clientStats;
                            foreach($clients as $key => $client){
                                if($client['id'] == $uuid || $client['password'] == $uuid){
                                    $found = true;
                                    $email = $client['email'];
                                    $emails = array_column($clientsStates,'email');
                                    $emailKey = array_search($email,$emails);
                                    
                                    $total = $client['totalGB'];
                                    $up = $clientsStates[$emailKey]->up;
                                    $enable = $clientsStates[$emailKey]->enable;
                                    $down = $clientsStates[$emailKey]->down; 
                                    $expiryTime = substr($clientsStates[$emailKey]->expiryTime, 0, -3);
                                    break;
                                }
                            }
                        }
                    }
                }
                $totalLeft = $total - $up - $down;
            }
            if(!$found){
                if($logedIn){
                    $stmt = $connection->prepare("DELETE FROM `orders_list` WHERE `id` = ?");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();
                    
                    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
                    $stmt->bind_param('i', $server_id);
                    $stmt->execute();
                    $serverTitle = $stmt->get_result()->fetch_assoc()['title'];
                    $stmt->close();
                    
                    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
                    $stmt->bind_param('i', $from_id);
                    $stmt->execute();
                    $serverInfo = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    $userName = $serverInfo['name'];
                    
                    sendMessage( "کانفیگ $remark مربوط به کاربر $userName ($from_id) توی سرور $serverTitle نبود و از دیتابیس حذف شد", null, null, $admin);
                }
                continue;
            }
            
            $leftgb = round( ($totalLeft) / 1073741824, 2);
            if($expiryTime != null && $total != null){
                $send = "";
                if($expiryTime < time() + 86400) $send = "روز"; elseif($leftgb < 1) $send = "گیگ";
                if($send != ""){  
                    $msg = " 
        از سرویس اشتراک $remark تنها (۱ $send) باقی مانده است. میتواند از قسمت خرید های من سرویس فعلی خود را تمدید کنید یا سرویس جدید خریداری کنید.";
                    sendMessage( $msg, null, null, $from_id);
                    $newTIme = $time + 86400 * 2;
                    $stmt = $connection->prepare("UPDATE `orders_list` SET `notif`= ? WHERE `uuid`=?");
                    $stmt->bind_param("is", $newTIme, $uuid);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
        file_put_contents("warnOffset.txt", $warnOffset + $limit);
    }else unlink('warnOffset.txt');
}


$stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `notif` !=0 AND `notif` < ? LIMIT 50");
$stmt->bind_param("i", $time);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

if($orders){
    if($orders->num_rows>0){
        while ($order = $orders->fetch_assoc()){
            $send = false;
    	    $from_id = $order['userid'];
    	    $token = $order['token'];
            $remark = $order['remark'];
            $uuid = $order['uuid']??"0";
            $server_id = $order['server_id'];
            $inbound_id = $order['inbound_id'];
            $links_list = $order['link']; 
            $notif = $order['notif'];
            $found = false;
            
        	$stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id` = ?");
        	$stmt->bind_param('i', $server_id);
        	$stmt->execute();
        	$serverConfig = $stmt->get_result()->fetch_assoc();
        	$stmt->close();
        	$serverType = $serverConfig['type'];
            $panel_url = $serverConfig['panel_url'];

            $found = false;
            if($serverType == "marzban"){
                $info = getMarzbanUser($server_id, $remark);
                if(isset($info->username)){
                    $found = true;
                    $total = $info->data_limit;
                    $totalLeft = $total - $info->used_traffic;
                    $expiryTime = $info->expire;
                    $enable = $info->status == "active"?true:false;
                }
            }else{
                $response = getJson($server_id)->obj;  
                foreach($response as $row){
                    if($inbound_id == 0) {
                        $clients = json_decode($row->settings)->clients;
                        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
                            $total = $row->total;
                            $up = $row->up;
                            $down = $row->down;
                            $expiryTime = substr($row->expiryTime, 0, -3);
                            $found = true;
                            break;
                        }
                    }else{
                        if($row->id == $inbound_id) {
                            $settings = json_decode($row->settings, true); 
                            $clients = $settings['clients'];
                            
                            
                            $clientsStates = $row->clientStats;
                            foreach($clients as $key => $client){
                                if($client['id'] == $uuid || $client['password'] == $uuid){
                                    $email = $client['email'];
                                    $emails = array_column($clientsStates,'email');
                                    $emailKey = array_search($email,$emails);
                                    
                                    $total = $client['totalGB'];
                                    $up = $clientsStates[$emailKey]->up;
                                    $enable = $clientsStates[$emailKey]->enable;
                                    $down = $clientsStates[$emailKey]->down; 
                                    $expiryTime = substr($clientsStates[$emailKey]->expiryTime, 0, -3);
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }
                } 
                $totalLeft = $total - $up - $down;
            }
            $leftgb = round( ($totalLeft) / 1073741824, 2);
            if(!$found) continue;
            if($expiryTime <= time()) $send = true; elseif($leftgb <= 0) $send = true;
            if($send){
                if($serverType == "marzban") $res = deleteMarzban($server_id, $remark);
                else{if($inbound_id > 0) $res = deleteClient($server_id, $inbound_id, $uuid, 1); else $res = deleteInbound($server_id, $uuid, 1); }
        		if(!is_null($res)){
                    $msg = "
    اشتراک سرویس $remark منقضی شد و از لیست سفارش ها حذف گردید. لطفا از فروشگاه, سرویس جدید خریداری کنید.";
                    sendMessage( $msg, null, null, $from_id);
                    $stmt = $connection->prepare("DELETE FROM `orders_list` WHERE `uuid`=?");
                    $stmt->bind_param("s", $uuid);
                    $stmt->execute();
                    $stmt->close();
                    continue;
        		}
            }                
            else{
                $stmt = $connection->prepare("UPDATE `orders_list` SET `notif`= 0 WHERE `uuid`=?");
                $stmt->bind_param("s", $uuid);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}
