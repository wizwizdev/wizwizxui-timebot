<?php
include_once 'baseInfo.php';
include_once 'config.php';
$time = time();

$stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `notif`=0");
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
        $server_id = $order['server_id'];
        $inbound_id = $order['inbound_id'];
        $links_list = $order['link']; 
        $notif = $order['notif'];
        $response = getJson($server_id)->obj;  
        foreach($response as $row){
            if($inbound_id == 0) { 
                if($row->remark == $remark) { 
                    $total = $row->total;
                    $up = $row->up;
                    $down = $row->down;
                    $expiryTime = $row->expiryTime;
                    break;
                }
            }else{
                if($row->id == $inbound_id) {
                    $settings = json_decode($row->settings, true); 
                    $clients = $settings['clients'];
                    foreach($clients as $key => $client) {
                        if($client['email'] == $remark) {
                            $total = $client['totalGB'];
                            break;
                        }
                    }
                    
                    $clientStats = $row->clientStats; 
                    foreach($clientStats as $key => $clientStat) {
                        if($clientStat->email == $remark) {
                            $up = $clientStat->up;
                            $down = $clientStat->down;
                            $expiryTime = $clientStat->expiryTime;
                            break;
                        }
                    }
                    break;
                }
            }
        } 
            $leftgb = round( ($total - $up - $down) / 1073741824, 2);
            $now_microdate = floor(microtime(true) * 1000);
            if($expiryTime != null && $total != null){
            if($expiryTime < $now_microdate + 86400) $send = "روز"; elseif($leftgb < 1) $send = "گیگ";
            if($send){  
                $msg = "❌ |  مشترک گرامی 
    از اشتراک $remark تنها (۱ $send) باقی مانده است لطفا هر چه سریع تر سرویس خود را تمدید کنید ...";
                sendMessage( $msg, null, null, $from_id);
                $newTIme = $time + 86400 * 2;
                $stmt = $connection->prepare("UPDATE `orders_list` SET `notif`= ? WHERE `remark`=?");
                $stmt->bind_param("is", $newTIme, $remark);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
  }
}

$stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `notif` !=0");
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
            $server_id = $order['server_id'];
            $inbound_id = $order['inbound_id'];
            $links_list = $order['link']; 
            $notif = $order['notif'];
            
            if($time > $notif) {
                $response = getJson($server_id)->obj;  
                foreach($response as $row){
                    if($inbound_id == 0) { 
                        if($row->remark == $remark) { 
                            $total = $row->total;
                            $up = $row->up;
                            $down = $row->down;
                            $expiryTime = $row->expiryTime;
                            break;
                        }
                    }else{
                        if($row->id == $inbound_id) {
                            $settings = json_decode($row->settings, true); 
                            $clients = $settings['clients'];
                            foreach($clients as $key => $client) {
                                if($client['email'] == $remark) {
                                    $total = $client['totalGB'];
                                    break;
                                }
                            }
                            
                            $clientStats = $row->clientStats; 
                            foreach($clientStats as $key => $clientStat) {
                                if($clientStat->email == $remark) {
                                    $up = $clientStat->up;
                                    $down = $clientStat->down;
                                    $expiryTime = $clientStat->expiryTime;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                } 
                $leftgb = round( ($total - $up - $down) / 1073741824, 2);
                $now_microdate = floor(microtime(true) * 1000);
                if($expiryTime <= $now_microdate) $send = true; elseif($leftgb <= 0) $send = true;
                if($send){  
                    if($inbound_id > 0) deleteClient($server_id, $inbound_id, $remark); else deleteInbound($server_id, $remark); 
                    $msg = "🥺 عزیزم
 سرویس $remark تموم شد و از لیست کانفیگ هات حذف شد ، لطفا یه سرویس جدید بخر ...";
                    sendMessage( $msg, null, null, $from_id);
                    $stmt = $connection->prepare("DELETE FROM `orders_list` WHERE `remark`=?");
                    $stmt->bind_param("s", $remark);
                    $stmt->execute();
                    $stmt->close();
                    continue;
                }                
                else{
                    $stmt = $connection->prepare("UPDATE `orders_list` SET `notif`= 0 WHERE `remark`=?");
                    $stmt->bind_param("s", $remark);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}
