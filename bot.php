<?php
require 'config.php';
require 'core.php';

// ------------------ { Start Source } ------------------ //
// ------------------ { Panel Admin } ------------------ //
if (in_array($from_id, $Config['admin'])) {
    if (preg_match('/^\/(start)$/i', $text) or $text == "🔽 میخوام به عقب برگردم 🔽") {
        sendMessage($chat_id,"سلام ادمین عزیز به پنل مدیریت خوشومدی 😏 هرچی میخوای درخدمتم ",null,$adminMainKey);
        
        setUser('step','none');
    }
    elseif($text=="لیست سرور ها" || $data== "serversList"){
        if(isset($data)){
            editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
        }else{
            sendMessage($chat_id,"سرور های ثبت شده",null,getServersList());
        }
    }
    elseif(preg_match('/^editServerType_(\d+)/',$data,$match)){
        $keys = json_encode(['inline_keyboard'=>[
            [
                ['text'=>"کانفیگ تکی",'callback_data'=>"serverTypeTogether_" . $match[1]],
                ['text'=>"کانفیگ جدا",'callback_data'=>"serverTypeSeperate_" . $match[1]]
            ],
            [['text'=>"برگشت",'callback_data'=>"serversList"]]
            ]]);
        editText($chat_id,$message_id,"لطفا نوعیت سرور مورد نظر را انتخاب کنید",$keys);
    }
    elseif(preg_match('/^serverType(?<type>\w+)_(?<serverId>\d+)/',$data,$match)){
        if($match['type'] == "Together"){
            $connection->query("UPDATE `servers` SET `type` = 'together' WHERE `id` = '{$match['serverId']}'");
            alert($callid,"با موفقیت ذخیره شد");
            editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
        }else{
            $connection->query("UPDATE `servers` SET `type` = 'seperate' WHERE `id` = '{$match['serverId']}'");
            alert($callid,"با موفقیت ذخیره شد");
            editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
        }
    }
    elseif($data=="addNewServer"){
        file_put_contents("$from_id.txt",$message_id);
        sendMessage($chat_id,"لطفا آدرس سرور را وارد کنید");
        setUser('step','setServerIp');
    }
    elseif($user['step']=="setServerIp"){
        $checkExist = $connection->query("SELECT * FROM `servers` WHERE `server_ip` = '$text'");
        if(mysqli_num_rows($checkExist)>0){
            sendMessage($chat_id,"این آدرس از قبل ثبت است");
        }else{
            sendMessage($chat_id,"لطفا نام کاربری سرور را وارد کنید");
            setUser('step',"setServerUser_$text");
        }
    }
    elseif(preg_match('/^setServerUser_(.*)/',$user['step'],$match)){
        $serverIp = $match[1];
        sendMessage($chat_id,"لطفا رمز کاربری سرور را وارد کنید");
        setUser('step',"setServerPass_/_{$serverIp}_/_{$text}");
    }
    elseif(preg_match('/^setServerPass_\/_/',$user['step'])){
        $param = explode("_/_",$user['step']);
        $serverIp =$param[1];
        $userName = $param[2];
        
        
        $response = getJson($serverIp, $userName, $text, $from_id);
        if($response['success']){
            $connection->query("INSERT INTO `servers` (`server_ip`, `user_name`, `password`) VALUES ('$serverIp', '$userName', '$text')");
            sendMessage($chat_id,"سرور جدید با موفقیت ذخیره شد");
            $msgId = file_get_contents("$from_id.txt");
            wait();
            delMessage($chat_id,($msgId + 1) . "-" . ($message_id+1));
            setUser('step','none');
            editText($chat_id,$msgId,"سرور های ثبت شده",getServersList());
            unlink("$from_id.txt");
        }else{
            sendMessage($chat_id,"ای وای ، اطلاعاتت اشتباهه 😔");
            $msgId = file_get_contents("$from_id.txt");
            wait();
            delMessage($chat_id,($msgId + 1) . "-" . ($message_id+1));
            setUser('step','none');
            unlink("$from_id.txt");
        }
    }
    elseif(preg_match('/^delServer_(.*)/',$data,$match)){
        $connection->query("DELETE FROM `servers` WHERE `id` = {$match[1]}");
        alert($callid,"با موفقیت حذف شد");
        editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
    }
}
elseif($tc=="private"){
    if (preg_match('/^\/(start)$/i', $text) or $text == "🔙") {
        if($user['uuid'] == null){
            sendMessage($chat_id,"عزیزم اگه میخوای از ربات استفاده کنی رو ( ورود به حساب ) کلیک کن 🫠",null,$loginKeys);
        }else{
            sendMessage($chat_id,"سلاااام عزیز دل ، یکی از دکمه هارو انتخاب کن 🤗",null,$userKeys);
        }
        setUser('step','none');
    }
    elseif($text=="🔽 میخوام به عقب برگردم 🔽"){
        if($user['uuid'] != null){
            sendMessage($chat_id,"خب به منوی اصلی برگشتیم ، چیزی لازم داری بگو 🫡",null,$userKeys);
        }else{
            sendMessage($chat_id,"خب به منوی اصلی برگشتیم ، چیزی لازم داری بگو 🫡",null,$loginKeys);
        }
        setUser('step','none');

    }
    elseif($text=="💮 Qr Code 💮"){
        sendMessage($chat_id,"لطفا کلید شناسه تو بزن که QrCode بهت بدم 😌",null,$backButton);
        setUser('step','SendQrCode');
    }
    elseif($user['step'] == "SendQrCode"){
        require_once('phpqrcode/qrlib.php');
        QRcode::png($text, "$from_id.png", QR_ECLEVEL_L, 4);
        if($user['uuid'] != null){
            $keys = $userKeys;
        }else{
            $keys = $loginKeys;
        }
        Bot('sendPhoto',[
            'chat_id'=>$chat_id,
            'photo'=>new CURLFILE(realpath("$from_id.png")),
            'reply_to_message_id'=>$message_id,
            'reply_markup'=>$keys
            ]);
        unlink("$from_id.png");
        setUser('step','none');
    }
    elseif($text == "🕯 ورود به حساب 🕯" && $user['uuid'] == null && $user['step']=="none"){
        sendMessage($chat_id,"کلید شناسه تو اینجا بزن بعدش وارد حسابت میشی 😁",null,$backButton);
        setUser('step','setUserUUID');
    }
    elseif($user['step']=="setUserUUID"){
        sendMessage($chat_id,"گلم لطفا یکم منتظر بمون ...");
        if(preg_match('/^vmess:\/\/(.*)/',$text,$match)){
            $jsonDecode = json_decode(base64_decode($match[1]),true);
            $text = $jsonDecode['id'];
        }
        $serversList = $connection->query("SELECT * FROM `servers`");
        $found = false;
        while($row = $serversList->fetch_assoc()){
            $serverIp = $row['server_ip'];
            $serverName = $row['user_name'];
            $serverPass = $row['password'];
            
            $response = getJson($serverIp, $serverName, $serverPass, $from_id);
            
            if($response['success']){
                
                $list = json_encode($response['obj']);
                
                
                if(strpos($list, $text)){
                    $connection->query("UPDATE `user` SET `uuid` = '$text', `step` = 'none', `sub_server` = '$serverIp' WHERE `id` = '$from_id'");

                    sendMessage($chat_id,"خیلی خوشومدی عزیزم چیزی میخوای؟ بگو !",null,$userKeys);
                    $found = true;
                    break;
                }

            }
        }
        if(!$found){
            sendMessage($chat_id,"ای وای ، اطلاعاتت اشتباهه 😔",null,$loginKeys);
            setUser('step','none');
        }
    }
    elseif($text=="🔓 خروج از حساب 🔓" && $user['uuid'] != null){
        $connection->query("UPDATE `user` SET `uuid` = NULL, `step` = 'none', `warned` = NULL, `sub_server` = NULL WHERE `id` = '$from_id'");
        sendMessage($chat_id,"مارو دور ننداز ، ما انقدارم به درد نخور نیستیم 🥺",null,$loginKeys);
    }
    elseif($text=="🪬 حساب من 🪬"){
        sendMessage($chat_id,"گلم لطفا یکم منتظر بمون ...");
        if($user['uuid'] != null){
            $serversList = $connection->query("SELECT * FROM `servers` WHERE `server_ip` = '{$user['sub_server']}'");
            $row = $serversList->fetch_assoc();
            $serverIp = $row['server_ip'];
            $serverName = $row['user_name'];
            $serverPass = $row['password'];
            $serverType = $row['type'];
    
            $response = getJson($serverIp, $serverName, $serverPass, $from_id);
            if($response['success']){
                $list = $response['obj'];
                
                if(!isset($list[0]['clientStats'])){
                    foreach($list as $keys=>$packageInfo){
                    	if(strpos($packageInfo['settings'], $user['uuid'])!=false){
                    	    $remark = $packageInfo['remark'];
                            $upload = sumerize($packageInfo['up']);
                            $download = sumerize($packageInfo['down']);
                            $state = $packageInfo['enable'] == true?"فعال 🟢":"غیر فعال 🔴";
                            $totalUsed = sumerize($packageInfo['up'] + $packageInfo['down']);
                            $total = $packageInfo['total']!=0?sumerize($packageInfo['total']):"نامحدود";
                            $expiryTime = $packageInfo['expiryTime'] != 0?date("Y-m-d H:i:s",substr($packageInfo['expiryTime'],0,-3)):"نامحدود";
                            $leftMb = $packageInfo['total']!=0?sumerize($packageInfo['total'] - $packageInfo['up'] - $packageInfo['down']):"نامحدود";
                            $expiryDay = $packageInfo['expiryTime'] != 0?
                                round(
                                    (substr($packageInfo['expiryTime'],0,-3)-time())/(60 * 60 * 24)
                                    ,2):
                                    "نامحدود";
                    	}
                    }
                }else{
                    $keys = -1;
                    $settings = array_column($list,'settings');
                    foreach($settings as $key => $value){
                    	if(strpos($value, $user['uuid'])!= false){
                    		$keys = $key;
                    		break;
                    	}
                    }
                    $clientsSettings = json_decode($list[$keys]['settings'],true)['clients'];
                    if(!is_array($clientsSettings)){
                        sendMessage($chat_id,"با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                        exit();
                    }
                    $settingsId = array_column($clientsSettings,'id');
                    $settingKey = array_search($user['uuid'],$settingsId);
                    
                    $email = $clientsSettings[$settingKey]['email'];

                    $clientState = $list[$keys]['clientStats'];
                    $emails = array_column($clientState,'email');
                    $emailKey = array_search($email,$emails);

                    if($clientState[$emailKey]['total'] != 0 || $clientState[$emailKey]['up'] != 0  ||  $clientState[$emailKey]['down'] != 0 || $clientState[$emailKey]['expiryTime'] != 0){
                        $upload = sumerize($clientState[$emailKey]['up']);
                        $download = sumerize($clientState[$emailKey]['down']);
                        $leftMb = $clientState[$emailKey]['total']!=0?sumerize($clientState[$emailKey]['total'] - $clientState[$emailKey]['up'] - $clientState[$emailKey]['down']):"نامحدود";
                        $totalUsed = sumerize($clientState[$emailKey]['up'] + $clientState[$emailKey]['down']);
                        $total = $clientState[$emailKey]['total']!=0?sumerize($clientState[$emailKey]['total']):"نامحدود";
                        $expiryTime = $clientState[$emailKey]['expiryTime'] != 0?date("Y-m-d H:i:s",substr($clientState[$emailKey]['expiryTime'],0,-3)):"نامحدود";
                        $expiryDay = $clientState[$emailKey]['expiryTime'] != 0?
                            round(
                                (substr($clientState[$emailKey]['expiryTime'],0,-3)-time())/(60 * 60 * 24)
                                ,2):
                                "نامحدود";
                        $state = $clientState[$emailKey]['enable'] == true?"فعال 🟢":"غیر فعال 🔴";
                        $remark = $email;
                    }
                    elseif($list[$keys]['total'] != 0 || $list[$keys]['up'] != 0  ||  $list[$keys]['down'] != 0 || $list[$keys]['expiryTime'] != 0){
                        $upload = sumerize($list[$keys]['up']);
                        $download = sumerize($list[$keys]['down']);
                        $leftMb = $list[$keys]['total']!=0?sumerize($list[$keys]['total'] - $list[$keys]['up'] - $list[$keys]['down']):"نامحدود";
                        $totalUsed = sumerize($list[$keys]['up'] + $list[$keys]['down']);
                        $total = $list[$keys]['total']!=0?sumerize($list[$keys]['total']):"نامحدود";
                        $expiryTime = $list[$keys]['expiryTime'] != 0?date("Y-m-d H:i:s",substr($list[$keys]['expiryTime'],0,-3)):"نامحدود";
                        $expiryDay = $list[$keys]['expiryTime'] != 0?
                            round(
                                (substr($list[$keys]['expiryTime'],0,-3)-time())/(60 * 60 * 24)
                                ,2):
                                "نامحدود";
                        $state = $list[$keys]['enable'] == true?"فعال 🟢":"غیر فعال 🔴";
                        $remark = $list[$keys]['remark'];
                    }
                }
                
                $subLeft = "*خب اینم از مشخصاتت!*\n\n".
                            "*▫️وضعیت حساب : $state*\n\n".
                            "💞 اسم عزیز دلم: \n".
                            "$remark\n".
                            "🔋حجم کلی: $total\n".
                            "📥 دانلود: $download\n".
                            "📤 آپلود: $upload\n".
                            "🔅استفاده کلی: $totalUsed\n".
                            "🤨 حجم باقیمانده: $leftMb\n".
                            "🏞 تعداد روز باقیمانده : $expiryDay روز \n".
                            "🧭 تاریخ ختم: $expiryTime\n\n".
                            "🔑 کلید ورود شما: \n".
                            "`" . $user['uuid'] . "`";
    
                sendMessage($chat_id,$subLeft,null,null,"MarkDown");
            }
        }else{
            sendMessage($chat_id,"اطلاعات شما ناقص است، لطفا مجددا به حساب کاربری خود وارد شوید",null,$loginKeys);
            $connection->query("UPDATE `user` SET `uuid` = NULL, `step` = 'none', `warned` = NULL, `sub_server` = NULL WHERE `id` = '$from_id'");
        }
    }
}

//-----------------------------//
unlink("error_log");
?>
