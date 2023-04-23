<?php
include_once '../baseInfo.php';
include_once '../config.php';

if(file_exists("botState.json")){
    $botState = json_decode(file_get_contents("botState.json"),true);
    $sellState=$botState['sellState']=="off"?"خاموش ❌":"روشن ✅";
    $searchState=$botState['searchState']=="off"?"خاموش ❌":"روشن ✅";
    $rewaredTime = ($botState['rewaredTime']??0);
    $rewaredChannel = $botState['rewardChannel'];

    if($rewaredTime>0 && $rewaredChannel != null){
        $lastTime = $botState['lastRewardMessage']??0;
        if(time() > $lastTime){
            $time = time() - ($rewaredTime * 60 * 60);
            $stmt = $connection->prepare("SELECT SUM(amount) as total FROM `orders_list` WHERE `date` > ?");
            $stmt->bind_param("i", $time);
            $stmt->execute();
            $totalRewards = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
            $stmt->close();
            $botState['lastRewardMessage']=time() + ($rewaredTime * 60 * 60);
            file_put_contents("botState.json",json_encode($botState));
            $txt = "⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
🔰درآمد من در $rewaredTime ساعت گذشته

💰مبلغ : $totalRewards تومان

☑️ $channelLock

";
            sendMessage($txt, null, null, $rewaredChannel);
        }
    }    
}
