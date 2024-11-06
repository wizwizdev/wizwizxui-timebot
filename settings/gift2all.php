<?php
include_once '../baseInfo.php';
include_once '../config.php';
$time = time();

$stmt = $connection->prepare("SELECT * FROM `gift_list` LIMIT 1");
$stmt->execute();
$giftList = $stmt->get_result();
$stmt->close();

if($giftList->num_rows>0){
    $info = $giftList->fetch_assoc();
    $rowId = $info['id'];
    $server_id = $info['server_id'];
    $volume = $info['volume'];
    $day = $info['day'];
    $offset = $info['offset'];

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $serverName = $stmt->get_result()->fetch_assoc()['title'];
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id` = ?");
    $stmt->bind_param('i', $server_id);
    $stmt->execute();
    $serverConfig = $stmt->get_result()->fetch_assoc();
    $serverType = $serverConfig['type'];
    $stmt->close();

    if($offset == 0) sendMessage("عملیات هدیه حجم و زمان سرور $serverName شروع شد",null,null,$admin);
    
    $rowCount = -1;
    $found = false;
    
    if($serverType == "marzban"){
        $usersList = getMarzbanJson($server_id)->users;
        foreach($usersList as $key => $config){
            $rowCount++;
            if($rowCount < $offset) continue;
            $found = true;
            
    	    $remark = $config->username;
            $total = $config->data_limit;
            $expiry_time = $config->expire;
            $state = $config->status == "active"?true:false;

            if($total > 0 && $expiry_time > 0 && $state){
                $editReponse = editMarzbanConfig($server_id, ['remark'=>$remark, 'plus_volume'=>$volume/1024, 'plus_day'=> $day]);
            }

            if(!is_null($editReponse)){
                $orderExistStmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `server_id` = ? AND `inbound_id` = 0 AND `remark` = ?");
                $orderExistStmt->bind_param("is", $server_id, $remark);
                $orderExistStmt->execute();
                $orderExist = $orderExistStmt->get_result();
                $orderExistStmt->close();
                
                if($orderExist->num_rows > 0){
                    $orderInfo = $orderExist->fetch_assoc();
                    $orderId = $orderInfo['id'];
                    $user_id = $orderInfo['userid'];
                    $gift = "";
                    if($volume != 0) $gift = "$volume مگابایت حجم ";
                    if($day != 0){
                        $expire_date = $day * 86400;
                        $gift .= " و $day روز زمان";
                        $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ? WHERE `id` = ?");
                        $stmt->bind_param("ii", $expire_date, $orderId);
                        $stmt->execute();
                        $stmt->close();
                    }
                        
                    
                    sendMessage("به اشتراک $remark شما $gift اضافه شد",null,null,$user_id);
                }
            }
            
            $stmt = $connection->prepare("UPDATE `gift_list` SET `offset` = `offset` + 1 WHERE `id` = ?");
            $stmt->bind_param("i", $rowId);
            $stmt->execute();
            $stmt->close();
        }
    }else{
        $response = getJson($server_id)->obj; 
        foreach($response as $row){
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $expiry_time = $row->expiryTime;
            $remark = $row->remark;
            $inbound_id = $row->id;
            $inboundEnable = $row->enable;

            $settings = json_decode($row->settings, true); 
            $clients = $settings['clients'];
            if(isset($row->clientStats)){
                foreach($clients as $key => $client) {
                    $rowCount++;
                    if($rowCount < $offset) continue;
                    $found = true;
                    $clientRemark = $client['email'];
                    $uuid = $client['id'];
                    $clientTotal = $client['totalGB'];
                    $clientUp = $client['up'];
                    $clientDown = $client['down'];
                    $clientExpiry = $client['expiryTime'];
                    $clientEnable = $client['enable'];

                
                    if(count($clients) > 1 && $clientTotal > 0 && $clientExpiry > 0 && $clientEnable){
                        $responseEdit = editClientTraffic($server_id, $inbound_id, $uuid, ($volume / 1024), $day);
                        $orderExistStmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `server_id` = ? AND `inbound_id` = ? AND `uuid` = ?");
                        $orderExistStmt->bind_param("iis", $server_id, $inbound_id, $uuid);
                    }
                    elseif($total > 0 && $expiry_time > 0 && $inboundEnable){
                        $responseEdit = editInboundTraffic($server_id, $uuid, ($volume/1024), $day);
                        $orderExistStmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `server_id` = ? AND `inbound_id` = 0 AND `uuid` = ?");
                        $orderExistStmt->bind_param("is", $server_id, $uuid);
                    }
                    
                    if(!is_null($responseEdit)){
                        $orderExistStmt->execute();
                        $orderExist = $orderExistStmt->get_result();
                        $orderExistStmt->close();
                        
                        if($orderExist->num_rows > 0){
                            $orderInfo = $orderExist->fetch_assoc();
                            $orderId = $orderInfo['id'];
                            $user_id = $orderInfo['userid'];
                            $gift = "";
                            if($volume != 0) $gift = "$volume مگابایت حجم ";
                            if($day != 0){
                                $expire_date = $day * 86400;
                                $gift .= " و $day روز زمان";
                                $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ? WHERE `id` = ?");
                                $stmt->bind_param("ii", $expire_date, $orderId);
                                $stmt->execute();
                                $stmt->close();
                            }
                                
                            
                            sendMessage("به اشتراک $clientRemark شما $gift اضافه شد",null,null,$user_id);
                        }
                    }
                    
                    $stmt = $connection->prepare("UPDATE `gift_list` SET `offset` = `offset` + 1 WHERE `id` = ?");
                    $stmt->bind_param("i", $rowId);
                    $stmt->execute();
                    $stmt->close();
                }
            }else{
                $rowCount++;
                if($rowCount < $offset) continue;
                $found = true;
                $uuid = $clients[0]['id'];
                if($total > 0 && $expiry_time > 0 && $inboundEnable){
                    $responseEdit = editInboundTraffic($server_id, $uuid, ($volume/1024), $day);
                    if(!is_null($responseEdit)){
                        $stmt = $connection->prepare("UPDATE `gift_list` SET `offset` = `offset` + 1 WHERE `id` = ?");
                        $stmt->bind_param("i", $rowId);
                        $stmt->execute();
                        $stmt->close();
                        
                        $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `server_id` = ? AND `inbound_id` = ? AND `uuid` = ?");
                        $stmt->bind_param("iis", $server_id, $inbound_id, $uuid);
                        $stmt->execute();
                        $orderExist = $stmt->get_result();
                        $stmt->close();
                        
                        if($orderExist->num_rows > 0){
                            $orderInfo = $orderExist->fetch_assoc();
                            $orderId = $orderInfo['id'];
                            $user_id = $orderInfo['userid'];
                            $gift = "";
                            if($volume != 0) $gift = "$volume مگابایت حجم ";
                            if($day != 0){
                                $expire_date = $day * 86400;
                                $gift .= " و $day روز زمان";
                                $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ? WHERE `id` = ?");
                                $stmt->bind_param("ii", $expire_date, $orderId);
                                $stmt->execute();
                                $stmt->close();
                            }
                            
                            sendMessage("به اشتراک $remark شما $gift اضافه شد",null,null,$user_id);
                        }
                    }
                }
                $stmt = $connection->prepare("UPDATE `gift_list` SET `offset` = `offset` + 1 WHERE `id` = ?");
                $stmt->bind_param("i", $rowId);
                $stmt->execute();
                $stmt->close();
            }
            
        }
    }
    
    if($found == false){
        $stmt = $connection->prepare("DELETE FROM `gift_list` WHERE `id` = ?");
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $stmt->close();
        
        sendMessage("عملیات هدیه حجم و زمان سرور $serverName به اتمام رسید",null,null,$admin);
    }
}
