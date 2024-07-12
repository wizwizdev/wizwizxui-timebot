<?php
include_once "settings/values.php";
include_once 'settings/jdf.php';
include_once 'baseInfo.php';

$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
if($connection->connect_error){
    exit("error " . $connection->connect_error);  
}
$connection->set_charset("utf8mb4");

function bot($method, $datas = []){
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
function editKeys($keys = null, $msgId = null, $ci = null){
    global $from_id,$message_id;
    $ci = $ci??$from_id;
    $msgId = $msgId??$message_id;
   
    bot('editMessageReplyMarkup',[
		'chat_id' => $ci,
		'message_id' => $msgId,
		'reply_markup' => $keys
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
function getFileUrl($fileid){
    $filePath = bot('getFile',[
        'file_id'=>$fileid
    ])->result->file_path;
    return "https://api.telegram.org/file/bot" . $botToken . "/" . $filePath;
}
function alert($txt, $type = false, $callid = null){
    global $callbackId;
    $callid = $callid??$callbackId;
    return bot('answercallbackquery', [
        'callback_query_id' => $callid,
        'text' => $txt,
        'show_alert' => $type
    ]);
}

$range = [
        '149.154.160.0/22',
        '149.154.164.0/22',
        '91.108.4.0/22',
        '91.108.56.0/22',
        '91.108.8.0/22',
        '95.161.64.0/20',
    ];
function check($return = false){
    global $range;
    foreach ($range as $rg) {
        if (ip_in_range($_SERVER['REMOTE_ADDR'], $rg)) {
            return true;
        }
    }
    if ($return == true) {
        return false;
    }

    die('You do not have access');

}
function curl_get_file_contents($URL){
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}

function ip_in_range($ip, $range){
    if (strpos($range, '/') == false) {
        $range .= '/32';
    }
    // $range is in IP/CIDR format eg 127.0.0.1/24
    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~$wildcard_decimal;
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}

$time = time();
$update = json_decode(file_get_contents("php://input"));
if(isset($update->message)){
    $from_id = $update->message->from->id;
    $text = $update->message->text;
    $first_name = htmlspecialchars($update->message->from->first_name);
    $caption = $update->message->caption;
    $chat_id = $update->message->chat->id;
    $last_name = htmlspecialchars($update->message->from->last_name);
    $username = $update->message->from->username?? " ندارد ";
    $message_id = $update->message->message_id;
    $forward_from_name = $update->message->reply_to_message->forward_sender_name;
    $forward_from_id = $update->message->reply_to_message->forward_from->id;
    $reply_text = $update->message->reply_to_message->text;
}
if(isset($update->callback_query)){
    $callbackId = $update->callback_query->id;
    $data = $update->callback_query->data;
    $text = $update->callback_query->message->text;
    $message_id = $update->callback_query->message->message_id;
    $chat_id = $update->callback_query->message->chat->id;
    $chat_type = $update->callback_query->message->chat->type;
    $username = htmlspecialchars($update->callback_query->from->username)?? " ندارد ";
    $from_id = $update->callback_query->from->id;
    $first_name = htmlspecialchars($update->callback_query->from->first_name);
    $markup = json_decode(json_encode($update->callback_query->message->reply_markup->inline_keyboard),true);
}
if($from_id < 0) exit();
$stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
$stmt->bind_param("i", $from_id);
$stmt->execute();
$uinfo = $stmt->get_result();
$userInfo = $uinfo->fetch_assoc();
$stmt->close();
 
$stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
$stmt->execute();
$paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
if(!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys,true);
else $paymentKeys = array();
$stmt->close();

$stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
$stmt->execute();
$botState = $stmt->get_result()->fetch_assoc()['value'];
if(!is_null($botState)) $botState = json_decode($botState,true);
else $botState = array();
$stmt->close();

$channelLock = $botState['lockChannel'];
$joniedState= bot('getChatMember', ['chat_id' => $channelLock,'user_id' => $from_id])->result->status;

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

$cancelKey=json_encode(['keyboard'=>[
    [['text'=>$buttonValues['cancel']]]
],'resize_keyboard'=>true]);
$removeKeyboard = json_encode(['remove_keyboard'=>true]);

function getMainKeys(){
    global $connection, $userInfo, $from_id, $admin, $botState, $buttonValues;
    $mainKeys = array();
    $temp = array();

    if($botState['agencyState'] == "on" && $userInfo['is_agent'] == 1){
        $mainKeys = array_merge($mainKeys, [
            [['text'=>$buttonValues['agency_setting'],'callback_data'=>"agencySettings"]],
            [['text'=>$buttonValues['agent_one_buy'],'callback_data'=>"agentOneBuy"],['text'=>$buttonValues['agent_much_buy'],'callback_data'=>"agentMuchBuy"]],
            [['text'=>$buttonValues['my_subscriptions'],'callback_data'=>"agentConfigsList"]],
            ]);
    }else{
        $mainKeys = array_merge($mainKeys,[
            (($botState['agencyState'] == "on" && $userInfo['is_agent'] == 0)?[
                ['text'=>$buttonValues['request_agency'],'callback_data'=>"requestAgency"]
                ]:
                []),
            (($botState['sellState'] == "on" || $from_id == $admin || $userInfo['isAdmin'] == true)?
                [['text'=>$buttonValues['my_subscriptions'],'callback_data'=>'mySubscriptions'],['text'=>$buttonValues['buy_subscriptions'],'callback_data'=>"buySubscription"]]
                :
                [['text'=>$buttonValues['my_subscriptions'],'callback_data'=>'mySubscriptions']]
                    )
            ]);
    }
    $mainKeys = array_merge($mainKeys,[
        (
            ($botState['testAccount'] == "on")?[['text'=>$buttonValues['test_account'],'callback_data'=>"getTestAccount"]]:
                []
            ),
        [['text'=>$buttonValues['sharj'],'callback_data'=>"increaseMyWallet"]],
        [['text'=>$buttonValues['invite_friends'],'callback_data'=>"inviteFriends"],['text'=>$buttonValues['my_info'],'callback_data'=>"myInfo"]],
        (($botState['sharedExistence'] == "on" && $botState['individualExistence'] == "on")?
        [['text'=>$buttonValues['shared_existence'],'callback_data'=>"availableServers"],['text'=>$buttonValues['individual_existence'],'callback_data'=>"availableServers2"]]:[]),
        (($botState['sharedExistence'] == "on" && $botState['individualExistence'] != "on")?
            [['text'=>$buttonValues['shared_existence'],'callback_data'=>"availableServers"]]:[]),
        (($botState['sharedExistence'] != "on" && $botState['individualExistence'] == "on")?
            [['text'=>$buttonValues['individual_existence'],'callback_data'=>"availableServers2"]]:[]
        ),
        [['text'=>$buttonValues['application_links'],'callback_data'=>"reciveApplications"],['text'=>$buttonValues['my_tickets'],'callback_data'=>"supportSection"]],
        (($botState['searchState']=="on" || $from_id == $admin || $userInfo['isAdmin'] == true)?
            [['text'=>$buttonValues['search_config'],'callback_data'=>"showUUIDLeft"]]
            :[]),
    ]);
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` LIKE '%MAIN_BUTTONS%'");
    $stmt->execute();
    $buttons = $stmt->get_result();
    $stmt->close();
    if($buttons->num_rows >0){
        while($row = $buttons->fetch_assoc()){
            $rowId = $row['id'];
            $title = str_replace("MAIN_BUTTONS","",$row['type']);
            
            $temp[] =['text'=>$title,'callback_data'=>"showMainButtonAns" . $rowId];
            if(count($temp)>=2){
                array_push($mainKeys,$temp);
                $temp = array();
            }
        }
    }
    array_push($mainKeys,$temp);
    if($from_id == $admin || $userInfo['isAdmin'] == true) array_push($mainKeys,[['text'=>"مدیریت ربات ⚙️",'callback_data'=>"managePanel"]]);
    return json_encode(['inline_keyboard'=>$mainKeys]); 
}
function getAgentKeys(){
    global $buttonValues, $mainValues, $from_id, $userInfo, $connection;
    $agencyDate = jdate("Y-m-d H:i:s",$userInfo['agent_date']);
    $joinedDate = jdate("Y-m-d H:i:s",$userInfo['date']);
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid` = ? AND `agent_bought` = 1");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $boughtAccounts = $stmt->get_result()->num_rows;
    $stmt->close();
    
    return json_encode(['inline_keyboard'=>[
        [['text'=>$boughtAccounts,'callback_data'=>"wizwizch"],['text'=>$buttonValues['agent_bought_accounts'],'callback_data'=>"wizwizch"]],
        [['text'=>$joinedDate,'callback_data'=>"wizwizch"],['text'=>$buttonValues['agent_joined_date'],'callback_data'=>"wizwizch"]],
        [['text'=>$agencyDate,'callback_data'=>"wizwizch"],['text'=>$buttonValues['agent_agency_date'],'callback_data'=>"wizwizch"]],
        [['text'=>$buttonValues['back_to_main'],'callback_data'=>"mainMenu"]],
    ]]);
}
function getAdminKeys(){
    global $buttonValues, $mainValues, $from_id, $admin;
    
    return json_encode(['inline_keyboard'=>[
        [['text'=>$buttonValues['bot_reports'],'callback_data'=>"botReports"],['text'=>$buttonValues['message_to_user'],'callback_data'=>"messageToSpeceficUser"]],
        [['text'=>$buttonValues['user_reports'],'callback_data'=>"userReports"]],
        ($from_id == $admin?[['text'=>$buttonValues['admins_list'],'callback_data'=>"adminsList"]]:[]),
        [['text'=>$buttonValues['increase_wallet'],'callback_data'=>"increaseUserWallet"],['text'=>$buttonValues['decrease_wallet'],'callback_data'=>"decreaseUserWallet"]],
        [['text'=>$buttonValues['create_account'],'callback_data'=>"createMultipleAccounts"],
        ['text'=>$buttonValues['gift_volume_day'],'callback_data'=>"giftVolumeAndDay"]],
        [['text'=>$buttonValues['ban_user'],'callback_data'=>"banUser"],['text'=>$buttonValues['unban_user'],'callback_data'=>"unbanUser"]],
        [['text'=>$buttonValues['search_admin_config'],'callback_data'=>"searchUsersConfig"]],
        [['text'=>$buttonValues['server_settings'],'callback_data'=>"serversSetting"]],
        [['text'=>$buttonValues['categories_settings'],'callback_data'=>"categoriesSetting"]],
        [['text'=>$buttonValues['plan_settings'],'callback_data'=>"backplan"]],
        [['text'=>$buttonValues['discount_settings'],'callback_data'=>"discount_codes"],['text'=>$buttonValues['main_button_settings'],'callback_data'=>"mainMenuButtons"]],
        [['text'=>$buttonValues['gateways_settings'],'callback_data'=>"gateWays_Channels"],['text'=>$buttonValues['bot_settings'],'callback_data'=>'botSettings']],
        [['text'=>$buttonValues['tickets_list'],'callback_data'=>"ticketsList"],['text'=>$buttonValues['message_to_all'],'callback_data'=>"message2All"]],
        [['text'=>$buttonValues['forward_to_all'],'callback_data'=>"forwardToAll"]],
        [
            ['text'=>$buttonValues['agent_list'],'callback_data'=>"agentsList"],
            ['text'=>'درخواست های رد شده','callback_data'=>"rejectedAgentList"]
            ],
        [['text'=>$buttonValues['back_to_main'],'callback_data'=>"mainMenu"]],
    ]]);
    
}

function setSettings($field, $value){
    global $connection, $botState;
    $botState[$field]= $value;
    
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
function getRejectedAgentList(){
    global $connection, $mainValues, $buttonValues;
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `is_agent` = 2");
    $stmt->execute();
    $list = $stmt->get_result();
    $stmt->close();
    
    if($list->num_rows>0){
        $keys = array();
        $keys[] = [['text'=>"آزاد ساختن",'callback_data'=>"wizwizch"],['text'=>"اسم کاربر",'callback_data'=>'wizwizch'],['text'=>"آیدی عددی",'callback_data'=>"wizwizch"]];
        while($row = $list->fetch_assoc()){
            $userId = $row['userid'];
            
            $userDetail = bot('getChat',['chat_id'=>$userId])->result;
            $fullName = $userDetail->first_name . " " . $userDetail->last_name;
            
            $keys[] = [['text'=>"✅",'callback_data'=>"releaseRejectedAgent" . $userId],['text'=>$fullName,'callback_data'=>"wizwizch"],['text'=>$userId,'callback_data'=>"wizwizch"]];
        }
        $keys[] = [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]];
        return json_encode(['inline_keyboard'=>$keys]);
    }else return null;
}
function getAgentDetails($userId){
    global $connection, $mainVAlues, $buttonValues;
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ? AND `is_agent` = 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $agentDetail = $stmt->get_result();
    $stmt->close();


    $today = strtotime("today");
    $yesterday = strtotime("yesterday");
    $lastWeek = strtotime("last week");
    $lastMonth = strtotime("last month");

    $stmt = $connection->prepare("SELECT COUNT(`id`) AS `count`, SUM(`amount`) AS `total` FROM `orders_list` WHERE `date` >= ? AND `agent_bought` = 1 AND `userid` = ?");
    
    $stmt->bind_param("ii", $today, $userId);
    $stmt->execute();
    $todayIncome = $stmt->get_result()->fetch_assoc();
    
    $stmt->bind_param("ii", $yesterday, $userId);
    $stmt->execute();
    $yesterdayIncome = $stmt->get_result()->fetch_assoc();
    
    $stmt->bind_param("ii", $lastWeek, $userId);
    $stmt->execute();
    $lastWeekIncome = $stmt->get_result()->fetch_assoc();
    
    $stmt->bind_param("ii", $lastMonth, $userId);
    $stmt->execute();
    $lastMonthIncome = $stmt->get_result()->fetch_assoc();
    
    $stmt->close();
    
    
    return json_encode(['inline_keyboard'=>[
        [
            ['text'=>"(" . $todayIncome['count'] . ") " . number_format($todayIncome['total']),'callback_data'=>'wizwizch'],
            ['text'=>"درآمد امروز",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>"(" . $yesterdayIncome['count'] . ") " . number_format($yesterdayIncome['total']),'callback_data'=>"wizwizch"],
            ['text'=>"درآمد دیروز",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>"(" . $lastWeekIncome['count'] . ") " . number_format($lastWeekIncome['total']),'callback_data'=>"wizwizch"],
            ['text'=>"درآمد یک هفته",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>"(" . $lastMonthIncome['count'] . ") " . number_format($lastMonthIncome['total']),'callback_data'=>"wizwizch"],
            ['text'=>"درآمد یک ماه",'callback_data'=>"wizwizch"]
            ],
        [['text' => $buttonValues['back_button'], 'callback_data' => "agentsList"]]
        ]]);
}
function checkSpam(){
    global $connection, $from_id, $userInfo, $admin;
    
    if($userInfo != null && $from_id != $admin){
        $spamInfo = json_decode($userInfo['spam_info'],true)??array();
        $spamDate = $spamInfo['date'];
        if(isset($spamInfo['banned'])){
            if(time() <= $spamInfo['banned']) return $spamInfo['banned'];
        }
        
        if(time() <= $spamDate) $spamInfo['count'] += 1;
        else{
            $spamInfo['count'] = 1;
            $spamInfo['date'] = strtotime("+1 minute");
        }
        if($spamInfo['count'] >= 50){
            $spamInfo['banned'] = strtotime("+1 day");
        }
        $spamInfo = json_encode($spamInfo);
        
        $stmt = $connection->prepare("UPDATE `users` SET `spam_info` = ? WHERE `userid` = ?");
        $stmt->bind_param("si", $spamInfo, $from_id);
        $stmt->execute();
        $stmt->close();
    }else return null;
}
function getAgentsList($offset = 0){
    global $connection, $mainValues, $buttonValues;
    $limit = 15;
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `is_agent` = 1 LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $agentList = $stmt->get_result();
    $stmt->close();
    
    $keys = array();
    if($agentList->num_rows == 0 && $offset == 0) return null;
    
    $keys[] = [['text'=>"حذف",'callback_data'=>"wizwizch"],['text'=>"درصد تخفیف",'callback_data'=>"wizwizch"],['text'=>"تاریخ نمایندگی",'callback_data'=>"wizwizch"],['text'=>"اسم نماینده",'callback_data'=>"wizwizch"],['text'=>"آیدی عددی",'callback_data'=>"wizwizch"]];
    if($agentList->num_rows > 0){
        while($row = $agentList->fetch_assoc()){
            $userId = $row['userid'];
            
            $userDetail = bot('getChat',['chat_id'=>$userId])->result;
            $userUserName = $userDetail->username;
            $fullName = $userDetail->first_name . " " . $userDetail->last_name;
            $joinedDate = jdate("Y-m-d H:i",$row['agent_date']);

            $keys[] = [['text'=>"❌",'callback_data'=>"removeAgent" . $userId],['text'=>"⚙️",'callback_data'=>"agentPercentDetails" . $userId],['text'=>$joinedDate,'callback_data'=>"wizwizch"],['text'=>$fullName,'callback_data'=>"agentDetails" . $userId],['text'=>$userId,'callback_data'=>"agentDetails" . $userId]];
        }
    }
    if($offset == 0 && $limit <= $agentList->num_rows)
        $keys[] = [
            ['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextAgentList" . ($offset + $limit)]
            ];
    elseif($limit <= $agentList->num_rows)
        $keys[] = [
            ['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextAgentList" . ($offset + $limit)],
            ['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextAgentList" . ($offset - $limit)]
            ];
    elseif($offset != 0)
        $keys[] = [
            ['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextAgentList" . ($offset - $limit)]
            ];
            
    $keys[] = [['text' => $buttonValues['back_button'], 'callback_data' => "managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getAgentDiscounts($agentId){
    global $connection, $mainValues, $buttonValues, $botState;
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `is_agent` = 1 AND `userid` = ?");
    $stmt->bind_param("i", $agentId);
    $stmt->execute();
    $agentInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $keys = array();
    
    $discounts = json_decode($agentInfo['discount_percent'],true);

    $normal = $discounts['normal'];
    $keys[] = [['text'=>" ",'callback_data'=>"wizwizch"],
    ['text'=>$normal . "%",'callback_data'=>"editAgentDiscountNormal" . $agentId . "_0"],
    ['text'=>"عمومی",'callback_data'=>"wizwizch"]];            
    
    if($botState['agencyPlanDiscount']=="on"){
        foreach($discounts['plans'] as $planId=>$discount){
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ?");
            $stmt->bind_param('i', $planId);
            $stmt->execute();
            $info = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id` = ?");
            $stmt->bind_param("i", $info['catid']);
            $stmt->execute();
            $catInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $keys[] = [['text'=>"❌",'callback_data'=>"removePercentOfAgentPlan" . $agentId . "_" . $planId],
            ['text'=>$discount . "%",'callback_data'=>"editAgentDiscountPlan" . $agentId . "_" . $planId],
            ['text'=>$info['title'] . " " . $catInfo['title'],'callback_data'=>"wizwizch"]];            
        }
    }else{
        foreach($discounts['servers'] as $serverId=>$discount){
            $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
            $stmt->bind_param('i', $serverId);
            $stmt->execute();
            $info = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            $keys[] = [['text'=>"❌",'callback_data'=>"removePercentOfAgentServer" . $agentId . "_" . $serverId],
            ['text'=>$discount . "%",'callback_data'=>"editAgentDiscountServer" . $agentId . "_" . $serverId],
            ['text'=>$info['title'],'callback_data'=>"wizwizch"]];            
        }                
    }
    if($botState['agencyPlanDiscount']=="on")$keys[] = [['text' => "افزودن تخفیف پلن", 'callback_data' => "addDiscountPlanAgent" . $agentId]];
    else $keys[] = [['text' => "افزودن تخفیف سرور", 'callback_data' => "addDiscountServerAgent" . $agentId]];
    $keys[] = [['text' => $buttonValues['back_button'], 'callback_data' => "agentsList"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function NOWPayments($method, $endpoint, $datas = []){
    global $paymentKeys;

    $base_url = 'https://api.nowpayments.io/v1/';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    switch ($method) {
        case 'GET':
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $paymentKeys['nowpayment']]);
            if(!empty($datas)) {
                if(is_array($datas)) {
                    $parameters = http_build_query($datas);
                    curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint . '?' . $parameters);
                } else {
                    if($endpoint == 'payment') curl_setopt($ch, CURLOPT_URL,$base_url . $endpoint . '/' . $datas);
                }
            } else {
                curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
            }
            break;

        case 'POST':
            $datas = json_encode($datas);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $paymentKeys['nowpayment'], 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
            curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
            break;

        default:
            break;
    }

    $res = curl_exec($ch);
    
    if(curl_error($ch)) var_dump(curl_error($ch));
    else return json_decode($res);
}
function getServerConfigKeys($serverId,$offset = 0){
    global $connection, $mainValues, $buttonValues;
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $serverId);
    $stmt->execute();
    $cats= $stmt->get_result();
    $stmt->close();
    
    $cty = $cats->fetch_assoc();
    $id = $cty['id'];
    $cname = $cty['title'];
    $flagwizwiz = $cty['flag'];
    $remarkwizwiz = $cty['remark'];
    $ucount = $cty['ucount'];
    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $serverConfig= $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $reality = $serverConfig['reality']=="true"?$buttonValues['active']:$buttonValues['deactive'];
    $panelUrl = $serverConfig['panel_url'];
    $sni = !empty($serverConfig['sni'])?$serverConfig['sni']:" ";
    $headerType = !empty($serverConfig['header_type'])?$serverConfig['header_type']:" ";
    $requestHeader = !empty($serverConfig['request_header'])?$serverConfig['request_header']:" ";
    $responseHeader = !empty($serverConfig['response_header'])?$serverConfig['response_header']:" ";
    $security = !empty($serverConfig['security'])?$serverConfig['security']:" ";
    $portType = $serverConfig['port_type']=="auto"?"خودکار":"تصادفی";
    $serverType = " ";
    switch ($serverConfig['type']){
        case "sanaei":
            $serverType = "سنایی";
            break;
        case "alireza":
            $serverType = "علیرضا";
            break;
        case "normal":
            $serverType = "ساده";
            break;
        case "marzban":
            $serverType = "مرزبان";
            break;
    }
    return json_encode(['inline_keyboard'=>array_merge([
        [
            ['text'=>$panelUrl,'callback_data'=>"wizwizch"],
            ],
        [
            ['text'=>$cname,'callback_data'=>"editServerName$id"],
            ['text'=>"❕نام سرور",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$flagwizwiz,'callback_data'=>"editServerFlag$id"],
            ['text'=>"🚩 پرچم سرور",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$remarkwizwiz,'callback_data'=>"editServerRemark$id"],
            ['text'=>"📣 ریمارک سرور",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$serverType??" ",'callback_data'=>"changeServerType$id"],
            ['text'=>"نوعیت سرور",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$ucount,'callback_data'=>"editServerMax$id"],
            ['text'=>"ظرفیت سرور",'callback_data'=>"wizwizch"]
            ]
            ],
            ($serverConfig['type'] != "marzban"?[
        [
            ['text'=>$portType,'callback_data'=>"changePortType$id"],
            ['text'=>"نوعیت پورت",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$sni,'callback_data'=>"editsServersni$id"],
            ['text'=>"sni",'callback_data'=>"wizwizch"],
            ],
        [
            ['text'=>$headerType,'callback_data'=>"editsServerheader_type$id"],
            ['text'=>"header type",'callback_data'=>"wizwizch"],
            ],
        [
            ['text'=>$requestHeader,'callback_data'=>"editsServerrequest_header$id"],
            ['text'=>"request header",'callback_data'=>"wizwizch"],
            ],
        [
            ['text'=>$responseHeader,'callback_data'=>"editsServerresponse_header$id"],
            ['text'=>"response header",'callback_data'=>"wizwizch"],
            ],
        [
            ['text'=>$security,'callback_data'=>"editsServersecurity$id"],
            ['text'=>"security",'callback_data'=>"wizwizch"],
            ],
        (($serverConfig['type'] == "sanaei" || $serverConfig['type'] == "alireza")?
        [
            ['text'=>$reality,'callback_data'=>"changeRealityState$id"],
            ['text'=>"reality",'callback_data'=>"wizwizch"],
            ]:[]),
        [
            ['text'=>"♻️ تغییر آیپی های سرور",'callback_data'=>"changesServerIp$id"],
            ],
        [
            ['text'=>"♻️ تغییر security setting",'callback_data'=>"editsServertlsSettings$id"],
            ]
            ]:[]),[
        [
            ['text'=>"🔅تغییر اطلاعات ورود",'callback_data'=>"changesServerLoginInfo$id"],
            ],
        [
            ['text'=>"✂️ حذف سرور",'callback_data'=>"wizwizdeleteserver$id"],
            ],
        [['text' => $buttonValues['back_button'], 'callback_data' => "nextServerPage" . $offset]]
        ])]);
}
function getServerListKeys($offset = 0){
    global $connection, $mainValues, $buttonValues;
    
    $limit = 15;
    
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1 LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $cats= $stmt->get_result();
    $stmt->close();


    $keys = array();
    $keys[] = [['text'=>"وضعیت",'callback_data'=>"wizwizch"],['text'=>"تنظیمات",'callback_data'=>"wizwizch"],['text'=>"نوعیت",'callback_data'=>"wizwizch"],['text'=>"سرور",'callback_data'=>"wizwizch"]];
    if($cats->num_rows == 0){
        $keys[] = [['text'=>"سروری یافت نشد",'callback_data'=>"wizwizch"]];
    }else {
        while($cty = $cats->fetch_assoc()){
            $id = $cty['id'];
            $cname = $cty['title'];
            $flagwizwiz = $cty['flag'];
            $remarkwizwiz = $cty['remark'];
            $state = $cty['state'] == "1"?$buttonValues['active']:$buttonValues['deactive'];
            $ucount = $cty['ucount'];
            $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $serverTypeInfo= $stmt->get_result()->fetch_assoc();
            $stmt->close(); 
            $portType = $serverTypeInfo['port_type']=="auto"?"خودکار":"تصادفی";
            $serverType = " ";
            switch ($serverTypeInfo['type']){
                case "sanaei":
                    $serverType = "سنایی";
                    break;
                case "alireza":
                    $serverType = "علیرضا";
                    break;
                case "normal":
                    $serverType = "ساده";
                    break;
                case "marzban":
                    $serverType = "مرزبان";
                    break;
            }
            $keys[] = [['text'=>$state,'callback_data'=>'toggleServerState' . $id . "_" . $offset],['text'=>"⚙️",'callback_data'=>"showServerSettings" . $id . "_" . $offset],['text'=>$serverType??" ",'callback_data'=>"wizwizch"],['text'=>$cname,'callback_data'=>"wizwizch"]];
        } 
    }
    if($offset == 0 && $cats->num_rows >= $limit){
        $keys[] = [['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextServerPage" . ($offset + $limit)]];
    }
    elseif($cats->num_rows >= $limit){
        $keys[] = [
            ['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextServerPage" . ($offset + $limit)],
            ['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextServerPage" . ($offset - $limit)]
            ];
    }
    elseif($offset != 0){
        $keys[] = [['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextServerPage" . ($offset - $limit)]];
    }
    $keys[] = [
        ['text'=>'➕ ثبت سرور xui','callback_data'=>"addNewServer"],
        ['text'=>"➕ ثبت سرور مرزبان",'callback_data'=>"addNewMarzbanPanel"]
        ];
    $keys[] = [['text' => $buttonValues['back_button'], 'callback_data' => "managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getCategoriesKeys($offset = 0){
    $limit = 15;
    
    global $connection, $mainValues, $buttonValues;
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `active`=1 AND `parent`=0 LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();


    $keys = array();
    $keys[] = [['text'=>"حذف",'callback_data'=>"wizwizch"],['text'=>"اسم دسته",'callback_data'=>"wizwizch"]];
    if($cats->num_rows == 0){
        $keys[] = [['text'=>"دسته بندی یافت نشد",'callback_data'=>"wizwizch"]];
    }else {
        while($cty = $cats->fetch_assoc()){
            $id = $cty['id'];
            $cname = $cty['title'];
            $keys[] = [['text'=>"❌",'callback_data'=>"wizwizcategorydelete$id" . "_" . $offset],['text'=>$cname,'callback_data'=>"wizwizcategoryedit$id" . "_" . $offset]];
        }
    }
    
    if($offset == 0 && $cats->num_rows >= $limit){
        $keys[] = [['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextCategoryPage" . ($offset + $limit)]];
    }
    elseif($cats->num_rows >= $limit){
        $keys[] = [
            ['text'=>" »» صفحه بعدی »»",'callback_data'=>"nextCategoryPage" . ($offset + $limit)],
            ['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextCategoryPage" . ($offset - $limit)]
            ];
    }
    elseif($offset != 0){
        $keys[] = [['text'=>" «« صفحه قبلی ««",'callback_data'=>"nextCategoryPage" . ($offset - $limit)]];
    }
    
    $keys[] = [['text'=>'➕ افزودن دسته جدید','callback_data'=>"addNewCategory"]];
    $keys[] = [['text' => $buttonValues['back_button'], 'callback_data' => "managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getGateWaysKeys(){
    global $connection, $mainValues, $buttonValues;
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    $stmt->execute();
    $botState = $stmt->get_result()->fetch_assoc()['value'];
    if(!is_null($botState)) $botState = json_decode($botState,true);
    else $botState = array();
    $stmt->close();
    
    $cartToCartState = $botState['cartToCartState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $walletState = $botState['walletState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $sellState = $botState['sellState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $weSwapState = $botState['weSwapState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $robotState = $botState['botState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $nowPaymentWallet = $botState['nowPaymentWallet']=="on"?$buttonValues['on']:$buttonValues['off'];
    $nowPaymentOther = $botState['nowPaymentOther']=="on"?$buttonValues['on']:$buttonValues['off'];
    $tronWallet = $botState['tronWallet']=="on"?$buttonValues['on']:$buttonValues['off'];
    $zarinpal = $botState['zarinpal']=="on"?$buttonValues['on']:$buttonValues['off'];
    $nextpay = $botState['nextpay']=="on"?$buttonValues['on']:$buttonValues['off'];
    $rewaredChannel = $botState['rewardChannel']??" ";
    $lockChannel = $botState['lockChannel']??" ";

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if(!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys,true);
    else $paymentKeys = array();
    $stmt->close();
    return json_encode(['inline_keyboard'=>[
        [
            ['text'=>(!empty($paymentKeys['bankAccount'])?$paymentKeys['bankAccount']:" "),'callback_data'=>"changePaymentKeysbankAccount"],
            ['text'=>"شماره حساب",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>(!empty($paymentKeys['holderName'])?$paymentKeys['holderName']:" "),'callback_data'=>"changePaymentKeysholderName"],
            ['text'=>"دارنده حساب",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>(!empty($paymentKeys['nowpayment'])?$paymentKeys['nowpayment']:" "),'callback_data'=>"changePaymentKeysnowpayment"],
            ['text'=>"کد درگاه nowPayment",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>(!empty($paymentKeys['zarinpal'])?$paymentKeys['zarinpal']:" "),'callback_data'=>"changePaymentKeyszarinpal"],
            ['text'=>"کد درگاه زرین پال",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>(!empty($paymentKeys['nextpay'])?$paymentKeys['nextpay']:" "),'callback_data'=>"changePaymentKeysnextpay"],
            ['text'=>"کد درگاه نکست پی",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>(!empty($paymentKeys['tronwallet'])?$paymentKeys['tronwallet']:" "),'callback_data'=>"changePaymentKeystronwallet"],
            ['text'=>"آدرس والت ترون",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$weSwapState,'callback_data'=>"changeGateWaysweSwapState"],
            ['text'=>"درگاه وی سواپ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$cartToCartState,'callback_data'=>"changeGateWayscartToCartState"],
            ['text'=>"کارت به کارت",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$nextpay,'callback_data'=>"changeGateWaysnextpay"],
            ['text'=>"درگاه نکست پی",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$zarinpal,'callback_data'=>"changeGateWayszarinpal"],
            ['text'=>"درگاه زرین پال",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$nowPaymentWallet,'callback_data'=>"changeGateWaysnowPaymentWallet"],
            ['text'=>"درگاه NowPayment کیف پول",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$nowPaymentOther,'callback_data'=>"changeGateWaysnowPaymentOther"],
            ['text'=>"درگاه NowPayment سایر",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$tronWallet,'callback_data'=>"changeGateWaystronWallet"],
            ['text'=>"درگاه ترون",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$walletState,'callback_data'=>"changeGateWayswalletState"],
            ['text'=>"کیف پول",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$rewaredChannel,'callback_data'=>'editRewardChannel'],
            ['text'=>"کانال گزارش درآمد",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$lockChannel,'callback_data'=>'editLockChannel'],
            ['text'=>"کانال قفل",'callback_data'=>'wizwizch']
            ],
        [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]]
        ]]);

}
function getBotSettingKeys(){
    global $connection, $mainValues, $buttonValues;
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    $stmt->execute();
    $botState = $stmt->get_result()->fetch_assoc()['value'];
    if(!is_null($botState)) $botState = json_decode($botState,true);
    else $botState = array();
    $stmt->close();

    $changeProtocole = $botState['changeProtocolState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $renewAccount = $botState['renewAccountState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $plandelkhahwiz = $botState['plandelkhahState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $switchLocation = $botState['switchLocationState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $increaseTime = $botState['increaseTimeState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $increaseVolume = $botState['increaseVolumeState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $subLink = $botState['subLinkState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $configLink = $botState['configLinkState']=="off"?$buttonValues['off']:$buttonValues['on'];
    $renewConfigLink = $botState['renewConfigLinkState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $updateConfigLink = $botState['updateConfigLinkState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $individualExistence = $botState['individualExistence']=="on"?$buttonValues['on']:$buttonValues['off'];
    $sharedExistence = $botState['sharedExistence']=="on"?$buttonValues['on']:$buttonValues['off'];
    $testAccount = $botState['testAccount']=="on"?$buttonValues['on']:$buttonValues['off'];
    $agency = $botState['agencyState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $agencyPlanDiscount = $botState['agencyPlanDiscount']=="on"?$buttonValues['plan_discount']:$buttonValues['server_discount'];
    $qrConfig = $botState['qrConfigState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $qrSub = $botState['qrSubState']=="on"?$buttonValues['on']:$buttonValues['off'];
    
    $requirePhone = $botState['requirePhone']=="on"?$buttonValues['on']:$buttonValues['off'];
    $requireIranPhone = $botState['requireIranPhone']=="on"?$buttonValues['on']:$buttonValues['off'];
    $sellState = $botState['sellState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $robotState = $botState['botState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $searchState = $botState['searchState']=="on"?$buttonValues['on']:$buttonValues['off'];
    $updateConnectionState = $botState['updateConnectionState']=="robot"?"از روی ربات":"از روی سایت";
    $rewaredTime = ($botState['rewaredTime']??0) . " ساعت";
    switch($botState['remark']){
        case "digits":
            $remarkType = "عدد رندم 5 حرفی";
            break;
        case "manual":
            $remarkType = "توسط کاربر";
            break;
        default:
            $remarkType = "آیدی و عدد رندوم";
            break;
    }
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if(!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys,true);
    else $paymentKeys = array();
    $stmt->close();
    return json_encode(['inline_keyboard'=>[
        [
            ['text'=>"🎗 بنر بازاریابی 🎗",'callback_data'=>"inviteSetting"]
            ],
        [
            ['text'=> $updateConnectionState,'callback_data'=>"changeUpdateConfigLinkState"],
            ['text'=>"آپدیت کانفیگ",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=> $agency,'callback_data'=>"changeBotagencyState"],
            ['text'=>"نمایندگی",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=> $agencyPlanDiscount,'callback_data'=>"changeBotagencyPlanDiscount"],
            ['text'=>"نوع تخفیف نمایندگی",'callback_data'=>"wizwizch"]
            ],
        [
            ['text'=>$individualExistence,'callback_data'=>"changeBotindividualExistence"],
            ['text'=>"موجودی اختصاصی",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$sharedExistence,'callback_data'=>"changeBotsharedExistence"],
            ['text'=>"موجودی اشتراکی",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$testAccount,'callback_data'=>"changeBottestAccount"],
            ['text'=>"اکانت تست",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$changeProtocole,'callback_data'=>"changeBotchangeProtocolState"],
            ['text'=>"تغییر پروتکل",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$renewAccount,'callback_data'=>"changeBotrenewAccountState"],
            ['text'=>"تمدید سرویس",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$plandelkhahwiz,'callback_data'=>"changeBotplandelkhahState"],
            ['text'=>"پلن دلخواه",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$switchLocation,'callback_data'=>"changeBotswitchLocationState"],
            ['text'=>"تغییر لوکیشن",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$increaseTime,'callback_data'=>"changeBotincreaseTimeState"],
            ['text'=>"افزایش زمان",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$increaseVolume,'callback_data'=>"changeBotincreaseVolumeState"],
            ['text'=>"افزایش حجم",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$requirePhone,'callback_data'=>"changeBotrequirePhone"],
            ['text'=>"تأیید شماره",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$requireIranPhone,'callback_data'=>"changeBotrequireIranPhone"],
            ['text'=>"تأیید شماره ایرانی",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$sellState,'callback_data'=>"changeBotsellState"],
            ['text'=>"فروش",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$robotState,'callback_data'=>"changeBotbotState"],
            ['text'=>"وضعیت ربات",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$subLink,'callback_data'=>"changeBotsubLinkState"],
            ['text'=>"لینک ساب و مشخصات وب",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$configLink,'callback_data'=>"changeBotconfigLinkState"],
            ['text'=>"لینک کانفیگ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$searchState,'callback_data'=>"changeBotsearchState"],
            ['text'=>"مشخصات کانفیگ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$renewConfigLink,'callback_data'=>"changeBotrenewConfigLinkState"],
            ['text'=>"دریافت لینک جدید",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$updateConfigLink,'callback_data'=>"changeBotupdateConfigLinkState"],
            ['text'=>"بروز رسانی لینک",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$qrConfig,'callback_data'=>"changeBotqrConfigState"],
            ['text'=>"کیو آر کد کانفیگ",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$qrSub,'callback_data'=>"changeBotqrSubState"],
            ['text'=>"کیو آر کد ساب",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$remarkType,'callback_data'=>"changeConfigRemarkType"],
            ['text'=>"نوع ریمارک",'callback_data'=>"wizwizch"]
        ],
        [
            ['text'=>$rewaredTime,'callback_data'=>'editRewardTime'],
            ['text'=>"ارسال گزارش درآمد", 'callback_data'=>'wizwizch']
            ],
        [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]]
        ]]);

}
function getBotReportKeys(){
    global $connection, $mainValues, $buttonValues;
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
    
    $stmt = $connection->prepare("SELECT SUM(price) as total FROM `pays` WHERE `state` = 'paid' OR `state` = 'approved'");
    $stmt->execute();
    $totalRewards = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
    $stmt->close();
    
    
    $persian = explode("-",jdate("Y-n-1", time()));
    $gregorian = jalali_to_gregorian($persian[0], $persian[1], $persian[2]);
    $date =  $gregorian[0] . "-" . $gregorian[1] . "-" . $gregorian[2];
    $dayTime = strtotime($date);
    $stmt = $connection->prepare("SELECT SUM(price) as total FROM `pays` WHERE `request_date` > ? AND (`state` = 'paid' OR `state` = 'approved')");
    $stmt->bind_param("i", $dayTime);
    $stmt->execute();
    $monthReward = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
    $stmt->close();
    
    $dayTime = strtotime("-" . (date("w")+1) . " days");
    $stmt = $connection->prepare("SELECT SUM(price) as total FROM `pays` WHERE `request_date` > ?  AND (`state` = 'paid' OR `state` = 'approved')");
    $stmt->bind_param("i", $dayTime);
    $stmt->execute();
    $weekReward = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
    $stmt->close();
    
    $dayTime = strtotime("today");
    $stmt = $connection->prepare("SELECT SUM(price) as total FROM `pays` WHERE `request_date` > ? AND (`state` = 'paid' OR `state` = 'approved')");
    $stmt->bind_param("i", $dayTime);
    $stmt->execute();
    $dayReward = number_format($stmt->get_result()->fetch_assoc()['total']) . " تومان";
    $stmt->close();
    
    return json_encode(['inline_keyboard'=>[
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
            ['text'=>$dayReward,'callback_data'=>'wizwizch'],
            ['text'=>"درآمد امروز",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$weekReward,'callback_data'=>'wizwizch'],
            ['text'=>"درآمد هفته",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>$monthReward,'callback_data'=>'wizwizch'],
            ['text'=>"درآمد ماه",'callback_data'=>'wizwizch']
            ],
        [
            ['text'=>"برگشت به مدیریت",'callback_data'=>'managePanel']
            ]
        ]]);
}
function getAdminsKeys(){
    global $connection, $mainValues, $buttonValues;
    $keys = array();
    
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `isAdmin` = true");
    $stmt->execute();
    $usersList = $stmt->get_result();
    $stmt->close();
    if($usersList->num_rows > 0){
        while($user = $usersList->fetch_assoc()){
            $keys[] = [['text'=>"❌",'callback_data'=>"delAdmin" . $user['userid']],['text'=>$user['name'], "callback_data"=>"wizwizch"]];
        }
    }else{
        $keys[] = [['text'=>"لیست ادمین ها خالی است ❕",'callback_data'=>"wizwizch"]];
    }
    $keys[] = [['text'=>"➕ افزودن ادمین",'callback_data'=>"addNewAdmin"]];
    $keys[] = [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getUserInfoKeys($userId){
    global $connection, $mainValues, $buttonValues; 
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i",$userId);
    $stmt->execute();
    $userCount = $stmt->get_result();
    $stmt->close();
    if($userCount->num_rows > 0){
        $userInfos = $userCount->fetch_assoc();
        $userWallet = number_format($userInfos['wallet']) . " تومان";
        
        $stmt = $connection->prepare("SELECT COUNT(amount) as count, SUM(amount) as total FROM `orders_list` WHERE `userid` = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $info = $stmt->get_result()->fetch_assoc();
        
        $boughtService = $info['count'];
        $totalBoughtPrice = number_format($info['total']) . " تومان";
        
        $userDetail = bot('getChat',['chat_id'=>$userId])->result;
        $userUserName = $userDetail->username;
        $fullName = $userDetail->first_name . " " . $userDetail->last_name;
        
        return json_encode(['inline_keyboard'=>[
            [
                ['text'=>$userUserName??" ",'url'=>"t.me/$userUserName"],
                ['text'=>"یوزرنیم",'callback_data'=>"wizwizch"]
                ],
            [
                ['text'=>$fullName??" ",'callback_data'=>"wizwizch"],
                ['text'=>"نام",'callback_data'=>"wizwizch"]
                ],
            [
                ['text'=>$boughtService??" ",'callback_data'=>"wizwizch"],
                ['text'=>"سرویس ها",'callback_data'=>"wizwizch"]
                ],
            [
                ['text'=>$totalBoughtPrice??" ",'callback_data'=>"wizwizch"],
                ['text'=>"مبلغ خرید",'callback_data'=>"wizwizch"]
                ],
            [
                ['text'=>$userWallet??" ",'callback_data'=>"wizwizch"],
                ['text'=>"موجودی کیف پول",'callback_data'=>"wizwizch"]
                ],
            [
                ['text'=>$buttonValues['back_button'],'callback_data'=>"mainMenu"]
                ],
            ]]);
    }else return null;
}
function getDiscountCodeKeys(){
    global $connection, $mainValues, $buttonValues;
    $time = time();
    $stmt = $connection->prepare("SELECT * FROM `discounts` WHERE (`expire_date` > $time OR `expire_date` = 0) AND (`expire_count` > 0 OR `expire_count` = -1)");
    $stmt->execute();
    $list = $stmt->get_result();
    $stmt->close();
    $keys = array();
    if($list->num_rows > 0){
        $keys[] = [['text'=>'حذف','callback_data'=>"wizwizch"],['text'=>"استفاده هر یوزر",'callback_data'=>"wizwizch"],['text'=>"تاریخ ختم",'callback_data'=>"wizwizch"],['text'=>"تعداد استفاده",'callback_data'=>"wizwizch"],['text'=>"مقدار تخفیف",'callback_data'=>"wizwizch"],['text'=>"کد تخفیف",'callback_data'=>"wizwizch"]];
        while($row = $list->fetch_assoc()){
            $date = $row['expire_date']!=0?jdate("Y/n/j H:i", $row['expire_date']):"نامحدود";
            $count = $row['expire_count']!=-1?$row['expire_count']:"نامحدود";
            $amount = $row['amount'];
            $amount = $row['type'] == 'percent'? $amount."%":$amount = number_format($amount) . " تومان";
            $hashId = $row['hash_id'];
            $rowId = $row['id'];
            $canUse = $row['can_use'];
            
            $keys[] = [['text'=>'❌','callback_data'=>"delDiscount" . $rowId],['text'=>$canUse, 'callback_data'=>"wizwizch"],['text'=>$date,'callback_data'=>"wizwizch"],['text'=>$count,'callback_data'=>"wizwizch"],['text'=>$amount,'callback_data'=>"wizwizch"],['text'=>$hashId,'callback_data'=>'copyHash' . $hashId]];
        }
    }else{
        $keys[] = [['text'=>"کد تخفیفی یافت نشد",'callback_data'=>"wizwizch"]];
    }
    
    $keys[] = [['text'=>"افزودن کد تخفیف",'callback_data'=>"addDiscountCode"]];
    $keys[] = [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getMainMenuButtonsKeys(){
    global $connection, $mainValues, $buttonValues;
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` LIKE '%MAIN_BUTTONS%'");
    $stmt->execute();
    $buttons = $stmt->get_result();
    $stmt->close();
    
    $keys = array();
    if($buttons->num_rows > 0){
        while($row = $buttons->fetch_assoc()){
            $rowId = $row['id'];
            $title = str_replace("MAIN_BUTTONS","", $row['type']);
            $answer = $row['value'];
            $keys[] = [
                        ['text'=>"❌",'callback_data'=>"delMainButton" . $rowId],
                        ['text'=>$title??" " ,'callback_data'=>"wizwizch"]];
        }
    }else{
        $keys[] = [['text'=>"دکمه ای یافت نشد ❕",'callback_data'=>"wizwizch"]];
    }
    $keys[] = [['text'=>"افزودن دکمه جدید ➕",'callback_data'=>"addNewMainButton"]];
    $keys[] = [['text'=>$buttonValues['back_button'],'callback_data'=>"managePanel"]];
    return json_encode(['inline_keyboard'=>$keys]);
}
function getPlanDetailsKeys($planId){
    global $connection, $mainValues, $buttonValues;
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    $pdResult = $stmt->get_result();
    $pd = $pdResult->fetch_assoc();
    $stmt->close();


    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $pd['server_id']);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $reality = $server_info['reality'];


    if($pdResult->num_rows == 0) return null;
    else {
        $id=$pd['id'];
        $name=$pd['title'];
        $price=$pd['price'];
        $acount =$pd['acount'];
        $rahgozar = $pd['rahgozar'];
        $customPath = $pd['custom_path']==true?$buttonValues['on']:$buttonValues['off'];
        $dest = $pd['dest']??" ";
        $spiderX = $pd['spiderX']??" ";
        $serverName = $pd['serverNames']??" ";
        $flow = $pd['flow'];
        $customPort = $pd['custom_port'];
        $customSni = $pd['custom_sni']??" ";

        $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `fileid`=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $wizwizplanaccnumber = $stmt->get_result()->num_rows;
        $stmt->close();

        $srvid= $pd['server_id'];
        $keyboard = [
            ($rahgozar==true?[['text'=>"* نوع پلن: رهگذر *",'callback_data'=>'wizwizch']]:[]),
            ($rahgozar==true?[
                ['text'=>$customPath,'callback_data'=>'changeCustomPath' . $id],
                ['text'=>"Path Custom",'callback_data'=>'wizwizch'],
                ]:[]),
            ($rahgozar==true?[
                ['text'=>$customPort,'callback_data'=>'changeCustomPort' . $id],
                ['text'=>"پورت دلخواه",'callback_data'=>'wizwizch'],
                ]:[]),
            ($rahgozar==true?[
                ['text'=>$customSni,'callback_data'=>'changeCustomSni' . $id],
                ['text'=>"sni دلخواه",'callback_data'=>'wizwizch'],
                ]:[]),
            [['text'=>$name,'callback_data'=>"wizwizplanname$id"],['text'=>"🔮 نام پلن",'callback_data'=>"wizwizch"]],
            ($reality == "true"?[['text'=>$dest,'callback_data'=>"editDestName$id"],['text'=>"dest",'callback_data'=>"wizwizch"]]:[]),
            ($reality == "true"?[['text'=>$serverName,'callback_data'=>"editServerNames$id"],['text'=>"serverNames",'callback_data'=>"wizwizch"]]:[]),
            ($reality == "true"?[['text'=>$spiderX,'callback_data'=>"editSpiderX$id"],['text'=>"spiderX",'callback_data'=>"wizwizch"]]:[]),
            ($reality == "true"?[['text'=>$flow,'callback_data'=>"editFlow$id"],['text'=>"flow",'callback_data'=>"wizwizch"]]:[]),
            [['text'=>$wizwizplanaccnumber,'callback_data'=>"wizwizch"],['text'=>"🎗 تعداد اکانت های فروخته شده",'callback_data'=>"wizwizch"]],
            ($pd['inbound_id'] != 0?[['text'=>"$acount",'callback_data'=>"wizwizplanslimit$id"],['text'=>"🚪 تغییر ظرفیت کانفیگ",'callback_data'=>"wizwizch"]]:[]),
            ($pd['inbound_id'] != 0?[['text'=>$pd['inbound_id'],'callback_data'=>"wizwizplansinobundid$id"],['text'=>"🚪 سطر کانفیگ",'callback_data'=>"wizwizch"]]:[]),
            [['text'=>"✏️ ویرایش توضیحات",'callback_data'=>"wizwizplaneditdes$id"]],
            [['text'=>number_format($price) . " تومان",'callback_data'=>"wizwizplanrial$id"],['text'=>"💰 قیمت پلن",'callback_data'=>"wizwizch"]],
            [['text'=>"♻️ دریافت لیست اکانت ها",'callback_data'=>"wizwizplanacclist$id"]],
            ($server_info['type'] == "marzban"?[['text'=>"انتخاب Host",'callback_data'=>"marzbanHostSettings" . $id]]:[]),
            [['text'=>"✂️ حذف",'callback_data'=>"wizwizplandelete$id"]],
            [['text' => $buttonValues['back_button'], 'callback_data' =>"plansList$srvid"]]
            ];
        return json_encode(['inline_keyboard'=>$keyboard]);
    }
}
function getUserOrderDetailKeys($id){
    global $connection, $botState, $mainValues, $buttonValues, $botUrl;
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id`=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();
    

    if($order->num_rows==0){
        return null;
    }else {
        $order = $order->fetch_assoc();
        $userId = $order['userid'];
        $firstName = bot('getChat',['chat_id'=>$userId])->result->first_name ?? " ";
        $fid = $order['fileid']; 
    	$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1"); 
        $stmt->bind_param("i", $fid);
        $stmt->execute();
        $respd = $stmt->get_result();
        $stmt->close();
	    $rahgozar = $order['rahgozar'];
        $agentBought = $order['agent_bought'];
        $isAgentBought = $agentBought == true?"بله":"نخیر";

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
        $uuid = $order['uuid']??"0";
        $acc_link = json_decode($order['link']);
        $protocol = $order['protocol'];
        $token = $order['token'];
        $server_id = $order['server_id'];
        $inbound_id = $order['inbound_id'];
        $link_status = $order['expire_date'] > time()  ? $buttonValues['active'] : $buttonValues['deactive'];
        $price = $order['amount'];
        
    	$stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id` = ?");
    	$stmt->bind_param('i', $server_id);
    	$stmt->execute();
    	$serverConfig = $stmt->get_result()->fetch_assoc();
    	$stmt->close();
    	$serverType = $serverConfig['type'];
    	$panelUrl = $serverConfig['panel_url'];

        if($serverType == "marzban"){
            $info = getMarzbanUser($server_id, $remark);
            $enable = $info->status =="active"?true:false;
            $total = $info->data_limit;
            $usedTraffic = $info->used_traffic;
            
            $leftgb = round( ($total - $usedTraffic) / 1073741824, 2) . " GB";
        }else{
            $response = getJson($server_id)->obj;
            if($inbound_id == 0) {
                foreach($response as $row){
                    $clients = json_decode($row->settings)->clients;
                    if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
                        $total = $row->total;
                        $up = $row->up;
                        $enable = $row->enable;
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
                        $clientsStates = $row->clientStats;
                        $clients = json_decode($row->settings)->clients;
                        foreach($clients as $key => $client){
                            if($client->id == $uuid || $client->password == $uuid){
                                $email = $client->email;
                                $emails = array_column($clientsStates,'email');
                                $emailKey = array_search($email,$emails);
                                
                                $total = $clientsStates[$emailKey]->total;
                                $up = $clientsStates[$emailKey]->up;
                                $enable = $clientsStates[$emailKey]->enable;
                                if(!$client->enable) $enable = false;
                                $down = $clientsStates[$emailKey]->down; 
                                break;
                            }
                        }
                    }
                }
            }
            $leftgb = round( ($total - $up - $down) / 1073741824, 2) . " GB";
        }
        $configLinks = "";
        foreach($acc_link as $acc_link_key => $acc_link){
	    if($acc_link_key == 10){
              $configLinks .= ($botState['configLinkState'] != "off"?"\n\n❗️برای مشاهده سایر کانفیگ‌ها از لینک سابسکریپشن استفاده کنید":"");
              break;
            }
            $configLinks .= $botState['configLinkState'] != "off"?"\n <code>$acc_link</code>":"";
        }
        $keyboard = array();
        if($inbound_id == 0){
            if($protocol == 'trojan') {
                if($security == "xtls"){
                    $keyboard = [
                        [
            			    ['text' => $userId, 'callback_data' => "wizwizch"],
                            ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                            ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                            ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        [
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                            ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                            ]
                    ];
                    
                }else{
                    $keyboard = [
                        [
            			    ['text' => $userId, 'callback_data' => "wizwizch"],
                            ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                            ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                            ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        [
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                            ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                            ]
                    ];
                    
                    
                }
            }else {
                if($netType == "grpc"){
                    $keyboard = [
                        [
            			    ['text' => $userId, 'callback_data' => "wizwizch"],
                            ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                            ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                            ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        [
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                            ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                            ]
                    ];
                    
                    
                }
                elseif($netType == "tcp" && $security == "xtls"){
                    $keyboard = [
                        [
            			    ['text' => $userId, 'callback_data' => "wizwizch"],
                            ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                            ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                            ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        [
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ],
                        [
                            ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                            ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                            ]
                    ];
                    
                }
                else{
                    $keyboard = [
                        [
            			    ['text' => $userId, 'callback_data' => "wizwizch"],
                            ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                            ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                            ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        [
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ],
                        ($rahgozar == true?
                        [
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ]:
                            [
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => "wizwizch"],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => "wizwizch"],
                        ]),
                        [
                            ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                            ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                            ]
                    ];
                    
                }
            }
        }else{
            $keyboard = [
                [
    			    ['text' => $userId, 'callback_data' => "wizwizch"],
                    ['text' => "آیدی کاربر", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => $firstName, 'callback_data' => "wizwizch"],
                    ['text' => "اسم کاربر", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => $isAgentBought, 'callback_data' => "wizwizch"],
                    ['text' => "خرید نماینده", 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
    			],
    			[
                    ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                ],
                [
                    ['text' => " $protocol ☑️", 'callback_data' => "wizwizch"],
                ],
                [
                    ['text'=>($enable == true?$buttonValues['disable_config']:$buttonValues['enable_config']),'callback_data'=>"changeUserConfigState" . $order['id']],
                    ['text'=>$buttonValues['delete_config'],'callback_data'=>"delUserConfig" . $order['id']],
                    ]
            ];
            

        }


        $stmt= $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if($serverType == "marzban") $subLink = $botState['subLinkState'] == "on"?"<code>" . $panelUrl . "/sub/" . $token . "</code>":"";
        else $subLink = $botState['subLinkState']=="on"?"<code>" . $botUrl . "settings/subLink.php?token=" . $token . "</code>":"";

        
        $enable = $enable == true? $buttonValues['active']:$buttonValues['deactive'];
        $msg = str_replace(['STATE', 'NAME','CONNECT-LINK', 'SUB-LINK'], [$enable, $remark, $configLinks, $subLink], $mainValues['config_details_message']);
    
        $keyboard[] = [['text' => $buttonValues['back_button'], 'callback_data' => "managePanel"]];
        return ["keyboard"=>json_encode([
                    'inline_keyboard' => $keyboard
                ]),
                "msg"=>$msg];
    }
}
function getOrderDetailKeys($from_id, $id){
    global $connection, $botState, $mainValues, $buttonValues, $botUrl;
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `id`=?");
    $stmt->bind_param("ii", $from_id, $id);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();


    if($order->num_rows==0){
        return null;
    }else {
        $order = $order->fetch_assoc();
        $fid = $order['fileid']; 
    	$stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1"); 
        $stmt->bind_param("i", $fid);
        $stmt->execute();
        $respd = $stmt->get_result();
        $stmt->close();
	    $rahgozar = $order['rahgozar'];
        $agentBought = $order['agent_bought'];

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
        $uuid = $order['uuid']??"0";
        $acc_link = json_decode($order['link']);
        $protocol = $order['protocol'];
        $token = $order['token'];
        $server_id = $order['server_id'];
        $inbound_id = $order['inbound_id'];
        $link_status = $order['expire_date'] > time()  ? $buttonValues['active'] : $buttonValues['deactive'];
        $price = $order['amount'];
        
    	$stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id` = ?");
    	$stmt->bind_param('i', $server_id);
    	$stmt->execute();
    	$serverConfig = $stmt->get_result()->fetch_assoc();
    	$stmt->close();
    	$serverType = $serverConfig['type'];
        $panel_url = $serverConfig['panel_url'];
        
        if($serverType == "marzban"){
            $info = getMarzbanUser($server_id, $remark);
            $enable = $info->status =="active"?true:false;
            $total = $info->data_limit;
            $usedTraffic = $info->used_traffic;
            
            $leftgb = round( ($total - $usedTraffic) / 1073741824, 2) . " GB";
        }else{
            $response = getJson($server_id)->obj;
            if($response){
                if($inbound_id == 0) {
                    foreach($response as $row){
                        $clients = json_decode($row->settings)->clients;
                        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
                            $total = $row->total;
                            $up = $row->up;
                            $down = $row->down; 
                            $enable = $row->enable;
                            $expiryTime = $row->expiryTime;
                            
                            $netType = json_decode($row->streamSettings)->network;
                            $security = json_decode($row->streamSettings)->security;
                            
                            $clientsStates = $row->clientStats;
                            
                            $inboundEmail = $clients[0]->email;
                            $allEmails = array_column($clientsStates,'email');
                            $clienEmailKey = array_search($inboundEmail,$allEmails);
    
                            $clientTotal = $clientsStates[$clienEmailKey]->total;
                            $clientUp = $clientsStates[$clienEmailKey]->up;
                            $clientDown = $clientsStates[$clienEmailKey]->down;
                            $clientExpiryTime = $clientsStates[$clienEmailKey]->expiryTime;
                                
                            if($clientTotal != 0 && $clientTotal != null && $clientExpiryTime != 0 && $clientExpiryTime != null){
                                $up += $clientUp;
                                $down += $clientDown;
                                $total = $clientTotal;
                            }
    
                            break;
                        }
                    }
                }else {
                    foreach($response as $row){
                        if($row->id == $inbound_id) {
                            $netType = json_decode($row->streamSettings)->network;
                            $security = json_decode($row->streamSettings)->security;
                            
                            $clientsStates = $row->clientStats;
                            $clients = json_decode($row->settings)->clients;
                            foreach($clients as $key => $client){
                                if($client->id == $uuid || $client->password == $uuid){
                                    $email = $client->email;
                                    $emails = array_column($clientsStates,'email');
                                    $emailKey = array_search($email,$emails);
                                    
                                    $total = $clientsStates[$emailKey]->total;
                                    $up = $clientsStates[$emailKey]->up;
                                    $enable = $clientsStates[$emailKey]->enable;
                                    if(!$client->enable) $enable = false;
                                    $down = $clientsStates[$emailKey]->down; 
                                    break;
                                }
                            }
                        }
                    }
                }
                $leftgb = round( ($total - $up - $down) / 1073741824, 2) . " GB";
            }else $leftgb = "⚠️";
        }
        $configLinks = "";
        foreach($acc_link as $acc_link_key => $acc_link){
	    if($acc_link_key == 10){
              $configLinks .= ($botState['configLinkState'] != "off"?"\n\n❗️برای مشاهده سایر کانفیگ‌ها از لینک سابسکریپشن استفاده کنید":"");
              break;
            }
            $configLinks .= ($botState['configLinkState'] != "off"?"\n <code>$acc_link</code>":"");
        }
        $keyboard = array();
        if($inbound_id == 0){
            if($protocol == 'trojan') {
                if($security == "xtls"){
                    $keyboard = [
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        ($serverType != "marzban"?[
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ]:[]),
                        ($serverType != "marzban"?[
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_trojan":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]:[]),
                    ];
                    
                    $temp = array();
                    if($price != 0 && $agentBought == true){
                        if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                        if($botState['switchLocationState']=="on") $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date']];
                    }
                    if(count($temp)>0) array_push($keyboard, $temp);
                }else{
                    $keyboard = [
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        ($serverType != "marzban"?[
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ]:[]),
                        ($serverType != "marzban"?[
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_trojan":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vmess":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]:[]),
                    ];
                    
                    
                    $temp = array();
                    if($price != 0 || $agentBought == true){
                        if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                        if($botState['switchLocationState']=="on") $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date'] ];
                    }
                    if(count($temp)>0) array_push($keyboard, $temp);
                }
            }else {
                if($netType == "grpc"){
                    $keyboard = [
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        ($serverType != "marzban"?[
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ]:[]),
                        ($serverType != "marzban"?[
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vmess":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]:[])
                    ];
                    
                    
                    $temp = array();
                    if($price != 0 || $agentBought == true){
                        if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                        if($botState['switchLocationState']=="on") $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date'] ];
                    }
                    if(count($temp)>0) array_push($keyboard, $temp);
                }
                elseif($netType == "tcp" && $security == "xtls"){
                    $keyboard = [
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        ($serverType != "marzban"?[
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ]:[]),
                        ($serverType != "marzban"?[
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_trojan":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]:[])
                    ];
                    
                    $temp = array();
                    if($price != 0 || $agentBought == true){
                        if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                        if($botState['switchLocationState']=="on") $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date'] ];
                    }
                    if(count($temp)>0) array_push($keyboard, $temp);

                }
                else{
                    $keyboard = [
                        [
            			    ['text' => "$name", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                        ],
                        [
            			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                            ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
            			],
                        ($serverType != "marzban"?[
                            ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                        ]:[]),
                        ($serverType != "marzban"?($rahgozar == true?
                        [
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vmess":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]:
                            [
                            ['text' => $protocol == 'trojan' ? '☑️ trojan' : 'trojan', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_trojan":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vmess' ? '☑️ vmess' : 'vmess', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vmess":"changeProtocolIsDisable")],
                            ['text' => $protocol == 'vless' ? '☑️ vless' : 'vless', 'callback_data' => ($botState['changeProtocolState']=="on"?"changeAccProtocol{$fid}_{$id}_vless":"changeProtocolIsDisable")],
                        ]):[])
                    ];
                    
                    $temp = array();
                    if($price != 0 || $agentBought == true){
                        if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                        if($botState['switchLocationState']=="on" && $rahgozar != true) $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date'] ];
                    }
                    if(count($temp)>0) array_push($keyboard, $temp);

                }
            }
        }else{
            $keyboard = [
                [
    			    ['text' => "$name", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['plan_name'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$date ", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['buy_date'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => "$expire_date ", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['expire_date'], 'callback_data' => "wizwizch"],
                ],
                [
    			    ['text' => " $leftgb", 'callback_data' => "wizwizch"],
                    ['text' => $buttonValues['volume_left'], 'callback_data' => "wizwizch"],
    			],
    			($serverType != "marzban"?[
                    ['text' => $buttonValues['selected_protocol'], 'callback_data' => "wizwizch"],
                ]:[]),
                ($serverType != "marzban"?[
                    ['text' => " $protocol ☑️", 'callback_data' => "wizwizch"],
                ]:[])
            ];
            
            $temp = array();
            if($price != 0 || $agentBought == true){
                if($botState['renewAccountState']=="on") $temp[] = ['text' => $buttonValues['renew_config'], 'callback_data' => "renewAccount$id" ];
                if($botState['switchLocationState']=="on" && $rahgozar != true) $temp[] = ['text' => $buttonValues['change_config_location'], 'callback_data' => "switchLocation{$id}_{$server_id}_{$leftgb}_".$order['expire_date'] ];
            }
            if(count($temp)>0) array_push($keyboard, $temp);

        }


        $stmt= $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if($serverType == "marzban") $subLink = $botState['subLinkState'] == "on"?"<code>" . $panel_url . "/sub/" . $token . "</code>":"";
        else $subLink = $botState['subLinkState']=="on"?"<code>" . $botUrl . "settings/subLink.php?token=" . $token . "</code>":"";

        $enable = $enable == true? $buttonValues['active']:$buttonValues['deactive'];
        $msg = str_replace(['STATE', 'NAME','CONNECT-LINK', 'SUB-LINK'], [$enable, $remark, $configLinks, $subLink], $mainValues['config_details_message']);
        
        
        $extrakey = [];
        if($botState['increaseVolumeState']=="on" && ($price != 0 || $agentBought == true)) $extrakey[] = ['text' => $buttonValues['increase_config_volume'], 'callback_data' => "increaseAVolume{$id}"];
        if($botState['increaseTimeState']=="on" && ($price != 0 || $agentBought == true)) $extrakey[] = ['text' => $buttonValues['increase_config_days'], 'callback_data' => "increaseADay{$id}"];
        $keyboard[] = $extrakey;
        
         
        if($botState['renewConfigLinkState'] == "on" && $botState['updateConfigLinkState'] == "on") $keyboard[] = [['text'=>$buttonValues['renew_connection_link'],'callback_data'=>'changAccountConnectionLink' . $id],['text'=>$buttonValues['update_config_connection'],'callback_data'=>'updateConfigConnectionLink' . $id]];
        elseif($botState['renewConfigLinkState'] == "on") $keyboard[] = [['text'=>$buttonValues['renew_connection_link'],'callback_data'=>'changAccountConnectionLink' . $id]];
        elseif($botState['updateConfigLinkState'] == "on") $keyboard[] = [['text'=>$buttonValues['update_config_connection'],'callback_data'=>'updateConfigConnectionLink' . $id]];
        
        $temp = [];
        if($botState['qrConfigState'] == "on") $temp[] = ['text'=>$buttonValues['qr_config'],'callback_data'=>"showQrConfig" . $id];
        if($botState['qrSubState'] == "on") $temp[] = ['text'=>$buttonValues['qr_sub'],'callback_data'=>"showQrSub" . $id];
        array_push($keyboard, $temp);
        
        $keyboard[] = [['text' => $buttonValues['delete_config'], 'callback_data' => "deleteMyConfig" . $id]];
        $keyboard[] = [['text' => $buttonValues['back_button'], 'callback_data' => ($agentBought == true?"agentConfigsList":"mySubscriptions")]];
        return ["keyboard"=>json_encode([
                    'inline_keyboard' => $keyboard
                ]),
                "msg"=>$msg];
    }
}

function RandomString($count = 9, $type = "all") {
    if($type == "all") $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
    elseif($type == "small") $characters = 'abcdef123456789';
    elseif($type == "domain") $characters = 'abcdefghijklmnopqrstuvwxyz';
    
    $randstring = null;
    for ($i = 0; $i < $count; $i++) {
        $randstring .= $characters[
            rand(0, strlen($characters)-1)
        ];
    }
    return $randstring;
}
function generateUID(){
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
    
    if($table == "server_plans") $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `active` = 0");
    if($table == "server_categories") $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `active` = 0");
    
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $res['step']; 
}
function setUser($value = 'none', $field = 'step'){
    global $connection, $from_id, $username, $first_name;

    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $uinfo = $stmt->get_result();
    $stmt->close();

    
    if($uinfo->num_rows == 0){
        $stmt = $connection->prepare("INSERT INTO `users` (`userid`, `name`, `username`, `refcode`, `wallet`, `date`)
                            VALUES (?,?,?, 0,0,?)");
        $time = time();
        $stmt->bind_param("issi", $from_id, $first_name, $username, $time);
        $stmt->execute();
        $stmt->close();
    }
    
    if($field == "wallet") $stmt = $connection->prepare("UPDATE `users` SET `wallet` = ? WHERE `userid` = ?");
    elseif($field == "phone") $stmt = $connection->prepare("UPDATE `users` SET `phone` = ? WHERE `userid` = ?");
    elseif($field == "refered_by") $stmt = $connection->prepare("UPDATE `users` SET `refered_by` = ? WHERE `userid` = ?");
    elseif($field == "step") $stmt = $connection->prepare("UPDATE `users` SET `step` = ? WHERE `userid` = ?");
    elseif($field == "freetrial") $stmt = $connection->prepare("UPDATE `users` SET `freetrial` = ? WHERE `userid` = ?");
    elseif($field == "isAdmin") $stmt = $connection->prepare("UPDATE `users` SET `isAdmin` = ? WHERE `userid` = ?");
    elseif($field == "first_start") $stmt = $connection->prepare("UPDATE `users` SET `first_start` = ? WHERE `userid` = ?");
    elseif($field == "temp") $stmt = $connection->prepare("UPDATE `users` SET `temp` = ? WHERE `userid` = ?");
    elseif($field == "is_agent") $stmt = $connection->prepare("UPDATE `users` SET `is_agent` = ? WHERE `userid` = ?");
    elseif($field == "discount_percent") $stmt = $connection->prepare("UPDATE `users` SET `discount_percent` = ? WHERE `userid` = ?");
    elseif($field == "agent_date") $stmt = $connection->prepare("UPDATE `users` SET `agent_date` = ? WHERE `userid` = ?");
    elseif($field == "spam_info") $stmt = $connection->prepare("UPDATE `users` SET `spam_info` = ? WHERE `userid` = ?");
    
    $stmt->bind_param("si", $value, $from_id);
    $stmt->execute();
    $stmt->close();
}
function generateRandomString($length, $protocol) {
    return ($protocol == 'trojan') ? substr(md5(time()),5,15) : generateUID();
}
function addBorderImage($add){
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

function sumerize2($amount){
    $gb = $amount / (1024 * 1024 * 1024);
    return round($gb,2);
}
function deleteClient($server_id, $inbound_id, $uuid, $delete = 0){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $serverType = $server_info['type'];

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    $old_data = []; $oldclientstat = [];
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $settings = json_decode($row->settings);
            $clients = $settings->clients;

            $clientsStates = $row->clientStats;
            foreach($clients as $key => $client){
                if($client->id == $uuid || $client->password == $uuid){
                    $old_data = $client;
                    unset($clients[$key]);
                    $email = $client->email;
                    $emails = array_column($clientsStates,'email');
                    $emailKey = array_search($email,$emails);
                    
                    $total = $clientsStates[$emailKey]->total;
                    $up = $clientsStates[$emailKey]->up;
                    $enable = $clientsStates[$emailKey]->enable;
                    $down = $clientsStates[$emailKey]->down; 
                    break;
                }
            }
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
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);
        
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
        $session = $match[1];
        
        $loginResponse = json_decode($body,true);
        
        if(!$loginResponse['success']){
            curl_close($curl);
            return $loginResponse;
        }
        
        if($serverType == "sanaei" || $serverType == "alireza"){
            if($serverType == "sanaei") $url = "$panel_url/panel/inbound/" . $inbound_id . "/delClient/" . rawurlencode($uuid);
            elseif($serverType == "alireza") $url = "$panel_url/xui/inbound/" . $inbound_id . "/delClient/" . rawurlencode($uuid);

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
                CURLOPT_POSTFIELDS => $dataArr,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array(
                    'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                    'Accept:  application/json, text/plain, */*',
                    'Accept-Language:  en-US,en;q=0.5',
                    'Accept-Encoding:  gzip, deflate',
                    'X-Requested-With:  XMLHttpRequest',
                    'Cookie: ' . $session
                )
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
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array(
                    'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                    'Accept:  application/json, text/plain, */*',
                    'Accept-Language:  en-US,en;q=0.5',
                    'Accept-Encoding:  gzip, deflate',
                    'X-Requested-With:  XMLHttpRequest',
                    'Cookie: ' . $session
                )
            ));
        }
        
        $response = curl_exec($curl);
        curl_close($curl);
    }	
    return ['id' => $old_data->id,'expiryTime' => $old_data->expiryTime, 'limitIp' => $old_data->limitIp, 'flow' => $old_data->flow, 'total' => $total, 'up' => $up, 'down' => $down,];

}
function editInboundRemark($server_id, $uuid, $newRemark){
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
    foreach($response as $row){
        $clients = json_decode($row->settings)->clients;
        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
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


    $dataArr = array('up' => $up,'down' => $down,'total' => $total,'remark' => $newRemark,'enable' => 'true',
        'expiryTime' => $row->expiryTime, 'listen' => '','port' => $row->port,'protocol' => $row->protocol,'settings' => $row->settings,
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];
    
    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$inbound_id";
    else $url = "$panel_url/xui/inbound/update/$inbound_id";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,      // timeout on connect
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
}
function editInboundTraffic($server_id, $uuid, $volume, $days, $editType = null){
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
    foreach($response as $row){
        $clients = json_decode($row->settings)->clients;
        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
            $inbound_id = $row->id;
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $expiryTime = $row->expiryTime;
            $port = $row->port;
            $netType = json_decode($row->streamSettings)->network;
            
            $email = $clients[0]->email;

            break;
        }
    }
    if($days != 0) {
        $now_microdate = floor(microtime(true) * 1000);
        $extend_date = (864000 * $days * 100);
        if($editType == "renew") $expire_microdate = $now_microdate + $extend_date;
        else $expire_microdate = ($now_microdate > $expiryTime) ? $now_microdate + $extend_date : $expiryTime + $extend_date;
    }

    if($volume != 0){
        $leftGB = $total - $up - $down;
        $extend_volume = floor($volume * 1073741824);
        if($editType == "renew"){
            $total = $extend_volume;
            $up = 0;
            $down = 0;
            $volume = $extend_volume;
            if($serverType == "sanaei" || $serverType == "alireza") resetClientTraffic($server_id, $email, $inbound_id);
            else resetClientTraffic($server_id, $email);
        }
        else $total = ($leftGB > 0) ? $total + $extend_volume : $extend_volume;
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);
    
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$inbound_id";
    else $url = "$panel_url/xui/inbound/update/$inbound_id";

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,      // timeout on connect
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    resetIpLog($server_id, $email);
    return $response = json_decode($response);
}
function changeInboundState($server_id, $uuid){
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
    foreach($response as $row){
        $settings = json_decode($row->settings, true);
        $clients = $settings['clients'];
        if($clients[0]['id'] == $uuid || $clients[0]['password'] == $uuid) {
            $inbound_id = $row->id;
            $enable = $row->enable;
            break;
        }
    }
    
    if(!isset($settings['clients'][0]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][0]['subId'] = RandomString(16);
    if(!isset($settings['clients'][0]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][0]['enable'] = true;

    $editedClient = $settings['clients'][$client_key];
    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings,488);

    $newEnable = $enable == true?false:true;
    
    $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $row->remark,'enable' => $newEnable,
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);
    
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];


    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$inbound_id";
    else $url = "$panel_url/xui/inbound/update/$inbound_id";

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,      // timeout on connect
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response);
    return $response;

}
function renewInboundUuid($server_id, $uuid){
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
    foreach($response as $row){
        $settings = json_decode($row->settings, true);
        $clients = $settings['clients'];
        if($clients[0]['id'] == $uuid || $clients[0]['password'] == $uuid) {
            $inbound_id = $row->id;
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $expiryTime = $row->expiryTime;
            $port = $row->port;
            $protocol = $row->protocol;
            $netType = json_decode($row->streamSettings)->network;
            break;
        }
    }
    
    $newUuid = generateRandomString(42,$protocol); 
    if($protocol == "trojan") $settings['clients'][0]['password'] = $newUuid;
    else $settings['clients'][0]['id'] = $newUuid;
    if(!isset($settings['clients'][0]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][0]['subId'] = RandomString(16);
    if(!isset($settings['clients'][0]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][0]['enable'] = true;

    $editedClient = $settings['clients'][$client_key];
    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings,488);


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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$inbound_id";
    else $url = "$panel_url/xui/inbound/update/$inbound_id";

    $phost = str_ireplace('https://','',str_ireplace('http://','',$panel_url));
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 15,      // timeout on connect
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response);
    $response->newUuid = $newUuid;
    return $response;

}
function changeClientState($server_id, $inbound_id, $uuid){
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
    $client_key = -1;
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $settings = json_decode($row->settings, true);
            $clients = $settings['clients'];
            
            foreach($clients as $key => $client){
                if($client['id'] == $uuid || $client['password'] == $uuid){
                    $client_key = $key;
                    $enable = $client['enable'];
                    break;
                }
            }
        }
    }
    if($client_key == -1) return null;
    
    if(!isset($settings['clients'][$client_key]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['subId'] = RandomString(16);
    $settings['clients'][$client_key]['enable'] = $enable == true?false:true;

    $editedClient = $settings['clients'][$client_key];
    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings,488);
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei" || $serverType == "alireza"){
        
        $newSetting = array();
        $newSetting['clients'][] = $editedClient;
        $newSetting = json_encode($newSetting);

        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $newSetting
            );
            
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/updateClient/" . rawurlencode($uuid);
        else $url = "$panel_url/xui/inbound/updateClient/" . rawurlencode($uuid);
        
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
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
    }

    $response = curl_exec($curl);
    $response = json_decode($response);
    curl_close($curl);
    return $response;

}
function renewClientUuid($server_id, $inbound_id, $uuid){
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
    $client_key = -1;
    foreach($response as $row){
        if($row->id == $inbound_id) {
            $settings = json_decode($row->settings, true);
            $clients = $settings['clients'];
            
            foreach($clients as $key => $client){
                if($client['id'] == $uuid || $client['password'] == $uuid){
                    $protocol = $row->protocol;
                    $client_key = $key;
                    break;
                }
            }
        }
    }
    if($client_key == -1) return null;
    
    $newUuid = generateRandomString(42,$protocol); 
    if($protocol == "trojan") $settings['clients'][$client_key]['password'] = $newUuid;
    else $settings['clients'][$client_key]['id'] = $newUuid;
    if(!isset($settings['clients'][$client_key]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['subId'] = RandomString(16);
    if(!isset($settings['clients'][$client_key]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['enable'] = true;

    $editedClient = $settings['clients'][$client_key];
    $settings['clients'] = array_values($settings['clients']);
    $settings = json_encode($settings,488);
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }

    if($serverType == "sanaei" || $serverType == "alireza"){
        
        $newSetting = array();
        $newSetting['clients'][] = $editedClient;
        $newSetting = json_encode($newSetting);

        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $newSetting
            );
            
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/updateClient/" . rawurlencode($uuid);
        else $url = "$panel_url/xui/inbound/updateClient/" . rawurlencode($uuid);
        
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
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
    }

    $response = curl_exec($curl);
    $response = json_decode($response);
    $response->newUuid = $newUuid;

    curl_close($curl);
    return $response;

}
function editClientRemark($server_id, $inbound_id, $uuid, $newRemark){
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
            
            $clientsStates = $row->clientStats;
            foreach($clients as $key => $client){
                if($client['id'] == $uuid || $client['password'] == $uuid){
                    $client_key = $key;
                    $email = $client['email'];
                    $emails = array_column($clientsStates,'email');
                    $emailKey = array_search($email,$emails);
                    
                    $total = $clientsStates[$emailKey]->total;
                    $up = $clientsStates[$emailKey]->up;
                    $enable = $clientsStates[$emailKey]->enable;
                    $down = $clientsStates[$emailKey]->down; 
                    break;
                }
            }
        }
    }
    $settings['clients'][$client_key]['email'] = $newRemark;
    if(!isset($settings['clients'][$client_key]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['subId'] = RandomString(16);
    if(!isset($settings['clients'][$client_key]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['enable'] = true;

    $editedClient = $settings['clients'][$client_key];
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse; 
    } 

    if($serverType == "sanaei" || $serverType == "alireza"){
        
        $newSetting = array();
        $newSetting['clients'][] = $editedClient;
        $newSetting = json_encode($newSetting);

        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $newSetting
            );
            
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/updateClient/" . rawurlencode($uuid);
        else $url = "$panel_url/xui/inbound/updateClient/" . rawurlencode($uuid);
        
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
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
    }

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);

}
function editClientTraffic($server_id, $inbound_id, $uuid, $volume, $days, $editType = null){
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
            
            $clientsStates = $row->clientStats;
            foreach($clients as $key => $client){
                if($client['id'] == $uuid || $client['password'] == $uuid){
                    $client_key = $key;
                    $email = $client['email'];
                    $emails = array_column($clientsStates,'email');
                    $emailKey = array_search($email,$emails);
                    
                    $total = $clientsStates[$emailKey]->total;
                    $up = $clientsStates[$emailKey]->up;
                    $enable = $clientsStates[$emailKey]->enable;
                    $down = $clientsStates[$emailKey]->down; 
                    break;
                }
            }
        }
    }
    if($volume != 0){
        $client_total = $settings['clients'][$client_key]['totalGB'];// - $up - $down;
        $extend_volume = floor($volume * 1073741824);
        $volume = ($client_total > 0) ? $client_total + $extend_volume : $extend_volume;
        if($editType == "renew"){
            $volume = $extend_volume;
            if($serverType == "sanaei" || $serverType == "alireza") resetClientTraffic($server_id, $email, $inbound_id);
            else resetClientTraffic($server_id, $email);
        }
        $settings['clients'][$client_key]['totalGB'] = $volume;
        if(!isset($settings['clients'][$client_key]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['subId'] = RandomString(16);
        if(!isset($settings['clients'][$client_key]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['enable'] = true;
    }
    
    if($days != 0){
        $expiryTime = $settings['clients'][$client_key]['expiryTime'];
        $now_microdate = floor(microtime(true) * 1000);
        $extend_date = (864000 * $days * 100);
        if($editType == "renew") $expire_microdate = $now_microdate + $extend_date;
        else $expire_microdate = ($now_microdate > $expiryTime) ? $now_microdate + $extend_date : $expiryTime + $extend_date;
        $settings['clients'][$client_key]['expiryTime'] = $expire_microdate;
        if(!isset($settings['clients'][$client_key]['subId']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['subId'] = RandomString(16);
        if(!isset($settings['clients'][$client_key]['enable']) && ($serverType == "sanaei" || $serverType == "alireza")) $settings['clients'][$client_key]['enable'] = true;
    }
    $editedClient = $settings['clients'][$client_key];
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);
    
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse; 
    } 

    if($serverType == "sanaei" || $serverType == "alireza"){
        
        $newSetting = array();
        $newSetting['clients'][] = $editedClient;
        $newSetting = json_encode($newSetting);

        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $newSetting
            );
            
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/updateClient/" . rawurlencode($uuid);
        else $url = "$panel_url/xui/inbound/updateClient/" . rawurlencode($uuid);
        
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
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
    }

    $response = curl_exec($curl);
    curl_close($curl);
    resetIpLog($server_id, $email);
    return $response = json_decode($response);

}
function deleteInbound($server_id, $uuid, $delete = 0){
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
    foreach($response as $row){
        $clients = json_decode($row->settings)->clients;
        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
            $inbound_id = $row->id;
            $protocol = $row->protocol;
            $uniqid = ($protocol == 'trojan') ? json_decode($row->settings)->clients[0]->password : json_decode($row->settings)->clients[0]->id;
            $netType = json_decode($row->streamSettings)->network;
            $oldData = [
                'total' => $row->total,
                'up' => $row->up,
                'down' => $row->down,
                'volume' => ((int)$row->total - (int)$row->up - (int)$row->down),
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
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
        $session = $match[1];

        $loginResponse = json_decode($body,true);
        if(!$loginResponse['success']){
            curl_close($curl);
            return $loginResponse;
        }
        
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/del/$inbound_id";
        else $url = "$panel_url/xui/inbound/del/$inbound_id";
       
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
    return $oldData;
}
function resetIpLog($server_id, $remark){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];


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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    if($serverType == "sanaei") $url = $panel_url. "/panel/inbound/clearClientIps/" . urlencode($remark);
    else $url = $panel_url. "/xui/inbound/clearClientIps/" . urlencode($remark);

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
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
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
    $serverType = $server_info['type'];


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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/$inboundId/resetClientTraffic/" . rawurlencode($remark);
    elseif($inboundId == null) $url = "$panel_url/xui/inbound/resetClientTraffic/" . rawurlencode($remark);
    else $url = "$panel_url/xui/inbound/$inboundId/resetClientTraffic/" . rawurlencode($remark);
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
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
}
function addInboundAccount($server_id, $client_id, $inbound_id, $expiryTime, $remark, $volume, $limitip = 1, $newarr = '', $planId = null){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];
    $reality = $server_info['reality'];
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
    if($newarr == ''){
		if($serverType == "sanaei" || $serverType == "alireza"){
		    if($reality == "true"){
                $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
                $stmt->bind_param("i", $planId);
                $stmt->execute();
                $file_detail = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            
                $flow = isset($file_detail['flow']) && $file_detail['flow'] != "None" ? $file_detail['flow'] : "";
                
                $newClient = [
                    "$id_label" => $client_id,
                    "enable" => true,
                    "email" => $remark,
                    "limitIp" => $limitip,
                    "flow" => $flow,
                    "totalGB" => $volume,
                    "expiryTime" => $expiryTime,
                    "subId" => RandomString(16)
                ];
		    }else{
                $newClient = [
                    "$id_label" => $client_id,
                    "enable" => true,
                    "email" => $remark,
                    "limitIp" => $limitip,
                    "totalGB" => $volume,
                    "expiryTime" => $expiryTime,
                    "subId" => RandomString(16)
                ];
		    }
    	}else{
            $newClient = [
                "$id_label" => $client_id,
                "flow" => "",
                "email" => $remark,
                "limitIp" => $limitip,
                "totalGB" => $volume,
                "expiryTime" => $expiryTime
            ];
		}
        $settings['clients'][] = $newClient;
    }elseif(is_array($newarr)) $settings['clients'][] = $newarr;

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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    if($serverType == "sanaei" || $serverType == "alireza"){
        $newSetting = array();
        if($newarr == '')$newSetting['clients'][] = $newClient;
        elseif(is_array($newarr)) $newSetting['clients'][] = $newarr;
        
        $newSetting = json_encode($newSetting);
        $dataArr = array(
            "id"=>$inbound_id,
            "settings" => $newSetting
            );
            
        if($serverType == "sanaei") $url = "$panel_url/panel/inbound/addClient/";
        else $url = "$panel_url/xui/inbound/addClient/";

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
            CURLOPT_POSTFIELDS => $dataArr,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
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
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
                'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
                'Accept:  application/json, text/plain, */*',
                'Accept-Language:  en-US,en;q=0.5',
                'Accept-Encoding:  gzip, deflate',
                'X-Requested-With:  XMLHttpRequest',
                'Cookie: ' . $session
            )
        ));
    }

    $response = curl_exec($curl);
    curl_close($curl);
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
function getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id = 0, $rahgozar = false, $customPath = false, $customPort = 0, $customSni = null){
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
    $serverType = $server_info['type'];
    preg_match("/^Host:(.*)/i",$request_header,$hostMatch);

    $panel_url = str_ireplace('http://','',$panel_url);
    $panel_url = str_ireplace('https://','',$panel_url);
    $panel_url = strtok($panel_url,":");
    if($server_ip == '') $server_ip = $panel_url;

    $response = getJson($server_id)->obj;
    foreach($response as $row){
        if($inbound_id == 0){
            $clients = json_decode($row->settings)->clients;
            if($clients[0]->id == $uniqid || $clients[0]->password == $uniqid) {
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = json_decode($row->settings,true);
                    $email = $settings['clients'][0]['email'];
                    // $remark = (!empty($row->remark)?($row->remark . "-"):"") . $email;
                    $remark = $row->remark;
                }
                $tlsStatus = json_decode($row->streamSettings)->security;
                $tlsSetting = json_decode($row->streamSettings)->tlsSettings;
                $xtlsSetting = json_decode($row->streamSettings)->xtlsSettings;
                $netType = json_decode($row->streamSettings)->network;
                if($netType == 'tcp') {
                    $header_type = json_decode($row->streamSettings)->tcpSettings->header->type;
                    $path = json_decode($row->streamSettings)->tcpSettings->header->request->path[0];
                    $host = json_decode($row->streamSettings)->tcpSettings->header->request->headers->Host[0];
                    
                    if($tlsStatus == "reality"){
                        $realitySettings = json_decode($row->streamSettings)->realitySettings;
                        $fp = $realitySettings->settings->fingerprint;
                        $spiderX = $realitySettings->settings->spiderX;
                        $pbk = $realitySettings->settings->publicKey;
                        $sni = $realitySettings->serverNames[0];
                        $flow = $settings['clients'][0]['flow'];
                        $sid = $realitySettings->shortIds[0];
                    }
                }
                if($netType == 'ws') {
                    $header_type = json_decode($row->streamSettings)->wsSettings->header->type;
                    $path = json_decode($row->streamSettings)->wsSettings->path;
                    $host = json_decode($row->streamSettings)->wsSettings->headers->Host;
                }
                if($header_type == 'http' && empty($host)){
                    $request_header = explode(':', $request_header);
                    $host = $request_header[1];
                }
                if($netType == 'grpc') {
                    if($tlsStatus == 'tls'){
                        $alpn = $tlsSetting->certificates->alpn;
						if(isset($tlsSetting->serverName)) $sni = $tlsSetting->serverName;
						if(isset($tlsSetting->settings->serverName)) $sni = $tlsSetting->settings->serverName;
                    } 
                    elseif($tlsStatus == "reality"){
                        $realitySettings = json_decode($row->streamSettings)->realitySettings;
                        $fp = $realitySettings->settings->fingerprint;
                        $spiderX = $realitySettings->settings->spiderX;
                        $pbk = $realitySettings->settings->publicKey;
                        $sni = $realitySettings->serverNames[0];
                        $flow = $settings['clients'][0]['flow'];
                        $sid = $realitySettings->shortIds[0];
                    }
                    $serviceName = json_decode($row->streamSettings)->grpcSettings->serviceName;
                    $grpcSecurity = json_decode($row->streamSettings)->security;
                }
                if($tlsStatus == 'tls'){
                    $serverName = $tlsSetting->serverName;
					if(isset($tlsSetting->serverName)) $sni = $tlsSetting->serverName;
                    if(isset($tlsSetting->settings->serverName)) $sni = $tlsSetting->settings->serverName;
                }
                if($tlsStatus == "xtls"){
                    $serverName = $xtlsSetting->serverName;
                    $alpn = $xtlsSetting->alpn;
					if(isset($xtlsSetting->serverName)) $sni = $xtlsSetting->serverName;
                    if(isset($xtlsSetting->settings->serverName)) $sni = $xtlsSetting->settings->serverName;
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
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = json_decode($row->settings);
                    $clients = $settings->clients;
                    foreach($clients as $key => $client){
                        if($client->id == $uniqid || $client->password == $uniqid){
                            $flow = $client->flow;
                            break;
                        }
                    }
                    // $remark = (!empty($row->remark)?($row->remark . "-"):"") . $remark;
                    $remark = $remark;
                }
                
                $port = $row->port;
                $tlsStatus = json_decode($row->streamSettings)->security;
                $tlsSetting = json_decode($row->streamSettings)->tlsSettings;
                $xtlsSetting = json_decode($row->streamSettings)->xtlsSettings;
                $netType = json_decode($row->streamSettings)->network;
                if($netType == 'tcp') {
                    $header_type = json_decode($row->streamSettings)->tcpSettings->header->type;
                    $path = json_decode($row->streamSettings)->tcpSettings->header->request->path[0];
                    $host = json_decode($row->streamSettings)->tcpSettings->header->request->headers->Host[0];
                    
                    if($tlsStatus == "reality"){
                        $realitySettings = json_decode($row->streamSettings)->realitySettings;
                        $fp = $realitySettings->settings->fingerprint;
                        $spiderX = $realitySettings->settings->spiderX;
                        $pbk = $realitySettings->settings->publicKey;
                        $sni = $realitySettings->serverNames[0];
                        $sid = $realitySettings->shortIds[0];
                    }
                }elseif($netType == 'ws') {
                    $header_type = json_decode($row->streamSettings)->wsSettings->header->type;
                    $path = json_decode($row->streamSettings)->wsSettings->path;
                    $host = json_decode($row->streamSettings)->wsSettings->headers->Host;
                }elseif($netType == 'grpc') {
                    if($tlsStatus == 'tls'){
                        $alpn = $tlsSetting->alpn;
						if(isset($tlsSetting->serverName)) $sni = $tlsSetting->serverName;
                        if(isset($tlsSetting->settings->serverName)) $sni = $tlsSetting->settings->serverName;
                    }
                    elseif($tlsStatus == "reality"){
                        $realitySettings = json_decode($row->streamSettings)->realitySettings;
                        $fp = $realitySettings->settings->fingerprint;
                        $spiderX = $realitySettings->settings->spiderX;
                        $pbk = $realitySettings->settings->publicKey;
                        $sni = $realitySettings->serverNames[0];
                        $sid = $realitySettings->shortIds[0];
                    }
                    $grpcSecurity = json_decode($row->streamSettings)->security;
                    $serviceName = json_decode($row->streamSettings)->grpcSettings->serviceName;
                }elseif($netType == 'kcp'){
                    $kcpSettings = json_decode($row->streamSettings)->kcpSettings;
                    $kcpType = $kcpSettings->header->type;
                    $kcpSeed = $kcpSettings->seed;
                }
                if($tlsStatus == 'tls'){
                    $serverName = $tlsSetting->serverName;
					if(isset($tlsSetting->serverName)) $sni = $tlsSetting->serverName;
                    if(isset($tlsSetting->settings->serverName)) $sni = $tlsSetting->settings->serverName;
                }
                if($tlsStatus == "xtls"){
                    $serverName = $xtlsSetting->serverName;
                    $alpn = $xtlsSetting->alpn;
					if(isset($xtlsSetting->serverName)) $sni = $xtlsSetting->serverName;
                    if(isset($xtlsSetting->settings->serverName)) $sni = $xtlsSetting->settings->serverName;
                }

                break;
            }
        }


    }
    $protocol = strtolower($protocol);
    $serverIp = explode("\n",$server_ip);
    $outputLink = array();
    foreach($serverIp as $server_ip){
        $server_ip = str_replace("\r","",($server_ip));
        if($inbound_id == 0) {
            if($protocol == 'vless'){
                if($rahgozar == true){
                    if(empty($host) && isset($hostMatch[1])) $host = $hostMatch[1];
                    
                    if(!empty($host)){
                        $parseAdd = parse_url($host);
                        $parseAdd = $parseAdd['host']??$parseAdd['path'];
                        $explodeAdd = explode(".", $parseAdd);
                        $subDomain = RandomString(4,"domain");
                        if($customSni != null) $sni = $customSni;
                        else{
                            if(count($explodeAdd) >= 3) $sni = $uniqid . "." . $explodeAdd[1] . "." . $explodeAdd[2];
                            else $sni = $uniqid . "." . $host;
                        }
                    }
                }
                $psting = '';
                if(($header_type == 'http' && $rahgozar != true && $netType != "grpc") || ($netType == "ws" && !empty($host) && $rahgozar != true)) $psting .= "&path=/&host=$host";;
                if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                if(strlen($sni) > 1 && $tlsStatus != "reality") $psting .= "&sni=$sni";
                if(strlen($serverName)>1 && $tlsStatus=="xtls") $server_ip = $serverName;
                if($tlsStatus == "xtls" && $netType == "tcp") $psting .= "&flow=xtls-rprx-direct";
                if($tlsStatus=="reality") $psting .= "&fp=$fp&pbk=$pbk&sni=$sni" . ($flow != ""?"&flow=$flow":"") . "&sid=$sid&spx=$spiderX";
                if($rahgozar == true) $psting .= "&path=" . rawurlencode($path . ($customPath == true?"?ed=2048":"")) . "&encryption=none&host=$host";
                $outputlink = "$protocol://$uniqid@$server_ip:" . ($rahgozar == true?($customPort!="0"?$customPort:"443"):$port) . "?type=$netType&security=" . ($rahgozar==true?"tls":$tlsStatus) . "{$psting}#$remark";
                if($netType == 'grpc' && $tlsStatus != "reality"){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$sni#$remark";
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
                $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus{$psting}#$remark";
                
                if($netType == 'grpc'){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$sni#$remark";
                    }else{
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName#$remark";
                    }
    
                }
            }elseif($protocol == 'vmess'){
                $vmessArr = [
                    "v"=> "2",
                    "ps"=> $remark,
                    "add"=> $server_ip,
                    "port"=> $rahgozar == true?($customPort!=0?$customPort:443):$port,
                    "id"=> $uniqid,
                    "aid"=> 0,
                    "net"=> $netType,
                    "type"=> $kcpType ? $kcpType : "none",
                    "host"=> ($rahgozar == true && empty($host))? $server_ip:(is_null($host) ? '' : $host),
                    "path"=> ($rahgozar == true)?($path . ($customPath == true?"?ed=2048":"")):((is_null($path) and $path != '') ? '/' : (is_null($path) ? '' : $path)),
                    "tls"=> $rahgozar == true?"tls":((is_null($tlsStatus)) ? 'none' : $tlsStatus)
                ];
                
                if($rahgozar == true){
                    if(empty($host) && isset($hostMatch[1])) $host = $hostMatch[1];
                    
                    if(!empty($host)){
                        $parseAdd = parse_url($host);
                        $parseAdd = $parseAdd['host']??$parseAdd['path'];
                        $explodeAdd = explode(".", $parseAdd);
                        $subDomain = RandomString(4,"domain");
                        if($customSni != null) $sni = $customSni;
                        else{
                            if(count($explodeAdd) >= 3) $sni = $uniqid . "." . $explodeAdd[1] . "." . $explodeAdd[2];
                            else $sni = $uniqid . "." . $host;
                        }
    
                        $vmessArr['alpn'] = 'http/1.1';
                    }
                }
                if($header_type == 'http' && $rahgozar != true){
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
                if($rahgozar == true){
                    if(empty($host) && isset($hostMatch[1])) $host = $hostMatch[1];
                    
                    if(!empty($host)){
                        $parseAdd = parse_url($host);
                        $parseAdd = $parseAdd['host']??$parseAdd['path'];
                        $explodeAdd = explode(".", $parseAdd);
                        $subDomain = RandomString(4,"domain");
                        if($customSni != null) $sni = $customSni;
                        else{
                            if(count($explodeAdd) >= 3) $sni = $uniqid . "." . $explodeAdd[1] . "." . $explodeAdd[2];
                            else $sni = $uniqid . "." .$host;
                        }
                    }
                }
                
                if(strlen($sni) > 1 && $tlsStatus != "reality") $psting = "&sni=$sni"; else $psting = '';
                if($netType == 'tcp'){
                    if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                    if($tlsStatus=="xtls") $psting .= "&flow=xtls-rprx-direct";
                    if($tlsStatus=="reality") $psting .= "&fp=$fp&pbk=$pbk&sni=$sni" . ($flow != ""?"&flow=$flow":"") . "&sid=$sid&spx=$spiderX";
                    if($header_type == "http") $psting .= "&path=/&host=$host";
                    $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus{$psting}#$remark";
                }elseif($netType == 'ws'){
                    if($rahgozar == true)$outputlink = "$protocol://$uniqid@$server_ip:" . ($customPort!=0?$customPort:"443") . "?type=$netType&security=tls&path=" . rawurlencode($path . ($customPath == true?"?ed=2048":"")) . "&encryption=none&host=$host{$psting}#$remark";
                    else $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&path=/&host=$host{$psting}#$remark";
                }
                elseif($netType == 'kcp')
                    $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&headerType=$kcpType&seed=$kcpSeed#$remark";
                elseif($netType == 'grpc'){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$sni#$remark";
                    }
                    elseif($tlsStatus=="reality"){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&fp=$fp&pbk=$pbk&sni=$sni" . ($flow != ""?"&flow=$flow":"") . "&sid=$sid&spx=$spiderX#$remark";
                    }
                    else{
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName#$remark";
                    }
                }
            }elseif($protocol == 'trojan'){                
                $psting = '';
                if($header_type == 'http') $psting .= "&path=/&host=$host";
                if($netType == 'tcp' and $header_type == 'http') $psting .= '&headerType=http';
                if(strlen($sni) > 1) $psting .= "&sni=$sni";
                $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus{$psting}#$remark";
                
                if($netType == 'grpc'){
                    if($tlsStatus == 'tls'){
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName&sni=$sni#$remark";
                    }else{
                        $outputlink = "$protocol://$uniqid@$server_ip:$port?type=$netType&security=$tlsStatus&serviceName=$serviceName#$remark";
                    }
    
                }
            }elseif($protocol == 'vmess'){
                $vmessArr = [
                    "v"=> "2",
                    "ps"=> $remark,
                    "add"=> $server_ip,
                    "port"=> $rahgozar == true?($customPort!=0?$customPort:443):$port,
                    "id"=> $uniqid,
                    "aid"=> 0,
                    "net"=> $netType,
                    "type"=> ($header_type) ? $header_type : ($kcpType ? $kcpType : "none"),
                    "host"=> ($rahgozar == true && empty($host))?$server_ip:(is_null($host) ? '' : $host),
                    "path"=> ($rahgozar == true)?($path . ($customPath == true?"?ed=2048":"")) :((is_null($path) and $path != '') ? '/' : (is_null($path) ? '' : $path)),
                    "tls"=> $rahgozar == true?"tls":((is_null($tlsStatus)) ? 'none' : $tlsStatus)
                ];
                if($rahgozar == true){
                    if(empty($host) && isset($hostMatch[1])) $host = $hostMatch[1];
                    
                    if(!empty($host)){
                        $subDomain = RandomString(4, "domain");
                        $parseAdd = parse_url($host);
                        $parseAdd = $parseAdd['host']??$parseAdd['path'];
                        $explodeAdd = explode(".", $parseAdd);
                        if($customSni != null) $sni = $customSni;
                        else{
                            if(count($explodeAdd) >= 3) $sni = $uniqid . "." . $explodeAdd[1] . "." .$explodeAdd[2];
                            else $sni = $uniqid . "." . $host;
                        }
                        
                        $vmessArr['alpn'] = 'http/1.1';
                    }
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
        }
        $outputLink[] = $outputlink;
    }

    return $outputLink;
}
function updateConfig($server_id, $inboundId, $protocol, $netType = 'tcp', $security = 'none', $rahgozar = false){
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
    $xtlsTitle = ($serverType == "sanaei" || $serverType == "alireza")?"XTLSSettings":"xtlsSettings";
    $sni = $server_info['sni'];
    if(!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza")){
        $tlsSettings = json_decode($tlsSettings,true);
        $tlsSettings['serverName'] = $sni;
        $tlsSettings = json_encode($tlsSettings,488|JSON_UNESCAPED_UNICODE);
    }
    
    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        if($row->id == $inboundId) {
            $iid = $row->id;
            $remark = $row->remark;
            $streamSettings = $row->streamSettings;
            $settings = $row->settings;
            break;
        }
    }
    if(!intval($iid)) return;
    $headers = getNewHeaders($netType, $request_header, $response_header, $header_type);
    $headers = empty($headers)?"{}":$headers;

    if($protocol == 'trojan'){
        if($security == 'none'){
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

        }elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
            
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
        	  "' . $xtlsTitle . '": '.$tlsSettings.',
              "wsSettings": {
                "path": "/",
                "headers": '.$headers.'
              }
            }';
        }
        else{
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
        }
        
        
                $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
		if($netType == 'grpc'){
		    $keyFileInfo = json_decode($tlsSettings,true);
		    $certificateFile = "/root/cert.crt";
		    $keyFile = '/root/private.key';
		    
		    if(isset($keyFileInfo['certificates'])){
		        $certificateFile = $keyFileInfo['certificates'][0]['certificateFile'];
		        $keyFile = $keyFileInfo['certificates'][0]['keyFile'];
		    }
			if($security == 'tls') {
				$streamSettings = '{
  "network": "grpc",
  "security": "tls",
  "tlsSettings": {
    "serverName": "' .
    (!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza") ?  $sni: parse_url($panel_url, PHP_URL_HOST))
     . '",
    "certificates": [
      {
        "certificateFile": "' . $certificateFile . '",
        "keyFile": "' . $keyFile . '"
      }
    ],
    "alpn": []'
    .'
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
    "serviceName": "' . parse_url($panel_url, PHP_URL_HOST) . '"
  }
}';
		}
	    }


        $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $remark,'enable' => 'true',
            'expiryTime' => $row->expiryTime,'listen' => '','port' => $row->port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings,
            'sniffing' => $row->sniffing);
    }else{
        if($netType != "grpc"){
            if($rahgozar == true){
                $wsSettings = '{
                      "network": "ws",
                      "security": "none",
                      "wsSettings": {
                        "path": "/wss' . $row->port . '",
                        "headers": {}
                      }
                    }';
            }
            else{
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
                }
                elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
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
            	  "' . $xtlsTitle . '": '.$tlsSettings.',
                  "wsSettings": {
                    "path": "/",
                    "headers": '.$headers.'
                  }
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
                }
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$iid";
    else $url = "$panel_url/xui/inbound/update/$iid";
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
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
}
function editInbound($server_id, $uniqid, $uuid, $protocol, $netType = 'tcp', $security = 'none', $rahgozar = false){
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
    $xtlsTitle = ($serverType == "sanaei" || $serverType == "alireza")?"XTLSSettings":"xtlsSettings";
    $sni = $server_info['sni'];
    if(!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza")){
        $tlsSettings = json_decode($tlsSettings,true);
        $tlsSettings['serverName'] = $sni;
        $tlsSettings = json_encode($tlsSettings);
    }

    $response = getJson($server_id);
    if(!$response) return null;
    $response = $response->obj;
    foreach($response as $row){
        $clients = json_decode($row->settings)->clients;
        if($clients[0]->id == $uuid || $clients[0]->password == $uuid) {
            $iid = $row->id;
            $remark = $row->remark;
            $streamSettings = $row->streamSettings;
            $settings = $row->settings;
            break;
        }
    }
    if(!intval($iid)) return;

    $headers = getNewHeaders($netType, $request_header, $response_header, $header_type);
    $headers = empty($headers)?"{}":$headers;

    if($protocol == 'trojan'){
        if($security == 'none'){
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

    	if($serverType == "sanaei" || $serverType == "alireza"){
            $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$uniqid.'",
                  "enable": true,
        		  "email": "' . $remark. '",
                  "limitIp": 0,
                  "totalGB": 0,
                  "expiryTime": 0,
                  "subId": "' . RandomString(16) . '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
    	}else{
            $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$uniqid.'",
        		  "flow": "",
        		  "email": "' . $remark. '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
    	}
        }elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
            
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
        	  "' . $xtlsTitle . '": '.$tlsSettings.',
              "wsSettings": {
                "path": "/",
                "headers": '.$headers.'
              }
            }';

                $settings = '{
              "clients": [
                {
                  "id": "'.$uniqid.'",
    			  "flow": "xtls-rprx-direct".
    			  "email": "' . $remark. '"
                }
              ],
              "decryption": "none",
        	  "fallbacks": []
            }';
        }
        else{
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
		if($serverType == "sanaei" || $serverType == "alireza"){
            $settings = '{
		  "clients": [
			{
			  "password": "'.$uniqid.'",
              "enable": true,
			  "email": "' . $remark. '",
              "limitIp": 0,
              "totalGB": 0,
              "expiryTime": 0,
              "subId": "' . RandomString(16) . '"
			}
		  ],
		  "fallbacks": []
		}';
		}else{
            $settings = '{
		  "clients": [
			{
			  "password": "'.$uniqid.'",
			  "flow": "",
			  "email": "' . $remark. '"
			}
		  ],
		  "fallbacks": []
		}';
		}
        }
        
        
                $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
		if($netType == 'grpc'){
		    $keyFileInfo = json_decode($tlsSettings,true);
		    $certificateFile = "/root/cert.crt";
		    $keyFile = '/root/private.key';
		    
		    if(isset($keyFileInfo['certificates'])){
		        $certificateFile = $keyFileInfo['certificates'][0]['certificateFile'];
		        $keyFile = $keyFileInfo['certificates'][0]['keyFile'];
		    }

			if($security == 'tls') {
				$streamSettings = '{
  "network": "grpc",
  "security": "tls",
  "tlsSettings": {
    "serverName": "' .
    (!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza") ?  $sni: parse_url($panel_url, PHP_URL_HOST))
     . '",
    "certificates": [
      {
        "certificateFile": "' . $certificateFile . '",
        "keyFile": "' . $keyFile . '"
      }
    ],
    "alpn": []'
    .'
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
    "serviceName": "' . parse_url($panel_url, PHP_URL_HOST) . '"
  }
}';
		}
	    }


        $dataArr = array('up' => $row->up,'down' => $row->down,'total' => $row->total,'remark' => $remark,'enable' => 'true',
            'expiryTime' => $row->expiryTime,'listen' => '','port' => $row->port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings,
            'sniffing' => $row->sniffing);
    }else{
        if($netType != "grpc"){
            if($rahgozar == true){
                $wsSettings = '{
                      "network": "ws",
                      "security": "none",
                      "wsSettings": {
                        "path": "/wss' . $row->port . '",
                        "headers": {}
                      }
                    }';
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = '{
            	  "clients": [
            		{
            		  "id": "'.$client_id.'",
                      "enable": true,
            		  "email": "' . $remark. '",
                      "limitIp": 0,
                      "totalGB": 0,
                      "expiryTime": 0
                      "subId": "' . RandomString(16) . '"
            		}
            	  ],
            	  "decryption": "none",
            	  "fallbacks": []
            	}';
                }else{
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
        		  "flow": "",
        		  "email": "' . $remark. '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }
            }
            else{
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
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = '{
                  "clients": [
                    {
                      "id": "'.$uniqid.'",
                      "enable": true,
                      "email": "' . $remark. '",
                      "limitIp": 0,
                      "totalGB": 0,
                      "expiryTime": 0,
                      "subId": "' . RandomString(16) . '"
                    }
                  ],
                  "decryption": "none",
            	  "fallbacks": []
                }';
                }else{
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
                }
                elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
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
            	  "' . $xtlsTitle . '": '.$tlsSettings.',
                  "wsSettings": {
                    "path": "/",
                    "headers": '.$headers.'
                  }
                }';
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = '{
                  "clients": [
                    {
                      "id": "'.$uniqid.'",
                      "enable": true,
                      "email": "' . $remark. '",
                      "limitIp": 0,
                      "totalGB": 0,
                      "expiryTime": 0,
                      "subId": "' . RandomString(16) . '"
                    }
                  ],
                  "decryption": "none",
            	  "fallbacks": []
                }';
                }else{
                    $settings = '{
                  "clients": [
                    {
                      "id": "'.$uniqid.'",
        			  "flow": "",
        			  "email": "' . $remark. '"
                    }
                  ],
                  "decryption": "none",
            	  "fallbacks": []
                }';
                }
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
                if($serverType == "sanaei" || $serverType == "alireza"){
                    $settings = '{
            	  "clients": [
            		{
            		  "id": "'.$uniqid.'",
                      "enable": true,
            		  "email": "' . $remark. '",
                      "limitIp": 0,
                      "totalGB": 0,
                      "expiryTime": 0,
                      "subId": "' . RandomString(16) . '"
            		}
            	  ],
            	  "decryption": "none",
            	  "fallbacks": []
            	}';
                }else{
                    $settings = '{
            	  "clients": [
            		{
            		  "id": "'.$uniqid.'",
            		  "flow": "",
            		  "email": "' . $remark. '"
            		}
            	  ],
            	  "decryption": "none",
            	  "fallbacks": []
            	}';
                }
                }
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/update/$iid";
    else $url = "$panel_url/xui/inbound/update/$iid";
    
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
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
}
function getMarzbanToken($server_id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id = ?");
    $stmt->bind_param('i', $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $panel_url = $server_info['panel_url'];
    $username = $server_info['username'];
    $password = $server_info['password'];
    
    $loginUrl = $panel_url .'/api/admin/token';
    $postFields = array(
        'username' => $username,
        'password' => $password
    );
    
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $loginUrl);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'accept: application/json'
        ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        return (object) ['success'=>false, 'detail'=>curl_error($curl)];
    }
    curl_close($curl);

    return json_decode($response);
}
function getMarzbanJson($server_id, $token = null){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];

    if($token == null) $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    $panel_url .= '/api/users';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' . $token->access_token
    ));

    $response = json_decode(curl_exec($curl));
    curl_close($curl);

    return $response;
}
function getMarzbanUserInfo($server_id, $remark){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];

    $configInfo = array();
    $curl = curl_init();
    for($i = 0; $i <= 10; $i++){
        $info = getMarzbanUser($server_id, $remark);
		$subLink = "/sub/" . (explode("/sub/", $info->subscription_url)[1]);
		$info->subscription_url = $subLink;
        curl_setopt($curl, CURLOPT_URL, $panel_url . $info->subscription_url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
        $response = curl_exec($curl);
        if($response && !curl_error($curl)){
            $configInfo = $info;
            break;
        }
		if($i == 10) $configInfo = $info;
    }
    curl_close($curl);

    return (object) $configInfo;
}
function getMarzbanUser($server_id, $remark, $token = null){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];

    if($token == null) $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    
    $panel_url .= '/api/user/' . urlencode($remark);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' . $token->access_token
    ));

    $response = json_decode(curl_exec($curl));
    
    curl_close($curl);
    return $response;
}
function getMarzbanHosts($server_id){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];

    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}

    $panel_url .= '/api/core/config';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' . $token->access_token
    ));

    $response = json_decode(curl_exec($curl));
    
    curl_close($curl);
    return $response;
}
function addMarzbanUser($server_id, $remark, $volume, $days, $plan_id){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    $serverType = $server_info['type'];
    
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ?");
    $stmt->bind_param('i', $plan_id);
    $stmt->execute();
    $planInfo = json_decode($stmt->get_result()->fetch_assoc()['custom_sni'],true);
    $stmt->close();

    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    $postFields = array(
        "inbounds" => $planInfo['inbounds'],
        "proxies" => $planInfo['proxies'],
        "expire" => time() + (86400 * $days),
        "data_limit" => $volume * 1073741824,
        "username" => urlencode($remark)
    );


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url . "/api/user");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token,
        'Content-Type: application/json'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postFields));

    $response = json_decode(curl_exec($curl));
    curl_close($curl);
    if(isset($response->detail) || !isset($response->links)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    $userInfo = getMarzbanUserInfo($server_id, $remark);

    return (object) [
        'success'=>true,
        'sub_link'=> $userInfo->subscription_url,
        'vray_links' => $response->links
        ];
}
function editMarzbanConfig($server_id,$info){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];

    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->error];}

    $remark = $info['remark'];
    $configInfo = getMarzbanUser($server_id, $remark, $token);
    
    
    $expireTime = $configInfo->expire;
    $volume = $configInfo->data_limit;
    
    if(isset($info['plus_day'])) $expireTime += (86400 * $info['plus_day']);
    elseif(isset($info['days'])) $expireTime = time() + (86400 * $info['days']);
    
    if(isset($info['plus_volume'])) $volume += $info['plus_volume'] * 1073741824;
    elseif(isset($info['volume'])){
        $volume = $info['volume'] * 1073741824;
        $response = resetMarzbanTraffic($server_id, $remark, $token);
        
        if(!$response->success) return $response;
    }
    
    $postFields = array(
        "inbounds" => $configInfo->inbounds,
        "proxies" => $configInfo->proxies,
        "expire" => $expireTime,
        "data_limit" => $volume,
        "username" => urlencode($remark),
        "note" => $configInfo->note,
        "data_limit_reset_strategy"=> $configInfo->data_limit_reset_strategy,
        "status" => $configInfo->status
    );
    
    $panel_url .=  '/api/user/'. $remark;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postFields));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token,
        'Content-Type: application/json'
        ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    if(isset($response->detail)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    return (object) ['success'=>true];
}
function resetMarzbanTraffic($server_id, $remark, $token){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];

    if($token == null) $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}

    $panel_url .=  '/api/user/' . $remark .'/reset';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_POST , true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    if(isset($response->detail)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    return (object) ['success'=>true];
}
function renewMarzbanUUID($server_id,$remark){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    $panel_url .= '/api/user/' . $remark .'/revoke_sub';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_POST , true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token
    ));

    $response = json_decode(curl_exec($curl));
    curl_close($curl);
    if(isset($response->detail)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    $response = getMarzbanUserInfo($server_id, $remark);
    return $response;
}

function deleteMarzban($server_id,$remark){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    $serverType = $server_info['type'];
    
    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    $panel_url .=  '/api/user/'. urlencode($remark);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token
    ));

    $response = json_decode(curl_exec($curl));
    curl_close($curl);
    
    if(isset($response->detail)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    
    return (object) ['success'=>true];
}
function changeMarzbanState($server_id,$remark){
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $panel_url = $server_info['panel_url'];
    $serverName = $server_info['username'];
    $serverPass = $server_info['password'];
    $serverType = $server_info['type'];
    
    $token = getMarzbanToken($server_id);
    if(isset($token->detail)){return (object) ['success'=>false, 'msg'=>$token->detail];}
    $configInfo = getMarzbanUser($server_id, $remark, $token);

    $panel_url .=  '/api/user/'. $remark;

    $postFields = array(
        "inbounds" => $configInfo->inbounds,
        "proxies" => $configInfo->proxies,
        "expire" => $configInfo->expire,
        "data_limit" => $configInfo->data_limit,
        "username" => urlencode($remark),
        "note" => $configInfo->note,
        "data_limit_reset_strategy"=> $configInfo->data_limit_reset_strategy,
        "status" => $configInfo->status == "active"?"disabled":"active"
    );


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $panel_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: Bearer ' .  $token->access_token,
        'Content-Type: application/json'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postFields));

    $response = json_decode(curl_exec($curl));
    curl_close($curl);

    if(isset($response->detail)){
		$detail = $response->detail;
        return (object) ['success'=>false, 'msg' => is_object($detail)?implode("-", (array) $detail):$detail];
    }
    return (object) ['success'=>true];
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
    $serverType = $server_info['type'];

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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);
    
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/list";
    else $url = "$panel_url/xui/inbound/list";
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
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        ),
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}
function getNewCert($server_id){
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);
    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$panel_url/server/getNewX25519Cert",
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
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response = json_decode($response);
}
function addUser($server_id, $client_id, $protocol, $port, $expiryTime, $remark, $volume, $netType, $security = 'none', $rahgozar = false, $planId = null){
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
    $sni = $server_info['sni'];
    $cookie = 'Cookie: session='.$server_info['cookie'];
    $serverType = $server_info['type'];
    $xtlsTitle = ($serverType == "sanaei" || $serverType == "alireza")?"XTLSSettings":"xtlsSettings";
    $reality = $server_info['reality'];

    if(!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza")){
        $tlsSettings = json_decode($tlsSettings,true);
        $tlsSettings['serverName'] = $sni;
        $tlsSettings = json_encode($tlsSettings);
    }
    
    $volume = ($volume == 0) ? 0 : floor($volume * 1073741824);
    $headers = getNewHeaders($netType, $request_header, $response_header, $header_type);
//---------------------------------------Trojan------------------------------------//
    if($protocol == 'trojan'){
        // protocol trojan
        if($security == 'none'){
            
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
            
        	if($serverType == "sanaei" || $serverType == "alireza"){
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
                  "enable": true,
                  "email": "' . $remark. '",
                  "limitIp": 0,
                  "totalGB": 0,
                  "expiryTime": 0,
                  "subId": "' . RandomString(16) . '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
        	}else{
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
        		  "flow": "",
        		  "email": "' . $remark. '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
        	}
        }elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
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
                      "alterId": 0
                    }
                  ],
                  "decryption": "none",
            	  "fallbacks": []
                }';
                }
        
        else{
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
		if($serverType == "sanaei" || $serverType == "alireza"){
            $settings = '{
		  "clients": [
			{
			  "password": "'.$client_id.'",
              "enable": true,
              "email": "' . $remark. '",
              "limitIp": 0,
              "totalGB": 0,
              "expiryTime": 0,
              "subId": "' . RandomString(16) . '"
			}
		  ],
		  "fallbacks": []
		}';
		}else{
            $settings = '{
		  "clients": [
			{
			  "password": "'.$client_id.'",
			  "flow": "",
			  "email": "' . $remark. '"
			}
		  ],
		  "fallbacks": []
		}';
		}
        }



        $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
		if($netType == 'grpc'){
		    $keyFileInfo = json_decode($tlsSettings,true);
		    $certificateFile = "/root/cert.crt";
		    $keyFile = '/root/private.key';
		    
		    if(isset($keyFileInfo['certificates'])){
		        $certificateFile = $keyFileInfo['certificates'][0]['certificateFile'];
		        $keyFile = $keyFileInfo['certificates'][0]['keyFile'];
		    }

			if($security == 'tls') {
				$streamSettings = '{
  "network": "grpc",
  "security": "tls",
  "tlsSettings": {
    "serverName": "' .
    (!empty($sni) && ($serverType == "sanaei" || $serverType == "alireza") ?  $sni: parse_url($panel_url, PHP_URL_HOST))
     . '",
    "certificates": [
      {
        "certificateFile": "' . $certificateFile . '",
        "keyFile": "' . $keyFile . '"
      }
    ],
    "alpn": []'
    .'
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
    "serviceName": "' . parse_url($panel_url, PHP_URL_HOST) . '"
  }
}';
		}
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
        if($rahgozar == true){
            $wsSettings = '{
                  "network": "ws",
                  "security": "none",
                  "wsSettings": {
                    "path": "/wss' . $port . '",
                    "headers": {}
                  }
                }';
            if($serverType == "sanaei" || $serverType == "alireza"){
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
                  "enable": true,
        		  "email": "' . $remark. '",
                  "limitIp": 0,
                  "totalGB": 0,
                  "expiryTime": 0,
                  "subId": "' . RandomString(16) . '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }else{
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
        		  "flow": "",
        		  "email": "' . $remark. '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }
        }else{
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
            if($serverType == "sanaei" || $serverType == "alireza"){
                $settings = '{
              "clients": [
                {
                  "id": "'.$client_id.'",
                  "enable": true,
                  "email": "' . $remark. '",
                  "limitIp": 0,
                  "totalGB": 0,
                  "expiryTime": 0,
                  "subId": "' . RandomString(16) . '"
                }
              ],
              "disableInsecureEncryption": false
            }';
            }else{
                $settings = '{
              "clients": [
                {
                  "id": "'.$client_id.'",
                  "alterId": 0
                }
              ],
              "disableInsecureEncryption": false
            }';
            }
            }elseif($security == 'xtls' && $serverType != "sanaei" && $serverType != "alireza") {
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
        	  "' . $xtlsTitle . '": '.$tlsSettings.',
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
            if($serverType == "sanaei" || $serverType == "alireza"){
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
        		  "enable": true,
        		  "email": "' . $remark. '",
                  "limitIp": 0,
                  "totalGB": 0,
                  "expiryTime": 0,
                  "subId": "' . RandomString(16) . '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }else{
                $settings = '{
        	  "clients": [
        		{
        		  "id": "'.$client_id.'",
        		  "flow": "",
        		  "email": "' . $remark. '"
        		}
        	  ],
        	  "decryption": "none",
        	  "fallbacks": []
        	}';
            }
            }
        }
        
        
		if($protocol == 'vless'){
		    if($serverType =="sanaei" || $serverType == "alireza"){
		        if($reality == "true"){
	                $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
                    $stmt->bind_param("i", $planId);
                    $stmt->execute();
                    $file_detail = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                
                    $dest = !empty($file_detail['dest'])?$file_detail['dest']:"yahoo.com";
                    $serverNames = !empty($file_detail['serverNames'])?$file_detail['serverNames']:
                                '[
                                    "yahoo.com",
                                    "www.yahoo.com"
                                ]';
                    $spiderX = !empty($file_detail['spiderX'])?$file_detail['spiderX']:"";
                    $flow = isset($file_detail['flow']) && $file_detail['flow'] != "None" ? $file_detail['flow'] : "";
                    


		            $certInfo = getNewCert($server_id)->obj;
		            $publicKey = $certInfo->publicKey;
		            $privateKey = $certInfo->privateKey;
		            $shortId = RandomString(8, "small");
		            $serverName = json_decode($tlsSettings,true)['serverName'];
		            if($netType == "grpc"){
    		            $tcpSettings = '{
                          "network": "grpc",
                          "security": "reality",
                          "realitySettings": {
                            "show": false,
                            "xver": 0,
                            "dest": "' . $dest . '",
                            "serverNames":' . $serverNames . ',
                            "privateKey": "' . $privateKey . '",
                            "minClient": "",
                            "maxClient": "",
                            "maxTimediff": 0,
                            "shortIds": [
                              "' . $shortId .'"
                            ],
                            "settings": {
                              "publicKey": "' . $publicKey . '",
                              "fingerprint": "firefox",
                              "serverName": "' . $serverName . '",
                              "spiderX": "' . $spiderX . '"
                            }
                          },
                          "grpcSettings": {
                            "serviceName": "",
                    		"multiMode": false
                          }
                        }';
		            }else{
    		            $tcpSettings = '{
                          "network": "tcp",
                          "security": "reality",
                          "realitySettings": {
                            "show": false,
                            "xver": 0,
                            "dest": "' . $dest . '",
                            "serverNames":' . $serverNames . ',
                            "privateKey": "' . $privateKey . '",
                            "minClient": "",
                            "maxClient": "",
                            "maxTimediff": 0,
                            "shortIds": [
                              "' . $shortId .'"
                            ],
                            "settings": {
                              "publicKey": "' . $publicKey . '",
                              "fingerprint": "firefox",
                              "serverName": "' . $serverName . '",
                              "spiderX": "' . $spiderX . '"
                            }
                          },
                          "tcpSettings": {
                            "acceptProxyProtocol": false,
                    		"header": '.$headers.'
                          }
                        }';
		            }
    			    $settings = '{
        			  "clients": [
        				{
        				  "id": "'.$client_id.'",
        				  "enable": true,
                          "email": "' . $remark. '",
                          "flow": "' . $flow .'",
                          "limitIp": 0,
                          "totalGB": 0,
                          "expiryTime": 0,
                          "subId": "' . RandomString(16) . '"
        				}
        			  ],
        			  "decryption": "none",
        			  "fallbacks": []
        			}';
		            $netType = "tcp";
		        }else{
    			    $settings = '{
        			  "clients": [
        				{
        				  "id": "'.$client_id.'",
        				  "enable": true,
                          "email": "' . $remark. '",
                          "limitIp": 0,
                          "totalGB": 0,
                          "expiryTime": 0,
                          "subId": "' . RandomString(16) . '"
        				}
        			  ],
        			  "decryption": "none",
        			  "fallbacks": []
        			}';
		        }
		    }else{
			$settings = '{
			  "clients": [
				{
				  "id": "'.$client_id.'",
				  "flow": "",
				  "email": "' . $remark. '"
				}
			  ],
			  "decryption": "none",
			  "fallbacks": []
			}';
		    }
		}

        $streamSettings = ($netType == 'tcp') ? $tcpSettings : $wsSettings;
		if($netType == 'grpc' && $reality != "true"){
		    $keyFileInfo = json_decode($tlsSettings,true);
		    $certificateFile = "/root/cert.crt";
		    $keyFile = '/root/private.key';
		    
		    if(isset($keyFileInfo['certificates'])){
		        $certificateFile = $keyFileInfo['certificates'][0]['certificateFile'];
		        $keyFile = $keyFileInfo['certificates'][0]['keyFile'];
		    }

			if($security == 'tls') {
				$streamSettings = '{
  "network": "grpc",
  "security": "tls",
  "tlsSettings": {
    "serverName": "' . parse_url($panel_url, PHP_URL_HOST) . '",
    "certificates": [
      {
        "certificateFile": "' . $certificateFile . '",
        "keyFile": "' . $keyFile . '"
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
    "serviceName": "' . parse_url($panel_url, PHP_URL_HOST) . '"
  }
}';
		}
	    }

        if(($serverType == "sanaei" || $serverType == "alireza") && $reality == "true"){
            $sniffing = '{
              "enabled": true,
              "destOverride": [
                "http",
                "tls",
                "quic"
              ]
            }';
        }else{
            $sniffing = '{
        	  "enabled": true,
        	  "destOverride": [
        		"http",
        		"tls"
        	  ]
        	}';
        }
        // vmess - vless
        $dataArr = array('up' => '0','down' => '0','total' => $volume, 'remark' => $remark,'enable' => 'true','expiryTime' => $expiryTime,'listen' => '','port' => $port,'protocol' => $protocol,'settings' => $settings,'streamSettings' => $streamSettings
        ,'sniffing' => $sniffing);
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $match);
    $session = $match[1];

    $loginResponse = json_decode($body,true);

    if(!$loginResponse['success']){
        curl_close($curl);
        return $loginResponse;
    }
    
    if($serverType == "sanaei") $url = "$panel_url/panel/inbound/add";
    else $url = "$panel_url/xui/inbound/add";
    
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
        CURLOPT_POSTFIELDS => $dataArr,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
            'Accept:  application/json, text/plain, */*',
            'Accept-Language:  en-US,en;q=0.5',
            'Accept-Encoding:  gzip, deflate',
            'X-Requested-With:  XMLHttpRequest',
            'Cookie: ' . $session
        )
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

?>
