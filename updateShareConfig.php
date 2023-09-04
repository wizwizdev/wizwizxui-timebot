<?php

require "baseInfo.php";
require "config.php";

$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE (`type` IS NULL OR `type` = '') AND `inbound_id` != 0");
$stmt->execute();
$list = $stmt->get_result();
$stmt->close();

if($list->num_rows > 0){
    while($row = $list->fetch_assoc()){
        $serverId = $row['server_id'];
        $inboundId = $row['inbound_id'];
        $rowId = $row['id'];
        
        $response = getJson($serverId);
        $response = $response->obj;
        foreach($response as $config){
            if($config->id == $inboundId) {
                $netType = json_decode($config->streamSettings)->network;
                break;
            }
        }
        
        if(!is_null($netType)){
            $stmt = $connection->prepare("UPDATE `server_plans` SET `type` = ? WHERE `id` = ?");
            $stmt->bind_param("si", $netType, $rowId);
            $stmt->execute();
            $stmt->close();
        }
    }
    echo "REFRESH PAGE";
}else echo "DONE";
