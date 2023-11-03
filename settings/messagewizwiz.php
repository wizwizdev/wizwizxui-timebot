<?php 
$msgInfo = json_decode(file_get_contents("messagewizwiz.json"),true);
$offset = $msgInfo['offset']??-1;
$messageParam = json_decode($msgInfo['text']);
$rateLimit = $msgInfo['rateLimit']??0;

include_once '../baseInfo.php';
include_once '../config.php';
include_once 'jdf.php';

if(time() > $rateLimit){
    $rate = json_decode(file_get_contents("https://api.changeto.technology/api/rate"),true)['result'];
    if(!empty($rate['USD'])) $botState['USDRate'] = $rate['USD'];
    if(!empty($rate['TRX'])) $botState['TRXRate'] = $rate['TRX'];
    
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
    
    $msgInfo['rateLimit'] = strtotime("+1 hour");
    file_put_contents("messagewizwiz.json",json_encode($msgInfo));

}


if($offset == '-1') exit;

if($offset == '0'){
    if($messageParam->type == "forwardall") $msg = "عملیات هدایت همگانی شروع شد";
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

if( $usersList->num_rows > 1 ) {
    while($user = $usersList->fetch_assoc()){
        if($messageParam->type == 'text'){
            sendMessage($messageParam->value,json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ]),null,$user['userid']);
        }elseif($messageParam->type == 'music'){
            bot('sendAudio',[
                'chat_id' => $user['userid'],
                'audio' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]);
        }elseif($messageParam->type == 'video'){
            bot('sendVideo',[
                'chat_id' => $user['userid'],
                'video' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]);
        }elseif($messageParam->type == 'voice'){
            bot('sendVoice',[
                'chat_id' => $user['userid'],
                'voice' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]);
        }elseif($messageParam->type == 'document'){
            bot('sendDocument',[
                'chat_id' => $user['userid'],
                'document' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]);
        }elseif($messageParam->type == 'photo'){
            bot('sendPhoto', [
                'chat_id' => $user['userid'],
                'photo' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]); 
        }elseif($messageParam->type == "forwardall"){
            forwardmessage($user['userid'], $messageParam->chat_id, $messageParam->message_id);
        }
        else {
            bot('sendDocument',[
                'chat_id' => $user['userid'],
                'document' => $messageParam->value->fileid,
                'caption' => $messageParam->value->caption,
                'reply_markup'=>json_encode([
                        'inline_keyboard' => [
                            [['text'=>$buttonValues['start_bot'],'callback_data'=>"mainMenu"]]
                            ]
                    ])
            ]);
        }
        $offset++;
    }
    $msgInfo['offset'] = $offset;
    file_put_contents("messagewizwiz.json",json_encode($msgInfo));
}else{
    if($messageParam->type == "forwardall") $msg = "عملیات هدایت همگانی با موفقیت انجام شد";
    else $msg = "عملیات ارسال پیام همگانی با موفقیت انجام شد";
    
    bot('sendMessage',[
        'chat_id'=>$admin,
        'text'=>$msg . "\nبه " . $offset . " نفر پیامتو فرستادم"
        ]);
    $msgInfo['offset'] = -1;
    file_put_contents("messagewizwiz.json",json_encode($msgInfo));
}

