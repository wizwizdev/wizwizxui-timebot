<?php 
$msgInfo = json_decode(file_get_contents("messagewizwiz.json"),true);

$offset = $msgInfo['offset'];
$messageParam = json_decode($msgInfo['text']);

if($offset == '-1') exit;

include_once 'baseInfo.php';
include_once 'config.php';
include_once 'jdf.php';
if($offset == '0'){
    bot('sendMessage',[
        'chat_id'=>$admin,
        'text'=>"عملیات ارسال پیام همگانی شروع شد"
        ]);
}
$stmt = $connection->prepare("SELECT * FROM `users`ORDER BY `id` LIMIT 250 OFFSET ?");
$stmt->bind_param("i", $offset);
$stmt->execute();
$usersList = $stmt->get_result();
$stmt->close();

if( $usersList->num_rows > 1 ) {
    while($user = $usersList->fetch_assoc()){
        if($messageParam->type == 'text'){
            sendMessage($messageParam->value,null,null,$user['userid']);
        }else {
            if($messageParam->type == 'music'){
                bot('sendAudio',[
                    'chat_id' => $user['userid'],
                    'audio' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]);
            }elseif($messageParam->type == 'video'){
                bot('sendVideo',[
                    'chat_id' => $user['userid'],
                    'video' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]);
            }elseif($messageParam->type == 'voice'){
                bot('sendVoice',[
                    'chat_id' => $user['userid'],
                    'voice' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]);
            }elseif($messageParam->type == 'document'){
                bot('sendDocument',[
                    'chat_id' => $user['userid'],
                    'document' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]);
            }elseif($messageParam->type == 'photo'){
                bot('sendPhoto', [
                    'chat_id' => $user['userid'],
                    'photo' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]); 
            }else {
                bot('sendDocument',[
                    'chat_id' => $user['userid'],
                    'document' => $messageParam->value->fileid,
                    'caption' => $messageParam->value->caption,
                ]);
            }
        }
        $offset++;
    }
    $msgInfo['offset'] = $offset;
    file_put_contents("messagewizwiz.json",json_encode($msgInfo));
}else{
    bot('sendMessage',[
        'chat_id'=>$admin,
        'text'=>"عملیات ارسال پیام همگانی با موفقیت انجام شد\nبه " . $offset . " نفر پیامتو فرستادم"
        ]);
    $msgInfo['offset'] = -1;
    file_put_contents("messagewizwiz.json",json_encode($msgInfo));
}

