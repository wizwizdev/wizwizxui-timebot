<?php


    if ( !is_dir( "cookies" ) ) {
        mkdir( "cookies" );       
    }
    $connection = mysqli_connect('localhost', $Database['username'], $Database['password'], $Database['dbname']);
    // ------------------ Functions ------------------ //
    function Bot($method, $datas = []) {
        global $Config;
        $curl = curl_init('https://api.telegram.org/bot'.$Config['api_token'].'/'.$method);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $datas,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ]);
        $response = json_decode(curl_exec($curl)); 
        return $response;
    }
    
    function delMessage($ci, $msg_id){
        $param = explode("-",$msg_id);
        if(count($param) == 2){
            $fromMsgId = $param[0];
            $toMsgId = $param[1];
            
            for ($i = $toMsgId; $i >= $fromMsgId; $i--){
                Bot('deleteMessage',['chat_id'=>$ci, 'message_id'=>$i]);
            }
    
        }else{
            Bot('deleteMessage',['chat_id'=>$ci, 'message_id'=>$msg_id]);
        }
    }
    function sendMessage($ci, $txt, $msg = null, $key = null, $parse = null){
        return Bot('sendmessage', [
                'chat_id' => $ci,
                'text' => $txt,
                'reply_to_message_id' => $msg,
                'parse_mode' => $parse,
                'reply_markup' => $key,
                'disable_web_page_preview' => true
            ]);
    }
    function editText($ci, $msg, $txt, $key = null, $parse = null){
        return Bot('editmessagetext', [
            'chat_id' => $ci,
            'message_id' => $msg,
            'text' => $txt,
            'parse_mode' => $parse,
            'reply_markup' =>  $key
            ]);
    }
    
    function alert($callback_query_id,$text,$show_alert=false){
        return Bot('answerCallbackQuery',['callback_query_id'=>$callback_query_id,
        'text'=>$text,
        'show_alert'=>$show_alert]);
    }
    function get($from){
        return Bot('getChat',['chat_id'=>$from]);
    }
    
    function wait(){
        while($i<500000000){
            $i++;
        }
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
    function getJson($site ,$user, $pass, $cookie){
        $loginUrl = $site . '/login';
        
        $postFields = array(
            "username" => $user,
            "password" => $pass
            );
            
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies/$cookie.txt");
        $loginResponse = json_decode(curl_exec($ch),true);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            sendMessage($cookie, $error_msg);
        }
        if($loginResponse['success']){
            $listUrl = $site . "/xui/inbound/list";
            
            curl_setopt($ch, CURLOPT_URL, $listUrl);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies/$cookie.txt");
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response,true);
        }else{
            return $loginResponse;
        }
    }
    // ------------------ Variables ------------------ //
    $update = json_decode(file_get_contents('php://input'));
    
    if (isset($update->message)) {
        $message = $update->message;
        $text = $message->text;
        $tc = $message->chat->type;
        $chat_id = $message->chat->id;
        $from_id = $message->from->id;
        $fwd_from_id = $message->forward_from->id;
        $message_id = $message->message_id;
        $first_name = $message->from->first_name;
        $entities = $message->entities;
        $caption = $message->caption;
        $caption_entities = $message->caption_entities;
        $last_name = $message->from->last_name;
        $username = $message->from->username?:'';
    }
    
    if (isset($update->callback_query)) {
        $callback_query = $update->callback_query;
        $data = $callback_query->data;
        $tc = $callback_query->message->chat->type;
        $chat_id = $callback_query->message->chat->id;
        $from_id = $callback_query->from->id;
        $message_id = $callback_query->message->message_id;
        $first_name = $callback_query->from->first_name;
        $last_name = $callback_query->from->last_name;
        $username = $callback_query->from->username;
        $callid = $callback_query->id;
    }
    
    // ------------------  Connect MySQL ------------------ //
    $user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `user` WHERE `id` = '{$from_id}' LIMIT 1"));
    $loginCount = $connection->query("SELECT * FROM `loged_info` WHERE `user_id` = '$from_id'");
    // ------------------ { Informations } ------------------ //
    
    function setUser($action, $value, $frm = "none"){
        global $from_id, $connection;
        $frm_id = $frm!="none"?$frm:$from_id;
        
        $checkExists = $connection->query("SELECT * FROM `user` WHERE `id` = $frm_id");
        if(mysqli_num_rows($checkExists)==0){
            $time = time();
            $connection->query("INSERT INTO `user` (`id`, `step`) VALUES ('{$frm_id}', 'none')");
        }
        $connection->query("UPDATE `user` SET `$action` = '$value' WHERE `id` = '{$frm_id}' LIMIT 1");
    }
    
    //------ User Keys ------//
    function getUserKeys(){
        global $loginCount, $from_id;
        
        if(mysqli_num_rows($loginCount)>0){
            return json_encode(['keyboard'=>[
                [['text'=>"➕ حساب جدید"]],
                [['text'=>"🪬 حساب من 🪬"],['text'=>"🔓 خروج از حساب 🔓"]],
                [['text'=>"💮 Qr Code 💮"],['text'=>"📞 پشتیبانی"]],
                [['text'=>"کپی رایت ©️ ویزویز"]]
                ],'resize_keyboard'=>true]);
        }else{
            return json_encode(['keyboard'=>[
                [['text'=>"🕯 ورود به حساب 🕯"],['text'=>"💮 Qr Code 💮"]],
                [['text'=>"کپی رایت ©️ ویزویز"]]
                ],'resize_keyboard'=>true]);
        }
    }

    if($text == "کپی رایت ©️ ویزویز" ){
        sendMessage($chat_id,"
            ممنون میشم از من حمایت کنید 🙂❤️
    
    🆔 dev: @wizwizpv
    📣 Gp: @wizwizdev
            ");
        setUser('step','setUserUUID');
    }

    $backButton = json_encode(['keyboard'=>[
        [['text'=>"🔽 میخوام به عقب برگردم 🔽"]]
        ],'resize_keyboard'=>true]);
    
    
    $botState = file_get_contents("botState.txt");
    //-------- Admin Keys------//
    function getAdminKeys(){
        global $botState;
        $botState = $botState=="false"?"خاموش ⛔️":"روشن ✅";
        return json_encode(['keyboard'=>[
            [['text'=>"لیست سرور ها"],['text'=>"آمار ربات"]],
            [['text'=>"وضعیت ربات: " . $botState]],
            [['text'=>"کپی رایت ©️ ویزویز"]]
            ],'resize_keyboard'=>true]);
    }
        
        
    function isJoined(){
        global $from_id, $Config;
        $keys = array();

        foreach($Config['channel_lock'] as $key){
            $isJoined = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id=@'. $key.'&user_id='.$from_id), true)['result']['status'];
            if ($isJoined == "left" || $isJoined == "kicked"){
    	        $title = get("@" . $key)->result->title ?? $channelLink;
                $keys[] = [['text'=>$title,"url"=>"https://t.me/" . $key]];
            }
        }    
        
        if(count($keys)>0){
            $keys[] = [['text'=>"عضو شدم ✅",'callback_data'=>"joined"]];
            return json_encode(['inline_keyboard'=>$keys]);
        }else{
            return null; 
        }
    }

        
    
    
    function getServersList(){
        global $connection;
        $serversList = $connection->query("SELECT * FROM `servers`");
        $keys = array();
        if(mysqli_num_rows($serversList)>0){
            while($row = $serversList->fetch_assoc()){
                $rowId = $row['id'];
                $serverIp = $row['server_ip'];
                $userName = $row['user_name'];
                $password = $row['password'];

                $keys[] = [['text'=>$serverIp,'callback_data'=>"betty"]];
                $keys[] = [['text'=>$userName,'callback_data'=>"betty"],['text'=>$password,'callback_data'=>'betty']];
                $keys[] = [['text'=>"حذف",'callback_data'=>"delServer_$rowId"]];
            }
        }else{
            $keys[] =[['text'=>"سروری ثبت نشده",'callback_data'=>"betty"]];
        }
        
        $keys[] = [['text'=>"افزودن سرور",'callback_data'=>"addNewServer"]];
        return json_encode(['inline_keyboard'=>$keys]);
    }
        

    $remove = json_encode(['remove_keyboard' => [
        ], 'remove_keyboard' => true
    ]);


?>
