<?php
include_once '../baseInfo.php';
include_once '../config.php';

$sellState=$botState['sellState']=="off"?"خاموش ❌":"روشن ✅";
$searchState=$botState['searchState']=="off"?"خاموش ❌":"روشن ✅";
$rewaredTime = ($botState['rewaredTime']??0);
$rewaredChannel = $botState['rewardChannel'];

if($rewaredTime>0 && $rewaredChannel != null){
    $lastTime = $botState['lastRewardMessage']??0;
    if(time() > $lastTime){
        $time = time() - ($rewaredTime * 60 * 60);
        
        $stmt = $connection->prepare("SELECT SUM(price) as total FROM `pays` WHERE `request_date` > ? AND (`state` = 'paid' OR `state` = 'approved')");
        $stmt->bind_param("i", $time);
        $stmt->execute();
        $totalRewards = number_format($stmt->get_result()->fetch_assoc()['total']);
        $stmt->close();
        
        $botState['lastRewardMessage']=time() + ($rewaredTime * 60 * 60);
        
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

        $txt = "⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
🔰درآمد من در $rewaredTime ساعت گذشته

💰مبلغ : $totalRewards تومان

☑️ $channelLock

";
        sendMessage($txt, null, null, $rewaredChannel);
    }
}    

if($botState['cartToCartAutoAcceptState']=="on"){
    $date = strtotime("-" . ($botState['cartToCartAutoAcceptTime']??10) . " minutes");
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `state` = 'have_sent' AND `request_date` <= ?");
    $stmt->bind_param('i', $date);
    $stmt->execute();
    $info = $stmt->get_result();
    $stmt->close();

    while($payInfo = $info->fetch_assoc()){
        $time = time();
        $rowId = $payInfo['id'];
        $price = $payInfo['price'];
        $user_id = $payInfo['user_id'];
        $payType = $payInfo['type'];
        $deviceId = $payInfo['device_id'];
        
        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $userinfo = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        
        if($userinfo['is_agent'] == 1 && ($botState['cartToCartAutoAcceptType']??2) == 1) continue;
        elseif($userinfo['is_agent'] != 1 && ($botState['cartToCartAutoAcceptType']??2) == 0) continue;
        
        $agentBought = $payInfo['agent_bought'];
        
        $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'paid' WHERE `id` =?");
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $stmt->close();
        
        
        if($payType == "INCREASE_WALLET"){
            $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
            $stmt->bind_param("ii", $price, $user_id);
            $stmt->execute();
            $stmt->close();
            
            sendMessage("افزایش حساب شما با موفقیت تأیید شد\n✅ مبلغ " . number_format($price). " تومان به حساب شما اضافه شد", null, null, $user_id);
        }
        elseif($payType == "BUY_SUB"){
            $fid = $payInfo['plan_id']; 
            $volume = $payInfo['volume'];
            $days = $payInfo['day'];
            $description = $payInfo['description'];
            
            
            $acctxt = '';
            
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $file_detail = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if($volume == 0 && $days == 0){
                $volume = $file_detail['volume'];
                $days = $file_detail['days'];
            }
            
            $date = time();
            $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
            $expire_date = $date + (86400 * $days);
            $type = $file_detail['type'];
            $protocol = $file_detail['protocol'];
            $price = $payInfo['price'];   
            
            $server_id = $file_detail['server_id'];
            $netType = $file_detail['type'];
            $acount = $file_detail['acount'];
            $inbound_id = $file_detail['inbound_id'];
            $limitip = $file_detail['limitip'];
            $rahgozar = $file_detail['rahgozar'];
            $customPath = $file_detail['custom_path'];
            $customPort = $file_detail['custom_port'];
            $customSni = $file_detail['custom_sni'];
            
            $accountCount = $payInfo['agent_count']!=0?$payInfo['agent_count']:1;
            $eachPrice = $price / $accountCount;
            if($acount == 0 and $inbound_id != 0){
                sendMessage('پرداخت شما انجام شد ولی ظرفیت این کانکشن پر شده است، مبلغ ' . number_format($price) . " تومان به کیف پول شما اضافه شد", null,null, $user_id);
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();
                
                sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id توسط درگاه اضافه شد میخواست کانفیگ بخره، ظرفیت پر بود",null,null,$admin);                

                exit;
            }
            if($inbound_id == 0) {
                $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
                $stmt->bind_param("i", $server_id);
                $stmt->execute();
                $server_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            
                if($server_info['ucount'] <= 0) {
                    sendMessage('پرداخت شما انجام شد ولی ظرفیت این سرور پر شده است، مبلغ ' . number_format($price) . " تومان به کیف پول شما اضافه شد", null,null, $user_id);
                    
                    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                    $stmt->bind_param("ii", $price, $user_id);
                    $stmt->execute();
                    $stmt->close();

                    sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id توسط درگاه اضافه شد میخواست کانفیگ بخره، ظرفیت پر بود",null,null,$admin);                
                    exit;
                }
            }
        
            $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $serverInfo = $stmt->get_result()->fetch_assoc();
            $serverTitle = $serverInfo['title'];
            $srv_remark = $serverInfo['remark'];
            $stmt->close();
        
            $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $serverConfig = $stmt->get_result()->fetch_assoc();
            $serverType = $serverConfig['type'];
            $portType = $serverConfig['port_type'];
            $panelUrl = $serverConfig['panel_url'];
            $stmt->close();
            include '../phpqrcode/qrlib.php';
        
            define('IMAGE_WIDTH',540);
            define('IMAGE_HEIGHT',540);
            for($i = 1; $i <= $accountCount; $i++){
                $uniqid = generateRandomString(42,$protocol);
                
                $savedinfo = file_get_contents('temp.txt');
                $savedinfo = explode('-',$savedinfo);
                $port = $savedinfo[0] + 1;
                $last_num = $savedinfo[1] + 1;
                
                if($botState['remark'] == "digits"){
                    $rnd = rand(10000,99999);
                    $remark = "{$srv_remark}-{$rnd}";
                }
                elseif($botState['remark'] == "manual"){
                    $remark = $payInfo['description'];
                }
                else{
                    $rnd = rand(1111,99999);
                    $remark = "{$srv_remark}-{$user_id}-{$rnd}";
                }
                if(!empty($description)) $remark = $description;
                if($portType == "auto"){
                    file_put_contents('temp.txt',$port.'-'.$last_num);
                }else{
                    $port = rand(1111,65000);
                }
                
                if($inbound_id == 0){    
                    if($serverType == "marzban"){
                        $response = addMarzbanUser($server_id, $remark, $volume, $days, $fid);
                        if(!$response->success){
                            if($response->msg == "User already exists"){
                                $remark .= rand(1111,99999);
                                $response = addMarzbanUser($server_id, $remark, $volume, $days, $fid);
                            }
                        }
                    }else{
                        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid); 
                        if(!$response->success){
                            if(strstr($response->msg, "Duplicate email")) $remark .= RandomString();
                            elseif(strstr($response->msg, "Port already exists")) $port = rand(1111,65000);
                            
                            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
                        } 
                    }
                }else {
                    $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid); 
                    if(!$response->success){
                        if(strstr($response->msg, "Duplicate email")) $remark .= RandomString();
        
                        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
                    } 
                }
                
                if(is_null($response)){
                    sendMessage('پرداخت شما با موفقیت انجام شد ولی گلم ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...مبلغ ' . number_format($price) ." به کیف پولت اضافه شد",null,null, $user_id);
                    
                    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                    $stmt->bind_param("ii", $price, $user_id);
                    $stmt->execute();
                    $stmt->close();

                    sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id توسط درگاه اضافه شد میخواست کانفیگ بخره، اتصال به سرور برقرار نبود",null,null,$admin);                
                    exit;
                }
                if($response == "inbound not Found"){
                    sendMessage("پرداخت شما با موفقیت انجام شد ولی ❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...مبلغ " . number_format($price) . " به کیف پول شما اضافه شد",null,null,$user_id);
            
                    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                    $stmt->bind_param("ii", $price, $user_id);
                    $stmt->execute();
                    $stmt->close();
                    
                    sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id توسط درگاه اضافه شد میخواست کانفیگ بخره، ولی انباند پیدا نشد",null,null,$admin);                
                	exit;
                }
                if(!$response->success){
                    sendMessage('پرداخت شما با موفقیت انجام شد ولی خطا داد لطفا سریع به مدیر بگو ... مبلغ '. number_format($price) . " تومان به کیف پولت اضافه شد",null,null,$user_id);
                    sendMessage("خطای سرور {$server_info['title']}:\n\n" . $response->msg, null, null, $admin);
                    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                    $stmt->bind_param("ii", $price, $user_id);
                    $stmt->execute();
                    $stmt->close();
                    sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id توسط درگاه اضافه شد میخواست کانفیگ بخره، ولی خطا داد",null,null,$admin);                
                    exit;
                }
                
                if($serverType == "marzban"){
                    $uniqid = $token = str_replace("/sub/", "", $response->sub_link);
                    $subLink = $botState['subLinkState'] == "on"?$panelUrl . $response->sub_link:"";
                    $vraylink = [$subLink];
                    $vray_link = json_encode($response->vray_links);
                }else{
                    $token = RandomString(30);
                    $subLink = $botState['subLinkState']=="on"?$botUrl . "settings/subLink.php?token=" . $token:"";
            
                    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar, $customPath, $customPort, $customSni);
                    $vray_link = json_encode($vraylink);
                }
                foreach($vraylink as $link){
                $acc_text = "
                
        😍 سفارش جدید شما
        📡 پروتکل: $protocol
        🔮 نام سرویس: $remark
        🔋حجم سرویس: $volume گیگ
        ⏰ مدت سرویس: $days روز⁮⁮ ⁮⁮
        " . ($botState['configLinkState'] != "off" && $serverType != "marzban"?"
        💝 config : <code>$link</code>":"");
        
        if($botState['subLinkState'] == "on") $acc_text .= "
        
        🔋 Volume web: <code> $botUrl"."search.php?id=".$uniqid."</code>
        
        
        🌐 subscription : <code>$subLink</code>
                
                ";
                      
                    $file = RandomString() .".png";
                    $ecc = 'L';
                    $pixel_Size = 11;
                    $frame_Size = 0;
                    
                    QRcode::png($link, $file, $ecc, $pixel_Size, $frame_Size);
                	addBorderImage($file);
                	
                	$backgroundImage = imagecreatefromjpeg("QRCode.jpg");
                    $qrImage = imagecreatefrompng($file);
                    
                    $qrSize = array('width' => imagesx($qrImage), 'height' => imagesy($qrImage));
                    imagecopy($backgroundImage, $qrImage, 300, 300 , 0, 0, $qrSize['width'], $qrSize['height']);
                    imagepng($backgroundImage, $file);
                    imagedestroy($backgroundImage);
                    imagedestroy($qrImage);
        
                	$res = sendPhoto($botUrl . "/settings/" . $file, $acc_text,json_encode(['inline_keyboard'=>[[['text'=>$buttonValues['back_to_main'],'callback_data'=>"mainMenu"]]]]),"HTML", $user_id);
                    unlink($file);
                }
                
                $agentBought = $payInfo['agent_bought'];
                
                $stmt = $connection->prepare("INSERT INTO `orders_list` 
                    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `uuid`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`, `agent_bought`)
                    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?, ?);");
                $stmt->bind_param("ssiiisssisiiii", $user_id, $token, $fid, $server_id, $inbound_id, $remark, $uniqid, $protocol, $expire_date, $vray_link, $eachPrice, $date, $rahgozar, $agentBought);
                $stmt->execute();
                $order = $stmt->get_result(); 
                $stmt->close();
            }
            
            if($userInfo['refered_by'] != null){
                $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
                $stmt->execute();
                $inviteAmount = $stmt->get_result()->fetch_assoc()['value']??0;
                $stmt->close();
                $inviterId = $userInfo['refered_by'];
                
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $inviteAmount, $inviterId);
                $stmt->execute();
                $stmt->close();
                 
                sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید",null,null,$inviterId);
            }
                
            if($inbound_id == 0) {
                $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - ? WHERE `id`=?");
                $stmt->bind_param("ii", $accountCount, $server_id);
                $stmt->execute();
                $stmt->close();
            }else{
                $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - ? WHERE id=?");
                $stmt->bind_param("ii", $accountCount, $fid);
                $stmt->execute();
                $stmt->close();
            }
        }
        elseif($payType == "RENEW_ACCOUNT"){
            $oid = $payInfo['plan_id'];
            $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
            $stmt->bind_param("i", $oid);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $fid = $order['fileid'];
            $remark = $order['remark'];
            $uuid = $order['uuid']??"0";
            $server_id = $order['server_id'];
            $inbound_id = $order['inbound_id'];
            $expire_date = $order['expire_date'];
            $expire_date = ($expire_date > $time) ? $expire_date : $time;
            
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ? AND `active` = 1");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $respd = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $name = $respd['title'];
            $days = $respd['days'];
            $volume = $respd['volume'];
            $price = $payInfo['price'];
            
            $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $server_info = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $serverType = $server_info['type'];
        
            if($serverType == "marzban"){
                $response = editMarzbanConfig($server_id, ['remark'=>$remark, 'days'=>$days, 'volume' => $volume]);
            }else{
                if($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $uuid, $volume, $days, "renew");
                else
                    $response = editInboundTraffic($server_id, $uuid, $volume, $days, "renew");
            }
            
            if(is_null($response)){
        		sendMessage('پرداخت شما با موفقیت انجام شد ولی مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید، مبلغ ' . number_format($price) . " تومان به کیف پول شما اضافه شد",null,null,$user_id);
        		
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();

                sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id اضافه شد، میخواست کانفیگش رو تمدید کنه، ولی اتصال به سرور برقرار نبود",null,null,$admin);
            	exit;
            }
            $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
            $newExpire = $time + $days * 86400;
            $stmt->bind_param("ii", $newExpire, $oid);
            $stmt->execute();
            $stmt->close();
            $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("iiisii", $user_id, $server_id, $inbound_id, $remark, $price, $time);
            $stmt->execute();
            $stmt->close();
        
            sendMessage("✅سرویس $remark با موفقیت تمدید شد",getMainKeys(), null, $user_id);
        }
        elseif(preg_match('/^INCREASE_DAY_(\d+)_(\d+)/',$payType, $increaseInfo)){
            $orderId = $increaseInfo[1];
            
            $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $orderInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $server_id = $orderInfo['server_id'];
            $inbound_id = $orderInfo['inbound_id'];
            $remark = $orderInfo['remark'];
            $uuid = $orderInfo['uuid']??"0";
            
            $planid = $increaseInfo[2];
        
            
            
            $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
            $stmt->bind_param("i", $planid);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $price = $payInfo['price'];
            $volume = $res['volume'];
        
            $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $server_info = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $serverType = $server_info['type'];
        
            if($serverType == "marzban"){
                $response = editMarzbanConfig($server_id, ['remark'=>$remark, 'plus_day'=>$volume]);
            }else{
                if($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $uuid, 0, $volume);
                else
                    $response = editInboundTraffic($server_id, $uuid, 0, $volume);
            }
            
            if($response->success){
                $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ?, `notif` = 0 WHERE `uuid` = ?");
                $newVolume = $volume * 86400;
                $stmt->bind_param("is", $newVolume, $uuid);
                $stmt->execute();
                $stmt->close();
                
                $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
                $newVolume = $volume * 86400;
                $stmt->bind_param("iiisii", $user_id, $server_id, $inbound_id, $remark, $price, $time);
                $stmt->execute();
                $stmt->close();
                
                sendMessage("✅$volume روز به مدت زمان سرویس شما اضافه شد",getMainKeys(), null, $user_id);
            }else {
                sendMessage("پرداخت شما با موفقیت انجام شد ولی به دلیل مشکل فنی امکان افزایش حجم نیست. لطفا به مدیریت اطلاع بدید یا 5دقیقه دیگر دوباره تست کنید مبلغ " . number_format($price) . " تومان به کیف پول شما اضافه شد", $user_id);
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();
    
                sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id اضافه شد، میخواست زمان سرویسشو افزایش بده",null,null,$admin);
            }
        }
        elseif(preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)/',$payType, $increaseInfo)){
            $orderId = $increaseInfo[1];
            
            $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $orderInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $server_id = $orderInfo['server_id'];
            $inbound_id = $orderInfo['inbound_id'];
            $remark = $orderInfo['remark'];
            $uuid = $orderInfo['uuid']??"0";
            
            $planid = $increaseInfo[2];
            
            $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
            $stmt->bind_param("i", $planid);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $price = $payInfo['price'];
            $volume = $res['volume'];
            
                $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
                $stmt->bind_param("i", $server_id);
                $stmt->execute();
                $server_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $serverType = $server_info['type'];
            
                if($serverType == "marzban"){
                    $response = editMarzbanConfig($server_id, ['remark'=>$remark, 'plus_volume'=>$volume]);
                }else{
                    if($inbound_id > 0)
                        $response = editClientTraffic($server_id, $inbound_id, $uuid, $volume, 0);
                    else
                        $response = editInboundTraffic($server_id, $uuid, $volume, 0);
                }
                
            if($response->success){
                $stmt = $connection->prepare("UPDATE `orders_list` SET `notif` = 0 WHERE `uuid` = ?");
                $stmt->bind_param("s", $uuid);
                $stmt->execute();
                $stmt->close();
                sendMessage( "✅$volume گیگ به حجم سرویس شما اضافه شد",getMainKeys(), null, $user_id);
            }else {
                sendMessage("پرداخت شما با موفقیت انجام شد ولی مشکل فنی در ارتباط با سرور. لطفا سلامت سرور را بررسی کنید مبلغ " . number_format($price) . " تومان به کیف پول شما اضافه شد",null,null,$user_id);
                
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();

                sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $user_id اضافه شد، میخواست حجم کانفیگشو افزایش بده",null,null,$admin);                
            }
        }
        elseif($payType == "RENEW_SCONFIG"){
            $user_id = $user_id;
            $fid = $payInfo['plan_id']; 
        
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $file_detail = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $volume = $file_detail['volume'];
            $days = $file_detail['days'];
            
            $price = $payInfo['price'];   
            $server_id = $file_detail['server_id'];
            $configInfo = json_decode($payInfo['description'],true);
            $remark = $configInfo['remark'];
            $uuid = $configInfo['uuid'];
            $isMarzban = $configInfo['marzban'];
            
            $remark = $payInfo['description'];
            $inbound_id = $payInfo['volume']; 
            
            if($isMarzban){
                $response = editMarzbanConfig($server_id, ['remark'=>$remark, 'days'=>$days, 'volume' => $volume]);
            }else{
                if($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $uuid, $volume, $days, "renew");
                else
                    $response = editInboundTraffic($server_id, $uuid, $volume, $days, "renew");
            }
            
        	if(is_null($response)){
        		sendMessage('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید',null,null,$user_Id);
        		exit;
        	}
        	$stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
        	$stmt->bind_param("iiisii", $user_id, $server_id, $inbound_id, $remark, $price, $time);
        	$stmt->execute();
        	$stmt->close();

            sendMessage("✅سرویس $remark با موفقیت تمدید شد",null,null,$user_id);
        }
        

        editKeys(json_encode(['inline_keyboard'=>[[['text'=>"خودکار تأیید شد",'callback_data'=>"wizwizch"]]]]), $payInfo['message_id'], $payInfo['chat_id']);
    }
}
