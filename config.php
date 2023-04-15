<?php

$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
if($connection->connect_error){
    exit("error " . $connection->connect_error);  
}

$connection->set_charset("utf8mb4");

function bot($method, $datas = [])
{
    global $botToken;
    $url = "https://api.telegram.org/bot" . $botToken . "/" . $method;
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
function sendMessage($txt, $key = null, $parse ="MarkDown", $ci= null, $msg = null){
    global $from_id;
    $ci = $ci??$from_id;
    return bot('sendMessage',[
        'chat_id'=>$ci,
        'text'=>$txt,
        'reply_to_message_id'=>$msg,
        'reply_markup'=>$key,
        'parse_mode'=>$parse
    ]);
}
function editText($msgId, $txt, $key = null, $parse = null, $ci = null){
    global $from_id;
    $ci = $ci??$from_id;

    return bot('editMessageText', [
        'chat_id' => $ci,
        'message_id' => $msgId,
        'text' => $txt,
        'parse_mode' => $parse,
        'reply_markup' =>  $key
        ]);
}
function delMessage($msg = null, $chat_id = null){
    global $from_id, $message_id;
    $msg = $msg??$message_id;
    $chat_id = $chat_id??$from_id;
    
    return bot('deleteMessage',[
        'chat_id'=>$chat_id,
        'message_id'=>$msg
        ]);
}
function sendAction($action, $ci= null){
    global $from_id;
    $ci = $ci??$from_id;

    return bot('sendChatAction',[
        'chat_id'=>$ci,
        'action'=>$action
    ]);
}

function forwardmessage($tochatId, $fromchatId, $message_id){
    return bot('forwardMessage',[
        'chat_id'=>$tochatId,
        'from_chat_id'=>$fromchatId,
        'message_id'=>$message_id
    ]);
}

function sendPhoto($photo, $caption = null, $keyboard = null, $parse = "MarkDown", $ci =null){
    global $from_id;
    $ci = $ci??$from_id;
    return bot('sendPhoto',[
        'chat_id'=>$ci,
        'caption'=>$caption,
        'reply_markup'=>$keyboard,
        'photo'=>$photo,
        'parse_mode'=>$parse
    ]);
}
function getFileUrl($fileid)
{
    $filePath = bot('getFile',[
        'file_id'=>$fileid
    ])->result->file_path;
    return "https://api.telegram.org/file/bot" . $botToken . "/" . $filepath;
}

function alert($txt, $type = false, $callid = null){
    global $call_id;
    $callid = $callid??$call_id;
    return bot('answercallbackquery', [
        'callback_query_id' => $callid,
        'text' => $txt,
        'show_alert' => $type
    ]);
}



$time = time();
$update = json_decode(file_get_contents("php://input"));

if(isset($update->message)){
    $from_id = $update->message->from->id;
    $text = $update->message->text;
    $first_name = $update->message->from->first_name;
    $caption = $update->message->caption;
    $last_name = $update->message->from->last_name;
    $username = $update->message->from->username?? " ندارد ";
    $message_id = $update->message->message_id;
    $forward_from_name = $update->message->reply_to_message->forward_sender_name;
    $forward_from_id = $update->message->reply_to_message->forward_from->id;
    $reply_text = $update->message->reply_to_message->text;
}
if(isset($update->callback_query)){
    $call_id = $update->callback_query->id;
    $data = $update->callback_query->data;
    $text = $update->callback_query->message->text;
    $message_id = $update->callback_query->message->message_id;
    $chat_id = $update->callback_query->message->chat->id;
    $chat_type = $update->callback_query->message->chat->type;
    $username = $update->callback_query->from->username?? " ندارد ";
    $from_id = $update->callback_query->from->id;
    $first_name = $update->callback_query->from->first_name;
}

$usersInfo = json_decode(file_get_contents("userInfo.json"),true);
$userInfo = $usersInfo[$from_id];
$botState =json_decode(file_get_contents("botState.json"),true);




if ($update->message->document->file_id) {
    $filetype = 'document';
    $fileid = $update->message->document->file_id;
} elseif ($update->message->audio->file_id) {
    $filetype = 'music';
    $fileid = $update->message->audio->file_id;
} elseif ($update->message->photo[0]->file_id) {
    $filetype = 'photo';
    $fileid = $update->message->photo->file_id;
    if (isset($update->message->photo[2]->file_id)) {
        $fileid = $update->message->photo[2]->file_id;
    } elseif ($fileid = $update->message->photo[1]->file_id) {
        $fileid = $update->message->photo[1]->file_id;
    } else {
        $fileid = $update->message->photo[1]->file_id;
    }
} elseif ($update->message->voice->file_id) {
    $filetype = 'voice';
    $voiceid = $update->message->voice->file_id;
} elseif ($update->message->video->file_id) {
    $filetype = 'video';
    $fileid = $update->message->video->file_id;
}


$cancelText = '😩 منصرف شدم بیخیال';
$cancelKey=json_encode(['keyboard'=>[
    [['text'=>$cancelText]]
],'resize_keyboard'=>true]);
$removeKeyboard = json_encode(['remove_keyboard'=>true]);

if ($from_id == $admin || $userInfo['isAdmin'] == true) {
    $mainKeys = json_encode(['inline_keyboard'=>[
        [['text'=>'📦  کانفیگ های من','callback_data'=>'mySubscriptions'],['text'=>'🛒  خرید کانفیگ جدید','callback_data'=>"buySubscription"]],
		[['text'=>"📡 وضعیت سرورها",'callback_data'=>"availableServers"],['text'=>"🧑‍💼 حساب من",'callback_data'=>"myInfo"]],
        [['text'=>'☑️ لینک نرم افزار ها','callback_data'=>"reciveApplications"],['text'=>"📨 تیکت های من",'callback_data'=>"supportSection"]],
        [['text'=>"🪫 مشخصات کانفیگ",'callback_data'=>"showUUIDLeft"]],
        [['text'=>"مدیریت ربات ⚙️",'callback_data'=>"managePanel"]]
        ]]); 
    $adminKeys = array();
    $adminKeys[] = [['text'=>"📉 آمار کلی ربات",'callback_data'=>"botReports"],['text'=>"💌 پیام به کاربر",'callback_data'=>"messageToSpeceficUser"]];
    if($from_id == $admin){
        $adminKeys[] = [['text'=>"👤 لیست ادمین ها",'callback_data'=>"adminsList"]];
    }
    $adminKeys[] = [['text'=>"💸 افزایش موجودی",'callback_data'=>"increaseUserWallet"],['text'=>"🚸 ساخت اکانت",'callback_data'=>"createMultipleAccounts"]];
    $adminKeys[] = [['text'=>"🔴 مسدود کردن کاربر",'callback_data'=>"banUser"],['text'=>"🟢 آزاد کردن کاربر",'callback_data'=>"unbanUser"]];
    $adminKeys[] = [['text'=>'➕ افزودن پلن جدید','callback_data'=>"addNewPlan"],['text'=>'✔️ مدیریت پلن ها','callback_data'=>"backplan"]];
    $adminKeys[] = [['text'=>'➕ افزودن دسته جدید','callback_data'=>"addNewCategory"],['text'=>'✔️ مدیریت دسته ها','callback_data'=>"categoriesSetting"]];
    $adminKeys[] = [['text'=>'🪙 ثبت سرور جدید','callback_data'=>"addNewServer"],['text'=>'📡 مدیریت سرورها','callback_data'=>"serversSetting"]];
    $adminKeys[] = [['text'=>'📪 تیکت ها','callback_data'=>"ticketsList"],['text'=>'⚙️ تنظیمات ربات','callback_data'=>'botSettings']];
    $adminKeys[] = [['text'=>"📬  ارسال پیام همگانی 📬",'callback_data'=>"message2All"]];
    $adminKeys[] = [['text'=>'⤵️ برگرد به منوی اصلی ','callback_data'=>"mainMenu"]];
    $adminKeys = json_encode(['inline_keyboard'=>$adminKeys]);
		
}else{
    $keys=array();
    $temp=array();
    
    $keys[] = [['text'=>"📡 وضعیت سرورها",'callback_data'=>"availableServers"],['text'=>"🧑‍💼 حساب من",'callback_data'=>"myInfo"]];
    if($botState['sellState']=="on"){
        $keys[]= [['text'=>'📦  کانفیگ های من','callback_data'=>'mySubscriptions'],['text'=>'🛒  خرید کانفیگ جدید','callback_data'=>"buySubscription"]];
    }
    $temp[] =['text'=>"📨 تیکت های من",'callback_data'=>"supportSection"];
    if($botState['searchState']=="on"){
        $temp[] = ['text'=>"🪫 مشخصات کانفیگ",'callback_data'=>"showUUIDLeft"];
        array_push($keys,$temp);
        $temp = array();
    }
    $temp[] =['text'=>'☑️ لینک نرم افزار ها','callback_data'=>"reciveApplications"];
    array_push($keys,$temp);
    $mainKeys=json_encode(['inline_keyboard'=>$keys]);
}


$joniedState= bot('getChatMember', ['chat_id' => $channelLock,'user_id' => $from_id])->result->status;

function RandomString($cont = 9) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randstring = null;
    for ($i = 0; $i < $cont; $i++) {
        $randstring .= $characters[
            rand(0, strlen($characters))
        ];
    }
    return $randstring;
}


function generateUID()
{
    $randomString = openssl_random_pseudo_bytes(16);
    $time_low = bin2hex(substr($randomString, 0, 4));
    $time_mid = bin2hex(substr($randomString, 4, 2));
    $time_hi_and_version = bin2hex(substr($randomString, 6, 2));
    $clock_seq_hi_and_reserved = bin2hex(substr($randomString, 8, 2));
    $node = bin2hex(substr($randomString, 10, 6));

    $time_hi_and_version = hexdec($time_hi_and_version);
    $time_hi_and_version = $time_hi_and_version >> 4;
    $time_hi_and_version = $time_hi_and_version | 0x4000;

    $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

    return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
}


function checkStep($table){
    global $connection;
    $sql = "SELECT * FROM `" . $table . "` WHERE `active`=0";
    $stmt = $connection->prepare("SELECT * FROM `$table` WHERE `active` = 0");
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $res['step']; 
}
function setUser($value = 'none', $field = 'step'){
    global $usersInfo, $from_id;
    $usersInfo[$from_id][$field] = $value;
    file_put_contents("userInfo.json",json_encode($usersInfo));
}

function generateRandomString($length = 10, $protocol) {
    return ($protocol == 'trojan') ? substr(md5(time()),5,15) : generateUID();
}

function addBorderImage($add)
{
    $border = 30;
    $im = ImageCreateFromPNG($add);
    $width = ImageSx($im);
    $height = ImageSy($im);
    $img_adj_width = $width + 2 * $border;
    $img_adj_height = $height + 2 * $border;
    $newimage = imagecreatetruecolor($img_adj_width, $img_adj_height);
    $border_color = imagecolorallocate($newimage, 255, 255, 255);
    imagefilledrectangle($newimage, 0, 0, $img_adj_width, $img_adj_height, $border_color);
    imageCopyResized($newimage, $im, $border, $border, 0, 0, $width, $height, $width, $height);
    ImagePNG($newimage, $add, 5);
}




function sumerize($amount){
    $gb = $amount / (1024 * 1024 * 1024);
    if($gb > 1){
       return round($gb,2) . " گیگابایت"; 
    }
    else{
        $gb *= 1024;
        return round($gb,2) . " مگابایت";
    }

}


function deleteClient($server_id, $inbound_id, $remark, $delete = 0){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    $old_data = []; $oldclientstat = [];
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $settings = json_decode($row->settings);
            $clients = $settings->clients;
            foreach($clients as $key => $client) {
                if($client->email == $remark) {
                    $old_data = $client;
                    unset($clients[$key]);
                    break;
                }
            }

            $clientStats = $row->clientStats;
            foreach($clientStats as $key => $clientStat) {
                if($clientStat->email == $remark) {
                    $total = $clientStat->total;
                    $up = $clientStat->up;
                    $down = $clientStat->down;
                    break;
                }
            }
            break;
        }
    }
    $settings->clients = $clients;
    $settings = json_encode($settings);
	
    if($delete == 1){
        $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $row->remark,'enable' => 'true',
        'expiryTime' => $row->expiryTime, 'listen' => '','port' => $row->port,'protocol' => $row->protocol,'settings' => $settings,
        'streamSettings' => $row->streamSettings, 'sniffing' => $row->sniffing);

        $serverName = $server_info['username'];
        $serverPass = $server_info['password'];
        
        $loginUrl = $panel_url . '/login';
        
        $postFields = array(
            "username" => $serverName,
            "password" => $serverPass
            );
            
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $loginUrl);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
        $loginResponse = json_decode(curl_exec($curl),true);
        if(!$loginResponse['success']){
            curl_close($curl);
            return $loginResponse;
        }
        
        // $cookie = file_get_contents("tempCookie.txt");
        // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
        // $cookie = $CookieInfo[1];
        // $cookie = 'Cookie: session='.$cookie;
        // unlink("tempCookie.txt");

        $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
        if($serverType == "sanaei"){
            $dataArr = array(
                "id"=>$inbound_id,
                "settings" => $settings
                );
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$panel_url/xui/inbound/delClient/$remark",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $dataArr,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
                // CURLOPT_HTTPHEADER => array(
                //     'Host: '.$phost,
                //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                //     'Accept:  application/json, text/plain, */*',
                //     'Accept-Language:  en-US,en;q=0.5',
                //     'Accept-Encoding:  gzip, deflate',
                //     'X-Requested-With:  XMLHttpRequest',
                //     $cookie
                // ),
            ));
        }else{
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$panel_url/xui/inbound/update/$inbound_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 15,  
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $dataArr,
                CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
                // CURLOPT_HTTPHEADER => array(
                //     'Host: '.$phost,
                //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                //     'Accept:  application/json, text/plain, */*',
                //     'Accept-Language:  en-US,en;q=0.5',
                //     'Accept-Encoding:  gzip, deflate',
                //     'X-Requested-With:  XMLHttpRequest',
                //     $cookie
                // ),
            ));
        }
        
        $response = curl_exec($curl);
        unlink("tempCookie.txt");

        curl_close($curl);
    }	
    return ['id' => $old_data->id,'expiryTime' => $old_data->expiryTime, 'limitIp' => $old_data->limitIp, 'flow' => $old_data->flow, 'total' => $total, 'up' => $up, 'down' => $down,];

}
function editInboundTraffic($server_id, $remark, $volume, $days){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        if($row->remark == $remark) {
            $inbound_id = $row->id;
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $expiryTime = $row->expiryTime;
            $port = $row->port;
            $netType = json_decode($row->streamSettings)->network;
            break;
        }
    }
    if($days != 0) {
        $now_microdate = floor(microtime(true) * 1000);
        $extend_date = (864000 * $days * 100);
        $expire_microdate = ($now_microdate > $expiryTime) ? $now_microdate + $extend_date : $expiryTime + $extend_date;
    }

    if($volume != 0){
        $leftGB = $total;// - $up - $down;
        $extend_volume = floor($volume * 1073741824);
        $total = ($leftGB > 0) ? $leftGB + $extend_volume : $extend_volume;
    }

    $dataArr = array('up' => $up,'down' => $down,'total' => is_null($total) ? $row->total : $total,'remark' => $row->remark,'enable' => 'true',
        'expiryTime' => is_null($expire_microdate) ? $row->expiryTime : $expire_microdate, 'listen' => '','port' => $row->port,'protocol' => $row->protocol,'settings' => $row->settings,
        'streamSettings' => $row->streamSettings, 'sniffing' => $row->sniffing);
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$panel_url/xui/inbound/update/$inbound_id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,      // timeout on connect
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
        // CURLOPT_HTTPHEADER => array(
        //     'Host: '.$phost,
        //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
        //     'Accept:  application/json, text/plain, */*',
        //     'Accept-Language:  en-US,en;q=0.5',
        //     'Accept-Encoding:  gzip, deflate',
        //     'X-Requested-With:  XMLHttpRequest',
        //     $cookie
        // ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    unlink("tempCookie.txt");
    return $response = json_decode($response);

}
function editClientTraffic($server_id, $inbound_id, $remark, $volume, $days){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    $client_key = 0;
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $settings = json_decode($row->settings, true);
            $clients = $settings['clients'];
            foreach($clients as $key => $client) {
                if($client['email'] == $remark) {
                    $client_key = $key;
                    break;
                }
            }

            $clientStats = $row->clientStats;
            foreach($clientStats as $key => $clientStat) {
                if($clientStat->email == $remark) {
                    $total = $clientStat->total;
                    $up = $clientStat->up;
                    $down = $clientStat->down;
                    break;
                }
            }
            break;
        }
    }
    if($volume != 0){
        /*if($serverType == "sanaei") $res = resetClientTraffic($server_id, $remark, $inbound_id);
        else $res = resetClientTraffic($server_id, $remark);*/
        $client_total = $settings['clients'][$client_key]['totalGB'];
        //else $client_total = $settings['clients'][$client_key]['totalGB'] - $up - $down;
        $extend_volume = floor($volume * 1073741824);
        $volume = ($client_total > 0) ? $client_total + $extend_volume : $extend_volume;
        $settings['clients'][$client_key]['totalGB'] = $volume;
    }

    if($days != 0){
        $expiryTime = $settings['clients'][$client_key]['expiryTime'];
        $now_microdate = floor(microtime(true) * 1000);
        $extend_date = (864000 * $days * 100);
        $expire_microdate = ($now_microdate > $expiryTime) ? $now_microdate + $extend_date : $expiryTime + $extend_date;
        $settings['clients'][$client_key]['expiryTime'] = $expire_microdate;
    }

    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings);
    $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $row->remark,'enable' => 'true',
        'expiryTime' => $row->expiryTime, 'listen' => '','port' => $row->port,'protocol' => $row->protocol,'settings' => $settings,
        'streamSettings' => $row->streamSettings, 'sniffing' => $row->sniffing);
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    if($serverType == "sanaei"){
        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $settings
            );
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$panel_url/xui/inbound/updateClient/$client_key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
            // CURLOPT_HTTPHEADER => array(
            //     'Host: '.$phost,
            //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            //     'Accept:  application/json, text/plain, */*',
            //     'Accept-Language:  en-US,en;q=0.5',
            //     'Accept-Encoding:  gzip, deflate',
            //     'X-Requested-With:  XMLHttpRequest',
            //     $cookie
            // ),
        ));
    }else{
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$panel_url/xui/inbound/update/$inbound_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
            // CURLOPT_HTTPHEADER => array(
            //     'Host: '.$phost,
            //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            //     'Accept:  application/json, text/plain, */*',
            //     'Accept-Language:  en-US,en;q=0.5',
            //     'Accept-Encoding:  gzip, deflate',
            //     'X-Requested-With:  XMLHttpRequest',
            //     $cookie
            // ),
        ));
    }

    $response = curl_exec($curl);

    curl_close($curl);
    unlink("tempCookie.txt");
    return $response = json_decode($response);

}

function deleteInbound($server_id, $remark, $delete = 0){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        if($row->remark == $remark) {
            $inbound_id = $row->id;
            $protocol = $row->protocol;
            $uniqid = ($protocol == 'trojan') ? json_decode($row->settings)->clients[0]->password : json_decode($row->settings)->clients[0]->id;
            $netType = json_decode($row->streamSettings)->network;
            $oldData = [
                'total' => $row->total,
                'up' => $row->up,
                'down' => $row->down,
                'volume' => $row->total - $row->up - $row->down,
                'port' => $row->port,
                'protocol' => $protocol,
                'expiryTime' => $row->expiryTime,
                'uniqid' => $uniqid,
                'netType' => $netType,
                'security' => json_decode($row->streamSettings)->security,
            ];
            break;
        }
    }
    if($delete == 1){
        $serverName = $server_info['username'];
        $serverPass = $server_info['password'];
        
        $loginUrl = $panel_url . '/login';
        
        $postFields = array(
            "username" => $serverName,
            "password" => $serverPass
            );
            
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $loginUrl);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
        $loginResponse = json_decode(curl_exec($curl),true);
        if(!$loginResponse['success']){
            curl_close($curl);
            return $loginResponse;
        }
        
        // $cookie = file_get_contents("tempCookie.txt");
        // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
        // $cookie = $CookieInfo[1];
        // $cookie = 'Cookie: session='.$cookie;
        // unlink("tempCookie.txt");

        $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$panel_url/xui/inbound/del/$inbound_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
            // CURLOPT_HTTPHEADER => array(
            //     'Host: '.$phost,
            //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            //     'Accept:  application/json, text/plain, */*',
            //     'Accept-Language:  en-US,en;q=0.5',
            //     'Accept-Encoding:  gzip, deflate',
            //     'X-Requested-With:  XMLHttpRequest',
            //     $cookie
            // ),
        ));

        $response = curl_exec($curl);
        unlink("tempCookie.txt");

        curl_close($curl);
    }
    return $oldData;

}
function resetClientTraffic($server_id, $remark, $inboundId = null){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];

    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    if($inboundId == null) $url = "$panel_url/xui/inbound/resetClientTraffic/$remark";
    else $url = "$panel_url/xui/inbound/$inboundId/resetClientTraffic/$remark";
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
        // CURLOPT_HTTPHEADER => array(
        //     'Host: '.$phost,
        //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
        //     'Accept:  application/json, text/plain, */*',
        //     'Accept-Language:  en-US,en;q=0.5',
        //     'Accept-Encoding:  gzip, deflate',
        //     'X-Requested-With:  XMLHttpRequest',
        //     $cookie
        // ),
    ));

    $response = curl_exec($curl);
    unlink("tempCookie.txt");

    curl_close($curl);
    return $response = json_decode($response);

}
function addInboundAccount($server_id, $client_id, $inbound_id, $expiryTime, $remark, $volume, $limitip = 1, $newarr = ''){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];
    $volume = ($volume == 0) ? 0 : floor($volume * 1073741824);

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $iid = $row->id;
            $protocol = $row->protocol;
            break;
        }
    }
    if(!intval($iid)) return "inbound not Found";

    $settings = json_decode($row->settings, true);
    $id_label = $protocol == 'trojan' ? 'password' : 'id';
    if($newarr == '')
        $settings['clients'][] = [
            "$id_label" => $client_id,
            "flow" => "xtls-rprx-direct",
            "email" => $remark,
            "limitIp" => $limitip,
            "totalGB" => $volume,
            "expiryTime" => $expiryTime
        ];
    elseif(is_array($newarr)) $settings['clients'][] = $newarr;

    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings);

    $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $row->remark,'enable' => 'true',
        'expiryTime' => $row->expiryTime, 'listen' => '','port' => $row->port,'protocol' => $row->protocol,'settings' => $settings,
        'streamSettings' => $row->streamSettings, 'sniffing' => $row->sniffing);

    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    $cookie = file_get_contents("tempCookie.txt");
    preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    $cookie = $CookieInfo[1];
    $cookie = 'Cookie: session='.$cookie;
    unlink("tempCookie.txt");

    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    if($serverType == "sanaei"){
        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $settings
            );
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$panel_url/xui/inbound/addClient/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
            // CURLOPT_HTTPHEADER => array(
            //     'Host: '.$phost,
            //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            //     'Accept:  application/json, text/plain, */*',
            //     'Accept-Language:  en-US,en;q=0.5',
            //     'Accept-Encoding:  gzip, deflate',
            //     'X-Requested-With:  XMLHttpRequest',
            //     $cookie
            // ),
        ));
    }else{
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$panel_url/xui/inbound/update/$iid",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
            // CURLOPT_HTTPHEADER => array(
            //     'Host: '.$phost,
            //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            //     'Accept:  application/json, text/plain, */*',
            //     'Accept-Language:  en-US,en;q=0.5',
            //     'Accept-Encoding:  gzip, deflate',
            //     'X-Requested-With:  XMLHttpRequest',
            //     $cookie
            // ),
        ));
    }

    $response = curl_exec($curl);
    curl_close($curl);
    unlink("tempCookie.txt");
    return $response = json_decode($response);

}
function getNewHeaders($netType, $request_header, $response_header, $type){
    global $connection;
    $input = explode(':', $request_header);
    $key = $input[0];
    $value = $input[1];

    $input = explode(':', $response_header);
    $reskey = $input[0];
    $resvalue = $input[1];

    $headers = '';
    if( $netType == 'tcp'){
        if($type == 'none') {
            $headers = '{
              "type": "none"
            }';
        }else {
            $headers = '{
              "type": "http",
              "request": {
                "method": "GET",
                "path": [
                  "/"
                ],
                "headers": {
                   "'.$key.'": [
                     "'.$value.'"
                  ]
                }
              },
              "response": {
                "version": "1.1",
                "status": "200",
                "reason": "OK",
                "headers": {
                   "'.$reskey.'": [
                     "'.$resvalue.'"
                  ]
                }
              }
            }';
        }

    }elseif( $netType == 'ws'){
        if($type == 'none') {
            $headers = '{}';
        }else {
            $headers = '{
              "'.$key.'": "'.$value.'"
            }';
        }
    }
    return $headers;

}
function getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id = 0){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $server_ip = $server_info['ip'];
    $sni = $server_info['sni'];
    $header_type = $server_info['header_type'];
    $request_header = $server_info['request_header'];
    $response_header = $server_info['response_header'];
    $cookie = 'Cookie: session='.$server_info['cookie'];

    $panel_url = str_ireplace('http://','',$panel_url);
    $panel_url = str_ireplace('https://','',$panel_url);
    $panel_url = strtok($panel_url,":");
    if($server_ip == '') $server_ip = $panel_url;

    $response = getJson($server_id)->obj;
    foreach($response as $row){
        if($inbound_id == 0){
            if($row->remark == $remark) {
                $tlsStatus = json_decode($row->streamSettings)->security;
                $tlsSetting = json_decode($row->streamSettings)->tlsSettings;
                $xtlsSetting = json_decode($row->streamSettings)->xtlsSettings;
                $netType = json_decode($row->streamSettings)->network;
                if($header_type == 'http'){
                    $request_header = explode(':', $request_header);
                    $host = $request_header[1];
                }
                if($netType == 'grpc') {
                    if($tlsStatus == 'tls'){
                        $alpn = $tlsSetting->certificates->alpn;
                    } 
                    $serviceName = json_decode($row->streamSettings)->grpcSettings->serviceName;
                    $grpcSecurity = json_decode($row->streamSettings)->security;
                }
                if($tlsStatus == 'tls'){
                    $serverName = $tlsSetting->serverName;
                }
                if($tlsStatus == "xtls"){
                    $serverName = $xtlsSetting->serverName;
                    $alpn = $tlsSetting->alpn;
                }
                if($netType == 'kcp'){
                    $kcpSettings = json_decode($row->streamSettings)->kcpSettings;
                    $kcpType = $kcpSettings->header->type;
                    $kcpSeed = $kcpSettings->seed;
                }

                break;
            }
        }else{
            if($row->id == $inbound_id) {
                $port = $row->port;
                $tlsStatus = json_decode($row->streamSettings)->security;
                $tlsSetting = json_decode($row->streamSettings)->tlsSettings;
                $xtlsSetting = json_decode($row->streamSettings)->xtlsSettings;
                $netType = json_decode($row->streamSettings)->network;
                if($netType == 'tcp') {
                    $headerType = json_decode($row->streamSettings)->tcpSettings->header->type;
                    $path = json_decode($row->streamSettings)->tcpSettings->header->request->path[0];
                    $host = json_decode($row->streamSettings)->tcpSettings->header->request->headers->Host[0];
                }elseif($netType == 'ws') {
                    $headerType = json_decode($row->streamSettings)->wsSettings->header->type;
                    $path = json_decode($row->streamSettings)->wsSettings->path;
                    $host = json_decode($row->streamSettings)->wsSettings->headers->Host;
                }elseif($netType == 'grpc') {
                    if($tlsStatus == 'tls'){
                        $alpn = $tlsSetting->alpn;
                    }
                    $serviceName = json_decode($row->streamSettings)->grpcSettings->serviceName;
                    $grpcSecurity = json_decode($row->streamSettings)->security;
                }elseif($netType == 'kcp'){
                    $kcpSettings = json_decode($row->streamSettings)->kcpSettings;
                    $kcpType = $kcpSettings->header->type;
                    $kcpSeed = $kcpSettings->seed;
                }
                if($tlsStatus == 'tls'){
                    $serverName = $tlsSetting->serverName;
                }
                if($tlsStatus == "xtls"){
                    $serverName = $xtlsSetting->serverName;
                    $alpn = $tlsSetting->alpn;
                }

                break;
            }
        }


    }
    $protocol = strtolower($protocol);
    
    $serverIp = explode("\n",$server_ip);
    $outputLink = array();
    foreach($serverIp as $server_ip){
        if($inbound_id == 0) {
            if($protocol == 'vless'){
                $psting = '';
                if($header_type == 'http') $psting .= "&path=/&host=$host"; else $psting .= '';
                if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                if(strlen($sni) > 1) $psting .= "&sni=$sni";
                if(strlen($serverName)>1 && $tlsStatus=="xtls") $server_ip = $serverName;
                if($tlsStatus == "xtls" && $netType == "tcp") $psting .= "&flow=xtls-rprx-direct";

                $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus{$psting}#$remark";
    
                if($netType == 'grpc'){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$serverName#$remark";
                    }else{
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName#$remark";
                    }
    
                }
            }
    
            if($protocol == 'trojan'){
                $psting = '';
                if($header_type == 'http') $psting .= "&path=/&host=$host";
                if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                if(strlen($sni) > 1) $psting .= "&sni=$sni";
                if($tlsStatus != 'none') $tlsStatus = 'tls';
                $outputlink = "$protocol://$uniqid@$server_ip:$port?security=$tlsStatus{$psting}#$remark";
            }elseif($protocol == 'vmess'){
                $vmessArr = [
                    "v"=> "2",
                    "ps"=> $remark,
                    "add"=> $server_ip,
                    "port"=> $port,
                    "id"=> $uniqid,
                    "aid"=> 0,
                    "net"=> $netType,
                    "type"=> $kcpType ? $kcpType : "none",
                    "host"=> is_null($host) ? '' : $host,
                    "path"=> (is_null($path) and $path != '') ? '/' : (is_null($path) ? '' : $path),
                    "tls"=> (is_null($tlsStatus)) ? 'none' : $tlsStatus
                ];
                if($header_type == 'http'){
                    $vmessArr['path'] = "/";
                    $vmessArr['type'] = $header_type;
                    $vmessArr['host'] = $host;
                }
                if($netType == 'grpc'){
                    if(!is_null($alpn) and json_encode($alpn) != '[]' and $alpn != '') $vmessArr['alpn'] = $alpn;
                    if(strlen($serviceName) > 1) $vmessArr['path'] = $serviceName;
    				$vmessArr['type'] = $grpcSecurity;
                    $vmessArr['scy'] = 'auto';
                }
                if($netType == 'kcp'){
                    $vmessArr['path'] = $kcpSeed ? $kcpSeed : $vmessArr['path'];
    	        }
                if(strlen($sni) > 1) $vmessArr['sni'] = $sni;
                $urldata = base64_encode(json_encode($vmessArr,JSON_UNESCAPED_SLASHES,JSON_PRETTY_PRINT));
                $outputlink = "vmess://$urldata";
            }
        }else {
            if($protocol == 'vless'){
                if(strlen($sni) > 1) $psting = "&sni=$sni"; else $psting = '';
                if($netType == 'tcp'){
                    if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                    if($tlsStatus=="xtls") $psting .= "&flow=xtls-rprx-direct";

                    $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&path=/&host=$host{$psting}#$remark";
                }elseif($netType == 'ws')
                    $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&path=/&host=$host{$psting}#$remark";
                elseif($netType == 'kcp')
                    $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&headerType=$kcpType&seed=$kcpSeed#$remark";
                elseif($netType == 'grpc'){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$serverName#$remark";
                    }else{
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName#$remark";
                    }
                }
            }elseif($protocol == 'trojan'){
                $psting = '';
                if($header_type == 'http') $psting .= "&path=/&host=$host";
                if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                if(strlen($sni) > 1) $psting .= "&sni=$sni";
                if($tlsStatus != 'none') $psting .= "&security=tls&flow=xtls-rprx-direct";
                if($netType == 'grpc') $psting = "&serviceName=$serviceName";
    
                $outputlink = "$protocol://$uniqid@$server_ip:$port{$psting}#$remark";
            }elseif($protocol == 'vmess'){
                $vmessArr = [
                    "v"=> "2",
                    "ps"=> $remark,
                    "add"=> $server_ip,
                    "port"=> $port,
                    "id"=> $uniqid,
                    "aid"=> 0,
                    "net"=> $netType,
                    "type"=> ($headerType) ? $headerType : ($kcpType ? $kcpType : "none"),
                    "host"=> is_null($host) ? '' : $host,
                    "path"=> (is_null($path) and $path != '') ? '/' : (is_null($path) ? '' : $path),
                    "tls"=> (is_null($tlsStatus)) ? 'none' : $tlsStatus
                ];
                if($netType == 'grpc'){
                    if(!is_null($alpn) and json_encode($alpn) != '[]' and $alpn != '') $vmessArr['alpn'] = $alpn;
                    if(strlen($serviceName) > 1) $vmessArr['path'] = $serviceName;
                    $vmessArr['type'] = $grpcSecurity;
                    $vmessArr['scy'] = 'auto';
                }
                if($netType == 'kcp'){
                    $vmessArr['path'] = $kcpSeed ? $kcpSeed : $vmessArr['path'];
    	        }
    
                if(strlen($sni) > 1) $vmessArr['sni'] = $sni;
                $urldata = base64_encode(json_encode($vmessArr,JSON_UNESCAPED_SLASHES,JSON_PRETTY_PRINT));
                $outputlink = "vmess://$urldata";
            }
        }
        $outputLink[] = $outputlink;
    }
    
    return $outputLink;
}

function editInbound($server_id, $uniqid, $remark, $protocol, $netType = 'tcp', $security = 'none'){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $security = $server_info['security'];
    $tlsSettings = $server_info['tlsSettings'];
    $header_type = $server_info['header_type'];
    $request_header = $server_info['request_header'];
    $response_header = $server_info['response_header'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];
    $xtlsTitle = $serverType == "sanaei"?"XTLSSettings":"xtlsSettings";
    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        if($row->remark == $remark) {
            $iid = $row->id;
            $streamSettings = $row->streamSettings;
            $settings = $row->settings;
            break;
        }
    }
    if(!intval($iid)) return;

    $headers = getNewHeaders($netType, $request_header, $response_header, $header_type);

    if($protocol == 'trojan'){
        if($security == 'none'){
            $streamSettings = '{
    	  "network": "tcp",
    	  "security": "none",
    	  "tcpSettings": {
    		"header": {
			  "type": "none"
			}
    	  }
    	}';
            $settings = '{
    	  "clients": [
    		{
    		  "id": "'.$uniqid.'",
    		  "flow": "xtls-rprx-direct"
    		}
    	  ],
    	  "decryption": "none",
    	  "fallbacks": []
    	}';
        }elseif($security == 'xtls') {
            $streamSettings = '{
    	  "network": "tcp",
    	  "security": "'.$security.'",
    	  "' . $xtlsTitle . '": '.$tlsSettings.',
    	  "tcpSettings": {
            "header": '.$headers.'
          }
    	}';
            $wsSettings = '{
          "network": "ws",
          "security": "'.$security.'",
    	  "' . $xtlsTitle .'": '.$tlsSettings.',
          "wsSettings": {
            "path": "/",
            "headers": '.$headers.'
          }
        }';
            $settings = '{
          "clients": [
            {
              "id": "'.$uniqid.'",
    		  "flow": "xtls-rprx-direct"
            }
          ],
          "decryption": "none",
    	  "fallbacks": []
        }';
        }
        else{
            $streamSettings = '{
		  "network": "tcp",
		  "security": "'.$security.'",
		  "'.$security.'Settings": '.$tlsSettings.',
		  "tcpSettings": {
			"header": {
			  "type": "none"
			}
		  }
		}';
            $settings = '{
		  "clients": [
			{
			  "password": "'.$uniqid.'",
			  "flow": "xtls-rprx-direct"
			}
		  ],
		  "fallbacks": []
		}';
        }

        $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $remark,'enable' => 'true',
            'expiryTime' => $row->expiryTime,'listen' => '','port' => $row->port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings,
            'sniffing' => $row->sniffing);
    }else{
        if($netType != "grpc"){
            if($security == 'tls') {
                $tcpSettings = '{
        	  "network": "tcp",
        	  "security": "'.$security.'",
        	  "tlsSettings": '.$tlsSettings.',
        	  "tcpSettings": {
                "header": '.$headers.'
              }
        	}';
                $wsSettings = '{
              "network": "ws",
              "security": "'.$security.'",
        	  "tlsSettings": '.$tlsSettings.',
              "wsSettings": {
                "path": "/",
                "headers": '.$headers.'
              }
            }';
                $settings = '{
              "clients": [
                {
                  "id": "'.$uniqid.'",
                  "alterId": 0
                }
              ],
              "decryption": "none",
        	  "fallbacks": []
            }';
            }elseif($security == 'xtls') {
                $tcpSettings = '{
        	  "network": "tcp",
        	  "security": "'.$security.'",
        	  "' . $xtlsTitle . '": '.$tlsSettings.',
        	  "tcpSettings": {
                "header": '.$headers.'
              }
        	}';
                $wsSettings = '{
              "network": "ws",
              "security": "'.$security.'",
        	  "' . $xtlsTitle .'": '.$tlsSettings.',
              "wsSettings": {
                "path": "/",
                "headers": '.$headers.'
              }
            }';
                $settings = '{
              "clients": [
                {
                  "id": "'.$uniqid.'",
        		  "flow": "xtls-rprx-direct"
                }
              ],
              "decryption": "none",
        	  "fallbacks": []
            }';
            }

            else {
                $tcpSettings = '{
        	  "network": "tcp",
        	  "security": "none",
        	  "tcpSettings": {
        		"header": '.$headers.'
        	  }
        	}';
                $wsSettings = '{
              "network": "ws",
              "security": "none",
              "wsSettings": {
                "path": "/",
                "headers": {}
              }
            }';
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$uniqid.'",
        		  "flow": "xtls-rprx-direct"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }
            $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
        }

        $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $remark,'enable' => 'true',
            'expiryTime' => $row->expiryTime,'listen' => '','port' => $row->port,'protocol' => $protocol,'settings' => $settings,
            'streamSettings' => $streamSettings,
            'sniffing' => $row->sniffing);
    }



    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$panel_url/xui/inbound/update/$iid",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
        // CURLOPT_HTTPHEADER => array(
        //     'Host: '.$phost,
        //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
        //     'Accept:  application/json, text/plain, */*',
        //     'Accept-Language:  en-US,en;q=0.5',
        //     'Accept-Encoding:  gzip, deflate',
        //     'X-Requested-With:  XMLHttpRequest',
        //     $cookie
        // ),
    ));

    $response = curl_exec($curl);
    unlink("tempCookie.txt");

    curl_close($curl);
    return $response = json_decode($response);
}
function getJson($server_id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];

    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$panel_url/xui/inbound/list",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
        // CURLOPT_HTTPHEADER => array(
        //     'Host: '.$phost,
        //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
        //     'Accept:  application/json, text/plain, */*',
        //     'Accept-Language:  en-US,en;q=0.5',
        //     'Accept-Encoding:  gzip, deflate',
        //     'X-Requested-With:  XMLHttpRequest',
        //     $cookie
        // ),
    ));

    $response = curl_exec($curl);
    unlink("tempCookie.txt");

    curl_close($curl);
    return $response = json_decode($response);


}

function addUser($server_id, $client_id, $protocol, $port, $expiryTime, $remark, $volume, $netType, $security = 'none'){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $security = $server_info['security'];
    $tlsSettings = $server_info['tlsSettings'];
    $header_type = $server_info['header_type'];
    $request_header = $server_info['request_header'];
    $response_header = $server_info['response_header'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];
    $xtlsTitle = $serverType == "sanaei"?"XTLSSettings":"xtlsSettings";

    $volume = ($volume == 0) ? 0 : floor($volume * 1073741824);
    $headers = getNewHeaders($netType, $request_header, $response_header, $header_type);

//---------------------------------------Trojan------------------------------------//
    if($protocol == 'trojan'){
        // protocol trojan
        if($security == 'none'){
            $streamSettings = '{
    	  "network": "tcp",
    	  "security": "none",
    	  "tcpSettings": {
    		"header": {
			  "type": "none"
			}
    	  }
    	}';
            $settings = '{
    	  "clients": [
    		{
    		  "id": "'.$client_id.'",
    		  "flow": "xtls-rprx-direct"
    		}
    	  ],
    	  "decryption": "none",
    	  "fallbacks": []
    	}';
        }
        elseif($security == 'xtls') {
            $streamSettings = '{
        	  "network": "tcp",
        	  "security": "'.$security.'",
        	  "' . $xtlsTitle . '": '.$tlsSettings.',
        	  "tcpSettings": {
                "header": '.$headers.'
              }
        	}';
                $wsSettings = '{
              "network": "ws",
              "security": "'.$security.'",
        	  "' . $xtlsTitle .'": '.$tlsSettings.',
              "wsSettings": {
                "path": "/",
                "headers": '.$headers.'
              }
            }';
                $settings = '{
              "clients": [
                {
                  "id": "'.$uniqid.'",
                  "alterId": 0
                }
              ],
              "decryption": "none",
        	  "fallbacks": []
            }';
        }
        
        else{
            $streamSettings = '{
		  "network": "tcp",
		  "security": "'.$security.'",
		  "'.$security.'Settings": '.$tlsSettings.',
		  "tcpSettings": {
			"header": {
			  "type": "none"
			}
		  }
		}';
            $settings = '{
		  "clients": [
			{
			  "password": "'.$client_id.'",
			  "flow": "xtls-rprx-direct"
			}
		  ],
		  "fallbacks": []
		}';
        }

        // trojan
        $dataArr = array('up' => '0','down' => '0','total' => $volume,'remark' => $remark,'enable' => 'true','expiryTime' => $expiryTime,'listen' => '','port' => $port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings,
            'sniffing' => '{
      "enabled": true,
      "destOverride": [
        "http",
        "tls"
      ]
    }');
    }else {
//-------------------------------------- vmess vless -------------------------------//
        if($security == 'tls') {
            $tcpSettings = '{
    	  "network": "tcp",
    	  "security": "'.$security.'",
    	  "tlsSettings": '.$tlsSettings.',
    	  "tcpSettings": {
            "header": '.$headers.'
          }
    	}';
            $wsSettings = '{
          "network": "ws",
          "security": "'.$security.'",
    	  "tlsSettings": '.$tlsSettings.',
          "wsSettings": {
            "path": "/",
            "headers": '.$headers.'
          }
        }';
            $settings = '{
          "clients": [
            {
              "id": "'.$client_id.'",
              "alterId": 0
            }
          ],
          "disableInsecureEncryption": false
        }';
        }elseif($security == 'xtls') {
            $tcpSettings = '{
    	  "network": "tcp",
    	  "security": "'.$security.'",
    	  "' . $xtlsTitle .'": '.$tlsSettings.',
    	  "tcpSettings": {
            "header": '.$headers.'
          }
    	}';
            $wsSettings = '{
          "network": "ws",
          "security": "'.$security.'",
    	  "' . $xtlsTitle .'": '.$tlsSettings.',
          "wsSettings": {
            "path": "/",
            "headers": '.$headers.'
          }
        }';
            $settings = '{
          "clients": [
            {
              "id": "'.$client_id.'",
              "alterId": 0
            }
          ],
          "disableInsecureEncryption": false
        }';
        }else {
            $tcpSettings = '{
    	  "network": "tcp",
    	  "security": "none",
    	  "tcpSettings": {
    		"header": '.$headers.'
    	  }
    	}';
            $wsSettings = '{
          "network": "ws",
          "security": "none",
          "wsSettings": {
            "path": "/",
            "headers": '.$headers.'
          }
        }';
            $settings = '{
    	  "clients": [
    		{
    		  "id": "'.$client_id.'",
    		  "flow": "xtls-rprx-direct"
    		}
    	  ],
    	  "decryption": "none",
    	  "fallbacks": []
    	}';
        }

        
        
		if($protocol == 'vless'){
			$settings = '{
			  "clients": [
				{
				  "id": "'.$client_id.'",
				  "flow": "xtls-rprx-direct"
				}
			  ],
			  "decryption": "none",
			  "fallbacks": []
			}';
		}

        $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
		if($netType == 'grpc'){
			if($security == 'tls') {
				$streamSettings = '{
  "network": "grpc",
  "security": "tls",
  "tlsSettings": {
    "serverName": "",
    "certificates": [
      {
        "certificateFile": "/root/cert.crt",
        "keyFile": "/root/private.key"
      }
    ],
    "alpn": []
  },
  "grpcSettings": {
    "serviceName": ""
  }
}';
		}else{
			$streamSettings = '{
  "network": "grpc",
  "security": "none",
  "grpcSettings": {
    "serviceName": ""
  }
}';
		}
	}

        // vmess - vless
        $dataArr = array('up' => '0','down' => '0','total' => $volume, 'remark' => $remark,'enable' => 'true','expiryTime' => $expiryTime,'listen' => '','port' => $port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings
        ,'sniffing' => '{
	  "enabled": true,
	  "destOverride": [
		"http",
		"tls"
	  ]
	}');
    }

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($curl),true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    // $cookie = file_get_contents("tempCookie.txt");
    // preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
    // $cookie = $CookieInfo[1];
    // $cookie = 'Cookie: session='.$cookie;
    // unlink("tempCookie.txt");

    curl_setopt_array($curl, array(
        CURLOPT_URL => "$panel_url/xui/inbound/add",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15, 
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEJAR => dirname(__FILE__) . '/tempCookie.txt',
        // CURLOPT_HTTPHEADER => array(
        //     'Host: '.$phost,
        //     'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
        //     'Accept:  application/json, text/plain, */*',
        //     'Accept-Language:  en-US,en;q=0.5',
        //     'Accept-Encoding:  gzip, deflate',
        //     'X-Requested-With:  XMLHttpRequest',
        //     $cookie
        // ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    unlink("tempCookie.txt");

    return json_decode($response);
}

?>
