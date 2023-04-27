<?php
include_once 'baseInfo.php';
include_once 'config.php';
include_once 'settings/jdf.php';
$robotState = $botState['botState']??"on";

if($userInfo['step'] == "banned"){
    sendMessage("❌ | هی بهت گفتم آدم باش گوش نکردی ، الان مسدود شدی 😑😂");
    exit();
}
if ($joniedState== "kicked" || $joniedState== "left"){
    sendMessage("
❌ برای استفاده از ربات حتما باید در کانال زیر عضو شوید:

🆔 $channelLock

✅ بعد از اینکه عضو شدید مجدد ربات رو /start کنید و لذت ببرید

🌀 ( Support us 💕 )
", null,"HTML");
    exit;
}

if($robotState == "off" && $from_id != $admin){
    sendMessage("🌛ربات در حال بروزرسانی می باشد ...");
    exit();
}

if (preg_match('/^\/([Ss]tart)/', $text) or $text == '⤵️ برگرد به منوی اصلی ' or $text == '🔙بازگشت به منوی اصلی' or $data == 'mainMenu') {

    setUser();

    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $count = $stmt->get_result()->num_rows;
    $stmt->close();
    
    if ($count == 0) {
        $refcode = time();
        $sql = "INSERT INTO `users` VALUES (NULL,?,?,?,?, 0,?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issii", $from_id, $first_name, $username, $refcode, $time);
        $stmt->execute();
        $stmt->close();
    }
    if(isset($data) and $data == "mainMenu"){
        $res = editText($message_id, 'سلااام به ربات ویزویز خوش اومدی 🫡🌸

🚪 /start
', $mainKeys);
        if(!$res->ok){
            sendMessage('سلااام به ربات ویزویز خوش اومدی 🫡🌸', $mainKeys);
        }
    }else{
        if($from_id != $admin && !isset($userInfo['first_start'])){
            setUser('sent','first_start');
            $keys = json_encode(['inline_keyboard'=>[
            [['text'=>"✉️ ارسال پیام به کاربر ",'callback_data'=>'sendMessageToUser' . $from_id]]
            ]]);
            sendMessage("
            📢 | یه گل جدید عضو ربات شد :

نام و نام خانوادگی: <a href='tg://user?id=$from_id'>$first_name</a>
نام کاربری: @$username

به نظرم یه پیام براش بفرست مثلا ( تبلیغی یا خوش آمد گویی ) 😍

            ",
            $keys, "html",$admin);
        }
        sendMessage('سلااام به ربات ویزویز خوش اومدی 🫡🌸

🚪 /start
',$mainKeys);
    }
}
if(preg_match('/^sendMessageToUser(\d+)/',$data,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    delMessage();
    sendMessage('❗️لطفا پیامت رو بفرست',$cancelKey);
    setUser($data);
}
if(preg_match('/^sendMessageToUser(\d+)/',$userInfo['step'],$match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    sendMessage($text,null,null,$match[1]);
    sendMessage("پیامت به کاربر ارسال شد",$removeKeyboard);
    sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$adminKeys);
    setUser();
}
if($data=='botReports' && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `users`");
    $stmt->execute();
    $allUsers = $stmt->get_result()->num_rows;
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `orders_list`");
    $stmt->execute();
    $allOrders = $stmt->get_result()->num_rows;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT * FROM `server_config`");
    $stmt->execute();
    $allServers = $stmt->get_result()->num_rows;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT * FROM `server_categories`");
    $stmt->execute();
    $allCategories = $stmt->get_result()->num_rows;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans`");
    $stmt->execute();
    $allPlans = $stmt->get_result()->num_rows;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT SUM(amount) as total FROM `orders_list`");
    $stmt->execute();
    $totalRewards = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
    $stmt->close();
    
    
    $keys = json_encode(['inline_keyboard'=>[
        [
            ['text'=>$allUsers,'callback_data'=>'wizwizch'],
            ['text'=>"تعداد کل کاربران",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$allOrders,'callback_data'=>'wizwizch'],
            ['text'=>"کل محصولات خریداری شده",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$allServers,'callback_data'=>'wizwizch'],
            ['text'=>"تعداد سرورها",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$allCategories,'callback_data'=>'wizwizch'],
            ['text'=>"تعداد دسته ها",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$allPlans,'callback_data'=>'wizwizch'],
            ['text'=>"تعداد پلن ها",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$totalRewards,'callback_data'=>'wizwizch'],
            ['text'=>"درآمد کل",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>"برگشت به مدیریت",'callback_data'=>'managePanel']
            ]
        ]]);
    editText($message_id, "آمار ربات در این لحظه",$keys);
}
if($data=="adminsList" && $from_id === $admin){
    $keys = array();
    foreach($usersInfo as $key => $user){
        if($user['isAdmin'] == true){
            $keys[] = [['text'=>"❌",'callback_data'=>"delAdmin" . $key],['text'=>$key, "callback_data"=>"wizwizch"]];
        }
    }
    $keys[] = [['text'=>"➕ افزودن ادمین",'callback_data'=>"addNewAdmin"]];
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]];
    $keys = json_encode(['inline_keyboard'=>$keys]);
    editText($message_id, "لیست ادمین ها",$keys);
}
if(preg_match('/^delAdmin(\d+)/',$data,$match) && $from_id === $admin){
    $usersInfo[$match[1]]['isAdmin'] = false;
    file_put_contents("userInfo.json",json_encode($usersInfo));
    $usersInfo = json_decode(file_get_contents("userInfo.json"),true);
    $keys = array();
    foreach($usersInfo as $key => $user){
        if($user['isAdmin'] == true){
            $keys[] = [['text'=>"❌",'callback_data'=>"delAdmin" . $key],['text'=>$key, "callback_data"=>"wizwizch"]];
        }
    }
    alert("کاربر مورد نظر از لیست ادمین ها حذف شد"); 
    $keys[] = [['text'=>"➕ افزودن ادمین",'callback_data'=>"addNewAdmin"]];
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]];
    $keys = json_encode(['inline_keyboard'=>$keys]);
    editText($message_id, "لیست ادمین ها",$keys);

}
if($data=="addNewAdmin" && $from_id === $admin){
    delMessage();
    sendMessage("🧑‍💻| کسی که میخوای ادمین کنی رو آیدی عددیشو بفرست ببینم:",$cancelKey);
    setUser($data);
}
if($userInfo['step'] == "addNewAdmin" && $from_id === $admin && $text != $cancelText){
    if(is_numeric($text)){
        $usersInfo[$text]['isAdmin'] = true;
        file_put_contents("userInfo.json",json_encode($usersInfo));
        
        sendMessage("✅ | 🥳 خب کاربر الان ادمین شد تبریک میگم",$removeKeyboard);
        setUser();
        
        $usersInfo = json_decode(file_get_contents("userInfo.json"),true);
        $keys = array();
        foreach($usersInfo as $key => $user){
            if($user['isAdmin'] == true){
                $keys[] = [['text'=>"❌",'callback_data'=>"delAdmin" . $key],['text'=>$key, "callback_data"=>"wizwizch"]];
            }
        }
        $keys[] = [['text'=>"➕ افزودن ادمین",'callback_data'=>"addNewAdmin"]];
        $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]];
        $keys = json_encode(['inline_keyboard'=>$keys]);
        sendMessage("لیست ادمین ها",$keys);

    }else{
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if(($data=="botSettings" or preg_match("/^changeBot(\w+)/",$data,$match)) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $botState = json_decode(file_get_contents("settings/botstate.json"),true);
    if($data!="botSettings"){
        $newValue = $botState[$match[1]]=="off"?"on":"off";
        $botState[$match[1]]= $newValue;
        file_put_contents("settings/botstate.json",json_encode($botState));
    }
    
    $cartToCartState=$botState['cartToCartState']=="off"?"خاموش ❌":"روشن ✅";
    $walletState=$botState['walletState']=="off"?"خاموش ❌":"روشن ✅";
    $sellState=$botState['sellState']=="off"?"خاموش ❌":"روشن ✅";
    $robotState=$botState['botState']=="off"?"خاموش ❌":"روشن ✅";
    $searchState=$botState['searchState']=="off"?"خاموش ❌":"روشن ✅";
    $rewaredTime = ($botState['rewaredTime']??0) . " ساعت";
    $rewaredChannel = $botState['rewardChannel']??" ";
    $nowPaymentWallet=$botState['nowPaymentWallet']=="off"?"خاموش ❌":"روشن ✅";
    $zarinpal=$botState['zarinpal']=="off"?"خاموش ❌":"روشن ✅";
    $keys=json_encode(['inline_keyboard'=>[
        [
            ['text'=>$sellState,'callback_data'=>"changeBotsellState"],
            ['text'=>"فروش",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$robotState,'callback_data'=>"changeBotbotState"],
            ['text'=>"وضعیت ربات",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$searchState,'callback_data'=>"changeBotsearchState"],
            ['text'=>"مشخصات کانفیگ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$zarinpal,'callback_data'=>"changeBotzarinpal"],
            ['text'=>"درگاه زرین پال",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$nowPaymentWallet,'callback_data'=>"changeBotnowPaymentWallet"],
            ['text'=>"درگاه NowPayment",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$cartToCartState,'callback_data'=>"changeBotcartToCartState"],
            ['text'=>"کارت به کارت",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$walletState,'callback_data'=>"changeBotwalletState"],
            ['text'=>"کیف پول",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$rewaredTime,'callback_data'=>'editRewardTime'],
            ['text'=>"ارسال گزارش درآمد", 'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$rewaredChannel,'callback_data'=>'editRewardChannel'],
            ['text'=>"کانال گزارش درآمد",'callback_data'=>'wizwizch']
            ],
        [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]]
        ]]);
    editText($message_id,'🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !',$keys);
}
if($data=="editRewardTime" && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    delMessage();
    sendMessage("❗️لطفا زمان تأخیر در ارسال گزارش رو به ساعت وارد کن\n\nنکته: هر n ساعت گزارش به ربات ارسال میشه! ",$cancelKey);
    setUser($data);
}
if($userInfo['step'] == "editRewardTime" && $from_id  == $admin && $text != $cancelText){
    if(!is_numeric($text)){
        sendMessage("❗️لطفا عدد بفرستید");
        exit();
    }
    elseif($text <0 ){
        sendMessage("مقدار وارد شده معتبر نیست");
        exit();
    }
    $botState = json_decode(file_get_contents("settings/botstate.json"),true);
    $botState['rewaredTime'] = $text;
    file_put_contents("settings/botstate.json",json_encode($botState));
    if($data!="botSettings"){
        $newValue = $botState[$match[1]]=="off"?"on":"off";
        $botState[$match[1]]= $newValue;
        file_put_contents("settings/botstate.json",json_encode($botState));
    }
    
$cartToCartState=$botState['cartToCartState']=="off"?"خاموش ❌":"روشن ✅";
    $walletState=$botState['walletState']=="off"?"خاموش ❌":"روشن ✅";
    $sellState=$botState['sellState']=="off"?"خاموش ❌":"روشن ✅";
    $robotState=$botState['botState']=="off"?"خاموش ❌":"روشن ✅";
    $searchState=$botState['searchState']=="off"?"خاموش ❌":"روشن ✅";
    $rewaredTime = ($botState['rewaredTime']??0) . " ساعت";
    $rewaredChannel = $botState['rewardChannel']??" ";
    $nowPaymentWallet=$botState['nowPaymentWallet']=="off"?"خاموش ❌":"روشن ✅";
    $zarinpal=$botState['zarinpal']=="off"?"خاموش ❌":"روشن ✅";
    $keys=json_encode(['inline_keyboard'=>[
        [
            ['text'=>$sellState,'callback_data'=>"changeBotsellState"],
            ['text'=>"فروش",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$robotState,'callback_data'=>"changeBotbotState"],
            ['text'=>"وضعیت ربات",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$searchState,'callback_data'=>"changeBotsearchState"],
            ['text'=>"مشخصات کانفیگ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$zarinpal,'callback_data'=>"changeBotzarinpal"],
            ['text'=>"درگاه زرین پال",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$nowPaymentWallet,'callback_data'=>"changeBotnowPaymentWallet"],
            ['text'=>"درگاه NowPayment",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$cartToCartState,'callback_data'=>"changeBotcartToCartState"],
            ['text'=>"کارت به کارت",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$walletState,'callback_data'=>"changeBotwalletState"],
            ['text'=>"کیف پول",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$rewaredTime,'callback_data'=>'editRewardTime'],
            ['text'=>"ارسال گزارش درآمد", 'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$rewaredChannel,'callback_data'=>'editRewardChannel'],
            ['text'=>"کانال گزارش درآمد",'callback_data'=>'wizwizch']
            ],
        [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]]
        ]]);
    sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !',$keys);
    setUser();
    exit();
}
if($data=="myInfo"){
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $totalBuys = $stmt->get_result()->num_rows;
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $myWallet = number_format($stmt->get_result()->fetch_assoc()['wallet']) . " تومان";
    $stmt->close();
    $keys = json_encode(['inline_keyboard'=>[
        [
            ['text'=>"شارژ کیف پول 💰",'callback_data'=>"increaseMyWallet"]],
        [
            ['text'=>"برگشت 🔙",'callback_data'=>"mainMenu"]
            ]
        ]]);
    editText($message_id, "♦️ اطلاعات حساب شما:\n\nآیدی عددی: <code>$from_id</code>\nیوزرنیم: @$username".
            "\nاسم: $first_name\nتعداد خریدهای شما: $totalBuys\nموجودی کیف پول شما: $myWallet \n ➖➖➖ \n 🆔 $channelLock",
            $keys,"html");
}
if($data=="increaseMyWallet"){
    delMessage();
    sendMessage("🙂 عزیزم مقدار شارژ مورد نظر خود را به تومان وارد کن (بیشتر از 5000 تومان)",$cancelKey);
    setUser($data);
}
if($userInfo['step'] == "increaseMyWallet" && $text != $cancelText){
    if(!is_numeric($text)){
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    }
    elseif($text < 5000){
        sendMessage("❗️لطفا مقداری بیشتر از 5000 وارد کن");
        exit();
    }
    
    
    $hash_id = RandomString();
    $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` = 'INCREASE_WALLET' AND `state` = 'pending'");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $stmt->close();
    
    $time = time();
    $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                VALUES (?, ?, 'INCREASE_WALLET', '0', '0', '0', ?, ?, 'pending')");
    $stmt->bind_param("siii", $hash_id, $from_id, $text, $time);
    $stmt->execute();
    $stmt->close();

    
    sendMessage("🪄 لطفا صبور باشید ...",$removeKeyboard);
    $keyboard = array();
    $temp = array();
    if($botState['cartToCartState'] == "on"){
	    $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "increaseWalletWithCartToCart" . $text];
    }
    if($botState['nowPaymentWallet'] == "on"){
	    $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if(count($temp) == 2){
        array_push($keyboard, $temp);
        $temp = array();
    }
    if($botState['zarinpal'] == "on"){
	    $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if(count($temp) > 0){
        array_push($keyboard, $temp);
        $temp = array();
    }
    $keyboard[] = [['text'=>$cancelText, 'callback_data'=> "mainMenu"]];

    
	$keys = json_encode(['inline_keyboard'=>$keyboard]);
    sendMessage("اطلاعات شارژ:\nمبلغ ". number_format($text) . " تومان\n\nلطفا روش پرداخت را انتخاب کنید\n\n ❗️نکته مهم: برای درگاه NowPayment مبلغ شارژ باید بالای 3.5 دلار باشد",$keys);
    setUser();
}
if(preg_match('/increaseWalletWithCartToCart/',$data)) {
    delMessage();  
    setUser($data);
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 $walletwizwiz

✅ بعد از اینکه پرداختت تایید شد مبلغ مورد نظر به کیف پولت اضافه میشه!",$cancelKey, "HTML");
    exit;
}
if(preg_match('/increaseWalletWithCartToCart(\d+)/',$userInfo['step'], $match) and $text != $cancelText){
    $fid = $match[1];
    setUser();
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $uid = $res['userid'];
    $name = $res['name'];
    $username = $res['username'];

    $infoc = strlen($text) > 1 ? $text : "$caption <a href='$fileurl'>&#8194;نمایش فیش</a>";
    $msg = "
🥇 سفارش شما با موفقیت ثبت شد.
بعد از تایید به کیف پولت اضافه میکنم ... 💞
";
        sendMessage($msg,$removeKeyboard);
        sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$mainKeys);
$price = number_format($match[1]);
    $msg = "
🛍 افزایش موجودی
💰مبلغ: $price تومان
🧑‍💻 نام و نام خانوادگی : $name
🎯 یوزرنیم : @$username
🎫 کد کاربری : $from_id
";
    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approvePayment{$uid}_{$match[1]}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decPayment{$uid}_{$match[1]}"]
            ]
        ]
    ]);
    if(isset($update->message->photo)){
        sendPhoto($fileid, $msg,$keyboard, "HTML", $admin);
    }else{
        $msg .= "\n\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard,"HTML",$admin);
    }
}
if(preg_match('/^approvePayment(\d+)_(\d+)/',$data,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $match[2], $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("افزایش حساب شما با موفقیت تأیید شد\n✅ مبلغ " . number_format($match[2]). " تومان به حساب شما اضافه شد",null,null,$match[1]);
    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $message_id,
		'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => '✅', 'callback_data' => "dontsendanymore"]]],
        ])
    ]);
}
if(preg_match('/^decPayment(\d+)_(\d+)/',$data,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    sendMessage("❗️لطفا دلیل عدم تأیید افزایش موجودی را وارد کنید",$cancelKey);
    setUser($data . "_" . $message_id);
}
if(preg_match('/^decPayment(\d+)_(\d+)_(\d+)/',$userInfo['step'],$match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    sendMessage("💔 افزایش موجودی شما به مبلغ "  . number_format($match[2]) . " به دلیل زیر رد شد\n\n$text",null,null,$match[1]);
    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $match[3],
		'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => '❌', 'callback_data' => "dontsendanymore"]]],
        ])
    ]);
    setUser();
    sendMessage('پیامت رو براش ارسال کردم ... 🤝',$removeKeyboard);
    sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
}
if($data=="increaseUserWallet" && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    delMessage();
    sendMessage("🀄️| آیدی عددی کاربر رو بفرس :",$cancelKey);
    setUser($data);
}
if($userInfo['step'] == "increaseUserWallet" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    if(is_numeric($text)){
        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $userCount = $stmt->get_result()->num_rows;
        $stmt->close();
        if($userCount > 0){
            setUser("increaseWalletUser" . $text);
            sendMessage("💸 | مبلغی که میخوای بهش بدی رو وارد کن:");
        }
        else{
            setUser();
            sendMessage("🥴 | همچین کسی رو نداریما اشتباه وارد کردی به نظرم ", $removeKeyboard);
            sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
        }
    }else{
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if(preg_match('/^increaseWalletUser(\d+)/',$userInfo['step'], $match) && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    if(is_numeric($text)){
        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $text, $match[1]);
        $stmt->execute();
        $stmt->close();
    
        sendMessage("✅ مبلغ " . number_format($text). " تومان به حساب شما اضافه شد",null,null,$match[1]);
        sendMessage("✅ مبلغ " . number_format($text) . " تومان به کیف پول کاربر مورد نظر اضافه شد",$removeKeyboard);
        sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
        setUser();
    }else{
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if($data=="editRewardChannel" && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    delMessage();
    sendMessage("❗️لطفا ربات رو در کانال ادمین کن و آیدی کانال رو با @ بفرست:",$cancelKey);
    setUser($data);
}
if($userInfo['step'] == "editRewardChannel" && $from_id  == $admin && $text != $cancelText){
    $botId = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getme"))->result->id;
    $result = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getChatMember?chat_id=$text&user_id=$botId"));
    if($result->ok){
        if($result->result->status == "administrator"){
            $botState = json_decode(file_get_contents("settings/botstate.json"),true);
            $botState['rewardChannel'] = $text;
            file_put_contents("settings/botstate.json",json_encode($botState));
            if($data!="botSettings"){
                $newValue = $botState[$match[1]]=="off"?"on":"off";
                $botState[$match[1]]= $newValue;
                file_put_contents("settings/botstate.json",json_encode($botState));
            }
            sendMessage("☑️ | 😁 با موفقیت ذخیره شد",$removeKeyboard);
            
            $cartToCartState=$botState['cartToCartState']=="off"?"خاموش ❌":"روشن ✅";
            $walletState=$botState['walletState']=="off"?"خاموش ❌":"روشن ✅";
            $sellState=$botState['sellState']=="off"?"خاموش ❌":"روشن ✅";
            $robotState=$botState['botState']=="off"?"خاموش ❌":"روشن ✅";
            $searchState=$botState['searchState']=="off"?"خاموش ❌":"روشن ✅";
            $rewaredTime = ($botState['rewaredTime']??0) . " ساعت";
            $rewaredChannel = $botState['rewardChannel']??" ";
            $nowPaymentWallet=$botState['nowPaymentWallet']=="off"?"خاموش ❌":"روشن ✅";
            $zarinpal=$botState['zarinpal']=="off"?"خاموش ❌":"روشن ✅";
            $keys=json_encode(['inline_keyboard'=>[
                [
                    ['text'=>$sellState,'callback_data'=>"changeBotsellState"],
                    ['text'=>"فروش",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$robotState,'callback_data'=>"changeBotbotState"],
                    ['text'=>"وضعیت ربات",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$searchState,'callback_data'=>"changeBotsearchState"],
                    ['text'=>"مشخصات کانفیگ",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$zarinpal,'callback_data'=>"changeBotzarinpal"],
                    ['text'=>"درگاه زرین پال",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$nowPaymentWallet,'callback_data'=>"changeBotnowPaymentWallet"],
                    ['text'=>"درگاه NowPayment",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$cartToCartState,'callback_data'=>"changeBotcartToCartState"],
                    ['text'=>"کارت به کارت",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$walletState,'callback_data'=>"changeBotwalletState"],
                    ['text'=>"کیف پول",'callback_data'=>"wizwizch"]
                ],
                [
                    ['text'=>$rewaredTime,'callback_data'=>'editRewardTime'],
                    ['text'=>"ارسال گزارش درآمد", 'callback_data'=>'wizwizch']
                    ],
                [
                    ['text'=>$rewaredChannel,'callback_data'=>'editRewardChannel'],
                    ['text'=>"کانال گزارش درآمد",'callback_data'=>'wizwizch']
                    ],
                [['text'=>"↩️ برگشت",'callback_data'=>"managePanel"]]
                ]]);
            sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !',$keys);
            setUser();
            exit();
        }
    }
    sendMessage("ربات هنوز تو کانال عضو نشده، اول ربات رو تو کانال ادمین کن و آیدیش رو بفرست");
}
if ($data=='buySubscription' && ($botState['sellState']=="on" || ($from_id == $admin || $userInfo['isAdmin'] == true))){
    if($botState['cartToCartState'] == "off" && $botState['walletState'] == "off"){
        alert("فعلا فروش نداریم");
        exit();
    }
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1 and `ucount` > 0 ORDER BY `id` ASC");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows==0){
        sendMessage("😔 | عزیز دلم هیچ سرور فعالی نداریم لطفا بعدا مجدد تست کن");
        exit;
    }
    $keyboard = [];
    while($cat = $respd->fetch_assoc()){
        $id = $cat['id'];
        $name = $cat['title'];
        $flag = $cat['flag'];
        $keyboard[] = ['text' => "$flag $name", 'callback_data' => "selectServer$id"];
    }
    $keyboard[] = ['text'=>"⤵️ برگرد صفحه قبلی ",'callback_data'=>"mainMenu"];
    $keyboard = array_chunk($keyboard,1);
    editText($message_id, '  1️⃣ مرحله یک:

لوکیشن مدنظرت رو برا خرید انتخاب کن: 😊', json_encode(['inline_keyboard'=>$keyboard]));
    

}
if ($data=='createMultipleAccounts' && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1 and `ucount` > 0 ORDER BY `id` ASC");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows==0){
        sendMessage("😔 | عزیز دلم هیچ سرور فعالی نداریم لطفا بعدا مجدد تست کن");
        exit;
    }
    $keyboard = [];
    while($cat = $respd->fetch_assoc()){
        $id = $cat['id'];
        $name = $cat['title'];
        $flag = $cat['flag'];
        $keyboard[] = ['text' => "$flag $name", 'callback_data' => "createAccServer$id"];
    }
    $keyboard[] = ['text'=>"⤵️ برگرد صفحه قبلی ",'callback_data'=>"managePanel"];
    $keyboard = array_chunk($keyboard,1);
    editText($message_id, '  1️⃣ مرحله یک:

لوکیشن مدنظرت رو برا خرید انتخاب کن: 😊', json_encode(['inline_keyboard'=>$keyboard]));
    

}
if(preg_match('/createAccServer(\d+)/',$data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true) ) {
    $sid = $match[1];
        
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent`=0 order by `id` asc");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows == 0){
        alert("هیچ دسته بندی برای این سرور وجود ندارد");
    }else{
        
        $keyboard = [];
        while ($file = $respd->fetch_assoc()){
            $id = $file['id'];
            $name = $file['title'];
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1");
            $stmt->bind_param("ii", $sid, $id);
            $stmt->execute();
            $rowcount = $stmt->get_result()->num_rows; 
            $stmt->close();
            if($rowcount) $keyboard[] = ['text' => "$name", 'callback_data' => "createAccCategory{$id}_{$sid}"];
        }
        if(empty($keyboard)){
            alert("هیچ دسته بندی برای این سرور وجود ندارد");exit;
        }
        alert("♻️ | دریافت دسته بندی ...");
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "createMultipleAccounts"];
        $keyboard = array_chunk($keyboard,1);
        editText($message_id, "2️⃣ مرحله دو:

دسته بندی مورد نظرت رو انتخاب کن 🤭", json_encode(['inline_keyboard'=>$keyboard]));
    }

}
if(preg_match('/createAccCategory(\d+)_(\d+)/',$data,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $call_id = $match[1];
    $sid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1 order by `id` asc");
    $stmt->bind_param("ii", $sid, $call_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows==0){
        alert("💡پلنی در این دسته بندی وجود ندارد ");
    }else{
        alert("📍در حال دریافت لیست پلن ها");
        $keyboard = [];
        while($file = $respd->fetch_assoc()){
            $id = $file['id'];
            $name = $file['title'];
            $keyboard[] = ['text' => "$name", 'callback_data' => "createAccPlan{$id}"];
        }
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "createAccServer$sid"];
        $keyboard = array_chunk($keyboard,1);
        editText($message_id, "3️⃣ مرحله سه:

یکی از پلن هارو انتخاب کن و برو برای پرداختش 🤲 🕋", json_encode(['inline_keyboard'=>$keyboard]));
    }

}
if(preg_match('/^createAccPlan(\d+)/',$data,$match)){
    delMessage();
    sendMessage("❗️لطفا مدت زمان اکانت را به ( روز ) وارد کن:",$cancelKey);
    setUser('createAccDate' . $match[1]);
}
if(preg_match('/^createAccDate(\d+)/',$userInfo['step'],$match) && $text != $cancelText){
    if(is_numeric($text)){
        if($text >0){
            sendMessage("❕حجم اکانت ها رو به گیگابایت ( GB ) وارد کن:");
            setUser('createAccVolume' . $match[1] . "_" . $text);
        }else{
            sendMessage("عدد باید بیشتر از 0 باشه");
        }
    }else{
        sendMessage('😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟');
    }
}
if(preg_match('/^createAccVolume(\d+)_(\d+)/',$userInfo['step'],$match) && $text != $cancelText){
    if(!is_numeric($text)){
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    }elseif($text <=0){
        sendMessage("مقداری بزرگتر از 0 وارد کن");
        exit();
    }
    sendMessage("♻️ تعداد اکانت درخواستی رو وارد کن حداکثر هربار 6 عدد:

⚠️ | نکته: در صورت وارد کردن به مقدار بالا احتمالا اکانت ساخته نشود و پنل x-ui گیر کند
");
    setUser("createAccAmount" . $match[1] . "_" . $match[2] . "_" . $text);
}
if(preg_match('/createAccAmount(\d+)_(\d+)_(\d+)/',$userInfo['step'], $match) && $text != $cancelText){
    if(!is_numeric($text)){
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    }elseif($text <=0){
        sendMessage("مقداری بزرگتر از 0 وارد کن");
        exit();
    }
    $uid = $from_id;
    $fid = $match[1];
    $acctxt = '';
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $userinfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $days = $match[2];
    $date = time();
    $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
    $expire_date = $date + (86400 * $days);
    $type = $file_detail['type'];
    $volume = $match[3];
    $protocol = $file_detail['protocol'];
    $price = $file_detail['price'];
    
    
    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];


    if($acount == 0 and $inbound_id != 0){
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();

        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    }else{
        if($acount != 0 && $acount >= $text) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
        else{
            sendMessage("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }

    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $portType = $stmt->get_result()->fetch_assoc()['port_type'];
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $srv_remark = $stmt->get_result()->fetch_assoc()['remark'];
    $stmt->close();
    $savedinfo = file_get_contents('temp.txt');
    $savedinfo = explode('-',$savedinfo);
    $port = $savedinfo[0];
    $last_num = $savedinfo[1];
    include 'phpqrcode/qrlib.php';
    $ecc = 'L';
    $pixel_Size = 10;
    $frame_Size = 10;

	$stmt = $connection->prepare("INSERT INTO `orders_list` VALUES (NULL,  ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0);");
    for($i = 1; $i<= $text; $i++){
        $uniqid = generateRandomString(42,$protocol); 
        
        if($portType == "auto"){
            $port++;
        }else{
            $port = rand(12222,65000);
        }
        $last_num++;
    
        //$remark = "{$srv_remark}-{$last_num}";
        //$rnd = RandomString(2);
        $randadad = rand(1000,99000);
        $remark = "{$srv_remark}-{$randadad}";
        if($inbound_id == 0){    
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType); 
        }else {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip); 
        }
        
        if(is_null($response)){
            sendMessage('❌ | 🥺 گلم ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        }
    	if($response == "inbound not Found"){
            sendMessage("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
    	}
    	if(!$response->success){
            sendMessage('❌ | تنظیمات ربات به درستی انجام نشده ');
        }
    
        $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
        foreach($vraylink as $vray_link){
            $acc_text = "
    
        🔮 $remark \n <code>$vray_link</code>
            
            ";
        
            $file = RandomString() .".png";
            QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
        	addBorderImage($file);
        	sendPhoto($botUrl . $file, $acc_text,json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]),"HTML", $uid);
            unlink($file);        
        }
        $vray_link = json_encode($vraylink);
        $stmt->bind_param("siiissisii", $uid, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date);
        $stmt->execute();
    }
    $stmt->close();
    if($portType == "auto"){
        file_put_contents('temp.txt',$port.'-'.$last_num);
    }
    sendMessage("☑️|❤️ اکانت های جدید با موفقیت ساخته شد",$mainKeys);
    setUser();
}
if(preg_match('/payWithCartToCart/',$data)) {
    setUser($data);
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 $walletwizwiz

✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!",$cancelKey, "HTML");
    exit;
}


if($data=="messageToSpeceficUser" && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    delMessage();
    sendMessage("🀄️| آیدی عددی کاربر رو بفرس :", $cancelKey);
    setUser($data);
}

if($userInfo['step'] == "messageToSpeceficUser" && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    if(!is_numeric($text)){
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    }
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $text);
    $stmt->execute();
    $usersCount = $stmt->get_result()->num_rows;
    $stmt->close();

    if($usersCount > 0 ){
        sendMessage("👀| خصوصی میخوای بهش پیام بدی شیطون، پیامت رو بفرس تا در گوشش بگم:");
        setUser("sendMessageToUser" . $text);
    }else{
        sendMessage("🥴 | همچین کسی رو نداریما اشتباه وارد کردی به نظرم ");
    }
}



if ($data == 'message2All' and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $sendInfo = json_decode(file_get_contents("settings/messagewizwiz.json"),true);
    $offset = $sendInfo['offset'];
    $msg = $sendInfo['text'];
    
    if(strlen($msg) > 1 and $offset != -1) {
        $stmt = $connection->prepare("SELECT * FROM `users`");
        $stmt->execute();
        $usersCount = $stmt->get_result()->num_rows;
        $stmt->close();
        
        $leftMessages = $offset == 0 ? $usersCount - $offset : $usersCount - $offset;
        $offset = $offset == 0 ? $offset : $offset;
        sendMessage("
❗️ یک پیام همگانی در صف انتشار می باشد لطفا صبور باشید ...

🔰 تعداد کاربران : $usersCount
☑️ ارسال شده : $offset
📣 باقیمانده : $leftMessages
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
");exit;
    }
    setUser('s2a');
    sendMessage("لطفا پیامت رو بنویس ، میخوام برا همه بفرستمش: 🙂",$cancelKey);
    exit;
}
if ($userInfo['step'] == 's2a' and $text != $cancelText){
    setUser();
    sendMessage('⏳ مرسی از پیامت ، کم کم برا همه ارسال میشه ...  ',$removeKeyboard);
    sendMessage("لطفا یکی از کلید های زیر را انتخاب کنید",$mainKeys);

    if($fileid !== null) {
        $value = ['fileid'=>$fileid,'caption'=>$caption];
        $type = $filetype;
    }
    else{
        $type = 'text';
        $value = $text;
    }
    $messageValue = json_encode(['type'=>$type,'value'=> $value]);
    
    $sendInfo = json_decode(file_get_contents("settings/messagewizwiz.json"),true);
    $sendInfo['offset'] = 0;
    $sendInfo['text'] = $messageValue;
    file_put_contents("settings/messagewizwiz.json",json_encode($sendInfo));
}

if(preg_match('/selectServer(\d+)/',$data, $match) && ($botState['sellState']=="on" || ($from_id == $admin || $userInfo['isAdmin'] == true)) ) {
    $sid = $match[1];
        
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent`=0 order by `id` asc");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows == 0){
        alert("هیچ دسته بندی برای این سرور وجود ندارد");
    }else{
        
        $keyboard = [];
        while ($file = $respd->fetch_assoc()){
            $id = $file['id'];
            $name = $file['title'];
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1");
            $stmt->bind_param("ii", $sid, $id);
            $stmt->execute();
            $rowcount = $stmt->get_result()->num_rows; 
            $stmt->close();
            if($rowcount) $keyboard[] = ['text' => "$name", 'callback_data' => "selectCategory{$id}_{$sid}"];
        }
        if(empty($keyboard)){
            alert("هیچ دسته بندی برای این سرور وجود ندارد");exit;
        }
        alert("♻️ | دریافت دسته بندی ...");
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "buySubscription"];
        $keyboard = array_chunk($keyboard,1);
        editText($message_id, "2️⃣ مرحله دو:

دسته بندی مورد نظرت رو انتخاب کن 🤭", json_encode(['inline_keyboard'=>$keyboard]));
    }

}
if(preg_match('/selectCategory(\d+)_(\d+)/',$data,$match) && ($botState['sellState']=="on" || $from_id == $admin || $userInfo['isAdmin'] == true)) {
    $call_id = $match[1];
    $sid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1 order by `id` asc");
    $stmt->bind_param("ii", $sid, $call_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if($respd->num_rows==0){
        alert("💡پلنی در این دسته بندی وجود ندارد ");
    }else{
        alert("📍در حال دریافت لیست پلن ها");
        $keyboard = [];
        while($file = $respd->fetch_assoc()){
            $id = $file['id'];
            $name = $file['title'];
            $price = $file['price'];
            $price = ($price == 0) ? 'رایگان' : number_format($price).' تومان ';
            $keyboard[] = ['text' => "$name - $price", 'callback_data' => "selectPlan{$id}_{$call_id}"];
        }
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "selectServer$sid"];
        $keyboard = array_chunk($keyboard,1);
        editText($message_id, "3️⃣ مرحله سه:

یکی از پلن هارو انتخاب کن و برو برای پرداختش 🤲 🕋", json_encode(['inline_keyboard'=>$keyboard]));
    }

}
if(preg_match('/selectPlan(\d+)_(\d+)/',$data, $match) && ($botState['sellState']=="on" ||$from_id ==$admin)){
    $id = $match[1];
	$call_id = $match[2];
    alert("♻️در حال دریافت جزییات ... ");
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? and `active`=1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $respd = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
    $stmt->bind_param("i", $respd['catid']);
    $stmt->execute();
    $catname = $stmt->get_result()->fetch_assoc()['title'];
    $stmt->close();
    
    $name = $catname." ".$respd['title'];
    $price =  $respd['price'];
    $desc = $respd['descr'];
	$sid = $respd['server_id'];
	$keyboard = array();
    if($price == 0 or ($from_id == $admin)){
        $keyboard[] = [['text' => '📥 دریافت رایگان', 'callback_data' => "freeTrial$id"]];
    }else{
        $token = base64_encode("{$from_id}.{$id}");
        $temp = array();
        
        
        $hash_id = RandomString();
        $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` = 'BUY_SUB' AND `state` = 'pending'");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $stmt->close();
        
        $time = time();
        $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                    VALUES (?, ?, 'BUY_SUB', ?, '0', '0', ?, ?, 'pending')");
        $stmt->bind_param("siiii", $hash_id, $from_id, $id, $price, $time);
        $stmt->execute();
        $stmt->close();

        
        
        
        if($botState['cartToCartState'] == "on"){
		    $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "payWithCartToCart$id"];
        }
        if($botState['walletState'] == "on"){
		    $temp[] = ['text' => "پرداخت با موجودی 💰",  'callback_data' => "payWithWallet$id"];
        }
        if(count($temp) == 2){
            array_push($keyboard, $temp);
            $temp = array();
        }
        if($botState['zarinpal'] == "on"){
		    $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
        }

	    

        array_push($keyboard, $temp);
    }
	$keyboard[] = [['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "selectCategory{$call_id}_{$sid}"]];
    $price = ($price == 0) ? 'رایگان' : number_format($price).' تومان ';
    editText($message_id, "
〽️ نام پلن: $name
➖➖➖➖➖➖➖
💎 قیمت پنل : $price
➖➖➖➖➖➖➖
📃 توضیحات :
$desc
➖➖➖➖➖➖➖
💳 پرداخت به صورت کارت به کارت
➖➖➖➖➖➖➖
", json_encode(['inline_keyboard'=>$keyboard]), "HTML");
}
if(preg_match('/payWithWallet(\d+)/',$data, $match)){
    setUser();

    $uid = $from_id;
    $fid = $match[1];
    $acctxt = '';
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $userinfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $days = $file_detail['days'];
    $date = time();
    $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
    $expire_date = $date + (86400 * $days);
    $type = $file_detail['type'];
    $volume = $file_detail['volume'];
    $protocol = $file_detail['protocol'];
    $price = $file_detail['price'];
    
    if($userinfo['wallet'] < $price){
        alert("موجودی حساب شما کم است");
        exit();
    }
    
    
    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];


    if($acount == 0 and $inbound_id != 0){
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();

        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    }else{
        if($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42,$protocol); 

    $savedinfo = file_get_contents('temp.txt');
    $savedinfo = explode('-',$savedinfo);
    $port = $savedinfo[0] + 1;
    $last_num = $savedinfo[1] + 1;


    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $portType = $stmt->get_result()->fetch_assoc()['port_type'];
    $stmt->close();


    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $srv_remark = $stmt->get_result()->fetch_assoc()['remark'];
    $stmt->close();

        //$remark = "{$srv_remark}-{$last_num}";
        //$rnd = RandomString(2);
        $randadad = rand(1000,99000);
        $remark = "{$srv_remark}-{$randadad}";
        
    if($portType == "auto"){
        file_put_contents('temp.txt',$port.'-'.$last_num);
    }else{
        $port = rand(12222,65000);
    }
    
    if($inbound_id == 0){    
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType); 
        if(! $response->success){
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType);
        } 
    }else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip); 
        if(! $response->success){
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip);
        } 
    }
    
    if(is_null($response)){
        alert('❌ | 🥺 گلم ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
	if($response == "inbound not Found"){
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
		exit;
	}
	if(!$response->success){
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');
    
    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $price, $uid);
    $stmt->execute();
    include 'phpqrcode/qrlib.php';
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
    delMessage();
    foreach($vraylink as $vray_link){
$acc_text = "

😍 سفارش جدید شما
📡 پروتکل: $protocol
💰 قیمت: $price تومان
🔮 نام سرویس: $remark

🔮 $remark \n <code>$vray_link</code>
    
    ";
    
        $file = RandomString() .".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;
        
        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
    	addBorderImage($file);
    	sendPhoto($botUrl . $file, $acc_text,json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]),"HTML", $uid);
        unlink($file);
    }
    $vray_link = json_encode($vraylink);
    sendMessage(' اینم از سفارش جدیدت 🌹', $mainKeys);
    

	$stmt = $connection->prepare("INSERT INTO `orders_list` VALUES (NULL,  ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0);");
    $stmt->bind_param("siiissisii", $uid, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date);
    $stmt->execute();
    $order = $stmt->get_result(); 
    $stmt->close();
    sendMessage("خرید اکانت جدید با کیف پول\n\nآیدی کاربر: $from_id\nاسم کاربر: <a href='tg://user?id=$from_id'>$first_name</a>\nیوزرنیم: @$username\nمبلغ پرداختی: $price\n",null,"html", $admin);
}
if(preg_match('/payWithCartToCart/',$data)) {
    setUser($data);
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 $walletwizwiz

✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!",$cancelKey, "HTML");
    exit;
}
if(preg_match('/payWithCartToCart(\d+)/',$userInfo['step'], $match) and $text != $cancelText){
    $fid = $match[1];
    setUser();
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $uid = $res['userid'];
    $name = $res['name'];
    $username = $res['username'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
    $stmt->bind_param("i", $res['catid']);
    $stmt->execute();
    $catname = $stmt->get_result()->fetch_assoc()['title'];
    $stmt->close();
    $filename = $catname." ".$res['title']; $fileprice = $res['price'];

    $infoc = strlen($text) > 1 ? $text : "$caption <a href='$fileurl'>&#8194;نمایش فیش</a>";
    $msg = "
🛍 سفارشت با موفقیت ثبت شد.
بعد از تایید برات ارسال میکنم ... 🥳
";
        sendMessage($msg,$removeKeyboard);
        sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$mainKeys);

    $msg = "
🛍 سفارش : خرید $filename 
💰قیمت: $fileprice تومان
🧑‍💻 نام و نام خانوادگی : $name
🎯 یوزرنیم : @$username
🎫 کد کاربری : $from_id
";
    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "accept{$uid}_{$fid}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decline$uid"]
            ]
        ]
    ]);
    if(isset($update->message->photo)){
        sendPhoto($fileid, $msg,$keyboard, "HTML", $admin);
    }else{
        $msg .= "\n\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard,"HTML",$admin);
    }
}
if($data=="availableServers"){
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `acount` != 0");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();

    $keys = array();
    $keys[] = [
        ['text'=>"تعداد باقیمانده",'callback_data'=>"wizwizch"],
        ['text'=>"پلن",'callback_data'=>"wizwizch"],
        ['text'=>'سرور','callback_data'=>"wizwizch"]
        ];
    while($file_detail = $serversList->fetch_assoc()){
        $days = $file_detail['days'];
        $title = $file_detail['title'];
        $server_id = $file_detail['server_id'];
        $acount = $file_detail['acount'];
        $inbound_id = $file_detail['inbound_id'];
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $name = $stmt->get_result();
        $stmt->close();

        if($name->num_rows>0){
            $name = $name->fetch_assoc()['title'];
            
            $keys[] = [
                ['text'=>$acount . " اکانت",'callback_data'=>"wizwizch"],
                ['text'=>$title,'callback_data'=>"wizwizch"],
                ['text'=>$name,'callback_data'=>"wizwizch"]
                ];
        }
    }
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"mainMenu"]];
    $keys = json_encode(['inline_keyboard'=>$keys]);
    editText($message_id, "🟢 | وضعیت سرورهای اشتراکی:", $keys);
}
if(preg_match('/accept(\d+)_(\d+)/',$data, $match) and $text != $cancelText){
    setUser();

    $uid = $match[1];
    $fid = $match[2];
    $acctxt = '';
    
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $days = $file_detail['days'];
    $date = time();
    $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
    $expire_date = $date + (86400 * $days);
    $type = $file_detail['type'];
    $volume = $file_detail['volume'];
    $protocol = $file_detail['protocol'];
    $price = $file_detail['price'];
    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];


    if($acount == 0 and $inbound_id != 0){
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();

        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    }else{
        if($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42,$protocol); 

    $savedinfo = file_get_contents('temp.txt');
    $savedinfo = explode('-',$savedinfo);
    $port = $savedinfo[0] + 1;
    $last_num = $savedinfo[1] + 1;

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $srv_remark = $stmt->get_result()->fetch_assoc()['remark'];
    $stmt->close();


    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $portType = $stmt->get_result()->fetch_assoc()['port_type'];
    $stmt->close();

        //$remark = "{$srv_remark}-{$last_num}";
        //$rnd = RandomString(2);
        $randadad = rand(1000,99000);
        $remark = "{$srv_remark}-{$randadad}";
        
    if($portType == "auto"){
        file_put_contents('temp.txt',$port.'-'.$last_num);
    }else{
        $port = rand(12222,65000);
    }
    
    if($inbound_id == 0){    
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType); 
        if(! $response->success){
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType);
        } 
    }else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip); 
        if(! $response->success){
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip);
        } 
    }
    
    if(is_null($response)){
        alert('❌ | 🥺 گلم ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
	if($response == "inbound not Found"){
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
		exit;
	}
	if(!$response->success){
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');
    
    include 'phpqrcode/qrlib.php';
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
    foreach($vraylink as $vray_link){
$acc_text = "

😍 سفارش جدید شما
📡 پروتکل: $protocol
💰 قیمت: $price تومان
🔮 نام سرویس: $remark

🔮 $remark \n <code>$vray_link</code>
    
    ";
    
        $file = RandomString() .".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;
    
        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
    	addBorderImage($file);
    	sendPhoto($botUrl . $file, $acc_text,json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]),"HTML", $uid);
        unlink($file);
    }
    sendMessage('✅ کانفیگ و براش ارسال کردم', $mainKeys);
    $vray_link = json_encode($vraylink);
	$stmt = $connection->prepare("INSERT INTO `orders_list` VALUES (NULL,  ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0);");
    $stmt->bind_param("siiissisii", $uid, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();

    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $message_id,
		'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => '✅', 'callback_data' => "dontsendanymore"]]],
        ])
    ]);
    
    $filename = $file_detail['title'];
    $fileprice = number_format($file_detail['price']);
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user_detail= $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $uname = $user_detail['name'];
    $user_name = $user_detail['username'];
    
    if($admin != $from_id) sendMessage("✅سفارش کارت به کارت زیر توسط یکی از همکاران رسیدگی شد. لطفا از تایید یا رد آن خودداری کنید
#$remark
🛍 سفارش : خرید $filename 
💰قیمت: $fileprice تومان
🧑‍💻 نام و نام خانوادگی : $name
🎯 یوزرنیم : @$username
🎫 کد کاربری : $from_id
",null,null,$admin);
    
}
if(preg_match('/decline/',$data) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    setUser($data . "_" . $message_id);
    sendMessage('دلیلت از عدم تایید چیه؟ ( بفرس براش ) 😔 ',$cancelKey);
}
if(preg_match('/decline(\d+)_(\d+)/',$userInfo['step'],$match) and $text != $cancelText){
    setUser();
    $uid = $match[1];
    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $match[2],
		'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => '❌', 'callback_data' => "dontsendanymore"]]],
        ])
    ]);
    sendMessage('پیامت رو براش ارسال کردم ... 🤝',$removeKeyboard);
    sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
    
    sendMessage($text, null, null, $uid);
}
if($data=="supportSection"){
    editText($message_id,"به بخش پشتیبانی خوش اومدی🛂\nلطفا، یکی از دکمه های زیر را انتخاب نمایید.",
        json_encode(['inline_keyboard'=>[
        [['text'=>"✉️ ثبت تیکت",'callback_data'=>"usersNewTicket"]],
        [['text'=>"تیکت های باز 📨",'callback_data'=>"usersOpenTickets"],['text'=>"📮 لیست تیکت ها", 'callback_data'=>"userAllTickets"]],
        [['text'=>"برگشت 🔙",'callback_data'=>"mainMenu"]]
        ]]));
}

if($data== "usersNewTicket"){
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    $keys = array();
    $temp = array();
    if($ticketCategory->num_rows >0){
        while($row = $ticketCategory->fetch_assoc()){
            $ticketName = $row['value'];
            $temp[] = ['text'=>$ticketName,'callback_data'=>"supportCat$ticketName"];
            
            if(count($temp) == 2){
                array_push($keys,$temp);
                $temp = null;
            }
        }
        
        if($temp != null){
            if(count($temp)>0){
                array_push($keys,$temp);
                $temp = null;
            }
        }
        $temp[] = ['text'=>"برگشت 🔙",'callback_data'=>"mainMenu"];
        array_push($keys,$temp);
        editText($message_id,"💠لطفا واحد مورد نظر خود را انتخاب نمایید!",json_encode(['inline_keyboard'=>$keys]));
    }else{
        alert("ای وای، ببخشید الان نیستم");
    }
}
if(preg_match('/^supportCat(.*)/',$data,$match)){
    delMessage();
    sendMessage("💠لطفا موضوع تیکت را ارسال کنید!", $cancelKey);
    setUser("newTicket_" . $match[1]);
}
if(preg_match('/^newTicket_(.*)/',$userInfo['step'],$match)  and $text!=$cancelText){
    file_put_contents("$from_id.txt",$text);
	setUser("sendTicket_" . $match[1]);
    sendMessage("💠لطفا متن تیکت خود را بصورت ساده و مختصر ارسال کنید!");
}
if(preg_match('/^sendTicket_(.*)/',$userInfo['step'],$match)  and $text!=$cancelText){
    $ticketCat = $match[1];
    
    $ticketTitle = file_get_contents("$from_id.txt");
    $time = time();
    $txt = "تیکت جدید:\n\nکاربر: <a href='tg://user?id=$from_id'>$first_name</a>\nنام کاربری: @$username\nآیدی عددی: $from_id\n\nموضوع تیکت: $ticketCat\n\nعنوان تیکت: " .$ticketTitle . "\nمتن تیکت: $text";

    $ticketTitle = str_replace(["/","'","#"],['\/',"\'","\#"],$ticketTitle);
    $text = str_replace(["/","'","#"],['\/',"\'","\#"],$text);
    $stmt = $connection->prepare("INSERT INTO `chats` (`user_id`,`create_date`, `title`,`category`,`state`,`rate`) VALUES 
                        (?,?,?,?,'0','0')");
    $stmt->bind_param("iiss", $from_id, $time, $ticketTitle, $ticketCat);
    $stmt->execute();
    $inserId = $stmt->get_result();
    $chatRowId = $stmt->insert_id;
    $stmt->close();
    
    $stmt = $connection->prepare("INSERT INTO `chats_info` (`chat_id`,`sent_date`,`msg_type`,`text`) VALUES
                (?,?,'USER',?)");
    $stmt->bind_param("iis", $chatRowId, $time, $text);
    $stmt->execute();
    $stmt->close();
    
    $keys = json_encode(['inline_keyboard'=>[
        [['text'=>"پاسخ",'callback_data'=>"reply_{$chatRowId}"]]
        ]]);
    sendMessage($txt,$keys,"html", $admin);
    sendMessage("پیام شما با موفقیت ثبت شد",$mainKeys,"HTML");
        
    unlink("$from_id.txt");
	setUser("none");
}
if($data== "usersOpenTickets" || $data == "userAllTickets"){
    if($data== "usersOpenTickets"){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 AND `user_id` = ? ORDER BY `state` ASC, `create_date` DESC");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = 2;
    }elseif($data == "userAllTickets"){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `user_id` = ? ORDER BY `state` ASC, `create_date` DESC");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = "all";
    }
	$allList = $ticketList->num_rows;
	$cont = 5;
	$current = 0;
	$keys = array();
	setUser("none");


	if($allList>0){
        while($row = $ticketList->fetch_assoc()){
		    $current++;
		    
            $rowId = $row['id'];
            $title = $row['title'];
            $category = $row['category'];
	        $state = $row['state'];

            $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC");
            $stmt->bind_param("i", $rowId);
            $stmt->execute();
            $ticketInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $lastmsg = $ticketInfo['text'];
            $sentType = $ticketInfo['msg_type']=="ADMIN"?"ادمین":"کاربر";
            
            if($state !=2){
                $keys = [
                        [['text'=>"بستن تیکت 🗳",'callback_data'=>"closeTicket_$rowId"],['text'=>"پاسخ به تیکت 📝",'callback_data'=>"replySupport_{$rowId}"]],
                        [['text'=>"آخرین پیام ها 📩",'callback_data'=>"latestMsg_$rowId"]]
                        ];
            }
            else{
                $keys = [
                    [['text'=>"آخرین پیام ها 📩",'callback_data'=>"latestMsg_$rowId"]]
                    ];
            }
                
            sendMessage(" 🔘 موضوع: $title
			💭 دسته بندی:  {$category}
			\n
			$sentType : $lastmsg",json_encode(['inline_keyboard'=>$keys]),"HTML");

			if($current>=$cont){
			    break;
			}
        }
        
		if($allList > $cont){
		    sendmessage("موارد بیشتر",json_encode(['inline_keyboard'=>[
                		        [['text'=>"دریافت",'callback_data'=>"moreTicket_{$type}_{$cont}"]]
                		        ]]),"HTML");
		}
	}else{
	    alert("تیکتی یافت نشد");
        exit();
	}
}
if(preg_match('/^closeTicket_(\d+)/',$data,$match) and  $from_id != $admin){
    $chatRowId = $match[1];
    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $from_id = $ticketInfo['user_id'];
    $title = $ticketInfo['title'];
    $category = $ticketInfo['category'];
        

    $stmt = $connection->prepare("UPDATE `chats` SET `state` = 2 WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $stmt->close();
    
    bot('editMessageReplyMarkup',['chat_id'=>$from_id,'message_id'=>$message_id,'reply_markup'=>null]);

    $ticketClosed = " $title : $category \n\n" . "این تیکت بسته شد\n به این تیکت رأی بدهید";;
    
    $keys = json_encode(['inline_keyboard'=>[
        [['text'=>"بسیار بد 😠",'callback_data'=>"rate_{$chatRowId}_1"]],
        [['text'=>"بد 🙁",'callback_data'=>"rate_{$chatRowId}_2"]],
        [['text'=>"خوب 😐",'callback_data'=>"rate_{$chatRowId}_3"]],
        [['text'=>"بسیار خوب 😃",'callback_data'=>"rate_{$chatRowId}_4"]],
        [['text'=>"عالی 🤩",'callback_data'=>"rate_{$chatRowId}_5"]]
        ]]);
    sendMessage($ticketClosed,$keys,'html');
    
    sendMessage("تیکت توسط کاربر بسته شد:\n\n[$title] <i>$category</i> \n\nآیدی کاربر: $from_id\nنام کاربر: <a href='tg://user?id=$from_id'>$first_name</a>","HTML",$admin);

}
if(preg_match('/^replySupport_(.*)/',$data,$match)){
    delMessage();
    sendMessage("💠لطفا متن پیام خود را بصورت ساده و مختصر ارسال کنید!",$cancelKey);
	setUser("sendMsg_" . $match[1]);
}
if(preg_match('/^sendMsg_(.*)/',$userInfo['step'],$match)  and $text!=$cancelText){
    $ticketRowId = $match[1];

    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $ticketRowId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $ticketTitle = $ticketInfo['title'];
    $ticketCat = $ticketInfo['category'];

    $time = time();
    $txt = "پیام جدید:\n[$ticketTitle] <i>{$ticketCat}</i>\n\nکاربر: <a href='tg://user?id=$from_id'>$first_name</a>\nنام کاربری: $username\nآیدی عددی: $from_id\n" . "\nمتن پیام: $text";

    $text = str_replace(["/","'","#"],['\/',"\'","\#"],$text);
    $stmt = $connection->prepare("INSERT INTO `chats_info` (`chat_id`,`sent_date`,`msg_type`,`text`) VALUES
                (?,?,'USER',?)");
    $stmt->bind_param("iis",$ticketRowId, $time, $text);
    $stmt->execute();
    $stmt->close();
                
    sendMessage($txt,json_encode(['inline_keyboard'=>[
        [['text'=>"پاسخ",'callback_data'=>"reply_{$ticketRowId}"]]
        ]]),"HTML",$admin);
    sendMessage("پیام شما با موفقیت ثبت شد",$mainKeys,"HTML");
	setUser("none");
}
if(preg_match("/^rate_+([0-9])+_+([0-9])/",$data,$match)){
    $rowChatId = $match[1];
    $rate = $match[2];
    
    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i",$rowChatId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $title = $ticketInfo['title'];
    $category = $ticketInfo['category'];
    
    
    $stmt = $connection->prepare("UPDATE `chats` SET `rate` = $rate WHERE `id` = ?");
    $stmt->bind_param("i", $rowChatId);
    $stmt->execute();
    $stmt->close();
    editText($message_id,"✅");
    sendMessage("رأی به تیکت\nآیدی کاربر: $from_id\nنام کاربر: <a href='tg://user?id=$from_id'>$first_name</a>\n\n $title : $category \n\nرأی: $rate",null,"HTML",$admin);
}



if($data=="ticketsList" and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $ticketSection = json_encode(['inline_keyboard'=>[
        [
            ['text'=>"تیکت های باز",'callback_data'=>"openTickets"],
            ['text'=>"تیکت های جدید",'callback_data'=>"newTickets"]
            ],
        [
            ['text'=>"همه ی تیکت ها",'callback_data'=>"allTickets"],
            ['text'=>"دسته بندی تیکت ها",'callback_data'=>"ticketsCategory"]
            ],
        [['text' => "↪ برگشت", 'callback_data' => "managePanel"]]
        ]]);
    editText($message_id, "به بخش تیکت ها خوش اومدید، 🏵 روی گزینه مورد نظرت کلیک کن:",$ticketSection);
}
if($data=='ticketsCategory' and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    $keys = array();
    $keys[] = [['text'=>"حذف",'callback_data'=>"wizwizch"],['text'=>"دسته بندی",'callback_data'=>"wizwizch"]];
    
    if($ticketCategory->num_rows>0){
        while($row = $ticketCategory->fetch_assoc()){
            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text'=>"❌",'callback_data'=>"delTicketCat_$rowId"],['text'=>$ticketName,'callback_data'=>"wizwizch"]];
        }
    }else{
        $keys[] = [['text'=>"دسته بندی یافت نشد",'callback_data'=>"wizwizch"]];
    }
    $keys[] = [['text'=>"افزودن دسته بندی",'callback_data'=>"addTicketCategory"]];
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"ticketsList"]];
    
    $keys =  json_encode(['inline_keyboard'=>$keys]);
    editText($message_id,"دسته بندی تیکت ها",$keys);
}
if($data=="addTicketCategory" and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    setUser('addTicketCategory');
    editText($message_id,"لطفا اسم دسته بندی را وارد کنید");
}
if ($userInfo['step']=="addTicketCategory" and ($from_id == $admin || $userInfo['isAdmin'] == true)){
	$stmt = $connection->prepare("INSERT INTO `setting` (`type`, `value`) VALUES ('TICKETS_CATEGORY', ?)");	
	$stmt->bind_param("s", $text);
	$stmt->execute();
	$stmt->close();
    setUser();
    sendMessage("☑️ | 😁 با موفقیت ذخیره شد");
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    
    $keys = array();
    $keys[] = [['text'=>"حذف",'callback_data'=>"wizwizch"],['text'=>"دسته بندی",'callback_data'=>"wizwizch"]];
    
    if($ticketCategory->num_rows>0){
        while ($row = $ticketCategory->fetch_assoc()){
            
            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text'=>"❌",'callback_data'=>"delTicketCat_$rowId"],['text'=>$ticketName,'callback_data'=>"wizwizch"]];
        }
    }else{
        $keys[] = [['text'=>"دسته بندی یافت نشد",'callback_data'=>"wizwizch"]];
    }
    $keys[] = [['text'=>"افزودن دسته بندی",'callback_data'=>"addTicketCategory"]];
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"ticketsList"]];
    
    $keys =  json_encode(['inline_keyboard'=>$keys]);
    sendMessage("دسته بندی تیکت ها",$keys);
}
if(preg_match("/^delTicketCat_(\d+)/",$data,$match) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("DELETE FROM `setting` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    
    alert("با موفقیت حذف شد");
        

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    
    $keys = array();
    $keys[] = [['text'=>"حذف",'callback_data'=>"wizwizch"],['text'=>"دسته بندی",'callback_data'=>"wizwizch"]];
    
    if($ticketCategory->num_rows>0){
        while ($row = $ticketCategory->fetch_assoc()){
            
            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text'=>"❌",'callback_data'=>"delTicketCat_$rowId"],['text'=>$ticketName,'callback_data'=>"wizwizch"]];
        }
    }else{
        $keys[] = [['text'=>"دسته بندی یافت نشد",'callback_data'=>"wizwizch"]];
    }
    $keys[] = [['text'=>"افزودن دسته بندی",'callback_data'=>"addTicketCategory"]];
    $keys[] = [['text'=>"↩️ برگشت",'callback_data'=>"ticketsList"]];
    
    $keys =  json_encode(['inline_keyboard'=>$keys]);
    editText($message_id, "دسته بندی تیکت ها",$keys);
}
if(($data=="openTickets" or $data=="newTickets" or $data == "allTickets")  and  $from_id ==$admin){
    if($data=="openTickets"){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 ORDER BY `state` ASC, `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = 2;
    }elseif($data=="newTickets"){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` = 0 ORDER BY `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = 0;
    }elseif($data=="allTickets"){
        $stmt = $connection->prepare("SELECT * FROM `chats` ORDER BY `state` ASC, `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = "all";
    }
	$allList =$ticketList->num_rows;
	$cont = 5;
	$current = 0;
	$keys = array();

	if($allList>0){
        while ($row = $ticketList->fetch_assoc()){
		    $current++;
		    
            $rowId = $row['id'];
            $admin = $row['user_id'];
            $title = $row['title'];
            $category = $row['category'];
	        $state = $row['state'];
	        $username = bot('getChat',['chat_id'=>$admin])->result->first_name ?? " ";

            $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC");
            $stmt->bind_param("i",$rowId);
            $stmt->execute();
            $ticketInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $lastmsg = $ticketInfo['text'];
            $sentType = $ticketInfo['msg_type']=="USER"?"کاربر":"ادمین";
            
            if($state !=2){
                $keys = [
                        [['text'=>"بستن تیکت",'callback_data'=>"closeTicket_$rowId"],['text'=>"پاسخ",'callback_data'=>"reply_{$rowId}"]],
                        [['text'=>"آخرین پیام ها",'callback_data'=>"latestMsg_$rowId"]]
                        ];
            }
            else{
                $keys = [[['text'=>"آخرین پیام ها",'callback_data'=>"latestMsg_$rowId"]]];
                $rate = "\nرأی: ". $row['rate'];
            }
            
            sendMessage("آیدی کاربر: $admin\nنام کاربر: $username\nدسته بندی: $category $rate\n\nموضوع: $title\nآخرین پیام:\n[$sentType] $lastmsg",
                json_encode(['inline_keyboard'=>$keys]),"html");

			if($current>=$cont){
			    break;
			}
        }
        
		if($allList > $cont){
		    $keys = json_encode(['inline_keyboard'=>[
		        [['text'=>"دریافت",'callback_data'=>"moreTicket_{$type}_{$cont}"]]
		        ]]);
            sendMessage("موارد بیشتر",$keys,"html");
		}
	}else{
        alert("تیکتی یافت نشد");
	}
}
if(preg_match('/^moreTicket_/',$data) and  ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $param = explode("_",$data);
    $type = $param[1];
    $offset = $param[2];
    if($type==2){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 ORDER BY `state` ASC, `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
    }elseif($type==0){
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` = 0 ORDER BY `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
    }elseif($type=="all"){
        $stmt = $connection->prepare("SELECT * FROM `chats` ORDER BY `state` ASC, `create_date` DESC");
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
    }
	$allList = $ticketList->num_rows;
	$cont = 5 + $offset;
	$current = 0;
	$keys = array();
	$rowCont = 0;
	if($allList>0){
	    while($row = $ticketList->num_rows){
            $rowCont++;
            if($rowCont>$offset){
    		    $current++;
    		    
                $rowId = $row['id'];
                $admin = $row['user_id'];
                $title = $row['title'];
                $category = $row['category'];
    	        $state = $row['state'];

    	        $username = bot('getChat',['chat_id'=>$admin])->result->first_name ?? " ";
                $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC");
                $stmt->bind_param("i", $rowId);
                $stmt->execute();
                $ticketInfo  = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
                $lastmsg = $ticketInfo['text'];
                $sentType = $ticketInfo['msg_type']=="USER"?"کاربر":"ادمین";

                if($state !=2){
                    $keys = [
                            [['text'=>"بستن تیکت",'callback_data'=>"closeTicket_$rowId"],['text'=>"پاسخ",'callback_data'=>"reply_{$rowId}"]],
                            [['text'=>"آخرین پیام ها",'callback_data'=>"latestMsg_$rowId"]]
                            ];
                }
                else{
                    $keys = [[['text'=>"آخرین پیام ها",'callback_data'=>"latestMsg_$rowId"]]];
                    $rate = "\nرأی: ". $row['rate'];
                }
                    
                sendMessage("آیدی کاربر: $admin\nنام کاربر: $username\nدسته بندی: $category $rate\n\nموضوع: $title\nآخرین پیام:\n[$sentType] $lastmsg",
                    json_encode(['inline_keyboard'=>$keys]),
                    "html");


    			if($current>=$cont){
    			    break;
    			}
            }
        }
        
		if($allList > $cont){
		    $keys = json_encode(['inline_keyboard'=>[
		        [['text'=>"دریافت",'callback_data'=>"moreTicket_{$type}_{$cont}"]]
		        ]]);
            sendMessage("موارد بیشتر",$keys);
		}
	}else{
        alert("تیکتی یافت نشد");
	}
}
if(preg_match('/^closeTicket_(\d+)/',$data,$match) and  ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $chatRowId = $match[1];
    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $userId = $ticketInfo['user_id'];
    $title = $ticketInfo['title'];
    $category = $ticketInfo['category'];
        

    $stmt = $connection->prepare("UPDATE `chats` SET `state` = 2 WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $stmt->close();
    
    $ticketClosed = "[$title] <i>$category</i> \n\n" . "این تیکت بسته شد\n به این تیکت رأی بدهید";;
    
    $keys = json_encode(['inline_keyboard'=>[
        [['text'=>"بسیار بد 😠",'callback_data'=>"rate_{$chatRowId}_1"]],
        [['text'=>"بد 🙁",'callback_data'=>"rate_{$chatRowId}_2"]],
        [['text'=>"خوب 😐",'callback_data'=>"rate_{$chatRowId}_3"]],
        [['text'=>"بسیار خوب 😃",'callback_data'=>"rate_{$chatRowId}_4"]],
        [['text'=>"عالی 🤩",'callback_data'=>"rate_{$chatRowId}_5"]]
        ]]);
    sendMessage($ticketClosed,$keys,'html', $userId);
    bot('editMessageReplyMarkup',['chat_id'=>$from_id,'message_id'=>$message_id,'reply_markup'=>json_encode(['inline_keyboard'=>[
        [['text'=>"تیکت بسته شد",'callback_data'=>"wizwizch"]]
        ]])]);

}
if(preg_match('/^latestMsg_(.*)/',$data,$match)){
    $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC LIMIT 10");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $chatList = $stmt->get_result();
    $stmt->close();
    $output = "";
    while($row = $chatList->fetch_assoc()){
        $type = $row['msg_type'] == "USER" ?"کاربر":"ادمین";
        $text = $row['text'];

        $output .= "<i>[$type]</i>\n$text\n\n";
    }
    sendMessage($output, null, "html");
}
if(($data == "banUser" || $data=="unbanUser") && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    delMessage();
    sendMessage("😡 | کی باز شلوغی کرده آیدی عددی شو بفرس تا برم ...... آرهههه:", $cancelKey);
    setUser($data);
}
if($userInfo['step'] == "banUser" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    if(is_numeric($text)){
        if($usersInfo[$text]['step'] != "banned"){
            $usersInfo[$text]['step'] = "banned";
            file_put_contents("userInfo.json", json_encode($usersInfo));
            sendMessage("❌ | خب خب برید کنار که مسدودش کردم 😎😂",$removeKeyboard);
        }else{
            sendMessage("☑️ | این کاربر که از قبل مسدود بود چیکارش داری بدبخت و 😂🤣",$removeKeyboard);
        }
        setUser();
        sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$adminKeys);
    }else{
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if($userInfo['step'] == "unbanUser" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    if(is_numeric($text)){
        if($usersInfo[$text]['step'] == "banned"){
            $usersInfo[$text]['step'] = "none";
            file_put_contents("userInfo.json", json_encode($usersInfo));
            sendMessage("✅ | آزاد شدم خوشحالم ننه ، ایشالا آزادی همه 😂",$removeKeyboard);
        }else{
            sendMessage("☑️ | این کاربری که فرستادی از قبل آزاد بود 🙁",$removeKeyboard);
        }
        setUser();
        sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$adminKeys);
    }else{
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if(preg_match("/^reply_(.*)/",$data,$match) and  ($from_id == $admin || $userInfo['isAdmin'] == true)){
    setUser("answer_" . $match[1]);
    sendMessage("لطفا پیام خود را ارسال کنید",$cancelKey);
}
if(preg_match('/^answer_(.*)/',$userInfo['step'],$match) and  $from_id ==$admin  and $text!=$cancelText){
    $chatRowId = $match[1];
    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $userId = $ticketInfo['user_id'];
    $ticketTitle = $ticketInfo['title'];
    $ticketCat = $ticketInfo['category'];
    
    sendMessage("\[$ticketTitle] _{$ticketCat}_\n\n" . $text,json_encode(['inline_keyboard'=>[
        [
            ['text'=>'پاسخ به تیکت 📝','callback_data'=>"replySupport_$chatRowId"],
            ['text'=>"بستن تیکت 🗳",'callback_data'=>"closeTicket_$chatRowId"]
            ]
        ]]),"MarkDown", $userId);
    $time = time();

    $ticketTitle = str_replace(["/","'","#"],['\/',"\'","\#"],$ticketTitle);
    $text = str_replace(["/","'","#"],['\/',"\'","\#"],$text);
    $stmt = $connection->prepare("INSERT INTO `chats_info` (`chat_id`,`sent_date`,`msg_type`,`text`) VALUES
                (?,?,'ADMIN',?)");
    $stmt->bind_param("iis", $chatRowId, $time, $text);
    $stmt->execute();
    $stmt->close();
    $stmt = $connection->prepare("UPDATE `chats` SET `state` = 1 WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $stmt->close();
    
    setUser();
    sendMessage("پیام شما با موفقیت ارسال شد ✅");
}

if(preg_match('/freeTrial(\d+)/',$data,$match)) {
    $id = $match[1];

    if($userInfo['freetrial'] == 'used' and !($from_id == $admin)){
        alert('⚠️شما قبلا هدیه رایگان خود را دریافت کردید');
        exit;
    }

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $days = $file_detail['days'];
    $date = time();
    $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
    $expire_date = $date + (86400 * $days);
    $type = $file_detail['type'];
    $volume = $file_detail['volume'];
    $protocol = $file_detail['protocol'];
    $price = $file_detail['price'];
    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];
    $netType = $file_detail['type'];

    if($acount == 0 and $inbound_id != 0){
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($server_info['ucount'] != 0){ 
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    }else{
        if($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $uniqid = generateRandomString(42,$protocol); 

    $savedinfo = file_get_contents('temp.txt');
    $savedinfo = explode('-',$savedinfo);
    $port = $savedinfo[0] + 1;
    $last_num = $savedinfo[1] + 1;

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $srv_remark = $stmt->get_result()->fetch_assoc()['remark'];
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $portType = $stmt->get_result()->fetch_assoc()['port_type'];
    $stmt->close();
    
    
        //$remark = "{$srv_remark}-{$last_num}";
        //$rnd = RandomString(2);
        $randadad = rand(1000,99000);
        $remark = "{$srv_remark}-{$randadad}";
    
    if($portType == "auto"){
        file_put_contents('temp.txt',$port.'-'.$last_num);
    }else{
        $port = rand(12222,65000);
    }
    if($inbound_id == 0){    
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType); 
        if(! $response->success){
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType);
        } 
    }else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip); 
        if(! $response->success){
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip);
        } 
    }
    if(is_null($response)){
        alert('❌ | 🥺 گلم ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
	if($response == "inbound not Found"){
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
		exit;
	}
	if(!$response->success){
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
	include 'phpqrcode/qrlib.php';
    foreach($vraylink as $vray_link){
$acc_text = "

😍 سفارش جدید شما
📡 پروتکل: $protocol
💰 قیمت: $price تومان
🔮 نام سرویس: $remark

🔮 $remark \n <code>$vray_link</code>
    
    ";
    
        $file = RandomString().".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;
        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_size);
    	addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text,json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]),"HTML");
        unlink($file);
    }
    $vray_link = json_encode($vraylink);
	$stmt = $connection->prepare("INSERT INTO `orders_list` VALUES (NULL,  ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0);");
    $stmt->bind_param("iiiissisii", $from_id, $id, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();

    setUser('used','freetrial');    

    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $message_id,
		'reply_markup' => json_encode([
            'inline_keyboard' => [[['text' => '✅', 'callback_data' => "dontsendanymore"]]],
        ])
    ]);
}
if ($data == 'addNewPlan' and (($from_id == $admin || $userInfo['isAdmin'] == true))){
    setUser($data);
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();

    $sql = "INSERT INTO `server_plans` VALUES (NULL, '', 0,0,0,0, 1, '', '', 0, 0, '', 0, '', '',0,1, ?);";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $time);
    $stmt->execute();
    $stmt->close();

    $msg = '❗️یه عنوان برا پلن انتخاب کن:';
    sendMessage($msg,$cancelKey);
    exit;
}

if($data=="showUUIDLeft" && ($botState['searchState']=="on" || $from_id== $admin)){
    sendMessage("❗️| لینک کانفیگ یا uuid رو برام بفرس اطلاعات کامل رو تحویلت بدم 🤭",$cancelKey);
    setUser('showAccount');
}


if($userInfo['step'] == "showAccount" and $text != "😩 منصرف شدم بیخیال"){
    if(preg_match('/^vmess:\/\/(.*)/',$text,$match)){
        $jsonDecode = json_decode(base64_decode($match[1]),true);
        $text = $jsonDecode['id'];
    }elseif(preg_match('/^vless:\/\/(.*?)\@/',$text,$match)){
        $text = $match[1];
        
    }elseif(preg_match('/^trojan:\/\/(.*?)\@/',$text,$match)){
        $text = $match[1];
        
    }
    
    sendMessage("گلم لطفا یکم منتظر بمون ...", $removeKeyboard);
    $stmt = $connection->prepare("SELECT * FROM `server_config`");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();
    $found = false;
    while($row = $serversList->fetch_assoc()){
        $serverId = $row['id'];

        $response = getJson($serverId);
        if($response->success){
            
            $list = json_encode($response->obj);
            
            if(strpos($list, $text)){
                setUser();
                $found = true;
                $list = $response->obj;
                if(!isset($list[0]->clientStats)){
                    foreach($list as $keys=>$packageInfo){
                    	if(strpos($packageInfo->settings, $text)!=false){
                    	    $remark = $packageInfo->remark;
                            $upload = sumerize($packageInfo->up);
                            $download = sumerize($packageInfo->down);
                            $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $totalUsed = sumerize($packageInfo->up + $packageInfo->down);
                            $total = $packageInfo->total!=0?sumerize($packageInfo->total):"نامحدود";
                            $expiryTime = $packageInfo->expiryTime != 0?date("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                            $leftMb = $packageInfo->total!=0?sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                            $expiryDay = $packageInfo->expiryTime != 0?
                                floor(
                                    (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24)
                                    ,2):
                                    "نامحدود";

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
                    $clientsSettings = json_decode($list[$keys]->settings,true)['clients'];
                    if(!is_array($clientsSettings)){
                        sendMessage($chat_id,"با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                        exit();
                    }
                    $settingsId = array_column($clientsSettings,'id');
                    $settingKey = array_search($text,$settingsId);
                    
                    if(!isset($clientsSettings[$settingKey]['email'])){
                        $packageInfo = $list[$keys];
                	    $remark = $packageInfo->remark;
                        $upload = sumerize($packageInfo->up);
                        $download = sumerize($packageInfo->down);
                        $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                        $totalUsed = sumerize($packageInfo->up + $packageInfo->down);
                        $total = $packageInfo->total!=0?sumerize($packageInfo->total):"نامحدود";
                        $expiryTime = $packageInfo->expiryTime != 0?date("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                        $leftMb = $packageInfo->total!=0?sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                        if(is_numeric($leftMb)){
                            if($leftMb<0){
                                $leftMb = 0;
                            }else{
                                $leftMb = sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down);
                            }
                        }

                        
                        $expiryDay = $packageInfo->expiryTime != 0?
                            floor(
                                (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24)
                                ):
                                "نامحدود";                                
                    }else{
                        $email = $clientsSettings[$settingKey]['email'];
                        $clientState = $list[$keys]->clientStats;
                        $emails = array_column($clientState,'email');
                        $emailKey = array_search($email,$emails);                    
             
                        if($clientState[$emailKey]->total != 0 || $clientState[$emailKey]->up != 0  ||  $clientState[$emailKey]->down != 0 || $clientState[$emailKey]->expiryTime != 0){
                            $upload = sumerize($clientState[$emailKey]->up);
                            $download = sumerize($clientState[$emailKey]->down);
                            $leftMb = $clientState[$emailKey]->total!=0?($clientState[$emailKey]->total - $clientState[$emailKey]->up - $clientState[$emailKey]->down):"نامحدود";
                            if(is_numeric($leftMb)){
                                if($leftMb<0){
                                    $leftMb = 0;
                                }else{
                                    $leftMb = sumerize($clientState[$emailKey]->total - $clientState[$emailKey]->up - $clientState[$emailKey]->down);
                                }
                            }
                            $totalUsed = sumerize($clientState[$emailKey]->up + $clientState[$emailKey]->down);
                            $total = $clientState[$emailKey]->total!=0?sumerize($clientState[$emailKey]->total):"نامحدود";
                            $expiryTime = $clientState[$emailKey]->expiryTime != 0?date("Y-m-d H:i:s",substr($clientState[$emailKey]->expiryTime,0,-3)):"نامحدود";
                            $expiryDay = $clientState[$emailKey]->expiryTime != 0?
                                floor(
                                    ((substr($clientState[$emailKey]->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                    ):
                                    "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                            $state = $clientState[$emailKey]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $remark = $email;
                        }
                        elseif($list[$keys]->total != 0 || $list[$keys]->up != 0  ||  $list[$keys]->down != 0 || $list[$keys]->expiryTime != 0){
                            $upload = sumerize($list[$keys]->up);
                            $download = sumerize($list[$keys]->down);
                            $leftMb = $list[$keys]->total!=0?($list[$keys]->total - $list[$keys]->up - $list[$keys]->down):"نامحدود";
                            if(is_numeric($leftMb)){
                                if($leftMb<0){
                                    $leftMb = 0;
                                }else{
                                    $leftMb = sumerize($list[$keys]->total - $list[$keys]->up - $list[$keys]->down);
                                }
                            }
                            $totalUsed = sumerize($list[$keys]->up + $list[$keys]->down);
                            $total = $list[$keys]->total!=0?sumerize($list[$keys]->total):"نامحدود";
                            $expiryTime = $list[$keys]->expiryTime != 0?date("Y-m-d H:i:s",substr($list[$keys]->expiryTime,0,-3)):"نامحدود";
                            $expiryDay = $list[$keys]->expiryTime != 0?
                                floor(
                                    ((substr($list[$keys]->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                    ):
                                    "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                            $state = $list[$keys]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $remark = $list[$keys]->remark;
                        }
                    }
                }

                $keys = json_encode(['inline_keyboard'=>[
                [
                    ['text'=>$remark??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"👦 اسم اکانت",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$state??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"📡 وضعیت حساب",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$upload?? " ",'callback_data'=>"wizwizch"],
                    ['text'=>"📥 آپلود",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$download??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"📤 دانلود",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$total??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"🔋حجم کلی",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$leftMb??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"⏳ حجم باقیمانده",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$expiryTime??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"📆 تاریخ اتمام",'callback_data'=>"wizwizch"],
                    ],
                [
                    ['text'=>$expiryDay??" ",'callback_data'=>"wizwizch"],
                    ['text'=>"🧭 تعداد روز باقیمانده",'callback_data'=>"wizwizch"],
                    ],
                [['text'=>"صفحه اصلی",'callback_data'=>"mainMenu"]]
                ]]);
                sendMessage("🔰مشخصات حسابت:",$keys,"MarkDown");
                break;
            }
        }
    }
    if(!$found){
         sendMessage("ای وای ، اطلاعاتت اشتباهه 😔",$cancelKey);
    }
}


if(preg_match('/renewAccount(\d+)/',$data,$match)){
    $oid = $match[1];
    
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $fid = $order['fileid'];
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ? AND `active` = 1");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $respd['price'];
    
    
    $hash_id = RandomString();
    $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` = 'RENEW_ACCOUNT' AND `state` = 'pending'");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $stmt->close();

    $time = time();
    $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                VALUES (?, ?, 'RENEW_ACCOUNT', ?, '0', '0', ?, ?, 'pending')");
    $stmt->bind_param("siiii", $hash_id, $from_id, $oid, $price, $time);
    $stmt->execute();
    $stmt->close();

    $keyboard = array();
    $temp = array();
    if($botState['cartToCartState'] == "on"){
	    $temp[] = ['text' => "💳 کارت به کارت مبلغ $price تومان ",  'callback_data' => "payRenewWithCartToCart$oid"];
    }
    if($botState['walletState'] == "on"){
	    $temp[] = ['text' => "پرداخت با موجودی مبلغ $price تومان 💰",  'callback_data' => "payRenewWithWallet$oid"];
    }
    if(count($temp) == 2){
        array_push($keyboard, $temp);
        $temp = array();
    }
    if($botState['zarinpal'] == "on"){
	    $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }

    array_push($keyboard, $temp);
        $keyboard[] = [['text'=>"⤵️ برگرد صفحه قبلی ",'callback_data'=>"mySubscriptions"]];



    editText($message_id,"لطفا با یکی از روش های زیر اکانت خود را تمدید کنید :",json_encode([
            'inline_keyboard' => $keyboard
        ]));
}
if(preg_match('/payRenewWithCartToCart(\d+)/',$data,$match)) {
    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 $walletwizwiz

✅ بعد از اینکه پرداختت تایید شد مبلغ مورد نظر به کیف پولت اضافه میشه!",$cancelKey, "HTML");
    exit;
}
if(preg_match('/payRenewWithCartToCart(\d+)/',$userInfo['step'],$match) and $text != $cancelText){
    $oid = $match[1];
    
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $fid = $order['fileid'];
    $remark = $order['remark'];
    $uid = $order['userid'];
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $userinfo = $stmt->get_result()->fetch_assoc();
    $userName = $userinfo['username'];
    $uname = $userinfo['name'];
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ? AND `active` = 1");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $respd['price'];
    $msg = "
    
    ☑️|درخواست تمدید با موفقیت ثبت شد 
🧑‍💻|بعد از بررسی و تایید مدیر سرویس شما تمدید می شود

/start
    
    ";
    sendMessage($msg,$removeKeyboard);
    sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
    // notify admin
    $msg = "
🏷 تمدید سرویس $remark ($price تومان)
✖کد کاربری: $uid
👤نام و نام خانوادگی: $uname
📧یوزرنیم: @$userName
 ";
    
    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approveRenewAcc{$uid}_{$match[1]}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decRenewAcc{$uid}_{$match[1]}"]
            ]
        ]
    ]);
    if(isset($update->message->photo)){
        sendPhoto($fileid, $msg,$keyboard, "HTML", $admin);
    }else{
        $msg .= "\n\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard,"HTML",$admin);
    }
    setUser();
}
if(preg_match('/approveRenewAcc(\d+)_(\d+)/',$data,$match)){
    $uid = $match[1];
    $oid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $fid = $order['fileid'];
    $remark = $order['remark'];
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
    $price = $respd['price'];

    editText($message_id,"سرویس $remark با موفقیت تمدید شد",$mainKeys);
    
    
    if($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, $days);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, $days);
    
	if(is_null($response)){
		alert('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید',true);
		exit;
	}
	$stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
	$newExpire = $expire_date + $days * 86400;
	$stmt->bind_param("ii", $newExpire, $oid);
	$stmt->execute();
	$stmt->close();
/*	$stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
	$stmt->bind_param("iiisii", $uid, $server_id, $inbound_id, $remark, $price, $time);
	$stmt->execute();
	$stmt->close();
*/    sendMessage("✅سرویس $remark با موفقیت تمدید شد",null,null,$uid);
    exit;
}
if(preg_match('/decRenewAcc(\d+)_(\d+)/',$data,$match)){
    $uid = $match[1];
    $oid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $fid = $order['fileid'];
    $remark = $order['remark'];
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
    $price = $respd['price'];

    editText($message_id,"تمدید $remark لغو شد",$mainKeys);

    sendMessage("تمدید سرویس $remark لغو شد",null,null,$uid);
    exit;
}
if(preg_match('/payRenewWithWallet(\d+)/', $data,$match)){
    $oid = $match[1];

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    $stmt->close();
    $fid = $order['fileid'];
    $remark = $order['remark'];
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
    $price = $respd['price'];


    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $userwallet = $stmt->get_result()->fetch_assoc()['wallet'];
    $stmt->close();

    if($userwallet < $price) {
        $needamount = $price - $userwallet;
        alert("💡موجودی کیف پول (".number_format($userwallet)." تومان) کافی نیست لطفا به مقدار ".number_format($needamount)." تومان شارژ کنید ",true);
        exit;
    }

    if($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, $days);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, $days);

	if(is_null($response)){
		alert('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید',true);
		exit;
	}
	$stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
	$newExpire = $expire_date + $days * 86400;
	$stmt->bind_param("ii", $newExpire, $oid);
	$stmt->execute();
	$stmt->close();
/*	$stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
	$stmt->bind_param("iiisii", $uid, $server_id, $inbound_id, $remark, $price, $time);
	$stmt->execute();
	$stmt->close();*/
	
	$stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
	$stmt->bind_param("ii", $price, $from_id);
	$stmt->execute();
	$stmt->close();
    editText($message_id, "✅سرویس $remark با موفقیت تمدید شد",$mainKeys);
    exit;
}



if(preg_match('/addNewPlan/',$userInfo['step']) and $text!=$cancelText){
    $catkey = [];
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent` =0 and `active`=1");
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();

    while ($cat = $cats->fetch_assoc()){
        $id = $cat['id'];
        $name = $cat['title'];
        $catkey[] = ["$id - $name"];
    }
    $catkey[] = [$cancelText];

    $step = checkStep('server_plans');

    if($step==1 and $text!=$cancelText){
        $msg = '🔰 لطفا قیمت پلن رو به تومان وارد کنید!';
        if(strlen($text)>1){
            $stmt = $connection->prepare("UPDATE `server_plans` SET `title`=?,`step`=2 WHERE `active`=0 and `step`=1");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $stmt->close();
            sendMessage($msg,$cancelKey);
        }
    } 
    if($step==2 and $text!=$cancelText){
        $msg = '🔰لطفا یه دسته از لیست زیر برا پلن انتخاب کن ';
        if(is_numeric($text)){
            $stmt = $connection->prepare("UPDATE `server_plans` SET `price`=?,`step`=3 WHERE `active`=0");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $stmt->close();
            sendMessage($msg,json_encode(['keyboard'=>$catkey, 'resize_keyboard'=>true]));
        }else{
            $msg = '‼️ لطفا یک مقدار عددی وارد کنید';
            sendMessage($msg,$cancelKey);
        }
    } 
    if($step==3 and $text!=$cancelText){
        $srvkey = [];
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1");
        $stmt->execute();
        $srvs = $stmt->get_result();
        $stmt->close();

        while($srv = $srvs->fetch_assoc()){
            $id = $srv['id'];
            $title = $srv['title'];
            $srvkey[] = ['text' => "$title", 'callback_data' => "selectNewPlanServer$id"];
        }
        $srvkey = array_chunk($srvkey,2);
        sendMessage("لطفا یکی از سرورها رو انتخاب کن 👇 ", json_encode([
                'inline_keyboard' => $srvkey]), "HTML");
        $inarr = 0;
        foreach ($catkey as $op) {
            if (in_array($text, $op) and $text != $cancelText) {
                $inarr = 1;
            }
        }
        if( $inarr==1 ){
            $input = explode(' - ',$text);
            $catid = $input[0];
            $stmt = $connection->prepare("UPDATE `server_plans` SET `catid`=?,`step`=50 WHERE `active`=0");
            $stmt->bind_param("i", $catid);
            $stmt->execute();
            $stmt->close();

            sendMessage($msg,$cancelKey);
        }else{
            $msg = '‼️ لطفا فقط یکی از گزینه های پیشنهادی زیر را انتخاب کنید';
            sendMessage($msg,$catkey);
        }
    } 
    if($step==50 and $text!=$cancelText and preg_match('/selectNewPlanServer(\d+)/', $data,$match)){
        $stmt = $connection->prepare("UPDATE `server_plans` SET `server_id`=?,`step`=51 WHERE `active`=0");
        $stmt->bind_param("i", $match[1]);
        $stmt->execute();
        $stmt->close();

        $keys = json_encode(['inline_keyboard'=>[
            [['text'=>"🎖پورت اختصاصی",'callback_data'=>"withSpecificPort"]],
            [['text'=>"🎗پورت اشتراکی",'callback_data'=>"withSharedPort"]]
            ]]);
        editText($message_id, "🔘|لطفا نوعیت پلن مورد نظر را انتخاب کنید", $keys);
    }
    if($step==51 and $text!=$cancelText and preg_match('/^with(Specific|Shared)Port/',$data,$match)){
        if($match[1] == "Shared"){
            editText($message_id, "📡 | لطفا پروتکل پلن مورد نظر را وارد کنید (vless | vmess | trojan)");
            $stmt = $connection->prepare("UPDATE `server_plans` SET `step`=60 WHERE `active`=0");
            $stmt->execute();
            $stmt->close();
        }
        elseif($match[1] == "Specific"){
            editText($message_id, "📡 | لطفا پروتکل پلن مورد نظر را وارد کنید (vless | vmess | trojan)");
            $stmt = $connection->prepare("UPDATE server_plans SET step=52 WHERE active=0");
            $stmt->execute();
            $stmt->close();
        }
    }
    if($step==60 and $text!=$cancelText){
        if($text != "vless" && $text != "vmess" && $text != "trojan"){
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید",$cancelKey);
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `protocol`=?,`step`=61 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();
        sendMessage("📅 | لطفا تعداد روز های اعتبار این پلن را وارد کنید:");
    }
    if($step==61 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `days`=?,`step`=62 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🔋 | لطفا مقدار حجم به GB - MB این پلن را وارد کنید: ( برای MB اعشاری بنویسید )");
    }
    if($step==62 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `volume`=?,`step`=63 WHERE `active`=0");
        $stmt->bind_param("d", $text);
        $stmt->execute();
        $stmt->close();
        sendMessage("🛡 | لطفا آیدی سطر کانکشن در پنل را وارد کنید:");
    }
    if($step==63 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `inbound_id`=?,`step`=64 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("لطفا ظرفیت تعداد اکانت رو پورت مورد نظر را وارد کنید");
    }
    if($step==64 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `acount`=?,`step`=65 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🧲 | لطفا تعداد چند کاربره این پلن را وارد کنید ( 0 نامحدود است )");
    }
    if($step==65 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        $stmt = $connection->prepare("UPDATE `server_plans` SET `limitip`=?,`step`=4 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        $msg = '🔻یه توضیح برای پلن مورد نظرت بنویس:';
        sendMessage($msg,$cancelKey); 
    }
    if($step==52 and $text!=$cancelText){
        if($text != "vless" && $text != "vmess" && $text != "trojan"){
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید",$cancelKey);
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `protocol`=?,`step`=53 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("📅 | لطفا تعداد روز های اعتبار این پلن را وارد کنید:");
    }
    if($step==53 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `days`=?,`step`=54 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🔋 | لطفا مقدار حجم به GB - MB این پلن را وارد کنید: ( برای MB اعشاری بنویسید )");
    }
    if($step==54 and $text!=$cancelText){
        if(!is_numeric($text)){
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        
        $stmt = $connection->prepare("UPDATE `server_plans` SET `volume`=?,`step`=55 WHERE `active`=0");
        $stmt->bind_param("d", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🔉 | لطفا نوع شبکه این پلن را در انتخاب کنید  (ws | tcp | grpc) :");
    }
    if($step==55 and $text!=$cancelText){
        if($text != "tcp" && $text != "ws" && $text != grpc){
            sendMessage("لطفا فقط نوع (ws | tcp | grpc) را وارد کنید");
            exit();
        }
        $stmt = $connection->prepare("UPDATE `server_plans` SET `type`=?,`step`=4 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();


        $msg = '🔻یه توضیح برای پلن مورد نظرت بنویس:';
        sendMessage($msg,$cancelKey); 
    }
    
    if($step==4 and $text!=$cancelText){
        $imgtxt = '☑️ | پنل با موفقیت ثبت و ایجاد شد ( لذت ببرید ) ';
        $stmt = $connection->prepare("UPDATE `server_plans` SET `descr`=?, `active`=1,`step`=10 WHERE `step`=4");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage($imgtxt,$removeKeyboard);
        sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$adminKeys);
        setUser();
    } 
    if($step==6 and $text!=$cancelText){
        if(preg_match('/seprator/',strtolower($text))){
            $stmt = $connection->prepare("UPDATE `server_plans` SET `fileid`='$fileid',`active`=1,`step`=10 WHERE `step`=6");
            $stmt->bind_param("s", $fileid);
            $stmt->execute();
            $stmt->close();

            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `active`=1 ORDER BY `id` DESC LIMIT 1");
            $stmt->execute();
            $id = $stmt->get_result()->fetch_assoc()['id'];
            $stmt->close();

            $accs = explode('seprator',$text);
            foreach ($accs as $acc){
                if(strlen($acc) > 5){
                    $stmt = $connection->prepare("INSERT INTO `server_accounts` (`id`, `fid`, `text`, `sold`, `active`) VALUES (NULL, ?, ?, '0', '1');");
                    $stmt->bind_param("ii", $id, $acc);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $msg = "✅️ اکانت های این پلن  با موفقیت ثبت شد";
                sendMessage($msg,$removeKeyboard);
            sendMessage("🏵 روی گزینه مورد نظرت کلیک کن:",$mainKeys);
            setUser();
        }else{
            $msg = '‼️ لطفا اکانت ها را با جداکننده معتبر ارسال کنید';
            sendMessage($msg,$cancelKey);
        }
    } 
}
if($data=="cantEditGrpc"){
    alert("نوعیت این کانفیگ رو تغییر داده نمیتونید!");
    exit();
}

if($data == 'backplan' and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if($res->num_rows==0){
        sendMessage( '❌ | هنوز هیچ پلنی ثبت نکردی ');
        exit;
    }
    $keyboard = [];
    while($cat = $res->fetch_assoc()){
        $id = $cat['id'];
        $title = $cat['title'];
        $keyboard[] = ['text' => "$title", 'callback_data' => "plansList$id"];
    }
    $keyboard[] = ['text' => "↪ برگشت", 'callback_data' => "managePanel"];
    $keyboard = array_chunk($keyboard,2);
    
    $msg = ' 😁 یکی از سرورها رو انتخاب کن که پلن هاشو تغییر بدیم';
    
    if(isset($data) and $data=='backplan') {
        editText($message_id, $msg, json_encode(['inline_keyboard'=>$keyboard]));
    }else { sendAction('typing');
        sendmessage($msg, json_encode(['inline_keyboard'=>$keyboard]));
    }
    
    
    exit;
}

if(preg_match('/plansList(\d+)/', $data,$match)){
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? ORDER BY`id` ASC");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if($res->num_rows==0){
        alert("متاسفانه، هیچ پلنی براش انتخاب نکردی 😑");
        exit;
    }else {
        $keyboard = [];
        while($cat = $res->fetch_assoc()){
            $id = $cat['id'];
            $title = $cat['title'];
            $keyboard[] = ['text' => "#$id $title", 'callback_data' => "planDetails$id"];
        }
        $keyboard = array_chunk($keyboard,2);
        $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "backplan"],];
        $msg = ' ▫️ یه پلن رو انتخاب کن بریم برای ادیت:';
        editText($message_id, $msg, json_encode(['inline_keyboard'=>$keyboard]), "HTML");
    }
    exit();
}
if(preg_match('/planDetails(\d+)/', $data,$match)){
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $pdResult = $stmt->get_result();
    $pd = $pdResult->fetch_assoc();
    $stmt->close();

    if($pdResult->num_rows == 0){
        alert("❌ | متاسفانه هیچ پلنی نداری");
        exit;
    }else {
        $id=$pd['id'];
        $name=$pd['title'];
        $price=$pd['price'];
        $acount =$pd['acount'];

        $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `fileid`=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $wizwizplanaccnumber = $stmt->get_result()->num_rows;
        $stmt->close();

        $srvid= $pd['server_id'];
        $msg = "
        
🔮 نام پلن: $name  /wizwizplanname$id
➖➖➖➖➖➖➖➖➖➖➖➖
🎗 تعداد اکانت های فروخته شده: $wizwizplanaccnumber
➖➖➖➖➖➖➖➖➖➖➖➖
✏️ ویرایش توضیحات: /wizwizplaneditdes$id
➖➖➖➖➖➖➖➖➖➖➖➖
💰 قیمت پلن : $price تومان  /wizwizplanrial$id
➖➖➖➖➖➖➖➖➖➖➖➖
♻️ دریافت لیست اکانت ها: /wizwizplanacclist$id
➖➖➖➖➖➖➖➖➖➖➖➖
✂️ حذف: /wizwizplandelete$id

⁮⁮ ⁮⁮ ⁮⁮
";
       if($pd['inbound_id'] != 0) $msg .= "🚪 تغییر ظرفیت کانفیگ: $acount /wizwizplanslimit$id";
       $keyboard = [[['text' => "↪ برگشت", 'callback_data' =>"plansList$srvid"],]];
       editText($message_id, $msg, json_encode([
                'inline_keyboard' => $keyboard
            ]), "HTML");
    }
    
}
if(preg_match('/wizwizplanacclist(\d+)/',$text,$match) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `fileid`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if($res->num_rows == 0){
        sendMessage('لیست خالی است');
        exit;
    }
    $txt = '';
    while($order = $res->fetch_assoc()){
		$suid = $order['userid'];
		$stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
        $stmt->bind_param("i", $suid);
        $stmt->execute();
        $ures = $stmt->get_result()->fetch_assoc();
        $stmt->close();


        $date = $order['date'];
        $remark = $order['remark'];
        $date = jdate('Y-m-d H:i', $date);
        $uname = $ures['name'];
        $sold = " 🚀 ".$uname. " ($date)";
        $accid = $order['id'];
        $orderLink = json_decode($order['link'],true);
        
        $txt = "$sold \n  ☑️ $remark \n";
        foreach($orderLink as $link){
            $txt .= "<code>".$link."</code> \n\n";
        }
        $txt .= "❗ $channelLock \n";
        sendMessage($txt, null, "HTML");
    }
}

if(preg_match('/wizwizplandelete(\d+)/',$text,$match) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    sendMessage("پلن رو برات حذفش کردم ☹️☑️", json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]));
}
if(preg_match('/wizwizplanname(\d+)/',$text) and ($from_id==$admin) && $text != $cancelText){
    setUser($text);
    sendMessage("🔅 یه اسم برا پلن جدید انتخاب کن:",$cancelKey);exit;
}
if(preg_match('/wizwizplanname(\d+)/',$userInfo['step'], $match) && $text != $cancelText){
    $stmt = $connection->prepare("UPDATE `server_plans` SET `title`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();
}
if(preg_match('/wizwizplanslimit(\d+)/',$text) and ($from_id==$admin) && $text != $cancelText){
    setUser($text);
    sendMessage("🔅 ظرفیت جدید برای پلن انتخاب کن:",$cancelKey);exit;
}
if(preg_match('/wizwizplanslimit(\d+)/',$userInfo['step'], $match) && $text != $cancelText){
    $stmt = $connection->prepare("UPDATE `server_plans` SET `acount`=? WHERE `id`=?");
    $stmt->bind_param("ii", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();
}
if(preg_match('/wizwizplaneditdes(\d+)/',$text) and ($from_id==$admin) && $text != $cancelText){
    setUser($text);
    sendMessage("🎯 توضیحاتت رو برام وارد کن:",$cancelKey);exit;
}
if(preg_match('/wizwizplaneditdes(\d+)/',$userInfo['step'], $match) && $text != $cancelText){
    $stmt = $connection->prepare("UPDATE `server_plans` SET `descr`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();


    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();
}
if(preg_match('/wizwizplanrial(\d+)/',$text) and ($from_id==$admin) && $text != $cancelText){
    setUser($text);
    sendMessage("🎯 شیطون قیمت و گرون کردی 😂 ، خب قیمت جدید و بزن ببینم :",$cancelKey);exit;
}
if(preg_match('/wizwizplanrial(\d+)/',$userInfo['step'], $match) && $text != $cancelText){
    if(is_numeric($text)){
        $stmt = $connection->prepare("UPDATE `server_plans` SET `price`=? WHERE `id`=?");
        $stmt->bind_param("ii", $text, $match[1]);
        $stmt->execute();
        $stmt->close();


        sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
        setUser();
    }else{
        sendMessage("بهت میگم قیمت وارد کن برداشتی یه چیز دیگه نوشتی 🫤 ( عدد وارد کن ) عجبا");
    }
}
if(($data == 'mySubscriptions' or preg_match('/changeOrdersPage(\d+)/',$data, $match) )&& ($botState['sellState']=="on" || $from_id ==$admin)){
    $results_per_page = 50;  
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `status`=1");  
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $number_of_result= $stmt->get_result()->num_rows;
    $stmt->close();

    $number_of_page = ceil ($number_of_result / $results_per_page);
    $page = $match[1] ??1;
    $page_first_result = ($page-1) * $results_per_page;  
    
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `status`=1 ORDER BY `id` DESC LIMIT ?, ?");
    $stmt->bind_param("iii", $from_id, $page_first_result, $results_per_page);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();


    if($orders->num_rows==0){
        alert('عزیزم هیچ سفارشی نداری 🙁 باید یه کانفیگ خریداری کنی');
        exit;
    }
    $keyboard = [];
    while($cat = $orders->fetch_assoc()){
        $id = $cat['id'];
        $remark = $cat['remark'];
        $keyboard[] = ['text' => "$remark", 'callback_data' => "orderDetails$id"];
    }
    $keyboard = array_chunk($keyboard,2);
    
    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($number_of_page/$results_per_page);
    $lpm1 = $lastpage - 1;
    
    $buttons = [];
    if ($prev > 0) $buttons[] = ['text' => "◀", 'callback_data' => "changeOrdersPage$prev"];

    if ($next > 0 and $page != $number_of_page) $buttons[] = ['text' => "➡", 'callback_data' => "changeOrdersPage$next"];   
    $keyboard[] = $buttons;
    $keyboard[] = [['text'=>"⤵️ برگرد صفحه قبلی ",'callback_data'=>"mainMenu"]];
    
    $msg = ' 🔅 یکی از سرویس هاتو انتخاب کن و مشخصات کاملش رو ببین :';
    
    if(isset($data)) {
        editText($message_id, $msg, json_encode(['inline_keyboard'=>$keyboard]));
    }else { sendAction('typing');
        sendMessage($msg, json_encode(['inline_keyboard'=>$keyboard]));
    }
    exit;
}
if(preg_match('/orderDetails(\d+)/', $data, $match) && ($botState['sellState']=="on" || ($from_id == $admin || $userInfo['isAdmin'] == true))){
    $id = $match[1];
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `id`=?");
    $stmt->bind_param("ii", $from_id, $id);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();


    if($order->num_rows==0){
        sendMessage("😞 | هیچ سفارشی نداری");exit;
    }else {
        $order = $order->fetch_assoc();
        $fid = $order['fileid']; 
    	$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1"); 
        $stmt->bind_param("i", $fid);
        $stmt->execute();
        $respd = $stmt->get_result();
        $stmt->close();


    	if($respd){
    	    $respd = $respd->fetch_assoc(); 
    	    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
            $stmt->bind_param("i", $respd['catid']);
            $stmt->execute();
            $cadquery = $stmt->get_result();
            $stmt->close();


    	    if($cadquery) {
    	        $catname = $cadquery->fetch_assoc()['title'];
        	    $name = $catname." ".$respd['title'];
    	    }else $name = "$id";
        	
    	}else $name = "$id";
    	
        $date = jdate("Y-m-d H:i",$order['date']);
        $expire_date = jdate("Y-m-d H:i",$order['expire_date']);
        $remark = $order['remark'];
        $acc_link = json_decode($order['link'],true);
        $protocol = $order['protocol'];
        $server_id = $order['server_id'];
        $inbound_id = $order['inbound_id'];
        $link_status = $order['expire_date'] > time()  ? 'فعال' : 'غیرفعال';
        $price = $order['amount'];
        
        $response = getJson($server_id)->obj;
        if($inbound_id == 0) {
            foreach($response as $row){
                if($row->remark == $remark) {
                    $total = $row->total;
                    $up = $row->up;
                    $down = $row->down; 
                    $netType = json_decode($row->streamSettings)->network;
                    $security = json_decode($row->streamSettings)->security;
                    break;
                }
            }
        }else {
            foreach($response as $row){
                if($row->id == $inbound_id) {
                    $netType = json_decode($row->streamSettings)->network;
                    $security = json_decode($row->streamSettings)->security;
                    $clients = $row->clientStats;
                    foreach($clients as $client) {
                        if($client->email == $remark) {
                            $total = $client->total;
                            $up = $client->up;
                            $down = $client->down; 
                            break;
                        }
                    }
                    break;
                }
            }
        }
        $leftgb = round( ($total - $up - $down) / 1073741824, 2) . " GB";
        $msg = "";

foreach($acc_link as $acc_link){
    $msg .= "\n 🌐 <code>$acc_link</code> \n";
}
$msg .= "\n";

if($inbound_id == 0){
    if($protocol == 'trojan') {
        if($security == "xtls"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => "cantEditTrojan"],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$id}_trojan"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
                ]:[])
            ];
        }else{
            $keyboard = [
            [
			    ['text' => "$name", 'callback_data' => "wizwizch"],
                ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
			[
                ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
			],
			[
                ['text' => $netType , 'callback_data' => "cantEditTrojan"],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
            [
                ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
            ],
            [
                ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$id}_trojan"],
                ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vmess"],
                ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vless"],
            ],
            ($price!=0?[
                ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
            ]:[])

        ];
        }
    }else {
        if($netType == "grpc"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => "cantEditGrpc"],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vmess"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
                ]:[])
    
            ];
        }
        elseif($netType == "tcp" && $security == "xtls"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => ($security=="xtls"?"cantEditGrpc":"changeNetworkType{$fid}_{$id}")],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$id}_trojan"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
                ]:[])
    
            ];
        }
        else{

            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => ($security=="xtls"?"cantEditGrpc":"changeNetworkType{$fid}_{$id}")],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$id}_trojan"],
                    ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vmess"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$id}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
                ]:[])
    
            ];
        }
    }
}else{
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
            [
			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
            ],
            [
            ['text' => " $protocol ☑️", 'callback_data' => "wizwizch"],
            ],
            ($price != 0?[
                ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
            ]:[])
    ];
}
        $stmt= $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();


        $extrakey = [];
        $keyboard[] = $extrakey;
        $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "mySubscriptions"]];
        editText($message_id, $msg, json_encode([
                    'inline_keyboard' => $keyboard
                ]), "HTML");
        }
    
}


if(preg_match('/changeNetworkType(\d+)_(\d+)/', $data, $match)){
    $fid = $match[1];
    $oid = $match[2];
    
	$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1"); 
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();


	if($respd){
		$respd = $respd->fetch_assoc(); 
		$stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
        $stmt->bind_param("i", $respd['catid']);
        $stmt->execute();
        $cadquery = $stmt->get_result();
        $stmt->close();


		if($cadquery) {
			$catname = $cadquery->fetch_assoc()['title'];
			$name = $catname." ".$respd['title'];
		}else $name = "$id";
		
	}else $name = "$id";

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id`=?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $date = jdate("Y-m-d H:i",$order['date']);
    $expire_date = jdate("Y-m-d H:i",$order['expire_date']);
    $remark = $order['remark'];
    $acc_link = json_decode($order['link'],true);
    $protocol = $order['protocol'];
    $server_id = $order['server_id'];
    $price = $order['amount'];
    
    $response = getJson($server_id)->obj;
    foreach($response as $row){
        if($row->remark == $remark) {
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $port = $row->port;
            $uniqid = ($protocol == 'trojan') ? json_decode($row->settings)->clients[0]->password : json_decode($row->settings)->clients[0]->id;
            $netType = json_decode($row->streamSettings)->network; 
            $netType = ($netType == 'tcp') ? 'ws' : 'tcp';
        break;
        }
    }

    if($protocol == 'trojan') $netType = 'tcp';
    $leftgb = round( ($total - $up - $down) / 1073741824, 2) . " GB";

    $update_response = editInbound($server_id, $uniqid, $remark, $protocol, $netType);
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType);
    $msg = "";
    foreach($vraylink as $vray_link){
        $msg .= " \n 🌐 <code>$vray_link</code> \n";
    }
$msg .= "\n";

            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
            [
			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                ['text' => $netType , 'callback_data' => ($security=="xtls"?"cantEditGrpc":"changeNetworkType{$fid}_{$id}")],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
            [
                ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
            ],
            [
            ['text' => $protocol == 'trojan' ? '  ☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_trojan"],
            ['text' => $protocol == 'vmess' ? '  ☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vmess"],
            ['text' => $protocol == 'vless' ? '  ☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
            ],
            ($price != 0?[
                ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$id" ],
            ]:[])

    ];
    
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=$server_id");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $extrakey = [];
    $keyboard[] = $extrakey;
    $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "mySubscriptions"]];
    
    editText($message_id, $msg, json_encode(['inline_keyboard'=>$keyboard]), "HTML");
    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $message_id,
		'reply_markup' => json_encode([
            'inline_keyboard' => $keyboard
        ])
	
    ]);
    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("UPDATE `orders_list` SET `protocol`=?,`link`=? WHERE `id`=?");
    $stmt->bind_param("ssi", $protocol, $vray_link, $id);
    $stmt->execute();
    $stmt->close();


}

if(preg_match('/changeAccProtocol(\d+)_(\d+)_(.*)/', $data,$match)){
    $fid = $match[1];
    $oid = $match[2];
    $protocol = $match[3];

	$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1"); 
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();


	if($respd){
		$respd = $respd->fetch_assoc(); 
		$stmt= $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
        $stmt->bind_param("i", $respd['catid']);
        $stmt->execute();
        $cadquery = $stmt->get_result();
        $stmt->close();


		if($cadquery) {
			$catname = $cadquery->fetch_assoc()['title'];
			$name = $catname." ".$respd['title'];
		}else $name = "$id";
		
	}else $name = "$id";

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id`=?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $date = jdate("Y-m-d H:i",$order['date']);
    $expire_date = jdate("Y-m-d H:i",$order['expire_date']);
    $remark = $order['remark'];
    $acc_link = $order['link'];
    $server_id = $order['server_id'];
    $price = $order['amount'];
    
    $response = getJson($server_id)->obj;
    foreach($response as $row){
        if($row->remark == $remark) {
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $port = $row->port;
            $netType = json_decode($row->streamSettings)->network;
            $security = json_decode($row->streamSettings)->security;
            break;
        }
    }
    if($protocol == 'trojan') $netType = 'tcp';
    $uniqid = generateRandomString(42,$protocol); 
    $leftgb = round( ($total - $up - $down) / 1073741824, 2) . " GB";

    $update_response = editInbound($server_id, $uniqid, $remark, $protocol, $netType, $security);
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType);
    

    $msg = "";
    foreach($vraylink as $vray_link){
            $msg .= "\n 🌐 <code>$vray_link</code> \n";
    }
    $msg .= "\n";
    if($protocol == 'trojan') {
        if($security == "xtls"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => "cantEditTrojan"],
                   ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_trojan"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$oid" ],
                ]:[])
    
            ];
        }else{
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
            [
			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
            ],
            [
			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                ['text' => $netType , 'callback_data' => "cantEditTrojan"],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
            [
                ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
            ],
            [
                ['text' => $protocol == 'trojan' ? '  ☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_trojan"],
                ['text' => $protocol == 'vmess' ? '  ☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vmess"],
                ['text' => $protocol == 'vless' ? '  ☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
            ],
            ($price != 0?[
                ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$oid" ],
            ]:[])
        
        ];
        }
    }else {
        if($netType == "grpc"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => "cantEditGrpc"],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'vmess' ? '  ☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vmess"],
                    ['text' => $protocol == 'vless' ? '  ☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$oid" ],
                ]:[])
    
    
            ];
        }elseif($netType == "tcp" && $security == "xtls"){
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => ($security=="xtls"?"cantEditGrpc":"changeNetworkType{$fid}_{$id}")],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_trojan"],
                    ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$oid" ],
                ]:[])
    
            ];
        }
        else{
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => " 🚀 نام پلن:", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$remark", 'callback_data' => "wizwizch"],
                    ['text' => "🔮 نام سرویس", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ خرید: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => "⏰  تاریخ انقضاء: ", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => "⏳ حجم باقیمانده:", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => "➖ میتونید نوع شبکه و پروتکل را تغییر بدید  ➖", 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $netType , 'callback_data' => ($security=="xtls"?"cantEditGrpc":"changeNetworkType{$fid}_{$order['id']}")],
                ['text' => "📡 نوع شبکه: ", 'callback_data' => "wizwizch"],
            ],
                [
                    ['text' => "پروتکل فعال", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => $protocol == 'trojan' ? '  ☑️ trojan' : 'trojan', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_trojan"],
                    ['text' => $protocol == 'vmess' ? '  ☑️ vmess' : 'vmess', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vmess"],
                    ['text' => $protocol == 'vless' ? '  ☑️ vless' : 'vless', 'callback_data' => "changeAccProtocol{$fid}_{$oid}_vless"],
                ],
                ($price != 0?[
                    ['text' => '♻ تمدید سرویس', 'callback_data' => "renewAccount$oid" ],
                ]:[])
    
            ];
        }
    }
    $stmt= $connection->prepare("SELECT * FROM `server_info` WHERE `id`=$server_id");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $extrakey = [];
    $keyboard[] = $extrakey;
    
    $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "mySubscriptions"]];
    
    editText($message_id, $msg, json_encode(['inline_keyboard'=>$keyboard]),"HTML");
    bot('editMessageReplyMarkup',[
		'chat_id' => $from_id,
		'message_id' => $message_id,
		'reply_markup' => json_encode([
            'inline_keyboard' => $keyboard
        ])
	
    ]);
    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("UPDATE `orders_list` SET `protocol`=?,`link`=? WHERE `id`=?");
    $stmt->bind_param("ssi", $protocol, $vray_link, $oid);
    $stmt->execute();
    $stmt->close();

}

if($data == 'cantEditTrojan'){
    alert("پروتکل تروجان فقط نوع شبکه TCP را دارد");
    exit;
}

if($data=='categoriesSetting' and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `active`=1 AND `parent`=0");
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();


    if($cats->num_rows == 0){
        $msg = "❌ | متاسفانه هیچ دسته ای نداری";
    }else {
        $msg = '';
        while($cty = $cats->fetch_assoc()){
            $id = $cty['id'];
            $cname = $cty['title'];
            $msg .= "
💠 نام دسته : $cname
✏️ ویرایش دسته : /wizwizcategoryedit$id
✂️ حذف دسته : /wizwizcategorydelete$id
➖➖➖➖➖➖➖➖
";
			if(strlen($msg) > 3950){
                sendMessage($msg);
                $msg = '';
            }
        }
    }
    sendMessage($msg, null, null);
}
if($data=='addNewCategory' and (($from_id == $admin || $userInfo['isAdmin'] == true))){
    setUser($data);
    $stmt = $connection->prepare("DELETE FROM `server_categories` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();


    $sql = "INSERT INTO `server_categories` VALUES (NULL, 0, '', 0,2,0);";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $stmt->close();


    $msg = '▪️یه اسم برای دسته بندی وارد کن:';
    sendMessage($msg,$cancelKey);
    exit;
}
if(preg_match('/addNewCategory/',$userInfo['step']) and $text!=$cancelText){
    $step = checkStep('server_categories');
    if($step==2 and $text!=$cancelText ){
        
        $stmt = $connection->prepare("UPDATE `server_categories` SET `title`=?,`step`=4,`active`=1 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        sendMessage($stmt->error);
        $stmt->close();


        $msg = 'یه دسته بندی جدید برات ثبت کردم 🙂☑️';
        sendMessage($msg,$removeKeyboard);
        sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$adminKeys);
        setUser();
    }
}
if(preg_match('/wizwizcategorydelete(.*)/',$text, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("DELETE FROM `server_categories` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("دسته بندی رو برات حذفش کردم ☹️☑️",json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]));
}
if(preg_match('/wizwizcategoryedit/',$text) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    setUser($text);
    sendMessage("〽️ یه اسم جدید برا دسته بندی انتخاب کن:",$cancelKey);exit;
}
if(preg_match('/wizwizcategoryedit(.*)/',$userInfo['step'], $match) && $text != $cancelText){
    $stmt = $connection->prepare("UPDATE `server_categories` SET `title`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();
}

if($data=='serversSetting' and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1");
    $stmt->execute();
    $cats= $stmt->get_result();
    $stmt->close();


    if($cats->num_rows == 0){
        $msg = "❌ | هنوز هیچ سروری ثبت نکردی";
    }else {
        $msg = '';
        while($cty = $cats->fetch_assoc()){
            $id = $cty['id'];
            $cname = $cty['title'];
            $flagwizwiz = $cty['flag'];
            $remarkwizwiz = $cty['remark'];
            $ucount = $cty['ucount'];
            $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $serverTypeInfo= $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $portType = $serverTypeInfo['port_type']=="auto"?"خودکار":"تصادفی";
            $serverType = $serverTypeInfo['type']=="sanaei"?"سنایی - علیرضا":"ساده";
            $msg .= "
❕نام سرور : $cname 
➖➖➖➖➖➖➖➖
🚩 پرچم سرور : $flagwizwiz 
➖➖➖➖➖➖➖➖
📣 ریمارک سرور : $remarkwizwiz 
➖➖➖➖➖➖➖➖
〽️ تعداد : $ucount
➖➖➖➖➖➖➖➖
🔅تغییر آیپی های سرور : /changesServerIp$id
➖➖➖➖➖➖➖➖
🔅نوعیت سرور : $serverType /changeServerType$id
➖➖➖➖➖➖➖➖
🔅نوعیت پورت : $portType /changePortType$id
➖➖➖➖➖➖➖➖
🔅ویرایش نام سرور : /editServerName$id
➖➖➖➖➖➖➖➖
🔅ویرایش ظرفیت سرور : /editServerMax$id
➖➖➖➖➖➖➖➖
🔅ویرایش ریمارک سرور : /editServerRemark$id
➖➖➖➖➖➖➖➖
🔅ویرایش پرچم سرور : /editServerFlag$id
➖➖➖➖➖➖➖➖
✂️ حذف سرور : /wizwizdeleteserver$id
🔻🔺🔻🔺🔻🔺🔻🔺🔻
"; 
			if(strlen($msg) > 3950){
                sendMessage($msg);
                $msg = '';
            }
        }
    }
    sendMessage($msg);
}
if(preg_match('/^\/changesServerIp(\d+)/',$text,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $serverIp= $stmt->get_result()->fetch_assoc()['ip']??"اطلاعاتی یافت نشد";
    $stmt->close();
    
    sendMessage("لیست آیپی های فعلی: \n$serverIp\nلطفا آیپی های جدید را در خط های جدا بفرستید\n\nبرای خالی کردن متن /empty را وارد کنید",$cancelKey,null,null,null);
    setUser($text);
    exit();
}
if(preg_match('/^\/changesServerIp(\d+)/',$userInfo['step'],$match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText){
    $stmt = $connection->prepare("UPDATE `server_config` SET `ip` = ? WHERE `id`=?");
    if($text == "/empty") $text = "";
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();
    sendMessage("☑️ | 😁 با موفقیت ذخیره شد",$removeKeyboard);
    setUser();
    exit();
}
if(preg_match('/^\/changePortType(\d+)/',$text,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("UPDATE `server_config` SET `port_type` = IF(`port_type` = 'auto', 'random', 'auto') WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    sendMessage("نوعیت پورت سرور مورد نظر با موفقیت تغییر کرد",json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]));
}
if(preg_match('/^\/changeServerType(\d+)/',$text,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    sendMessage("🔰 نکته مهم:

🔹 اگر از پنل سنایی نسخه v1.1.1 و علیرضا از نسخه 0.4.2 بالاتر استفاده میکنید لطفا نوع پنل را ( سنایی - علیرضا ) انتخاب کنید 
🔸 اگر از سنایی نسخه 1.0.9 و علیرضا نسخه 0.3.2 به پایین استفاده میکنید  لطفا نوع پنل را ( ساده ) انتخاب کنید 
🔘 همچنین  پنل های نیدوکا - وکسیلو ( چینی ) برای تمامی نسخه ها می توانید نوع پنل را ( ساده ) انتخاب کنید 
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
📣 حتما نوع پنل را انتخاب کنید وگرنه پنل کار نمی کند! ( و حجم و تاریخ را نامحدود می زند )
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮

",json_encode(['inline_keyboard'=>[
        [['text'=>"ساده",'callback_data'=>"chhangeServerTypenormal_" . $match[1]],['text'=>"سنایی - علیرضا",'callback_data'=>"chhangeServerTypesanaei_" . $match[1]]]
        ]]));
    exit();
}
if(preg_match('/^chhangeServerType(\w+)_(\d+)/',$data,$match) && ($from_id == $admin || $userInfo['isAdmin'] == true)){
    editText($message_id, "☑️ | 😁 با موفقیت ذخیره شد");
    $stmt = $connection->prepare("UPDATE `server_config` SET `type` = ? WHERE `id`=?");
    $stmt->bind_param("si",$match[1], $match[2]);
    $stmt->execute();
    $stmt->close();
}
if($data=='addNewServer' and (($from_id == $admin || $userInfo['isAdmin'] == true))){
    setUser('addserverName');
    sendMessage("مرحله اول: 
▪️یه اسم برا سرورت انتخاب کن:",$cancelKey);
    exit();
}
if($userInfo['step'] == 'addserverName' and $text != $cancelText) {
	sendMessage('مرحله دوم: 
▪️ظرفیت تعداد ساخت کانفیگ رو برای سرورت مشخص کن ( عدد باشه )');
    $data = array();
    $data['title'] = $text;

    setUser('addServerUCount' . json_encode($data,JSON_UNESCAPED_UNICODE));
}
if(preg_match('/^addServerUCount(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['ucount'] = $text;

    sendMessage("مرحله سوم: 
▪️یه اسم ( ریمارک ) برا کانفیگ انتخاب کن:
 ( به صورت انگیلیسی و بدون فاصله )
");
    setUser('addServerRemark' . json_encode($data,JSON_UNESCAPED_UNICODE));
}
if(preg_match('/^addServerRemark(.*)/',$userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['remark'] = $text;

    sendMessage("مرحله چهارم:
▪️لطفا یه ( ایموجی پرچم 🇮🇷 ) برا سرورت انتخاب کن:");
    setUser('addServerFlag' . json_encode($data,JSON_UNESCAPED_UNICODE));
}
if(preg_match('/^addServerFlag(.*)/',$userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['flag'] = $text;

    sendMessage("مرحله پنجم:

▪️لطفا آدرس پنل رو به صورت مثال زیر وارد کن:
❕https://yourdomain.com:54321
❕https://yourdomain.com:54321/path
❗️http://125.12.12.36:54321
❗️http://125.12.12.36:54321/path

اگر سرور مورد نظر با دامنه و ssl هست از مثال ( ❕) استفاده کنید
اگر سرور مورد نظر با ip و بدون ssl هست از مثال ( ❗️) استفاده کنید
");
    setUser('addServerPanelUrl' . json_encode($data,JSON_UNESCAPED_UNICODE));
}
if(preg_match('/^addServerPanelUrl(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['panel_url'] = $text;
    setUser('addServerIp' . json_encode($data,JSON_UNESCAPED_UNICODE));
    sendMessage( "
🔅 لطفا آیپی پنل را وارد کنید:\n
❗️دامنه یا ip های مورد نظر را در یک خط و زیر هم ارسال کنید برای مثال:\n
google.com
sub.test3.com
45.257.21.4

🔻برای خالی گذاشتن متن /empty را وارد کنید
");
}
if(preg_match('/^addServerIp(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['panel_ip'] = $text;
    setUser('addServerSni' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "🔅 لطفا sni پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
}
if(preg_match('/^addServerSni(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['sni'] = $text;
    setUser('addServerHeaderType' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "🔅 لطفا header type پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
}
if(preg_match('/^addServerHeaderType(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['header_type'] = $text;
    setUser('addServerRequestHeader' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "🔅 لطفا request header پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
}
if(preg_match('/^addServerRequestHeader(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['request_header'] = $text;
    setUser('addServerResponseHeader' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "🔅 لطفا response header پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
}
if(preg_match('/^addServerResponseHeader(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['response_header'] = $text;
    setUser('addServerSecurity' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "🔅 لطفا security پنل را وارد کنید

⚠️ توجه: برای استفاده از tls یا xtls لطفا کلمه tls یا xtls رو تایپ کنید در غیر این صورت 👇
\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
}
if(preg_match('/^addServerSecurity(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['security'] = $text;
    setUser('addServerTlsSetting' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("
    🔅 لطفا tls|xtls setting پنل را وارد کنید🔻برای خالی گذاشتن متن /empty را وارد کنید 

⚠️ لطفا تنظیمات سرتیفیکیت رو با دقت انجام بدید مثال:
▫️serverName: yourdomain
▫️certificateFile: /root/cert.crt
▫️keyFile: /root/private.key
\n
"
        .'<b>tls setting:</b> <code>{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}]}</code>'
        ."\n".'<b>xtls setting:</b> <code>{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}],"alpn": []}</code>', null, "HTML");
}
if(preg_match('/^addServerTlsSetting(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['tls_setting'] = $text;
    setUser('addServerPanelUser' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "مرحله ششم: 
▪️لطفا یوزر پنل را وارد کنید:");
}
if(preg_match('/^addServerPanelUser(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    $data = json_decode($match[1],true);
    $data['panel_user'] = $text;
    setUser('addServerPanePassword' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage( "مرحله هفتم: 
▪️لطفا پسورد پنل را وارد کنید:");
}
if(preg_match('/^addServerPanePassword(.*)/',$userInfo['step'],$match) and $text != $cancelText) {
    sendMessage("⏳ در حال ورود به اکانت ...");
    $data = json_decode($match[1],true);

    $title = $data['title'];
    $ucount = $data['ucount'];
    $remark = $data['remark'];
    $flag = $data['flag'];

    $panel_url = $data['panel_url'];
    $ip = $data['panel_ip']!="/empty"?$data['panel_ip']:"";
    $sni = $data['sni']!="/empty"?$data['sni']:"";
    $header_type = $data['header_type']!="/empty"?$data['header_type']:"none";
    $request_header = $data['request_header']!="/empty"?$data['request_header']:"";
    $response_header = $data['response_header']!="/empty"?$data['response_header']:"";
    $security = $data['security']!="/empty"?$data['security']:"none";
    $tlsSettings = $data['tls_setting']!="/empty"?$data['tls_setting']:"";
    $serverName = $data['panel_user'];
    $serverPass = $text;
    $loginUrl = $panel_url . '/login';
    
    $postFields = array(
        "username" => $serverName,
        "password" => $serverPass
        );
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tempCookie.txt');
    $loginResponse = json_decode(curl_exec($ch),true);
    curl_close($ch);
    if($loginResponse['success']){
        $cookie = file_get_contents("tempCookie.txt");
        preg_match('/\ssession\s(.*)/',$cookie,$CookieInfo);
        $cookie = $CookieInfo[1];
        unlink("tempCookie.txt");
    }else{
        file_put_contents("usersteps/$from_id.txt",'addServerPanelUser' . json_encode($data, JSON_UNESCAPED_UNICODE));
        sendMessage( "
        اطلاعاتی که وارد کردی اشتباهه 😂

❗️لطفا مجدد پسورد سرور رو وارد کن: 🥴
⚠️ اگه دیدی اینبارم نشد لغو کن از اول سرور رو ثبت کن احتمالا یوزرت رو اول راه اشتباه وارد کردی
        ");
        exit();
    }

    $stmt = $connection->prepare("INSERT INTO `server_info` VALUES (NULL,?,?,?,?,1)");
    $stmt->bind_param("siss", $title, $ucount, $remark, $flag);
    $stmt->execute();
    $rowId = $stmt->insert_id;
    $stmt->close();


    $stmt = $connection->prepare("INSERT INTO `server_config` (`id`, `panel_url`, `ip`, `sni`, `header_type`, `request_header`, `response_header`, `security`, `tlsSettings`, `cookie`, `username`, `password`)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssss", $rowId, $panel_url, $ip, $sni, $header_type, $request_header, $response_header, $security, $tlsSettings, $cookie, $serverName, $serverPass);
    $stmt->execute();
    $rowId = $stmt->insert_id;
    $stmt->close();

    sendMessage(" تبریک ; سرورت رو ثبت کردی 🥹",$removeKeyboard);

    sendMessage("🔰 نکته مهم:

🔹 اگر از پنل سنایی نسخه v1.1.1 و علیرضا از نسخه 0.4.2 بالاتر استفاده میکنید لطفا نوع پنل را ( سنایی - علیرضا ) انتخاب کنید 
🔸 اگر از سنایی نسخه 1.0.9 و علیرضا نسخه 0.3.2 به پایین استفاده میکنید  لطفا نوع پنل را ( ساده ) انتخاب کنید 
🔘 همچنین  پنل های نیدوکا - وکسیلو ( چینی ) برای تمامی نسخه ها می توانید نوع پنل را ( ساده ) انتخاب کنید 
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
📣 حتما نوع پنل را انتخاب کنید وگرنه پنل کار نمی کند! ( و حجم و تاریخ را نامحدود می زند )
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮

",json_encode(['inline_keyboard'=>[
        [['text'=>"ساده",'callback_data'=>"chhangeServerTypenormal_" . $rowId],['text'=>"سنایی - علیرضا",'callback_data'=>"chhangeServerTypesanaei_" . $rowId]]
        ]]));
    sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$adminKeys);
    setUser();
}
if(preg_match('/wizwizdeleteserver(\d+)/',$text,$match) and ($from_id == $admin || $userInfo['isAdmin'] == true)){
    $stmt = $connection->prepare("DELETE FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $connection->prepare("DELETE FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("🙂 سرور رو چرا حذف کردی اخه ...",json_encode(['inline_keyboard'=>[[['text'=>"صفحه اصلی 🏘",'callback_data'=>"mainMenu"]]]]));
}
if(preg_match('/^\/editServer(\D+)(\d+)/',$text,$match) && $text != $cancelText){
    switch($match[1]){
        case "Name":
            $txt ="اسم";
            break;
        case "Max":
            $txt = "ظرفیت";
            break;
        case "Remark":
            $txt ="ریمارک";
            break;
        case "Flag":
            $txt = "پرچم";
            break;
    }
    sendMessage("❗️لطفا " . $txt . " جدید را وارد کنید",$cancelKey);
    setUser($text);
}
if(preg_match('/^\/editServer(\D+)(\d+)/',$userInfo['step'],$match) && $text != $cancelText){
    switch($match[1]){
        case "Name":
            $txt ="title";
            break;
        case "Max":
            $txt = "ucount";
            break;
        case "Remark":
            $txt ="remark";
            break;
        case "Flag":
            $txt = "flag";
            break;
    }
    
    $stmt = $connection->prepare("UPDATE `server_info` SET `$txt`=? WHERE `id`=?");
    $stmt->bind_param("si",$text, $match[2]);
    $stmt->execute();
    $stmt->close();

    
    sendMessage("☑️ | 😁 با موفقیت ذخیره شد",$removeKeyboard);
    setUser();
}


if($data == "managePanel" and (($from_id == $admin || $userInfo['isAdmin'] == true))){
    
    setUser();
    $msg = '👤 به بخش مدیریت خوشومدی 
🤌 هرچی نیاز داشتی میتونی اینجا طبق نیازهات اضافه و تغییر بدی !

🚪 /start

';
    editText($message_id, $msg, $adminKeys);
}


if($data == 'reciveApplications') {
    $stmt = $connection->prepare("SELECT * FROM `needed_sofwares` WHERE `status`=1");
    $stmt->execute();
    $respd= $stmt->get_result();
    $stmt->close();

    $keyboard = [];
    while($file =  $respd->fetch_assoc()){
        $link = $file['link'];
        $title = $file['title'];
        $keyboard[] = ['text' => "$title", 'url' => $link];
    }
    $keyboard[] = ['text'=>"⤵️ برگرد صفحه قبلی ",'callback_data'=>"mainMenu"];
    $keyboard = array_chunk($keyboard,1);
    editText($message_id, "
🔸می توانید به راحتی همه فایل ها را (به صورت رایگان) دریافت کنید
📌 شما میتوانید برای راهنمای اتصال به سرویس کانال رسمی مارا دنبال کنید و همچنین از دکمه های زیر میتوانید برنامه های مورد نیاز هر سیستم عامل را دانلود کنید

✅ پیشنهاد ما برنامه V2rayng است زیرا کار با آن ساده است و برای تمام سیستم عامل ها قابل اجرا است، میتوانید به بخش سیستم عامل مورد نظر مراجعه کنید و لینک دانلود را دریافت کنید
", json_encode(['inline_keyboard'=>$keyboard]));
}




if ($text == $cancelText) {
    setUser();
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();

    sendMessage('⏳ در حال انتظار ...',$removeKeyboard);
    sendMessage('🏵 روی گزینه مورد نظرت کلیک کن:',$mainKeys);
}

?>
