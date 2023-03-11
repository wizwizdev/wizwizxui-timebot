<?php
require 'config.php';
require 'core.php';

// ------------------ { Start Source } ------------------ //
// ------------------ { Panel Admin } ------------------ //
if (in_array($from_id, $Config['admin'])) {
    if (preg_match('/^\/(start)$/i', $text) or $text == "🔽 میخوام به عقب برگردم 🔽") {
        sendMessage($chat_id,"سلام ادمین عزیز به پنل مدیریت خوشومدی 😏 هرچی میخوای درخدمتم ",null,getAdminKeys());
        
        setUser('step','none');
    }
    elseif(preg_match('/^replyTo(\d+)/',$data,$match)){
        sendMessage($chat_id,"لطفا پیامتو ارسال کن");
        setUser('step',$data);
    }
    elseif(preg_match('/^replyTo(\d+)/',$user['step'],$match)){
        sendMessage($chat_id,"پیام شما با موفقیت به کاربر ارسال شد",$message_id);
        sendMessage($match[1],$text,null,json_encode(['inline_keyboard'=>[
            [['text'=>"پاسخ",'callback_data'=>"sendMessageToAdmin"]]
            ]]));
        setUser('step',null);
    }
    elseif(preg_match('/^وضعیت ربات:/',$text)){
        $botState = $botState=="false"?"true":"false";
        file_put_contents("botState.txt", $botState);
        sendMessage($chat_id,"وضعیت ربات با موفقیت تغییر کرد",null,getAdminKeys());
    }
    elseif($text=="آمار ربات"){
        $stmt = $connection->prepare("SELECT * FROM `user`");
        $stmt->execute();
        $allUsers = $stmt->get_result()->num_rows;
        $stmt->close();
        
        $stmt = $connection->prepare("SELECT `user_id` FROM `loged_info` GROUP BY `user_id`");
        $stmt->execute();
        $logedUsers = $stmt->get_result()->num_rows;
        $stmt->close();
        
        $notLogedUsers = $allUsers - $logedUsers;
        sendMessage($chat_id,"آمار ربات شما", null, json_encode(['inline_keyboard'=>[
            [
                ['text'=>$allUsers??"0", 'callback_data'=>"shoaib_ryan"],
                ['text'=>"تعداد کاربران", 'callback_data'=>"shoaib_ryan"]
            ],
            [
                ['text'=>$logedUsers??"0", 'callback_data'=>"shoaib_ryan"],
                ['text'=>"وارد شده به حساب", 'callback_data'=>"shoaib_ryan"]
            ],
            [
                ['text'=>$notLogedUsers??"0", 'callback_data'=>"shoaib_ryan"],
                ['text'=>"وارده نشده به حساب", 'callback_data'=>"shoaib_ryan"]
            ]
            ]]));
    }
    elseif($text=="لیست سرور ها" || $data== "serversList"){
        if(isset($data)){
            editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
        }else{
            sendMessage($chat_id,"سرور های ثبت شده",null,getServersList());
        }
    }
    elseif($data=="addNewServer"){
        file_put_contents("$from_id.txt",$message_id);
        sendMessage($chat_id,"لطفا آدرس سرور را وارد کنید");
        setUser('step','setServerIp');
    }
    elseif($user['step']=="setServerIp"){
        $stmt = $connection->prepare("SELECT * FROM `servers` WHERE `server_ip` = ?");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $checkExist = $stmt->get_result();
        $stmt->close();
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
            $stmt = $connection->prepare("INSERT INTO `servers` (`server_ip`, `user_name`, `password`) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $serverIp, $userName, $text);
            $stmt->execute();
            $stmt->close();
            sendMessage($chat_id,"سرور جدید با موفقیت ذخیره شد");
            $msgId = file_get_contents("$from_id.txt");
            wait();
            delMessage($chat_id,($msgId + 1) . "-" . ($message_id+1));
            setUser('step','none');
            editText($chat_id,$msgId,"سرور های ثبت شده",getServersList());
            unlink("$from_id.txt");
        }else{
            sendMessage($chat_id,"
بیسواد ، اشتباه وارد کردی 😂
            ");
            $msgId = file_get_contents("$from_id.txt");
            wait();
            delMessage($chat_id,($msgId + 1) . "-" . ($message_id+1));
            setUser('step','none');
            unlink("$from_id.txt");
        }
    }
    elseif(preg_match('/^delServer_(.*)/',$data,$match)){
        $stmt = $connection->prepare("DELETE FROM `servers` WHERE `id` =?");
        $stmt->bind_param("i", $match[1]);
        $stmt->execute();
        $stmt->close();
        alert($callid,"با موفقیت حذف شد");
        editText($chat_id,$message_id,"سرور های ثبت شده",getServersList());
    }
}
elseif($tc=="private"){
    if($botState == "false"){
        sendMessage($chat_id,"ربات در حال بروزرسانی است، لطفا بعدا تلاش کنید");
        exit();
    }
    $isJoined = isJoined();
    if($data=="joined"){
        if($isJoined != null){
            alert($callid,"هنوز عضو کانال نشدید، لطفا عضو کانال شده و رو کلید  لمس کنید",true);
            exit();
        }
        delMessage($chat_id,$message_id);
        $text = '/start';
        setUser('step','none');
    }
    if($isJoined != null){
        sendMessage($chat_id,"لطفا در کانال های زیر عضو شده و روی کلید عضو شدم لمس کنید",null,$isJoined);
        exit();
    }
    
    if (preg_match('/^\/(start)$/i', $text) or $text == "🔙") {
        sendMessage($chat_id,"سلاااام عزیز دل ، یکی از دکمه هارو انتخاب کن 🤗",null,getUserKeys());
        setUser('step','none');
    }
    elseif($text=="🔽 میخوام به عقب برگردم 🔽"){
        sendMessage($chat_id,"خب به منوی اصلی برگشتیم ، چیزی لازم داری بگو 🫡",null,getUserKeys());
        setUser('step','none');

    }
    elseif($text=="💮 Qr Code 💮"){
        sendMessage($chat_id,"لطفا لینک سرورتو بزن که QrCode بهت بدم 😌",null,$backButton);
        setUser('step','SendQrCode');
    }
    elseif($user['step'] == "SendQrCode"){
        require_once('phpqrcode/qrlib.php');
        QRcode::png($text, "$from_id.png", QR_ECLEVEL_L, 4);
        Bot('sendPhoto',[
            'chat_id'=>$chat_id,
            'photo'=>new CURLFILE(realpath("$from_id.png")),
            'reply_to_message_id'=>$message_id,
            'reply_markup'=>getUserKeys()
            ]);
        unlink("$from_id.png");
        setUser('step','none');
    }
    elseif($text == "🕯 ورود به حساب 🕯" && $user['uuid'] == null && $user['step']=="none"){
        sendMessage($chat_id,"کلید شناسه تو اینجا بزن بعدش وارد حسابت میشی 😁",null,$backButton);
        setUser('step','setUserUUID');
    }
    elseif($text=="➕ حساب جدید" && $loginCount->num_rows >0){
        sendMessage($chat_id,"کلید شناسه ( uuid ) یا لینک سرورت رو اینجا بزن برات یه حساب جدیدت اضافه کنم 🫠",null,$backButton);
        setUser('step','setUserUUID');
    }
    elseif($user['step']=="setUserUUID"){
        if(preg_match('/^vmess:\/\/(.*)/',$text,$match)){
            $jsonDecode = json_decode(base64_decode($match[1]),true);
            $text = $jsonDecode['id'];
        }elseif(preg_match('/^vless:\/\/(.*?)\@/',$text,$match)){
            $text = $match[1];
            
        }elseif(preg_match('/^trojan:\/\/(.*?)\@/',$text,$match)){
            $text = $match[1];
            
        }
        $stmt = $connection->prepare("SELECT * FROM `loged_info` WHERE `uuid` = ? AND `user_id` = ?");
        $stmt->bind_param("si", $text, $from_id);
        $stmt->execute();
        $checkExist = $stmt->get_result();
        $stmt->close();
        
        if($checkExist->num_rows>0){
            sendMessage($chat_id,"این اکانت از قبل تو حسابت هستاا!",null,getUserKeys());
            setUser('step','none');
            exit();
        }
        sendMessage($chat_id,"گلم لطفا یکم منتظر بمون ...");
        $stmt = $connection->prepare("SELECT * FROM `servers`");
        $stmt->execute();
        $serversList = $stmt->get_result();
        $stmt->close();
        $found = false;
        while($row = $serversList->fetch_assoc()){
            $serverIp = $row['server_ip'];
            $serverName = $row['user_name'];
            $serverPass = $row['password'];
            
            $response = getJson($serverIp, $serverName, $serverPass, $from_id);
            
            if($response['success']){
                
                $list = json_encode($response['obj']);
                
                if(strpos($list, $text)){

                    $list = $response['obj'];
                    if(!isset($list[0]['clientStats'])){
                        foreach($list as $keys=>$packageInfo){
                        	if(strpos($packageInfo['settings'], $text)!=false){
                        	    $remark = $packageInfo['remark'];
                        	    break;
                        	}
                        }
                    }
                    else{
                        $keys = -1;
                        $settings = array_column($list,'settings');
                        foreach($settings as $key => $value){
                        	if(strpos($value, $text)!= false){
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
                        $settingKey = array_search($text,$settingsId);
                        
                        $email = $clientsSettings[$settingKey]['email'];
    
                        $clientState = $list[$keys]['clientStats'];
                        $emails = array_column($clientState,'email');
                        $emailKey = array_search($email,$emails);
    
                        if($clientState[$emailKey]['total'] != 0 || $clientState[$emailKey]['up'] != 0  ||  $clientState[$emailKey]['down'] != 0 || $clientState[$emailKey]['expiryTime'] != 0){
                            $remark = $email;
                        }
                        elseif($list[$keys]['total'] != 0 || $list[$keys]['up'] != 0  ||  $list[$keys]['down'] != 0 || $list[$keys]['expiryTime'] != 0){
                            $remark = $list[$keys]['remark'];
                        }
                    }

                    $insertRow = $connection->prepare("INSERT INTO `loged_info` (`user_id`, `remark`, `uuid`, `sub_server`) VALUES (?, ?, ? ,?)");
                    $insertRow->bind_param("isss",$from_id, $remark, $text, $serverIp);
                    $insertRow->execute();
                    $insertRow->close();
                    
                    if($loginCount->num_rows==0){
                        $txt = "خیلی خوشومدی عزیزم چیزی میخوای؟ بگو !";
                    }else{
                        $txt = "🙃 یه حساب جدید برات باز کردم ";
                    }
                    $stmt = $connection->prepare("SELECT * FROM `loged_info` WHERE `user_id` = ?");
                    $stmt->bind_param("i", $from_id);
                    $stmt->execute();
                    $loginCount = $stmt->get_result();
                    $stmt->close();
                    
                    sendMessage($chat_id,$txt,null,getUserKeys());
                    $found = true;
                    break;
                }

            }
        }
        if(!$found){
            sendMessage($chat_id,"ای وای ، اطلاعاتت اشتباهه 😔",null,getUserKeys());
        }
        setUser('step','none');
    }
    elseif($text=="🔓 خروج از حساب 🔓" && $loginCount->num_rows >0){
        $keys = array();
        while($row = $loginCount->fetch_assoc()){
            $keys[] = [
                ['text'=>$row['remark'],'callback_data'=>"logout" . $row['id']]];
        }
        $keys = json_encode(['inline_keyboard'=>$keys]);
        $txt = "یکی از حساب هات رو انتخاب کن 🙃";
        if(isset($data)){
            editText($chat_id,$message_id,$txt,$keys);
        }else{
            sendMessage($chat_id,$txt,null,$keys);
        }
    }
    elseif(preg_match('/^logout(\d+)/',$data,$match)){
        $delete = $connection->prepare("DELETE FROM `loged_info` WHERE `id` = ?");
        $delete->bind_param("i", $match[1]);
        $delete->execute();
        $delete->close();
        
        delMessage($chat_id,$message_id);  
        $stmt = $connection->prepare("SELECT * FROM `loged_info` WHERE `user_id` = ?");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $loginCount = $stmt->get_result();
        $stmt->close();
        sendMessage($chat_id,"مارو دور ننداز ، ما انقدارم به درد نخور نیستیم 🥺",null,getUserKeys());
    }
    elseif(($data == 'backToAccounts' || $text=="🪬 حساب من 🪬") &&  $loginCount->num_rows >0){
        $keys = array();
        while($row = $loginCount->fetch_assoc()){
            $keys[] = [
                ['text'=>$row['remark'],'callback_data'=>"showAccount" . $row['id']]];
        }
        $keys = json_encode(['inline_keyboard'=>$keys]);
        $txt = "یکی از حساب هات رو انتخاب کن 🙃";
        if(isset($data)){
            editText($chat_id,$message_id,$txt,$keys);
        }else{
            sendMessage($chat_id,$txt,null,$keys);
        }
    }
    elseif(preg_match('/^showAccount(.*)/',$data,$match)){
        alert($callid,"گلم لطفا یکم منتظر بمون ...");
        $stmt = $connection->prepare("SELECT * FROM `loged_info` WHERE `id`  = ?");
        $stmt->bind_param("i", $match[1]);
        $stmt->execute();
        $accinfo = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $stmt = $connection->prepare("SELECT * FROM `servers` WHERE `server_ip` = ?");
        $stmt->bind_param("s", $accinfo['sub_server']);
        $stmt->execute();
        $serversList = $stmt->get_result();
        $stmt->close();
        
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
                	if(strpos($packageInfo['settings'], $accinfo['uuid'])!=false){
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
                	if(strpos($value, $accinfo['uuid'])!= false){
                		$keys = $key;
                		break;
                	}
                }
                $clientsSettings = json_decode($list[$keys]['settings'],true)['clients'];
                if(!is_array($clientsSettings)){
                    alert($callid,"با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                    exit();
                }
                $settingsId = array_column($clientsSettings,'id');
                $settingKey = array_search($accinfo['uuid'],$settingsId);
                
                $email = $clientsSettings[$settingKey]['email'];

                $clientState = $list[$keys]['clientStats'];
                $emails = array_column($clientState,'email');
                $emailKey = array_search($email,$emails);

                if($clientState[$emailKey]['total'] != 0 || $clientState[$emailKey]['up'] != 0  ||  $clientState[$emailKey]['down'] != 0 || $clientState[$emailKey]['expiryTime'] != 0){
                    $upload = sumerize($clientState[$emailKey]['up']);
                    $download = sumerize($clientState[$emailKey]['down']);
                    $leftMb = $clientState[$emailKey]['total']!=0?($clientState[$emailKey]['total'] - $clientState[$emailKey]['up'] - $clientState[$emailKey]['down']):"نامحدود";
                    if(is_numeric($leftMb)){
                        if($leftMb<0){
                            $leftMb = 0;
                        }else{
                            $leftMb = sumerize($clientState[$emailKey]['total'] - $clientState[$emailKey]['up'] - $clientState[$emailKey]['down']);
                        }
                    }
                    $totalUsed = sumerize($clientState[$emailKey]['up'] + $clientState[$emailKey]['down']);
                    $total = $clientState[$emailKey]['total']!=0?sumerize($clientState[$emailKey]['total']):"نامحدود";
                    $expiryTime = $clientState[$emailKey]['expiryTime'] != 0?date("Y-m-d H:i:s",substr($clientState[$emailKey]['expiryTime'],0,-3)):"نامحدود";
                    $expiryDay = $clientState[$emailKey]['expiryTime'] != 0?
                        floor(
                            ((substr($clientState[$emailKey]['expiryTime'],0,-3)-time())/(60 * 60 * 24))
                            ):
                            "نامحدود";
                    if(is_numeric($expiryDay)){
                        if($expiryDay<0) $expiryDay = 0;
                    }
                    $state = $clientState[$emailKey]['enable'] == true?"فعال 🟢":"غیر فعال 🔴";
                    $remark = $email;
                }
                elseif($list[$keys]['total'] != 0 || $list[$keys]['up'] != 0  ||  $list[$keys]['down'] != 0 || $list[$keys]['expiryTime'] != 0){
                    $upload = sumerize($list[$keys]['up']);
                    $download = sumerize($list[$keys]['down']);
                    $leftMb = $list[$keys]['total']!=0?($list[$keys]['total'] - $list[$keys]['up'] - $list[$keys]['down']):"نامحدود";
                    if(is_numeric($leftMb)){
                        if($leftMb<0){
                            $leftMb = 0;
                        }else{
                            $leftMb = sumerize($list[$keys]['total'] - $list[$keys]['up'] - $list[$keys]['down']);
                        }
                    }
                    $totalUsed = sumerize($list[$keys]['up'] + $list[$keys]['down']);
                    $total = $list[$keys]['total']!=0?sumerize($list[$keys]['total']):"نامحدود";
                    $expiryTime = $list[$keys]['expiryTime'] != 0?date("Y-m-d H:i:s",substr($list[$keys]['expiryTime'],0,-3)):"نامحدود";
                    $expiryDay = $list[$keys]['expiryTime'] != 0?
                        floor(
                            ((substr($list[$keys]['expiryTime'],0,-3)-time())/(60 * 60 * 24))
                            ):
                            "نامحدود";
                    if(is_numeric($expiryDay)){
                        if($expiryDay<0) $expiryDay = 0;
                    }
                    $state = $list[$keys]['enable'] == true?"فعال 🟢":"غیر فعال 🔴";
                    $remark = $list[$keys]['remark'];
                }
            }
            
            
            $keys = json_encode(['inline_keyboard'=>[
                [
                    ['text'=>$remark??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"👦 اسم اکانت",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$state??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"📡 وضعیت حساب",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$upload?? " ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"📥 آپلود",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$download??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"📤 دانلود",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$total??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"🔋حجم کلی",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$leftMb??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"⏳ حجم باقیمانده",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$expiryTime??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"📆 تاریخ اتمام",'callback_data'=>"shoaib_ryan"],
                    ],
                [
                    ['text'=>$expiryDay??" ",'callback_data'=>"shoaib_ryan"],
                    ['text'=>"🧭 تعداد روز باقیمانده",'callback_data'=>"shoaib_ryan"],
                    ],
                [['text'=>"🔑 کلید ورود شما ( بزن کپی شه ) 👇",'callback_data'=>"shoaib_ryan"]],
                [['text'=>$accinfo['uuid']??" ",'callback_data'=>"copy" . $accinfo['uuid']]],
                [['text'=>"برگشت",'callback_data'=>"backToAccounts"]]
                ]]);
            editText($chat_id,$message_id,"🔰مشخصات حسابت:",$keys,"MarkDown");
        }
    }
    elseif($text=="📞 پشتیبانی" && $loginCount->num_rows>0){
        sendMessage($chat_id,"چه مشکلی برات پیش اومده؟ هر مشکلی داری بفرس کمکت کنم",null,$backButton);
        setUser('step','sendMessagetoAdmin');
    }
    elseif($data=="sendMessageToAdmin"){
        Bot('editMessageReplyMarkup',['chat_id'=>$chat_id,'message_id'=>$message_id]);
        sendMessage($chat_id,"چه مشکلی برات پیش اومده؟ هر مشکلی داری بفرس کمکت کنم",null,$backButton);
        setUser('step','sendMessagetoAdmin');
    }
    elseif($user['step'] == 'sendMessagetoAdmin'){
        sendMessage($Config['admin'][0], "پیام جدید از طرف:\n\n"
                ."اسم کاربر: $first_name\n".
                "یوزرنیم کاربر: @$username\n".
                "آیدی عددی: $from_id\n\n".
                $text,null,json_encode(['inline_keyboard'=>[
                    [['text'=>"پاسخ", 'callback_data'=>"replyTo" . $from_id]]
                    ]]));
        sendMessage($chat_id,"ممنونم از پیامت ، پیامتو بررسی کنم چشم جواب میدم", null, getUserKeys());
        setUser('step','none');
    }
    elseif(preg_match('/copy(.*)/',$data,$match)){
        sendMessage($chat_id,"`" . $match[1] . "`",null,null,"MarkDown");
    }
}

//-----------------------------//
unlink("error_log");
?>
