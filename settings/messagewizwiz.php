<?php 
include_once '../baseInfo.php';
include_once '../config.php';
include_once 'jdf.php';

$rateLimit = $botState['rateLimit']??0;
if(time() > $rateLimit){
    $rate = json_decode(curl_get_file_contents("https://api.pooleno.ir/v1/currency/short-name/trx?type=buy"),true);
    $botState['USDRate'] = round($rate['priceUsdt'],2);
    $botState['TRXRate'] = round($rate['priceFiat'] / 10,2);
    $botState['rateLimit'] = strtotime("+1 hour");
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    $stmt->execute();
    $isExists = $stmt->get_result();
    $stmt->close();
    if($isExists->num_rows>0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
    else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
    $newData = json_encode($botState);
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $newData);
    $stmt->execute();
    $stmt->close();
}

$stmt = $connection->prepare("SELECT * FROM `send_list` WHERE `state` = 1 LIMIT 1");
$stmt->execute();
$list = $stmt->get_result();
$stmt->close();

if($list->num_rows > 0){
    $info = $list->fetch_assoc();
    
    $sendId = $info['id'];
    $offset = $info['offset'];
    $type = $info['type'];
    $file_id = $info['file_id'];
    $chat_id = $info['chat_id'];
    $text = $info['text'];
    $message_id = $info['message_id'];
    
    if($offset == '0'){
        if($type == "forwardall") $msg = "عملیات هدایت همگانی شروع شد";
        else $msg = "عملیات ارسال پیام همگانی شروع شد";
        
        bot('sendMessage',[
            'chat_id'=>$admin,
            'text'=>$msg
            ]);
    }
    
    $stmt = $connection->prepare("SELECT * FROM `users`ORDER BY `id` LIMIT 50 OFFSET ?");
    $stmt->bind_param("i", $offset);
    $stmt->execute();
    $usersList = $stmt->get_result();
    $stmt->close();
    
    $keys = json_encode([
                'inline_keyboard' => [
                    [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                    ]
            ]);
    if($usersList->num_rows > 1) {
        while($user = $usersList->fetch_assoc()){
            if($type == 'text'){
                sendMessage($text,$keys,null,$user['userid']);
            }elseif($type == 'music'){
                bot('sendAudio',[
                    'chat_id' => $user['userid'],
                    'audio' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$keys
                ]);
            }elseif($type == 'video'){
                bot('sendVideo',[
                    'chat_id' => $user['userid'],
                    'video' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$keys
                ]);
            }elseif($type == 'voice'){
                bot('sendVoice',[
                    'chat_id' => $user['userid'],
                    'voice' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$keys
                ]);
            }elseif($type == 'document'){
                bot('sendDocument',[
                    'chat_id' => $user['userid'],
                    'document' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$kes
                ]);
            }elseif($type == 'photo'){
                bot('sendPhoto', [
                    'chat_id' => $user['userid'],
                    'photo' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$keys
                ]); 
            }elseif($type == "forwardall"){
                forwardmessage($user['userid'], $chat_id, $message_id);
            }
            else {
                bot('sendDocument',[
                    'chat_id' => $user['userid'],
                    'document' => $file_id,
                    'caption' => $text,
                    'reply_markup'=>$keys
                ]);
            }
            $offset++;
        }
        $stmt = $connection->prepare("UPDATE `send_list` SET `offset` = ? WHERE `id` = ?");
        $stmt->bind_param("ii", $offset, $sendId);
        $stmt->execute();
        $stmt->close();
    }else{
        if($type == "forwardall") $msg = "عملیات هدایت همگانی با موفقیت انجام شد";
        else $msg = "عملیات ارسال پیام همگانی با موفقیت انجام شد";
        
        bot('sendMessage',[
            'chat_id'=>$admin,
            'text'=>$msg . "\nپیام شمابه  " . $offset . "نفر ارسال شد"
            ]);
            
        $stmt = $connection->prepare("DELETE FROM `send_list` WHERE `id` = ?");
        $stmt->bind_param('i', $sendId);
        $stmt->execute();
        $stmt->close();
    }
}


?>
