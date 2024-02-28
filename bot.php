<?php
include_once 'baseInfo.php';
include_once 'config.php';
include_once 'settings/jdf.php';

check();

$robotState = $botState['botState'] ?? "on";
if ($userInfo['step'] == "banned" && $from_id != $admin && $userInfo['isAdmin'] != true) {
    sendMessage("❌ | هی بهت گفتم آدم باش گوش نکردی ، الان مسدود شدی 😑😂");
    exit();
}
if ($joniedState == "kicked" || $joniedState == "left") {
    sendMessage("
❌ برای استفاده از ربات حتما باید در کانال زیر عضو شوید:

🆔 $channelLock

✅ بعد از اینکه عضو شدید مجدد ربات رو /start کنید و لذت ببرید

🌀 @ ( Support us 💕 )
", null, "HTML");
    exit;
}
if ($robotState == "off" && $from_id != $admin) {
    sendMessage("🌛ربات در حال بروزرسانی می باشد ...");
    exit();
}
if (strpos($text, "/start ") !== false) {
    $inviter = str_replace("/start ", "", $text);

    if ($uinfo->num_rows == 0 && $inviter != $from_id) {

        $first_name = !empty($first_name) ? $first_name : " ";
        $username = !empty($username) ? $username : " ";
        if ($uinfo->num_rows == 0) {
            $sql = "INSERT INTO `users` (`userid`, `name`, `username`, `refcode`, `wallet`, `date`, `refered_by`)
                                VALUES (?,?,?, 0,0,?,?)";
            $stmt = $connection->prepare($sql);
            $time = time();
            $stmt->bind_param("issii", $from_id, $first_name, $username, $time, $inviter);
            $stmt->execute();
            $stmt->close();
        } else {
            $refcode = time();
            $sql = "UPDATE `users` SET `refered_by` = ? WHERE `userid` = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("si", $inviter, $from_id);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $uinfo = $stmt->get_result();
        $userInfo = $uinfo->fetch_assoc();
        $stmt->close();

        setUser("referedBy" . $inviter);
        $userInfo['step'] = "referedBy" . $inviter;
        sendMessage("😍|تبریک یه نفر با لینک شما وارد ربات شد", null, null, $inviter);
    }

    $text = "/start";
}
if ($userInfo['phone'] == null && $from_id != $admin && $userInfo['isAdmin'] != true && $botState['requirePhone'] == "on") {
    if (isset($update->message->contact)) {
        $contact = $update->message->contact;
        $phone_number = $contact->phone_number;
        $phone_id = $contact->user_id;
        if ($phone_id != $from_id) {
            sendMessage("🔘|لطفا فقط از کلید زیر استفاده کنید");
            exit();
        } else {
            if (!preg_match('/^\+98(\d+)/', $phone_number) && !preg_match('/^98(\d+)/', $phone_number) && !preg_match('/^0098(\d+)/', $phone_number) && $botState['requireIranPhone'] == 'on') {
                sendMessage("🔘|لطفا فقط با شماره ایرانی اقدام کنید");
                exit();
            }
            setUser($phone_number, 'phone');

            sendMessage("✅|شماره شما با موفقیت تأیید شد", $removeKeyboard);
            $text = "/start";
        }
    } else {
        sendMessage("سلام عزیزم، برای استفاده از ربات شماره تماس خود را با استفاده از کلید زیر ارسال کنید 👇", json_encode([
            'keyboard' => [[[
                'text' => '☎️ ارسال شماره',
                'request_contact' => true,
            ]]],
            'resize_keyboard' => true
        ]));
        exit();
    }
}
if (preg_match('/^\/([Ss]tart)/', $text) or $text == '⤵️ برگرد به منوی اصلی ' or $text == '🔙بازگشت به منوی اصلی' or $data == 'mainMenu') {
    setUser();
    if ($uinfo->num_rows == 0) {
        $first_name = !empty($first_name) ? $first_name : " ";
        $username = !empty($username) ? $username : " ";
        $refcode = time();
        $sql = "INSERT INTO `users` VALUES (NULL,?,?,?,?, 0,?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issii", $from_id, $first_name, $username, $refcode, $time);
        $stmt->execute();
        $stmt->close();
    }
    if (isset($data) and $data == "mainMenu") {
        $res = editText($message_id, '❤️ به ربات وی پی ان مسترز خوش آمدید ❤️
🥇 وی پی ان تخصص ماست 🥇

ما اینجاییم تا شما را بدون هیچ محدویتی به شبکه جهانی متصل کنیم 🖤

🤹‍♂ متصل با تمامی اپراتور ها
📡 برقرای امنیت در ارتباط شما
💣 کیفیت در ساخت انواع کانکشن ها
🔄 قابلیت عودت وجه در صورت نارضایتی تا 24 ساعت و پشتیبانی تا اخرین روز اشتراک

🚪 /start
', $mainKeys);
        if (!$res->ok) {
            sendMessage('❤️ به ربات وی پی ان مسترز خوش آمدید ❤️
🥇 وی پی ان تخصص ماست 🥇

ما اینجاییم تا شما را بدون هیچ محدویتی به شبکه جهانی متصل کنیم 🧙🏻

🤹‍♂️ متصل با تمامی اپراتور ها
📡 برقرای امنیت در ارتباط شما
💣 کیفیت در ساخت انواع کانکشن ها
🔄 قابلیت عودت وجه در صورت نارضایتی تا 24 ساعت و پشتیبانی تا اخرین روز اشتراک

▪️ /start', $mainKeys);
        }
    } else {
        if ($from_id != $admin && !isset($userInfo['first_start'])) {
            setUser('sent', 'first_start');
            $keys = json_encode(['inline_keyboard' => [
                [['text' => "✉️ ارسال پیام به کاربر ", 'callback_data' => 'sendMessageToUser' . $from_id]]
            ]]);
            sendMessage(
                "
            📢 | یک کاربر جدید عضو ربات شد :

نام و نام خانوادگی: <a href='tg://user?id=$from_id'>$first_name</a>
نام کاربری: @$username
آیدی عددی: <code>$from_id</code>

به نظرم یه پیام براش بفرست مثلا ( تبلیغی یا خوش آمد گویی ) 😍

            ",
                $keys,
                "html",
                $admin
            );
        }
        sendMessage('❤️ به ربات وی پی ان مسترز خوش آمدید ❤️
🥇 وی پی ان تخصص ماست 🥇

ما اینجاییم تا شما را بدون هیچ محدویتی به شبکه جهانی متصل کنیم 🧙🏻

🤹‍♂️ متصل با تمامی اپراتور ها
📡 برقرای امنیت در ارتباط شما
💣 کیفیت در ساخت انواع کانکشن ها
🔄 قابلیت عودت وجه در صورت نارضایتی تا 24 ساعت و پشتیبانی تا اخرین روز اشتراک

▪️ /start', $mainKeys);
    }
}
if (preg_match('/^sendMessageToUser(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    editText($message_id, '🔘|لطفا پیامت رو بفرست');
    setUser($data);
}
if (preg_match('/^sendMessageToUser(\d+)/', $userInfo['step'], $match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    sendMessage($text, null, null, $match[1]);
    sendMessage("پیامت به کاربر ارسال شد", $removeKeyboard);
    sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $adminKeys);
    setUser();
}
if ($data == 'botReports' && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    editText($message_id, "آمار ربات در این لحظه", getBotReportKeys());
}
if ($data == "adminsList" && $from_id == $admin) {
    editText($message_id, "لیست ادمین ها", getAdminsKeys());
}
if (preg_match('/^delAdmin(\d+)/', $data, $match) && $from_id === $admin) {
    $stmt = $connection->prepare("UPDATE `users` SET `isAdmin` = false WHERE `userid` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    editText($message_id, "لیست ادمین ها", getAdminsKeys());
}
if ($data == "addNewAdmin" && $from_id === $admin) {
    delMessage();
    sendMessage("🧑‍💻| کسی که میخوای ادمین کنی رو آیدی عددیشو بفرست ببینم:", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "addNewAdmin" && $from_id === $admin && $text != $cancelText) {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("UPDATE `users` SET `isAdmin` = true WHERE `userid` = ?");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("✅ | 🥳 خب کاربر الان ادمین شد تبریک میگم", $removeKeyboard);
        setUser();

        sendMessage("لیست ادمین ها", getAdminsKeys());
    } else {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if (($data == "botSettings" or preg_match("/^changeBot(\w+)/", $data, $match)) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if ($data != "botSettings") {
        $newValue = $botState[$match[1]] == "on" ? "off" : "on";
        $botState[$match[1]] = $newValue;

        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
        $stmt->execute();
        $isExists = $stmt->get_result();
        $stmt->close();
        if ($isExists->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
        else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
        $newData = json_encode($botState);

        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $newData);
        $stmt->execute();
        $stmt->close();
    }
    editText($message_id, '🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getBotSettingKeys());
}
if (($data == "gateWays_Channels" or preg_match("/^changeGateWays(\w+)/", $data, $match)) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if ($data != "gateWays_Channels") {
        $newValue = $botState[$match[1]] == "on" ? "off" : "on";
        $botState[$match[1]] = $newValue;

        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
        $stmt->execute();
        $isExists = $stmt->get_result();
        $stmt->close();
        if ($isExists->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
        else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
        $newData = json_encode($botState);

        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $newData);
        $stmt->execute();
        $stmt->close();
    }
    editText($message_id, '🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getGateWaysKeys());
}
if (preg_match('/^changePaymentKeys(\w+)/', $data, $match)) {
    delMessage();
    switch ($match[1]) {
        case "nextpay":
            $gate = "کد جدید درگاه نکست پی";
            break;
        case "nowpayment":
            $gate = "کد جدید درگاه nowPayment";
            break;
        case "zarinpal":
            $gate = "کد جدید درگاه زرین پال";
            break;
        case "bankAccount":
            $gate = "شماره حساب جدید";
            break;
        case "holderName":
            $gate = "اسم دارنده حساب";
            break;
    }
    sendMessage("🔘|لطفا $gate را وارد کنید", $cancelKey);
    setUser($data);
}
if (preg_match('/^changePaymentKeys(\w+)/', $userInfo['step'], $match) && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentInfo = $stmt->get_result();
    $stmt->close();
    $paymentKeys = json_decode($paymentInfo->fetch_assoc()['value'], true) ?? array();
    $paymentKeys[$match[1]] = $text;
    $paymentKeys = json_encode($paymentKeys);

    if ($paymentInfo->num_rows > 0) $stmt = $connection->prepare("UPDATE `setting` SET `value` = ? WHERE `type` = 'PAYMENT_KEYS'");
    else $stmt = $connection->prepare("INSERT INTO `setting` (`type`, `value`) VALUES ('PAYMENT_KEYS', ?)");
    $stmt->bind_param("s", $paymentKeys);
    $stmt->execute();
    $stmt->close();


    sendMessage("✅|با موفقیت ذخیره شد", $removeKeyboard);
    sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getGateWaysKeys());
    setUser();
}

if ($data == "editRewardTime" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🙃 | لطفا زمان تأخیر در ارسال گزارش رو به ساعت وارد کن\n\nنکته: هر n ساعت گزارش به ربات ارسال میشه! ", $cancelKey);
    setUser($data);
}
if ($data == "userReports" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🙃 | لطفا آیدی عددی کاربر رو وارد کن", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "userReports" && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (is_numeric($text)) {
        sendMessage("🙃 | لطفا منتظر باشید", $removeKeyboard);
        $keys = getUserInfoKeys($text);
        if ($keys != null) {
            sendMessage("اطلاعات کاربر <a href='tg://user?id=$text'>$fullName</a>", $keys, "html");
            setUser();
        } else sendMessage("کاربری با این آیدی یافت نشد");
    } else {
        sendMessage("😡|لطفا فقط عدد ارسال کن");
    }
}
if ($data == "inviteSetting" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
    $stmt->execute();
    $inviteAmount = number_format($stmt->get_result()->fetch_assoc()['value'] ?? 0) . " تومان";
    $stmt->close();
    setUser();
    $keys = json_encode(['inline_keyboard' => [
        [['text' => "❗️بنر دعوت", 'callback_data' => "inviteBanner"]],
        [
            ['text' => $inviteAmount, 'callback_data' => "editInviteAmount"],
            ['text' => "مقدار پورسانت", 'callback_data' => "wizwizch"]
        ],
        [
            ['text' => "برگشت 🔙", 'callback_data' => "botSettings"]
        ],
    ]]);
    $res = editText($message_id, "✅ تنظیمات بازاریابی", $keys);
    if (!$res->ok) {
        delMessage();
        sendMessage("✅ تنظیمات بازاریابی", $keys);
    }
}
if ($data == "inviteBanner" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_TEXT'");
    $stmt->execute();
    $inviteText = $stmt->get_result()->fetch_assoc()['value'];
    $inviteText = $inviteText != null ? json_decode($inviteText, true) : array('type' => 'text');
    $stmt->close();
    $keys = json_encode(['inline_keyboard' => [
        [['text' => "ویرایش", 'callback_data' => 'editInviteBannerText']],
        [['text' => "برگشت 🔙", 'callback_data' => 'inviteSetting']]
    ]]);
    if ($inviteText['type'] == "text") {
        editText($message_id, "بنر فعلی: \n" . $inviteText['text'], $keys);
    } else {
        delMessage();
        $res = sendPhoto($inviteText['file_id'], $inviteText['caption'], $keys, null);
        if (!$res->ok) {
            sendMessage("تصویر فعلی یافت نشد، لطفا اقدام به ویرایش بنر کنید", $keys);
        }
    }
    setUser();
}
if ($data == "editInviteBannerText" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🤖 | لطفا بنر جدید را بفرستید از متن  LINK برای نمایش لینک دعوت استفاده کنید)", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "editInviteBannerText" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    $data = array();
    if (isset($update->message->photo)) {
        $data['type'] = 'photo';
        $data['caption'] = $caption;
        $data['file_id'] = $fileid;
    } elseif (isset($update->message->text)) {
        $data['type'] = 'text';
        $data['text'] = $text;
    } else {
        sendMessage("🥺 | بنر ارسال شده پشتیبانی نمی شود");
        exit();
    }

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_TEXT'");
    $stmt->execute();
    $checkExist = $stmt->get_result();
    $stmt->close();
    $data = json_encode($data);
    if ($checkExist->num_rows > 0) {
        $stmt = $connection->prepare("UPDATE `setting` SET `value` = ? WHERE `type` = 'INVITE_BANNER_TEXT'");
        $stmt->bind_param("s", $data);
        $stmt->execute();
        $checkExist = $stmt->get_result();
        $stmt->close();
    } else {
        $stmt = $connection->prepare("INSERT INTO `setting` (`value`, `type`) VALUES (?, 'INVITE_BANNER_TEXT')");
        $stmt->bind_param("s", $data);
        $stmt->execute();
        $checkExist = $stmt->get_result();
        $stmt->close();
    }

    sendMessage("✅ | با موفقیت ذخیره شد", $removeKeyboard);
    $keys = json_encode(['inline_keyboard' => [
        [['text' => "ویرایش", 'callback_data' => 'editInviteBannerText']],
        [['text' => "برگشت 🔙", 'callback_data' => 'inviteSetting']]
    ]]);
    if (isset($update->message->text)) {
        sendMessage("بنر فعلی: \n" . $text, $keys);
    } else {
        sendPhoto($fileid, $caption, $keys);
    }
    setUser();
}
if ($data == "editInviteAmount" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("لطفا مبلغ پورسانت رو به تومان وارد کن", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "editInviteAmount") {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $checkExist = $stmt->get_result();
        $stmt->close();

        if ($checkExist->num_rows > 0) {
            $stmt = $connection->prepare("UPDATE `setting` SET `value` = ? WHERE `type` = 'INVITE_BANNER_AMOUNT'");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $checkExist = $stmt->get_result();
            $stmt->close();
        } else {
            $stmt = $connection->prepare("INSERT INTO `setting` (`value`, `type`) VALUES (?, 'INVITE_BANNER_AMOUNT')");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $checkExist = $stmt->get_result();
            $stmt->close();
        }
        sendMessage("✅ | با موفقیت ذخیره شد", $removeKeyboard);

        $keys = json_encode(['inline_keyboard' => [
            [['text' => "❗️بنر دعوت", 'callback_data' => "inviteBanner"]],
            [
                ['text' => number_format($text) . " تومان", 'callback_data' => "editInviteAmount"],
                ['text' => "مقدار پورسانت", 'callback_data' => "wizwizch"]
            ],
            [
                ['text' => "برگشت 🔙", 'callback_data' => "botSettings"]
            ],
        ]]);
        sendMessage("✅ تنظیمات بازاریابی", $keys);
        setUser();
    } else sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
}
if ($userInfo['step'] == "editRewardTime" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    if (!is_numeric($text)) {
        sendMessage("لطفا عدد بفرستید");
        exit();
    } elseif ($text < 0) {
        sendMessage("مقدار وارد شده معتبر نیست");
        exit();
    }
    $botState['rewaredTime'] = $text;

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    $stmt->execute();
    $isExist = $stmt->get_result();
    $stmt->close();
    if ($isExist->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
    else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
    $newData = json_encode($botState);

    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $newData);
    $stmt->execute();
    $stmt->close();


    sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getBotSettingKeys());
    setUser();
    exit();
}
if ($data == "inviteFriends") {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_TEXT'");
    $stmt->execute();
    $inviteText = $stmt->get_result()->fetch_assoc()['value'];
    if ($inviteText != null) {
        $inviteText = json_decode($inviteText, true);

        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $inviteAmount = number_format($stmt->get_result()->fetch_assoc()['value'] ?? 0) . " تومان";
        $stmt->close();

        $getBotInfo = json_decode(file_get_contents("http://api.telegram.org/bot" . $botToken . "/getMe"), true);
        $botId = $getBotInfo['result']['username'];

        $link = "t.me/$botId?start=" . $from_id;
        if ($inviteText['type'] == "text") {
            $txt = str_replace('LINK', "<code>$link</code>", $inviteText['text']);
            $res = sendMessage($txt, null, "HTML");
        } else {
            $txt = str_replace('LINK', "$link", $inviteText['caption']);
            $res = sendPhoto($inviteText['file_id'], $txt, null, "HTML");
        }
        $msgId = $res->result->message_id;
        sendMessage("با لینک بالا دوستاتو به ربات دعوت کن و با هر خرید $inviteAmount بدست بیار", null, null, null, $msgId);
    } else alert("این قسمت غیر فعال است");
}
if ($data == "myInfo") {
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid` = ?");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $totalBuys = $stmt->get_result()->num_rows;
    $stmt->close();

    $stmt = $connection->prepare("SELECT COUNT(amount) as count, SUM(amount) as total FROM `orders_list` WHERE `userid` = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $totalBoughtPrice = number_format($info['total']) . " تومان";

    $myWallet = number_format($userInfo['wallet']) . " تومان";

    $keys = getUserInfoKeys($userId);
    editText(
        $message_id,
        "💞 اطلاعات حساب شما:",
        $keys,
        "html"
    );
}
if ($data == "transferMyWallet") {
    delMessage();
    sendMessage("لطفا آیدی عددی کاربر مورد نظر رو وارد کن", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "transferMyWallet" && $text != $cancelText) {
    if (is_numeric($text)) {
        if ($text != $from_id) {
            $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
            $stmt->bind_param("i", $text);
            $stmt->execute();
            $checkExist = $stmt->get_result();
            $stmt->close();

            if ($checkExist->num_rows > 0) {
                setUser("tranfserUserAmount" . $text);
                sendMessage("لطفا مبلغ مورد نظر رو وارد کن");
            } else sendMessage("کاربری با این آیدی یافت نشد");
        } else sendMessage("میخای به خودت انتقال بدی ؟؟");
    } else sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
}
if (preg_match('/^tranfserUserAmount(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if (is_numeric($text)) {
        if ($userInfo['wallet'] >= $text) {
            $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
            $stmt->bind_param("ii", $text, $match[1]);
            $stmt->execute();
            $stmt->close();

            $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
            $stmt->bind_param("ii", $text, $from_id);
            $stmt->execute();
            $stmt->close();

            sendMessage("✅|مبلغ " . number_format($text) . " تومان به کیف پول شما توسط کاربر $from_id انتقال یافت", null, null, $match[1]);
            setUser();
            sendMessage("✅|مبلغ " . number_format($text) . " تومان به کیف پول کاربر مورد نظر شما انتقال یافت", $removeKeyboard);
            sendMessage("لطفا یکی از کلید های زیر را انتخاب کنید", $mainKeys);
        } else sendMessage("موجودی حساب شما کم است");
    } else sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
}
if ($data == "increaseMyWallet") {
    delMessage();
    sendMessage("🙂 عزیزم مقدار شارژ مورد نظر خود را به تومان وارد کن (بیشتر از 5000 تومان)", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "increaseMyWallet" && $text != $cancelText) {
    if (!is_numeric($text)) {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    } elseif ($text < 5000) {
        sendMessage("لطفا مقداری بیشتر از 5000 وارد کن");
        exit();
    }
    sendMessage("🪄 لطفا صبور باشید ...", $removeKeyboard);
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


    $keyboard = array();
    $temp = array();
    if ($botState['cartToCartState'] == "on") {
        $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "increaseWalletWithCartToCart" . $text];
    }
    if ($botState['nowPaymentWallet'] == "on") {
        $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['zarinpal'] == "on") {
        $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['nextpay'] == "on") {
        $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['weSwapState'] == "on") {
        $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
    }

    array_push($keyboard, $temp);
    $keyboard[] = [['text' => $cancelText, 'callback_data' => "mainMenu"]];


    $keys = json_encode(['inline_keyboard' => $keyboard]);
    sendMessage("اطلاعات شارژ:\nمبلغ " . number_format($text) . " تومان\n\nلطفا روش پرداخت را انتخاب کنید", $keys);
    setUser();
}
if (preg_match('/increaseWalletWithCartToCart/', $data)) {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if (!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys, true);
    else $paymentKeys = array();
    $stmt->close();

    delMessage();
    setUser($data);
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

✅ بعد از اینکه پرداختت تایید شد مبلغ مورد نظر به کیف پولت اضافه میشه!", $cancelKey, "HTML");
    exit;
}
if (preg_match('/increaseWalletWithCartToCart(\d+)/', $userInfo['step'], $match) and $text != $cancelText) {
    $fid = $match[1];
    setUser();
    $uid = $userInfo['userid'];
    $name = $userInfo['name'];
    $username = $userInfo['username'];

    $infoc = strlen($text) > 1 ? $text : "$caption <a href='$fileurl'>&#8194;نمایش فیش</a>";
    $msg = "
🥇 سفارش شما با موفقیت ثبت شد.
بعد از تایید به کیف پولت اضافه میکنم ... 💞
";
    sendMessage($msg, $removeKeyboard);
    sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $mainKeys);
    $price = number_format($match[1]);
    $msg = "
💳 درخواست ( افزایش موجودی )

💰مبلغ: $price تومان
🧑‍💻 نام و نام خانوادگی : $name
🎯 یوزرنیم : @$username
🎫 کد کاربری : <code>$from_id</code>
";

    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approvePayment{$uid}_{$match[1]}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decPayment{$uid}_{$match[1]}"]
            ]
        ]
    ]);
    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
}
if (preg_match('/^approvePayment(\d+)_(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $match[2], $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("افزایش حساب شما با موفقیت تأیید شد\n✅ مبلغ " . number_format($match[2]) . " تومان به حساب شما اضافه شد", null, null, $match[1]);

    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => '✅', 'callback_data' => "dontsendanymore"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);
}
if (preg_match('/^decPayment(\d+)_(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => '❌', 'callback_data' => "dontsendanymore"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);
    file_put_contents("temp" . $from_id . ".txt", $keys);
    sendMessage("لطفا دلیل عدم تأیید افزایش موجودی را وارد کنید", $cancelKey);
    setUser($data . "_" . $message_id);
}
if (preg_match('/^decPayment(\d+)_(\d+)_(\d+)/', $userInfo['step'], $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    sendMessage("💔 افزایش موجودی شما به مبلغ "  . number_format($match[2]) . " به دلیل زیر رد شد\n\n$text", null, null, $match[1]);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $match[3],
        'reply_markup' => file_get_contents("temp" . $from_id . ".txt")
    ]);
    setUser();
    sendMessage('پیامت رو براش ارسال کردم ... 🤝', $removeKeyboard);
    unlink("temp" . $from_id . ".txt");
}
if ($data == "increaseUserWallet" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🀄️| آیدی عددی کاربر رو بفرس :", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "increaseUserWallet" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $userCount = $stmt->get_result()->num_rows;
        $stmt->close();
        if ($userCount > 0) {
            setUser("increaseWalletUser" . $text);
            sendMessage("💸 | مبلغی که میخوای بهش بدی رو وارد کن:");
        } else {
            setUser();
            sendMessage("🥴 | همچین کسی رو نداریما اشتباه وارد کردی به نظرم ", $removeKeyboard);
            sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);
        }
    } else {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if (preg_match('/^increaseWalletUser(\d+)/', $userInfo['step'], $match) && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $text, $match[1]);
        $stmt->execute();
        $stmt->close();

        sendMessage("✅ مبلغ " . number_format($text) . " تومان به حساب شما اضافه شد", null, null, $match[1]);
        sendMessage("✅ مبلغ " . number_format($text) . " تومان به کیف پول کاربر مورد نظر اضافه شد", $removeKeyboard);
        sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);
        setUser();
    } else {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if ($data == "editRewardChannel" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🤗|لطفا ربات رو در کانال ادمین کن و آیدی کانال رو بفرست", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "editRewardChannel" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    $botId = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getme"))->result->id;
    $result = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getChatMember?chat_id=$text&user_id=$botId"));
    if ($result->ok) {
        if ($result->result->status == "administrator") {
            $botState['rewardChannel'] = $text;

            $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
            $stmt->execute();
            $isExist = $stmt->get_result();
            $stmt->close();
            if ($isExist->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
            else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
            $newData = json_encode($botState);

            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $newData);
            $stmt->execute();
            $stmt->close();

            sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getGateWaysKeys());
            setUser();
            exit();
        }
    }
    sendMessage("😡|ای بابا ،ربات هنوز تو کانال عضو نشده، اول ربات رو تو کانال ادمین کن و آیدیش رو بفرست");
}
if ($data == "editLockChannel" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🤗|لطفا ربات رو در کانال ادمین کن و آیدی کانال رو بفرست", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "editLockChannel" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    $botId = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getme"))->result->id;
    $result = json_decode(file_get_contents("https://api.telegram.org/bot$botToken/getChatMember?chat_id=$text&user_id=$botId"));
    if ($result->ok) {
        if ($result->result->status == "administrator") {
            $botState['lockChannel'] = $text;

            $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
            $stmt->execute();
            $isExists = $stmt->get_result();
            $stmt->close();
            if ($isExists->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
            else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
            $newData = json_encode($botState);

            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $newData);
            $stmt->execute();
            $stmt->close();

            sendMessage('🔰هرکدوم از امکانات رو اگه تو ربات استفاده ای نداره ( خاموش ) کن !', getGateWaysKeys());
            setUser();
            exit();
        }
    }
    sendMessage("😡|ای بابا ،ربات هنوز تو کانال عضو نشده، اول ربات رو تو کانال ادمین کن و آیدیش رو بفرست");
}
if ($data == 'buySubscription' && ($botState['sellState'] == "on" || ($from_id == $admin || $userInfo['isAdmin'] == true))) {
    if ($botState['cartToCartState'] == "off" && $botState['walletState'] == "off") {
        alert("فعلا فروش نداریم");
        exit();
    }
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1 and `state` = 1 and `ucount` > 0 ORDER BY `id` ASC");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert("😔 | عزیز دلم هیچ سرور فعالی نداریم لطفا بعدا مجدد تست کن");
        exit;
    }
    $keyboard = [];
    while ($cat = $respd->fetch_assoc()) {
        $id = $cat['id'];
        $name = $cat['title'];
        $flag = $cat['flag'];
        $keyboard[] = ['text' => "$flag $name", 'callback_data' => "selectServer$id"];
    }
    $keyboard[] = ['text' => "⤵️ برگرد صفحه قبلی ", 'callback_data' => "mainMenu"];
    $keyboard = array_chunk($keyboard, 1);
    editText($message_id, '  1️⃣ مرحله یک:

لوکیشن مدنظرت رو برا خرید انتخاب کن: 🌐', json_encode(['inline_keyboard' => $keyboard]));
}
if ($data == 'createMultipleAccounts' && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1 and `ucount` > 0 ORDER BY `id` ASC");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        sendMessage("😔 | عزیز دلم هیچ سرور فعالی نداریم لطفا بعدا مجدد تست کن");
        exit;
    }
    $keyboard = [];
    while ($cat = $respd->fetch_assoc()) {
        $id = $cat['id'];
        $name = $cat['title'];
        $flag = $cat['flag'];
        $keyboard[] = ['text' => "$flag $name", 'callback_data' => "createAccServer$id"];
    }
    $keyboard[] = ['text' => "⤵️ برگرد صفحه قبلی ", 'callback_data' => "managePanel"];
    $keyboard = array_chunk($keyboard, 1);
    editText($message_id, '  1️⃣ مرحله یک:

لوکیشن مدنظرت رو برا خرید انتخاب کن: 🌐', json_encode(['inline_keyboard' => $keyboard]));
}
if (preg_match('/createAccServer(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $sid = $match[1];

    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent`=0 order by `id` asc");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert("هیچ دسته بندی برای این سرور وجود ندارد");
    } else {

        $keyboard = [];
        while ($file = $respd->fetch_assoc()) {
            $id = $file['id'];
            $name = $file['title'];
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1");
            $stmt->bind_param("ii", $sid, $id);
            $stmt->execute();
            $rowcount = $stmt->get_result()->num_rows;
            $stmt->close();
            if ($rowcount > 0) $keyboard[] = ['text' => "$name", 'callback_data' => "createAccCategory{$id}_{$sid}"];
        }
        if (empty($keyboard)) {
            alert("هیچ دسته بندی برای این سرور وجود ندارد");
            exit;
        }
        alert("♻️ | دریافت دسته بندی ...");
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "createMultipleAccounts"];
        $keyboard = array_chunk($keyboard, 1);
        editText($message_id, "2️⃣ مرحله دو:

دسته بندی مورد نظرت رو انتخاب کن 📚", json_encode(['inline_keyboard' => $keyboard]));
    }
}
if (preg_match('/createAccCategory(\d+)_(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $call_id = $match[1];
    $sid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1 order by `id` asc");
    $stmt->bind_param("ii", $sid, $call_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert("💡پلنی در این دسته بندی وجود ندارد ");
    } else {
        alert("📍در حال دریافت لیست پلن ها");
        $keyboard = [];
        while ($file = $respd->fetch_assoc()) {
            $id = $file['id'];
            $name = $file['title'];
            $keyboard[] = ['text' => "$name", 'callback_data' => "createAccPlan{$id}"];
        }
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "createAccServer$sid"];
        $keyboard = array_chunk($keyboard, 1);
        editText($message_id, "3️⃣ مرحله سه:

یکی از پلن هارو انتخاب کن و برو برای پرداختش 🧮", json_encode(['inline_keyboard' => $keyboard]));
    }
}
if (preg_match('/^createAccPlan(\d+)/', $data, $match) && $text != $cancelText) {
    delMessage();
    sendMessage("❗️لطفا مدت زمان اکانت را به ( روز ) وارد کن:", $cancelKey);
    setUser('createAccDate' . $match[1]);
}
if (preg_match('/^createAccDate(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if (is_numeric($text)) {
        if ($text > 0) {
            sendMessage("❕حجم اکانت ها رو به گیگابایت ( GB ) وارد کن:");
            setUser('createAccVolume' . $match[1] . "_" . $text);
        } else {
            sendMessage("عدد باید بیشتر از 0 باشه");
        }
    } else {
        sendMessage('😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟');
    }
}
if (preg_match('/^createAccVolume(\d+)_(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if (!is_numeric($text)) {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    } elseif ($text <= 0) {
        sendMessage("مقداری بزرگتر از 0 وارد کن");
        exit();
    }
    sendMessage("♻️ تعداد اکانت درخواستی رو وارد کن حداکثر هربار 6 عدد:

⚠️ | نکته: در صورت وارد کردن به مقدار بالا احتمالا اکانت ساخته نشود و پنل x-ui گیر کند
");
    setUser("createAccAmount" . $match[1] . "_" . $match[2] . "_" . $text);
}
if (preg_match('/^createAccAmount(\d+)_(\d+)_(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if (!is_numeric($text)) {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    } elseif ($text <= 0) {
        sendMessage("مقداری بزرگتر از 0 وارد کن");
        exit();
    }
    $uid = $from_id;
    $fid = $match[1];
    $acctxt = '';


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
    $rahgozar = $file_detail['rahgozar'];



    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];


    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0 && $acount >= $text) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - ? WHERE id=?");
            $stmt->bind_param("ii", $text, $fid);
            $stmt->execute();
            $stmt->close();
        } else {
            sendMessage("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $srv_remark = $stmt->get_result()->fetch_assoc()['remark'];
    $stmt->close();
    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
    $port = $savedinfo[0];
    $last_num = $savedinfo[1];
    include 'phpqrcode/qrlib.php';
    $ecc = 'L';
    $pixel_Size = 10;
    $frame_Size = 10;

    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $portType = $stmt->get_result()->fetch_assoc()['port_type'];
    $stmt->close();


    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
    for ($i = 1; $i <= $text; $i++) {
        $token = RandomString(30);
        $uniqid = generateRandomString(42, $protocol);
        if ($portType == "auto") {
            $port++;
        } else {
            $port = rand(1111, 65000);
        }
        $last_num++;

        $rnd = rand(1111, 99999);
        $remark = "{$srv_remark}-{$from_id}-{$rnd}";

        if ($inbound_id == 0) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        } else {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        }

        if (is_null($response)) {
            sendMessage('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
            break;
        }
        if ($response == "inbound not Found") {
            sendMessage("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
            break;
        }
        if (!$response->success) {
            sendMessage('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
            break;
        }

        $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
        $subLink = $botUrl . "settings/subLink.php?token=" . $token;
        foreach ($vraylink as $vray_link) {
            $acc_text = "
    
        🔮 $remark \n <code>$vray_link</code>
            ";
            if ($botState['subLinkState'] == "on") $acc_text .=
                " \n🌐 subscription : <code>$subLink</code>";

            $file = RandomString() . ".png";
            QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
            addBorderImage($file);
            sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
            unlink($file);
        }
        $vray_link = json_encode($vraylink);
        $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
        $stmt->execute();
    }
    $stmt->close();
    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    }
    sendMessage("☑️|❤️ اکانت های جدید با موفقیت ساخته شد", $mainKeys);
    setUser();
}
if (preg_match('/payWithCartToCart(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $fid = $payInfo['plan_id'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];


    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] == 0) {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0 && $acount <= 0) {
            alert("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if (!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys, true);
    else $paymentKeys = array();
    $stmt->close();


    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!", $cancelKey, "HTML");
    exit;
}
if (preg_match('/payWithWeSwap(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $fid = $payInfo['plan_id'];
    $type = $payInfo['type'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];


    if ($type != "INCREASE_WALLET" && $type != "RENEW_ACCOUNT") {
        if ($acount == 0 and $inbound_id != 0) {
            alert('ظرفیت این کانکشن پر شده است');
            exit;
        }
        if ($inbound_id == 0) {
            $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $server_info = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($server_info['ucount'] == 0) {
                alert('ظرفیت این سرور پر شده است');
                exit;
            }
        } else {
            if ($acount == 0) {
                alert('ظرفیت این سرور پر شده است');
                exit();
            }
        }
    }


    delMessage();
    sendMessage("لطفا منتظر باشید", $removeKeyboard);


    $price = $payInfo['price'];
    $rate = json_decode(file_get_contents("https://api.weswap.digital/api/rate"), true)['result'];
    $priceInUSD = round($price / $rate['USD'], 2);
    $priceInTrx = round($price / $rate['TRX'], 2);
    $pay = NOWPayments('POST', 'payment', [
        'price_amount' => $priceInUSD,
        'price_currency' => 'usd',
        'pay_currency' => 'trx'
    ]);
    if (isset($pay->pay_address)) {
        $payAddress = $pay->pay_address;

        $payId = $pay->payment_id;

        $stmt = $connection->prepare("UPDATE `pays` SET `payid` = ? WHERE `hash_id` = ?");
        $stmt->bind_param("is", $payId, $match[1]);
        $stmt->execute();
        $stmt->close();

        $keys = json_encode(['inline_keyboard' => [
            [['text' => "پرداخت با درگاه وی سواپ", 'url' => "https://weswap.digital/quick?amount=$priceInTrx&currency=TRX&address=$payAddress"]],
            [['text' => "پرداخت کردم ✅", 'callback_data' => "havePaiedWeSwap" . $match[1]]]
        ]]);
        sendMessage("
✅ لینک پرداخت با موفقیت ایجاد شد

💰مبلغ : " . $priceInTrx . " ترون

✔️ بعد از پرداخت حدود 1 الی 15 دقیقه صبر کنید تا پرداخت به صورت کامل انجام شود سپس روی پرداخت کردم کلیک کنید
⁮⁮ ⁮⁮
", $keys);
    } else {
        if ($pay->statusCode == 400) {
            sendMessage("مقدار انتخاب شده کمتر از حد مجاز است");
        } else {
            sendMessage("مشکلی رخ داده است، لطفا به پشتیبانی اطلاع بدهید");
        }
        sendMessage("لطفا یکی از کلید های زیر را انتخاب کنید", $mainKeys);
    }
}
if (preg_match('/havePaiedWeSwap(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($payInfo['state'] == "pending") {
        $payid = $payInfo['payid'];
        $payType = $payInfo['type'];
        $price = $payInfo['price'];

        $request_json = NOWPayments('GET', 'payment', $payid);
        if ($request_json->payment_status == 'finished' or $request_json->payment_status == 'confirmed' or $request_json->payment_status == 'sending') {
            $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
            $stmt->bind_param("s", $match[1]);
            $stmt->execute();
            $stmt->close();

            if ($payType == "INCREASE_WALLET") {
                $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                $stmt->bind_param("ii", $price, $from_id);
                $stmt->execute();
                $stmt->close();

                sendMessage("افزایش حساب شما با موفقیت تأیید شد\n✅ مبلغ " . number_format($price) . " تومان به حساب شما اضافه شد");
                sendMessage("✅ مبلغ " . number_format($price) . " تومان به کیف پول کاربر $from_id توسط درگاه وی سواپ اضافه شد", null, null, $admin);
            } elseif ($payType == "BUY_SUB") {
                $uid = $from_id;
                $fid = $payInfo['plan_id'];
                $volume = $payInfo['volume'];
                $days = $payInfo['day'];


                $acctxt = '';

                $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
                $stmt->bind_param("i", $fid);
                $stmt->execute();
                $file_detail = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($volume == 0 && $days == 0) {
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



                if ($acount == 0 and $inbound_id != 0) {
                    alert('ظرفیت این کانکشن پر شده است');
                    exit;
                }
                if ($inbound_id == 0) {
                    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
                    $stmt->bind_param("i", $server_id);
                    $stmt->execute();
                    $server_info = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    if ($server_info['ucount'] != 0) {
                        $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
                        $stmt->bind_param("i", $server_id);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        alert('ظرفیت این سرور پر شده است');
                        exit;
                    }
                } else {
                    if ($acount != 0) {
                        $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
                        $stmt->bind_param("i", $fid);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

                $uniqid = generateRandomString(42, $protocol);

                $savedinfo = file_get_contents('settings/temp.txt');
                $savedinfo = explode('-', $savedinfo);
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

                $rnd = rand(1111, 99999);
                $remark = "{$srv_remark}-{$from_id}-{$rnd}";

                if ($portType == "auto") {
                    file_put_contents('settings/temp.txt', $port . '-' . $last_num);
                } else {
                    $port = rand(1111, 65000);
                }

                if ($inbound_id == 0) {
                    $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
                    if (!$response->success) {
                        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
                    }
                } else {
                    $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
                    if (!$response->success) {
                        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
                    }
                }

                if (is_null($response)) {
                    alert('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
                    exit;
                }
                if ($response == "inbound not Found") {
                    alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
                    exit;
                }
                if (!$response->success) {
                    alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
                    exit;
                }
                alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');

                include 'phpqrcode/qrlib.php';
                $token = RandomString(30);
                $subLink = $botUrl . "settings/subLink.php?token=" . $token;

                $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
                foreach ($vraylink as $vray_link) {
                    $acc_text = "

💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
                    if ($botState['subLinkState'] == "on") $acc_text .= "

🌐 subscription : <code>$subLink</code>

";

                    $file = RandomString() . ".png";
                    $ecc = 'L';
                    $pixel_Size = 10;
                    $frame_Size = 10;

                    QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
                    addBorderImage($file);
                    sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
                    unlink($file);
                }


                if ($userInfo['refered_by'] != null) {
                    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
                    $stmt->execute();
                    $inviteAmount = $stmt->get_result()->fetch_assoc()['value'] ?? 0;
                    $stmt->close();
                    $inviterId = $userInfo['refered_by'];

                    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
                    $stmt->bind_param("ii", $inviteAmount, $inviterId);
                    $stmt->execute();
                    $stmt->close();

                    sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید", null, null, $inviterId);
                }
                $vray_link = json_encode($vraylink);

                $stmt = $connection->prepare("INSERT INTO `orders_list` 
    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
                $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
                $stmt->execute();
                $order = $stmt->get_result();
                $stmt->close();
                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => "بنازم خرید جدید ❤️", 'callback_data' => "mainMenu"]
                    ],
                ]]);
                sendMessage("

💓 خرید پلن جدید ( وی سواپ )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: <a href='tg://user?id=$from_id'>$first_name</a>
⚡️ نام کاربری: @$username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
", $keys, "html", $admin);
            } elseif ($payType == "RENEW_ACCOUNT") {
                $oid = $payInfo['plan_id'];
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
                $price = $payInfo['price'];

                if ($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, $days);
                else
                    $response = editInboundTraffic($server_id, $remark, $volume, $days);

                if (is_null($response)) {
                    alert('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید', true);
                    exit;
                }
                $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
                $newExpire = $expire_date + $days * 86400;
                $stmt->bind_param("ii", $newExpire, $oid);
                $stmt->execute();
                $stmt->close();
                $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
                $stmt->bind_param("iiisii", $uid, $server_id, $inbound_id, $remark, $price, $time);
                $stmt->execute();
                $stmt->close();

                sendMessage("✅سرویس $remark با موفقیت تمدید شد", $mainKeys);
                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => "به به تمدید 😍", 'callback_data' => "mainMenu"]
                    ],
                ]]);

                sendMessage("
♻️ تمدید سرویس ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
⁮⁮ ⁮⁮
", $keys, "html", $admin);
            } elseif (preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo)) {
                $server_id = $increaseInfo[1];
                $inbound_id = $increaseInfo[2];
                $remark = $increaseInfo[3];
                $planid = $increaseInfo[4];


                $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
                $stmt->bind_param("i", $planid);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $price = $payInfo['price'];
                $volume = $res['volume'];


                if ($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $remark, 0, $volume);
                else
                    $response = editInboundTraffic($server_id, $remark, 0, $volume);

                if ($response->success) {
                    $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ?, `notif` = 0 WHERE `remark` = ?");
                    $newVolume = $volume * 86400;
                    $stmt->bind_param("is", $newVolume, $remark);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
                    $newVolume = $volume * 86400;
                    $stmt->bind_param("iiisii", $from_id, $server_id, $inbound_id, $remark, $price, $time);
                    $stmt->execute();
                    $stmt->close();

                    sendMessage("✅$volume روز به مدت زمان سرویس شما اضافه شد", $mainKeys);

                    $keys = json_encode(['inline_keyboard' => [
                        [
                            ['text' => "اخیش یکی زمان زد 😁", 'callback_data' => "wizwizch"]
                        ],
                    ]]);
                    sendMessage("
🔋|💰 افزایش زمان با ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
⏰ مدت افزایش: $volume روز
💰قیمت: $price تومان
⁮⁮ ⁮⁮
", $keys, "html", $admin);

                    exit;
                } else {
                    alert("به دلیل مشکل فنی امکان افزایش حجم نیست. لطفا به مدیریت اطلاع بدید یا 5دقیقه دیگر دوباره تست کنید", true);
                    exit;
                }
            } elseif (preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo)) {
                $server_id = $increaseInfo[1];
                $inbound_id = $increaseInfo[2];
                $remark = $increaseInfo[3];
                $planid = $increaseInfo[4];

                $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
                $stmt->bind_param("i", $planid);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $price = $payInfo['price'];
                $volume = $res['volume'];

                if ($inbound_id > 0)
                    $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, 0);
                else
                    $response = editInboundTraffic($server_id, $remark, $volume, 0);

                if ($response->success) {
                    $stmt = $connection->prepare("UPDATE `orders_list` SET `notif` = 0 WHERE `remark` = ?");
                    $stmt->bind_param("s", $remark);
                    $stmt->execute();
                    $stmt->close();
                    $keys = json_encode(['inline_keyboard' => [
                        [
                            ['text' => "اخیش یکی حجم زد 😁", 'callback_data' => "wizwizch"]
                        ],
                    ]]);
                    sendMessage("
🔋|💰 افزایش حجم با ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
⏰ مدت افزایش: $volume گیگ
💰قیمت: $price تومان
⁮⁮ ⁮⁮
", $keys, "html", $admin);
                    sendMessage("✅$volume گیگ به حجم سرویس شما اضافه شد", $mainKeys);
                    exit;
                } else {
                    alert("به دلیل مشکل فنی امکان افزایش حجم نیست. لطفا به مدیریت اطلاع بدید یا 5دقیقه دیگر دوباره تست کنید", true);
                    exit;
                }
            }

            bot('editMessageReplyMarkup', [
                'chat_id' => $from_id,
                'message_id' => $message_id,
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [['text' => "پرداخت انجام شد", 'callback_data' => "wizwizch"]]
                ]])
            ]);
        } else {
            if ($request_json->payment_status == 'partially_paid') {
                $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'partiallyPaied' WHERE `hash_id` = ?");
                $stmt->bind_param("s", $match[1]);
                $stmt->execute();
                $stmt->close();
                alert("شما هزینه کمتری پرداخت کردید، لطفا به پشتیبانی پیام بدهید");
            } else {
                alert("پرداخت مورد نظر هنوز تکمیل نشده!");
            }
        }
    } else alert("این لینک پرداخت منقضی شده است");
}
if ($data == "messageToSpeceficUser" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🀄️| آیدی عددی کاربر رو بفرس :", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "messageToSpeceficUser" && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (!is_numeric($text)) {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
        exit();
    }
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
    $stmt->bind_param("i", $text);
    $stmt->execute();
    $usersCount = $stmt->get_result()->num_rows;
    $stmt->close();

    if ($usersCount > 0) {
        sendMessage("👀| خصوصی میخوای بهش پیام بدی شیطون، پیامت رو بفرس تا در گوشش بگم:");
        setUser("sendMessageToUser" . $text);
    } else {
        sendMessage("🥴 | همچین کسی رو نداریما اشتباه وارد کردی به نظرم ");
    }
}
if ($data == 'message2All' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $sendInfo = json_decode(file_get_contents("settings/messagewizwiz.json"), true);
    $offset = $sendInfo['offset'];
    $msg = $sendInfo['text'];

    if (strlen($msg) > 1 and $offset != -1) {
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
");
        exit;
    }
    setUser('s2a');
    sendMessage("لطفا پیامت رو بنویس ، میخوام برا همه بفرستمش: 🙂", $cancelKey);
    exit;
}
if ($userInfo['step'] == 's2a' and $text != $cancelText) {
    setUser();
    sendMessage('⏳ مرسی از پیامت ، کم کم برا همه ارسال میشه ...  ', $removeKeyboard);
    sendMessage("لطفا یکی از کلید های زیر را انتخاب کنید", $mainKeys);

    if ($fileid !== null) {
        $value = ['fileid' => $fileid, 'caption' => $caption];
        $type = $filetype;
    } else {
        $type = 'text';
        $value = $text;
    }
    $messageValue = json_encode(['type' => $type, 'value' => $value]);

    $sendInfo = json_decode(file_get_contents("settings/messagewizwiz.json"), true);
    $sendInfo['offset'] = 0;
    $sendInfo['text'] = $messageValue;
    file_put_contents("settings/messagewizwiz.json", json_encode($sendInfo));
}
if (preg_match('/selectServer(\d+)/', $data, $match) && ($botState['sellState'] == "on" || ($from_id == $admin || $userInfo['isAdmin'] == true))) {
    $sid = $match[1];

    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent`=0 order by `id` asc");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert("هیچ دسته بندی برای این سرور وجود ندارد");
    } else {

        $keyboard = [];
        while ($file = $respd->fetch_assoc()) {
            $id = $file['id'];
            $name = $file['title'];
            $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1");
            $stmt->bind_param("ii", $sid, $id);
            $stmt->execute();
            $rowcount = $stmt->get_result()->num_rows;
            $stmt->close();
            if ($rowcount > 0) $keyboard[] = ['text' => "$name", 'callback_data' => "selectCategory{$id}_{$sid}"];
        }
        if (empty($keyboard)) {
            alert("هیچ دسته بندی برای این سرور وجود ندارد");
            exit;
        }
        alert("♻️ | دریافت دسته بندی ...");
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "buySubscription"];
        $keyboard = array_chunk($keyboard, 1);
        editText($message_id, "2️⃣ مرحله دو:

دسته بندی مورد نظرت رو انتخاب کن 📚", json_encode(['inline_keyboard' => $keyboard]));
    }
}
if (preg_match('/selectCategory(\d+)_(\d+)/', $data, $match) && ($botState['sellState'] == "on" || $from_id == $admin || $userInfo['isAdmin'] == true)) {
    $call_id = $match[1];
    $sid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `price` != 0 and `catid`=? and `active`=1 order by `id` asc");
    $stmt->bind_param("ii", $sid, $call_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert("💡پلنی در این دسته بندی وجود ندارد ");
    } else {
        alert("📍در حال دریافت لیست پلن ها");
        $keyboard = [];
        while ($file = $respd->fetch_assoc()) {
            $id = $file['id'];
            $name = $file['title'];
            $price = $file['price'];
            $price = ($price == 0) ? 'رایگان' : number_format($price) . ' تومان ';
            $keyboard[] = ['text' => "$name - $price", 'callback_data' => "selectPlan{$id}_{$call_id}"];
        }
        if ($botState['plandelkhahState'] == "on") {
            $keyboard[] = ['text' => '➕ پلن دلخواه تو بخر', 'callback_data' => "selectCustomPlan{$call_id}_{$sid}"];
        }
        $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "selectServer$sid"];
        $keyboard = array_chunk($keyboard, 1);
        editText($message_id, "3️⃣ مرحله سه:

یکی از پلن هارو انتخاب کن و برو برای پرداختش 🧮", json_encode(['inline_keyboard' => $keyboard]));
    }
}
if (preg_match('/selectCustomPlan(\d+)_(\d+)/', $data, $match) && ($botState['sellState'] == "on" || $from_id == $admin || $userInfo['isAdmin'] == true)) {
    $call_id = $match[1];
    $sid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? and `catid`=? and `active`=1 order by `id` asc");
    $stmt->bind_param("ii", $sid, $call_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    alert("📍در حال دریافت لیست پلن ها");
    $keyboard = [];
    while ($file = $respd->fetch_assoc()) {
        $id = $file['id'];
        $name = preg_replace("/پلن\s(\d+)\sگیگ\s/", "", $file['title']);
        $keyboard[] = ['text' => "$name", 'callback_data' => "selectCustomePlan{$id}_{$call_id}"];
    }
    $keyboard[] = ['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "selectServer$sid"];
    $keyboard = array_chunk($keyboard, 1);
    editText($message_id, "یکی از پلن ها رو انتخاب کن تا برات ویرایشش کنم", json_encode(['inline_keyboard' => $keyboard]));
}
if (preg_match('/selectCustomePlan(\d+)_(\d+)/', $data, $match) && ($botState['sellState'] == "on" || $from_id == $admin)) {
    delMessage();
    sendMessage("🔋|لطفا مقدار گیگابایت سرویست رو وارد کن\n💰|هزینه هر گیگ: " . $botState['gbPrice'], $cancelKey);
    setUser("selectCustomPlanGB" . $match[1] . "_" . $match[2]);
}
if (preg_match('/selectCustomPlanGB(\d+)_(\d+)/', $userInfo['step'], $match) && ($botState['sellState'] == "on" || $from_id == $admin) && $text != $cancelText) {
    if (!is_numeric($text)) {
        sendMessage("😡|لطفا فقط عدد ارسال کن");
        exit();
    } elseif ($text <= 0) {
        sendMessage("لطفا عددی بزرگتر از 0 وارد کن");
        exit();
    }
    $id = $match[1];

    sendMessage("⏰|لطفا  تعداد روز اشتراکت رو وارد کن\n💰|هزینه هر روز: " . $botState['dayPrice']);
    setUser("selectCustomPlanDay" . $match[1] . "_" . $match[2] . "_" . $text);
}
if ((preg_match('/^discountCustomPlanDay(\d+)_(\d+)_(\d+)_(\d+)_(\d+)/', $userInfo['step'], $match) || preg_match('/selectCustomPlanDay(\d+)_(\d+)_(\d+)/', $userInfo['step'], $match)) && ($botState['sellState'] == "on" || $from_id == $admin) && $text != $cancelText) {
    if (preg_match('/^discountCustomPlanDay/', $userInfo['step'])) {
        $days = $match[4];
        $rowId = $match[5];

        $time = time();
        $stmt = $connection->prepare("SELECT * FROM `discounts` WHERE (`expire_date` > $time OR `expire_date` = 0) AND (`expire_count` > 0 OR `expire_count` = -1) AND `hash_id` = ?");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $list = $stmt->get_result();
        $stmt->close();

        $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `id` = ?");
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $payInfo = $stmt->get_result()->fetch_assoc();
        $hash_id = $payInfo['hash_id'];
        $price = $payInfo['price'];
        $stmt->close();

        if ($list->num_rows > 0) {
            $discountInfo = $list->fetch_assoc();
            $amount = $discountInfo['amount'];
            $type = $discountInfo['type'];
            $count = $discountInfo['expire_count'];
            $usedBy = !is_null($discountInfo['used_by']) ? json_decode($discountInfo['used_by'], true) : array();
            if (!in_array($from_id, $usedBy)) {
                $usedBy[] = $from_id;
                $encodeUsedBy = json_encode($usedBy);

                if ($count != -1) $query = "UPDATE `discounts` SET `expire_count` = `expire_count` - 1, `used_by` = ? WHERE `id` = ?";
                else $query = "UPDATE `discounts` SET `used_by` = ? WHERE `id` = ?";

                $stmt = $connection->prepare($query);
                $stmt->bind_param("si", $encodeUsedBy, $discountInfo['id']);
                $stmt->execute();
                $stmt->close();

                if ($type == "percent") {
                    $discount = $price * $amount / 100;
                    $price -= $discount;
                    $discount = number_format($discount) . " تومان";
                } else {
                    $price -= $amount;
                    $discount = number_format($amount) . " تومان";
                }
                if ($price < 0) $price = 0;

                $stmt = $connection->prepare("UPDATE `pays` SET `price` = ? WHERE `id` = ?");
                $stmt->bind_param("ii", $price, $rowId);
                $stmt->execute();
                $stmt->close();
                sendMessage(" ✅|کد تخفیف با موفقیت استفاده شد\nمقدار تخفیف $discount");
                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => "❤️", "callback_data" => "wizwizch"]
                    ],
                ]]);
                sendMessage("
         ☑️|🎁 کد تخفیف استفاده شد
        
        🔰آیدی کاربر: $from_id
        👨‍💼اسم کاربر: $first_name
        ⚡️ نام کاربری: $username
        🎁 کد تخفیف: $text
        💰مقدار تخفیف: $discount
        ⁮⁮ ⁮⁮
        ", $keys, null, $admin);
            } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
        } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
    } else {
        if (!is_numeric($text)) {
            sendMessage("😡|لطفا فقط عدد ارسال کن");
            exit();
        } elseif ($text <= 0) {
            sendMessage("لطفا عددی بزرگتر از 0 وارد کن");
            exit();
        }
        $days = $text;
    }
    $id = $match[1];
    $call_id = $match[2];
    $volume = $match[3];
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

    $name = $catname . " " . $respd['title'];
    $desc = $respd['descr'];
    $sid = $respd['server_id'];
    $keyboard = array();
    $token = base64_encode("{$from_id}.{$id}");
    $temp = array();

    if (!preg_match('/^discountCustomPlanDay/', $userInfo['step'])) {
        $price =  $volume * $botState['gbPrice'] + $days * $botState['dayPrice'];
        $hash_id = RandomString();
        $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` = 'BUY_SUB' AND `state` = 'pending'");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $stmt->close();

        $time = time();
        $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                    VALUES (?, ?, 'BUY_SUB', ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("siiiiii", $hash_id, $from_id, $id, $volume, $days, $price, $time);
        $stmt->execute();
        $rowId = $stmt->insert_id;
        $stmt->close();
    }


    if ($botState['cartToCartState'] == "on") {
        $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "payCustomWithCartToCart$hash_id"];
    }
    if ($botState['nowPaymentOther'] == "on") {
        $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['zarinpal'] == "on") {
        $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['nextpay'] == "on") {
        $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['weSwapState'] == "on") {
        $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['walletState'] == "on") {
        $temp[] = ['text' => "💰پرداخت با موجودی ",  'callback_data' => "payCustomWithWallet$hash_id"];
    }
    array_push($keyboard, $temp);
    if (!preg_match('/^discountCustomPlanDay/', $userInfo['step'])) $keyboard[] = [['text' => " 🎁 نکنه کد تخفیف داری؟ ",  'callback_data' => "haveDiscountCustom_" . $match[1] . "_" . $match[2] . "_" . $match[3] . "_" . $text . "_" . $rowId]];
    $keyboard[] = [['text' => $cancelText, 'callback_data' => "mainMenu"]];
    $price = ($price == 0) ? 'رایگان' : number_format($price) . ' تومان ';
    sendMessage("
〽️ نام پلن: $name
حجم اختصاصی: $volume GB
مدت اختصاصی: $days روز
➖➖➖➖➖➖➖
💎 قیمت پنل : $price
➖➖➖➖➖➖➖
📃 توضیحات :
$desc
➖➖➖➖➖➖➖
", json_encode(['inline_keyboard' => $keyboard]), "HTML");
    setUser();
}
if (preg_match('/^haveDiscount(.+?)_(.*)/', $data, $match)) {
    delMessage();
    sendMessage("🎁|کد تخفیف تو را وارد کن:", $cancelKey);
    if ($match[1] == "Custom") setUser('discountCustomPlanDay' . $match[2]);
    elseif ($match[1] == "SelectPlan") setUser('discountSelectPlan' . $match[2]);
    elseif ($match[1] == "Renew") setUser('discountRenew' . $match[2]);
}
if ($data == "getTestAccount") {
    if ($userInfo['freetrial'] != null) {
        alert("شما اکانت تست را قبلا استفاده کرده اید");
        exit();
    }
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `price`=0");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();

    if ($respd->num_rows > 0) {
        alert("♻️در حال دریافت جزییات ... ");
        $keyboard = array();
        while ($row = $respd->fetch_assoc()) {
            $id = $row['id'];
            $catInfo = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
            $catInfo->bind_param("i", $row['catid']);
            $catInfo->execute();
            $catname = $catInfo->get_result()->fetch_assoc()['title'];
            $catInfo->close();

            $name = $catname . " " . $row['title'];
            $price =  $row['price'];
            $desc = $row['descr'];
            $sid = $row['server_id'];

            $keyboard[] = [['text' => $name, 'callback_data' => "freeTrial$id"]];
        }
        $keyboard[] = [['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "mainMenu"]];
        editText($message_id, "لطفا یکی از کلید های زیر را انتخاب کنید", json_encode(['inline_keyboard' => $keyboard]), "HTML");
    } else alert("این بخش موقتا غیر فعال است");
}
if ((preg_match('/^discountSelectPlan(\d+)_(\d+)_(\d+)/', $userInfo['step'], $match) || preg_match('/selectPlan(\d+)_(\d+)/', $data, $match)) && ($botState['sellState'] == "on" || $from_id == $admin) && $text != $cancelText) {
    if (preg_match('/^discountSelectPlan/', $userInfo['step'])) {
        $rowId = $match[3];

        $time = time();
        $stmt = $connection->prepare("SELECT * FROM `discounts` WHERE (`expire_date` > $time OR `expire_date` = 0) AND (`expire_count` > 0 OR `expire_count` = -1) AND `hash_id` = ?");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $list = $stmt->get_result();
        $stmt->close();

        $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `id` = ?");
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $payInfo = $stmt->get_result()->fetch_assoc();
        $hash_id = $payInfo['hash_id'];
        $afterDiscount = $payInfo['price'];
        $stmt->close();

        if ($list->num_rows > 0) {
            $discountInfo = $list->fetch_assoc();
            $amount = $discountInfo['amount'];
            $type = $discountInfo['type'];
            $count = $discountInfo['expire_count'];
            $usedBy = !is_null($discountInfo['used_by']) ? json_decode($discountInfo['used_by'], true) : array();
            if (!in_array($from_id, $usedBy)) {
                $usedBy[] = $from_id;
                $encodeUsedBy = json_encode($usedBy);

                if ($count != -1) $query = "UPDATE `discounts` SET `expire_count` = `expire_count` - 1, `used_by` = ? WHERE `id` = ?";
                else $query = "UPDATE `discounts` SET `used_by` = ? WHERE `id` = ?";

                $stmt = $connection->prepare($query);
                $stmt->bind_param("si", $encodeUsedBy, $discountInfo['id']);
                $stmt->execute();
                $stmt->close();

                if ($type == "percent") {
                    $discount = $afterDiscount * $amount / 100;
                    $afterDiscount -= $discount;
                    $discount = number_format($discount) . " تومان";
                } else {
                    $afterDiscount -= $amount;
                    $discount = number_format($amount) . " تومان";
                }
                if ($afterDiscount < 0) $afterDiscount = 0;

                $stmt = $connection->prepare("UPDATE `pays` SET `price` = ? WHERE `id` = ?");
                $stmt->bind_param("ii", $afterDiscount, $rowId);
                $stmt->execute();
                $stmt->close();
                sendMessage(" ✅|کد تخفیف با موفقیت استفاده شد\nمقدار تخفیف $discount");
                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => "❤️", "callback_data" => "wizwizch"]
                    ],
                ]]);
                sendMessage("
 ☑️|🎁 کد تخفیف استفاده شد

🔰آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎁 کد تخفیف: $text
💰مقدار تخفیف: $discount
⁮⁮ ⁮⁮
                ", $keys, null, $admin);
            } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
        } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
        setUser();
    } else delMessage();

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

    $name = $catname . " " . $respd['title'];
    $desc = $respd['descr'];
    $sid = $respd['server_id'];
    $keyboard = array();
    $price =  $respd['price'];
    if ($price == 0 or ($from_id == $admin)) {
        $keyboard[] = [['text' => '📥 دریافت رایگان', 'callback_data' => "freeTrial$id"]];
    } else {
        $token = base64_encode("{$from_id}.{$id}");
        $temp = array();


        if (!preg_match('/^discountSelectPlan/', $userInfo['step'])) {
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
            $rowId = $stmt->insert_id;
            $stmt->close();
        } else {
            $price = $afterDiscount;
        }


        if ($botState['cartToCartState'] == "on") {
            $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "payWithCartToCart$hash_id"];
        }
        if ($botState['nowPaymentOther'] == "on") {
            $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
        }
        if (count($temp) == 2) {
            array_push($keyboard, $temp);
            $temp = array();
        }
        if ($botState['zarinpal'] == "on") {
            $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
        }
        if (count($temp) == 2) {
            array_push($keyboard, $temp);
            $temp = array();
        }
        if ($botState['nextpay'] == "on") {
            $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
        }
        if (count($temp) == 2) {
            array_push($keyboard, $temp);
            $temp = array();
        }
        if ($botState['weSwapState'] == "on") {
            $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
        }
        if (count($temp) == 2) {
            array_push($keyboard, $temp);
            $temp = array();
        }
        if ($botState['walletState'] == "on") {
            $temp[] = ['text' => "💰پرداخت با موجودی ",  'callback_data' => "payWithWallet$hash_id"];
        }
        array_push($keyboard, $temp);

        if (!preg_match('/^discountSelectPlan/', $userInfo['step'])) $keyboard[] = [['text' => " 🎁 نکنه کد تخفیف داری؟ ",  'callback_data' => "haveDiscountSelectPlan_" . $match[1] . "_" . $match[2] . "_" . $rowId]];
    }
    $keyboard[] = [['text' => '⤵️ برگرد صفحه قبلی ', 'callback_data' => "selectCategory{$call_id}_{$sid}"]];
    $price = ($price == 0) ? 'رایگان' : number_format($price) . ' تومان ';
    sendMessage("
〽️ نام پلن: $name
➖➖➖➖➖➖➖
💎 قیمت پنل : $price
➖➖➖➖➖➖➖
📃 توضیحات :
$desc
➖➖➖➖➖➖➖
", json_encode(['inline_keyboard' => $keyboard]), "HTML");
}
if (preg_match('/payCustomWithWallet(.*)/', $data, $match)) {
    setUser();

    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->close();

    $uid = $from_id;
    $fid = $payInfo['plan_id'];
    $volume = $payInfo['volume'];
    $days = $payInfo['day'];

    $acctxt = '';

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $date = time();
    $expire_microdate = floor(microtime(true) * 1000) + (864000 * $days * 100);
    $expire_date = $date + (86400 * $days);
    $type = $file_detail['type'];
    $protocol = $file_detail['protocol'];
    $price = $payInfo['price'];

    if ($userInfo['wallet'] < $price) {
        alert("موجودی حساب شما کم است");
        exit();
    }


    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];
    $rahgozar = $file_detail['rahgozar'];



    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42, $protocol);

    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
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

    $rnd = rand(1111, 99999);
    $remark = "{$srv_remark}-{$from_id}-{$rnd}";

    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    } else {
        $port = rand(1111, 65000);
    }

    if ($inbound_id == 0) {
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        if (!$response->success) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        }
    } else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        if (!$response->success) {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        }
    }

    if (is_null($response)) {
        alert('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
    if ($response == "inbound not Found") {
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
        exit;
    }
    if (!$response->success) {
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');

    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $price, $uid);
    $stmt->execute();
    include 'phpqrcode/qrlib.php';
    $token = RandomString(30);
    $subLink = $botUrl . "settings/subLink.php?token=" . $token;

    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
    delMessage();
    foreach ($vraylink as $vray_link) {
        $acc_text = "
💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
        if ($botState['subLinkState'] == "on") $acc_text .= "

🌐 subscription : <code>$subLink</code>";

        $file = RandomString() . ".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;

        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
        addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
        unlink($file);
    }


    if ($userInfo['refered_by'] != null) {
        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $inviteAmount = $stmt->get_result()->fetch_assoc()['value'] ?? 0;
        $stmt->close();
        $inviterId = $userInfo['refered_by'];

        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $inviteAmount, $inviterId);
        $stmt->execute();
        $stmt->close();

        sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید", null, null, $inviterId);
    }
    $vray_link = json_encode($vraylink);

    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
    $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();
    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => "بنازم خرید جدید ❤️", 'callback_data' => "mainMenu"]
        ],
    ]]);
    sendMessage("
💓 خرید پلن دلخواه ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: <a href='tg://user?id=$from_id'>$first_name</a>
⚡️ نام کاربری: @$username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
", $keys, "html", $admin);
}
if (preg_match('/payCustomWithCartToCart(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $fid = $payInfo['plan_id'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];


    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] == 0) {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0 && $acount <= 0) {
            sendMessage("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }

    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if (!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys, true);
    else $paymentKeys = array();
    $stmt->close();


    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!", $cancelKey, "HTML");
    exit;
}
if (preg_match('/payCustomWithCartToCart(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'sent' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->execute();

    $fid = $payInfo['plan_id'];
    $volume = $payInfo['volume'];
    $days = $payInfo['day'];

    setUser();
    $uid = $userInfo['userid'];
    $name = $userInfo['name'];
    $username = $userInfo['username'];

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
    $filename = $catname . " " . $res['title'];
    $fileprice = $payInfo['price'];

    $infoc = strlen($text) > 1 ? $text : "$caption <a href='$fileurl'>&#8194;نمایش فیش</a>";
    $msg = "
🛍 سفارشت با موفقیت ثبت شد.
بعد از تایید برات ارسال میکنم ... 🥳
";
    sendMessage($msg, $removeKeyboard);
    sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $mainKeys);

    $msg = "
💓 خرید پلن دلخواه ( کارت به کارت )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: <a href='tg://user?id=$from_id'>$first_name</a>
⚡️ نام کاربری: @$username
💰مبلغ پرداختی: $fileprice تومان
✏️ نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
";
    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "accCustom" . $match[1]],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decline$uid"]
            ]
        ]
    ]);
    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
}
if (preg_match('/accCustom(.*)/', $data, $match) and $text != $cancelText) {
    setUser();

    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->close();

    $fid = $payInfo['plan_id'];
    $volume = $payInfo['volume'];
    $days = $payInfo['day'];
    $uid = $payInfo['user_id'];

    $acctxt = '';


    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

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

    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42, $protocol);

    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
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

    $rnd = rand(1111, 99999);
    $remark = "{$srv_remark}-{$uid}-{$rnd}";

    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    } else {
        $port = rand(1111, 65000);
    }

    if ($inbound_id == 0) {
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        if (!$response->success) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        }
    } else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        if (!$response->success) {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        }
    }

    if (is_null($response)) {
        alert('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
    if ($response == "inbound not Found") {
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
        exit;
    }
    if (!$response->success) {
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');

    include 'phpqrcode/qrlib.php';
    $token = RandomString(30);
    $subLink = $botUrl . "settings/subLink.php?token=" . $token;

    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
    foreach ($vraylink as $vray_link) {
        $acc_text = "
💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
        if ($botState['subLinkState'] == "on") $acc_text .= "

\n🌐 subscription : <code>$subLink</code>";

        $file = RandomString() . ".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;

        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
        addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
        unlink($file);
    }
    sendMessage('✅ کانفیگ و براش ارسال کردم', $mainKeys);

    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
    $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();


    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => "✅", 'callback_data' => "wizwizch"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);



    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);

    $filename = $file_detail['title'];
    $fileprice = number_format($file_detail['price']);
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    if ($user_detail['refered_by'] != null) {
        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $inviteAmount = $stmt->get_result()->fetch_assoc()['value'] ?? 0;
        $stmt->close();
        $inviterId = $user_detail['refered_by'];

        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $inviteAmount, $inviterId);
        $stmt->execute();
        $stmt->close();

        sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید", null, null, $inviterId);
    }


    $uname = $user_detail['name'];
    $user_name = $user_detail['username'];

    if ($admin != $from_id) {
        $keys = json_encode(['inline_keyboard' => [
            [
                ['text' => "به به 🛍", 'callback_data' => "wizwizch"]
            ],
        ]]);
        sendMessage("
👨‍👦‍👦 خرید ( زیر مجموعه )

🧝‍♂️آیدی کاربر: $uid
🛡اسم کاربر: $uname
🔖 نام کاربری: $user_name
💰مبلغ پرداختی: $price تومان
🔮 نام سرویس: $remark
💮 سفارش: $filename
⁮⁮ ⁮⁮
        ", null, null, $admin);
    }
}
if (preg_match('/payWithWallet(.*)/', $data, $match)) {
    setUser();

    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $uid = $from_id;
    $fid = $payInfo['plan_id'];
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
    $rahgozar = $file_detail['rahgozar'];
    $price = $payInfo['price'];

    if ($userInfo['wallet'] < $price) {
        alert("موجودی حساب شما کم است");
        exit();
    }



    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];


    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->close();

    $uniqid = generateRandomString(42, $protocol);

    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
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

    $rnd = rand(1111, 99999);
    $remark = "{$srv_remark}-{$from_id}-{$rnd}";

    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    } else {
        $port = rand(1111, 65000);
    }

    if ($inbound_id == 0) {
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        if (!$response->success) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        }
    } else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        if (!$response->success) {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        }
    }

    if (is_null($response)) {
        alert('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
    if ($response == "inbound not Found") {
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
        exit;
    }
    if (!$response->success) {
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');

    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $price, $uid);
    $stmt->execute();
    include 'phpqrcode/qrlib.php';
    delMessage();
    $token = RandomString(30);
    $subLink = $botUrl . "settings/subLink.php?token=" . $token;

    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
    foreach ($vraylink as $vray_link) {
        $acc_text = "
💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
        if ($botState['subLinkState'] == "on") $acc_text .= "

\n🌐 subscription : <code>$subLink</code>";

        $file = RandomString() . ".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;

        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
        addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
        unlink($file);
    }

    $vray_link = json_encode($vraylink);


    if ($userInfo['refered_by'] != null) {
        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $inviteAmount = $stmt->get_result()->fetch_assoc()['value'] ?? 0;
        $stmt->close();
        $inviterId = $userInfo['refered_by'];

        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $inviteAmount, $inviterId);
        $stmt->execute();
        $stmt->close();

        sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید", null, null, $inviterId);
    }

    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
    $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();
    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => "بنازم خرید جدید ❤️", 'callback_data' => "mainMenu"]
        ],
    ]]);
    sendMessage("
❗️خرید جدید ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: <a href='tg://user?id=$from_id'>$first_name</a>
⚡️ نام کاربری: @$username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
", $keys, "html", $admin);
}
if (preg_match('/payWithCartToCart(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $fid = $payInfo['plan_id'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];


    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] == 0) {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0 && $acount < $text) {
            alert("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }


    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if (!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys, true);
    else $paymentKeys = array();
    $stmt->close();


    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!", $cancelKey, "HTML");
    exit;
}
if (preg_match('/payWithCartToCart(.*)/', $userInfo['step'], $match) and $text != $cancelText) {

    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->close();


    $fid = $payInfo['plan_id'];
    setUser();
    $uid = $userInfo['userid'];
    $name = $userInfo['name'];
    $username = $userInfo['username'];

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
    $filename = $catname . " " . $res['title'];
    $fileprice = $payInfo['price'];

    $infoc = strlen($text) > 1 ? $text : "$caption <a href='$fileurl'>&#8194;نمایش فیش</a>";
    $msg = "
🛍 سفارشت با موفقیت ثبت شد.
بعد از تایید برات ارسال میکنم ... 🥳
";
    sendMessage($msg, $removeKeyboard);
    sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $mainKeys);

    $msg = "
❗️|💳 خرید جدید ( کارت به کارت )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $name
⚡️ نام کاربری: $username
💰مبلغ پرداختی: $fileprice تومان
✏️ نام سرویس: $filename

    ";
    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "accept" . $match[1]],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decline$uid"]
            ]
        ]
    ]);

    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
}
if ($data == "availableServers") {
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `acount` != 0 AND `inbound_id` != 0");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();

    $keys = array();
    $keys[] = [
        ['text' => "تعداد باقیمانده", 'callback_data' => "wizwizch"],
        ['text' => "پلن", 'callback_data' => "wizwizch"],
        ['text' => 'سرور', 'callback_data' => "wizwizch"]
    ];
    while ($file_detail = $serversList->fetch_assoc()) {
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

        if ($name->num_rows > 0) {
            $name = $name->fetch_assoc()['title'];

            $keys[] = [
                ['text' => $acount . " اکانت", 'callback_data' => "wizwizch"],
                ['text' => $title ?? " ", 'callback_data' => "wizwizch"],
                ['text' => $name ?? " ", 'callback_data' => "wizwizch"]
            ];
        }
    }
    $keys[] = [['text' => "↩️ برگشت", 'callback_data' => "mainMenu"]];
    $keys = json_encode(['inline_keyboard' => $keys]);
    editText($message_id, "🟢 | موجودی پلن اشتراکی:", $keys);
}
if ($data == "availableServers2") {
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `inbound_id` = 0");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();

    $keys = array();
    $keys[] = [
        ['text' => "تعداد باقیمانده", 'callback_data' => "wizwizch"],
        ['text' => 'سرور', 'callback_data' => "wizwizch"]
    ];
    while ($file_detail2 = $serversList->fetch_assoc()) {
        $days2 = $file_detail2['days'];
        $title2 = $file_detail2['title'];
        $server_id2 = $file_detail2['server_id'];
        $inbound_id2 = $file_detail2['inbound_id'];

        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
        $stmt->bind_param("i", $server_id2);
        $stmt->execute();
        $name = $stmt->get_result();
        $stmt->close();

        if ($name->num_rows > 0) {
            $sInfo = $name->fetch_assoc();
            $name = $sInfo['title'];
            $acount2 = $sInfo['ucount'];

            $keys[] = [
                ['text' => $acount2 . " اکانت", 'callback_data' => "wizwizch"],
                ['text' => $title2 ?? " ", 'callback_data' => "wizwizch"],
            ];
        }
    }
    $keys[] = [['text' => "↩️ برگشت", 'callback_data' => "mainMenu"]];
    $keys = json_encode(['inline_keyboard' => $keys]);
    editText($message_id, "🟢 | موجودی پلن اختصاصی:", $keys);
}

if (preg_match('/accept(.*)/', $data, $match) and $text != $cancelText) {
    setUser();

    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `pays` SET `state` = 'approved' WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $stmt->close();


    $uid = $payInfo['user_id'];
    $fid = $payInfo['plan_id'];
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
    $price = $payInfo['price'];
    $server_id = $file_detail['server_id'];
    $netType = $file_detail['type'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    $limitip = $file_detail['limitip'];
    $rahgozar = $file_detail['rahgozar'];

    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE id=?");
            $stmt->bind_param("i", $fid);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42, $protocol);

    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
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

    $rnd = rand(1111, 99999);
    $remark = "{$srv_remark}-{$uid}-{$rnd}";

    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    } else {
        $port = rand(1111, 65000);
    }

    if ($inbound_id == 0) {
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        if (!$response->success) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $fid);
        }
    } else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        if (!$response->success) {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $fid);
        }
    }
    if (is_null($response)) {
        alert('❌ | 🥺  ، اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
    if ($response == "inbound not Found") {
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
        exit;
    }
    if (!$response->success) {
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');
    $token = RandomString(30);
    $subLink = $botUrl . "settings/subLink.php?token=" . $token;

    include 'phpqrcode/qrlib.php';
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
    foreach ($vraylink as $vray_link) {
        $acc_text = "
💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
        if ($botState['subLinkState'] == "on") $acc_text .= "

\n🌐 subscription : <code>$subLink</code>";

        $file = RandomString() . ".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;

        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_Size);
        addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML", $uid);
        unlink($file);
    }
    sendMessage('✅ کانفیگ و براش ارسال کردم', $mainKeys);

    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");
    $stmt->bind_param("ssiiissisiii", $uid, $token, $fid, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();

    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => "✅", 'callback_data' => "wizwizch"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);

    $filename = $file_detail['title'];
    $fileprice = number_format($file_detail['price']);
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid`=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user_detail['refered_by'] != null) {
        $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'INVITE_BANNER_AMOUNT'");
        $stmt->execute();
        $inviteAmount = $stmt->get_result()->fetch_assoc()['value'] ?? 0;
        $stmt->close();
        $inviterId = $user_detail['refered_by'];

        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` + ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $inviteAmount, $inviterId);
        $stmt->execute();
        $stmt->close();

        sendMessage("تبریک یکی از زیر مجموعه های شما خرید انجام داد شما مبلغ " . number_format($inviteAmount) . " تومان جایزه دریافت کردید", null, null, $inviterId);
    }


    $uname = $user_detail['name'];
    $user_name = $user_detail['username'];

    if ($admin != $from_id) {
        $keys = json_encode(['inline_keyboard' => [
            [
                ['text' => "به به 🛍", 'callback_data' => "wizwizch"]
            ],
        ]]);
        sendMessage("
👨‍👦‍👦 خرید ( زیر مجموعه )

🧝‍♂️آیدی کاربر: $uid
🛡اسم کاربر: $uname
🔖 نام کاربری: $user_name
💰مبلغ پرداختی: $price تومان
🔮 نام سرویس: $remark
💮 سفارش: $filename
⁮⁮ ⁮⁮
        ", null, null, $admin);
    }
}
if (preg_match('/decline/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data . "_" . $message_id);
    sendMessage('دلیلت از عدم تایید چیه؟ ( بفرس براش ) 😔 ', $cancelKey);
}
if (preg_match('/decline(\d+)_(\d+)/', $userInfo['step'], $match) and $text != $cancelText) {
    setUser();
    $uid = $match[1];
    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $match[2],
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => "لغو شد ❌", 'callback_data' => "wizwizch"]]
        ]])
    ]);

    sendMessage('پیامت رو براش ارسال کردم ... 🤝', $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);

    sendMessage($text, null, null, $uid);
}
if ($data == "supportSection") {
    editText(
        $message_id,
        "به بخش پشتیبانی خوش اومدی🛂\nلطفا، یکی از دکمه های زیر را انتخاب نمایید.",
        json_encode(['inline_keyboard' => [
            [['text' => "✉️ ثبت تیکت", 'callback_data' => "usersNewTicket"]],
            [['text' => "تیکت های باز 📨", 'callback_data' => "usersOpenTickets"], ['text' => "📮 لیست تیکت ها", 'callback_data' => "userAllTickets"]],
            [['text' => "برگشت 🔙", 'callback_data' => "mainMenu"]]
        ]])
    );
}
if ($data == "usersNewTicket") {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    $keys = array();
    $temp = array();
    if ($ticketCategory->num_rows > 0) {
        while ($row = $ticketCategory->fetch_assoc()) {
            $ticketName = $row['value'];
            $temp[] = ['text' => $ticketName, 'callback_data' => "supportCat$ticketName"];

            if (count($temp) == 2) {
                array_push($keys, $temp);
                $temp = null;
            }
        }

        if ($temp != null) {
            if (count($temp) > 0) {
                array_push($keys, $temp);
                $temp = null;
            }
        }
        $temp[] = ['text' => "برگشت 🔙", 'callback_data' => "mainMenu"];
        array_push($keys, $temp);
        editText($message_id, "💠لطفا واحد مورد نظر خود را انتخاب نمایید!", json_encode(['inline_keyboard' => $keys]));
    } else {
        alert(" ببخشید الان نیستم");
    }
}
if ($data == 'dayPlanSettings' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `increase_day`");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        editText($message_id, 'لیست پلن های زمانی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "backplan"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "تعداد روز", 'callback_data' => "wizwizch"]];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";
        $acount = $cat['acount'];

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteDayPlan" . $id], ['text' => $price, 'callback_data' => "changeDayPlanPrice" . $id], ['text' => $title, 'callback_data' => "changeDayPlanDay" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "backplan"]];
    $msg = ' 📍 برای دیدن جزییات پلن زمانی روی آن بزنید👇';

    editText($message_id, $msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));

    exit;
}
if ($data == 'addNewDayPlan' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("تعداد روز و قیمت آن را بصورت زیر وارد کنید :
10-30000

مقدار اول مدت زمان (10) روز
مقدار دوم قیمت (30000) تومان
 ", $cancelKey);
    exit;
}
if ($userInfo['step'] == "addNewDayPlan" and $text != $cancelText) {
    $input = explode('-', $text);
    $volume = $input[0];
    $price = $input[1];
    $stmt = $connection->prepare("INSERT INTO `increase_day` VALUES (NULL, ?, ?)");
    $stmt->bind_param("ii", $volume, $price);
    $stmt->execute();
    $stmt->close();

    sendMessage("پلن زمانی جدید با موفقیت اضافه شد", $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $adminKeys);
    setUser();
}
if (preg_match('/^deleteDayPlan(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    alert("پلن موردنظر با موفقیت حذف شد");


    $stmt = $connection->prepare("SELECT * FROM `increase_day`");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        editText($message_id, 'لیست پلن های زمانی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "تعداد روز", 'callback_data' => "wizwizch"]];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";
        $acount = $cat['acount'];

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteDayPlan" . $id], ['text' => $price, 'callback_data' => "changeDayPlanPrice" . $id], ['text' => $title, 'callback_data' => "changeDayPlanDay" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
    $msg = ' 📍 برای دیدن جزییات پلن زمانی روی آن بزنید👇';

    editText($message_id, $msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));

    exit;
}
if (preg_match('/^changeDayPlanPrice(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("قیمت جدید را وارد کنید:", $cancelKey);
    exit;
}
if (preg_match('/^changeDayPlanPrice(\d+)/', $userInfo['step'], $match) and $text != $cancelText) {
    if (is_numeric($text)) {
        setUser();
        $stmt = $connection->prepare("UPDATE `increase_day` SET `price` = ? WHERE `id` = ?");
        $stmt->bind_param("ii", $text, $match[1]);
        $stmt->execute();
        $stmt->close();

        sendMessage("✅عملیات با موفقیت انجام شد", $removeKeyboard);

        $stmt = $connection->prepare("SELECT * FROM `increase_day`");
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res->num_rows == 0) {
            sendMessage('لیست پلن های زمانی خالی است ', json_encode([
                'inline_keyboard' => [
                    [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]],
                    [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
                ]
            ]));
            exit;
        }
        $keyboard = [];
        $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "تعداد روز", 'callback_data' => "wizwizch"]];
        while ($cat = $res->fetch_assoc()) {
            $id = $cat['id'];
            $title = $cat['volume'];
            $price = number_format($cat['price']) . " تومان";
            $acount = $cat['acount'];

            $keyboard[] = [['text' => "❌", 'callback_data' => "deleteDayPlan" . $id], ['text' => $price, 'callback_data' => "changeDayPlanPrice" . $id], ['text' => $title, 'callback_data' => "changeDayPlanDay" . $id]];
        }
        $keyboard[] = [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]];
        $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
        $msg = ' 📍 برای دیدن جزییات پلن زمانی روی آن بزنید👇';

        sendMessage($msg, json_encode([
            'inline_keyboard' => $keyboard
        ]));
    } else {
        sendMessage("یک مقدار عددی و صحیح وارد کنید");
    }
}
if (preg_match('/^changeDayPlanDay(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("روز جدید را وارد کنید:", $cancelKey);
    exit;
}
if (preg_match('/^changeDayPlanDay(\d+)/', $userInfo['step'], $match) and $text != $cancelText) {
    setUser();
    $stmt = $connection->prepare("UPDATE `increase_day` SET `volume` = ? WHERE `id` = ?");
    $stmt->bind_param("ii", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("✅عملیات با موفقیت انجام شد", $removeKeyboard);

    $stmt = $connection->prepare("SELECT * FROM `increase_day`");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        sendMessage('لیست پلن های زمانی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "تعداد روز", 'callback_data' => "wizwizch"]];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";
        $acount = $cat['acount'];

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteDayPlan" . $id], ['text' => $price, 'callback_data' => "changeDayPlanPrice" . $id], ['text' => $title, 'callback_data' => "changeDayPlanDay" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن زمانی جدید", 'callback_data' => "addNewDayPlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
    $msg = ' 📍 برای دیدن جزییات پلن زمانی روی آن بزنید👇';

    sendMessage($msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if ($data == 'volumePlanSettings' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `increase_plan`");
    $stmt->execute();
    $plans = $stmt->get_result();
    $stmt->close();

    if ($plans->num_rows == 0) {
        editText($message_id, 'لیست پلن های حجمی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "backplan"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "مقدار حجم", 'callback_data' => "wizwizch"]];
    while ($cat = $plans->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteVolumePlan" . $id], ['text' => $price, 'callback_data' => "changeVolumePlanPrice" . $id], ['text' => $title, 'callback_data' => "changeVolumePlanVolume" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "backplan"]];
    $msg = ' 📍 برای دیدن جزییات پلن حجمی روی آن بزنید👇';

    $res = editText($message_id, $msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));
    exit;
}
if ($data == 'addNewVolumePlan' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("حجم و قیمت آن را بصورت زیر وارد کنید :
10-30000

مقدار اول حجم (10) گیگابایت
مقدار دوم قیمت (30000) تومان
 ", $cancelKey);
    exit;
}
if ($userInfo['step'] == "addNewVolumePlan" and $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $input = explode('-', $text);
    $volume = $input[0];
    $price = $input[1];
    $stmt = $connection->prepare("INSERT INTO `increase_plan` VALUES (NULL, ? ,?)");
    $stmt->bind_param("ii", $volume, $price);
    $stmt->execute();
    $stmt->close();

    sendMessage("پلن حجمی جدید با موفقیت اضافه شد", $removeKeyboard);
    sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $adminKeys);
    setUser();
}
if (preg_match('/^deleteVolumePlan(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    alert("پلن موردنظر با موفقیت حذف شد");


    $stmt = $connection->prepare("SELECT * FROM `increase_plan`");
    $stmt->execute();
    $plans = $stmt->get_result();
    $stmt->close();

    if ($plans->num_rows == 0) {
        editText($message_id, 'لیست پلن های حجمی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "مقدار حجم", 'callback_data' => "wizwizch"]];
    while ($cat = $plans->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteVolumePlan" . $id], ['text' => $price, 'callback_data' => "changeVolumePlanPrice" . $id], ['text' => $title, 'callback_data' => "changeVolumePlanVolume" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
    $msg = ' 📍 برای دیدن جزییات پلن حجمی روی آن بزنید👇';

    $res = editText($message_id, $msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/^changeVolumePlanPrice(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("قیمت جدید را وارد کنید:", $cancelKey);
    exit;
}
if (preg_match('/^changeVolumePlanPrice(\d+)/', $userInfo['step'], $match) and $text != $cancelText and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $pid = $match[1];
    if (is_numeric($text)) {
        $stmt = $connection->prepare("UPDATE `increase_plan` SET `price` = ? WHERE `id` = ?");
        $stmt->bind_param("ii", $text, $pid);
        $stmt->execute();
        $stmt->close();
        sendMessage("عملیات با موفقیت انجام شد", $removeKeyboard);

        setUser();
        $stmt = $connection->prepare("SELECT * FROM `increase_plan`");
        $stmt->execute();
        $plans = $stmt->get_result();
        $stmt->close();

        if ($plans->num_rows == 0) {
            sendMessage('لیست پلن های حجمی خالی است ', json_encode([
                'inline_keyboard' => [
                    [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]],
                    [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
                ]
            ]));
            exit;
        }
        $keyboard = [];
        $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "مقدار حجم", 'callback_data' => "wizwizch"]];
        while ($cat = $plans->fetch_assoc()) {
            $id = $cat['id'];
            $title = $cat['volume'];
            $price = number_format($cat['price']) . " تومان";

            $keyboard[] = [['text' => "❌", 'callback_data' => "deleteVolumePlan" . $id], ['text' => $price, 'callback_data' => "changeVolumePlanPrice" . $id], ['text' => $title, 'callback_data' => "changeVolumePlanVolume" . $id]];
        }
        $keyboard[] = [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]];
        $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
        $msg = ' 📍 برای دیدن جزییات پلن حجمی روی آن بزنید👇';

        $res = sendMessage($msg, json_encode([
            'inline_keyboard' => $keyboard
        ]));
    } else {
        sendMessage("یک مقدار عددی و صحیح وارد کنید");
    }
}
if (preg_match('/^changeVolumePlanVolume(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser($data);
    delMessage();
    sendMessage("حجم جدید را وارد کنید:", $cancelKey);
    exit;
}
if (preg_match('/^changeVolumePlanVolume(\d+)/', $userInfo['step'], $match) and $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $pid = $match[1];
    $stmt = $connection->prepare("UPDATE `increase_plan` SET `volume` = ? WHERE `id` = ?");
    $stmt->bind_param("ii", $text, $pid);
    $stmt->execute();
    $stmt->close();
    sendMessage("✅عملیات با موفقیت انجام شد", $removeKeyboard);
    setUser();

    $stmt = $connection->prepare("SELECT * FROM `increase_plan`");
    $stmt->execute();
    $plans = $stmt->get_result();
    $stmt->close();

    if ($plans->num_rows == 0) {
        sendMessage('لیست پلن های حجمی خالی است ', json_encode([
            'inline_keyboard' => [
                [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]],
                [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]]
            ]
        ]));
        exit;
    }
    $keyboard = [];
    $keyboard[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "قیمت", 'callback_data' => "wizwizch"], ['text' => "مقدار حجم", 'callback_data' => "wizwizch"]];
    while ($cat = $plans->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']) . " تومان";

        $keyboard[] = [['text' => "❌", 'callback_data' => "deleteVolumePlan" . $id], ['text' => $price, 'callback_data' => "changeVolumePlanPrice" . $id], ['text' => $title, 'callback_data' => "changeVolumePlanVolume" . $id]];
    }
    $keyboard[] = [['text' => "افزودن پلن حجمی جدید", 'callback_data' => "addNewVolumePlan"]];
    $keyboard[] = [['text' => "برگشت 🔙", 'callback_data' => "managePanel"]];
    $msg = ' 📍 برای دیدن جزییات پلن حجمی روی آن بزنید👇';

    $res = sendMessage($msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/^supportCat(.*)/', $data, $match)) {
    delMessage();
    sendMessage("💠لطفا موضوع تیکت را ارسال کنید!", $cancelKey);
    setUser("newTicket_" . $match[1]);
}
if (preg_match('/^newTicket_(.*)/', $userInfo['step'], $match)  and $text != $cancelText) {
    file_put_contents("$from_id.txt", $text);
    setUser("sendTicket_" . $match[1]);
    sendMessage("💠لطفا متن تیکت خود را بصورت ساده و مختصر ارسال کنید!");
}
if (preg_match('/^sendTicket_(.*)/', $userInfo['step'], $match)  and $text != $cancelText) {
    $ticketCat = $match[1];

    $ticketTitle = file_get_contents("$from_id.txt");
    $time = time();
    $txt = "تیکت جدید:\n\nکاربر: <a href='tg://user?id=$from_id'>$first_name</a>\nنام کاربری: @$username\nآیدی عددی: $from_id\n\nموضوع تیکت: $ticketCat\n\nعنوان تیکت: " . $ticketTitle . "\nمتن تیکت: $text";

    $ticketTitle = str_replace(["/", "'", "#"], ['\/', "\'", "\#"], $ticketTitle);
    $text = str_replace(["/", "'", "#"], ['\/', "\'", "\#"], $text);
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

    $keys = json_encode(['inline_keyboard' => [
        [['text' => "پاسخ", 'callback_data' => "reply_{$chatRowId}"]]
    ]]);
    sendMessage($txt, $keys, "html", $admin);
    sendMessage("پیام شما با موفقیت ثبت شد", $removeKeyboard, "HTML");
    sendMessage("لطفا یکی از کلید های زیر را انتخاب کنید", $mainKeys);

    unlink("$from_id.txt");
    setUser("none");
}
if ($data == "usersOpenTickets" || $data == "userAllTickets") {
    if ($data == "usersOpenTickets") {
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 AND `user_id` = ? ORDER BY `state` ASC, `create_date` DESC");
        $stmt->bind_param("i", $from_id);
        $stmt->execute();
        $ticketList = $stmt->get_result();
        $stmt->close();
        $type = 2;
    } elseif ($data == "userAllTickets") {
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


    if ($allList > 0) {
        while ($row = $ticketList->fetch_assoc()) {
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
            $sentType = $ticketInfo['msg_type'] == "ADMIN" ? "ادمین" : "کاربر";

            if ($state != 2) {
                $keys = [
                    [['text' => "بستن تیکت 🗳", 'callback_data' => "closeTicket_$rowId"], ['text' => "پاسخ به تیکت 📝", 'callback_data' => "replySupport_{$rowId}"]],
                    [['text' => "آخرین پیام ها 📩", 'callback_data' => "latestMsg_$rowId"]]
                ];
            } else {
                $keys = [
                    [['text' => "آخرین پیام ها 📩", 'callback_data' => "latestMsg_$rowId"]]
                ];
            }

            sendMessage(" 🔘 موضوع: $title
			💭 دسته بندی:  {$category}
			\n
			$sentType : $lastmsg", json_encode(['inline_keyboard' => $keys]), "HTML");

            if ($current >= $cont) {
                break;
            }
        }

        if ($allList > $cont) {
            sendmessage("موارد بیشتر", json_encode(['inline_keyboard' => [
                [['text' => "دریافت", 'callback_data' => "moreTicket_{$type}_{$cont}"]]
            ]]), "HTML");
        }
    } else {
        alert("تیکتی یافت نشد");
        exit();
    }
}
if (preg_match('/^closeTicket_(\d+)/', $data, $match) and  $from_id != $admin) {
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

    bot('editMessageReplyMarkup', ['chat_id' => $from_id, 'message_id' => $message_id, 'reply_markup' => null]);

    $ticketClosed = " $title : $category \n\n" . "این تیکت بسته شد\n به این تیکت رأی بدهید";;

    $keys = json_encode(['inline_keyboard' => [
        [['text' => "بسیار بد 😠", 'callback_data' => "rate_{$chatRowId}_1"]],
        [['text' => "بد 🙁", 'callback_data' => "rate_{$chatRowId}_2"]],
        [['text' => "خوب 😐", 'callback_data' => "rate_{$chatRowId}_3"]],
        [['text' => "بسیار خوب 😃", 'callback_data' => "rate_{$chatRowId}_4"]],
        [['text' => "عالی 🤩", 'callback_data' => "rate_{$chatRowId}_5"]]
    ]]);
    sendMessage($ticketClosed, $keys, 'html');

    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => "$from_id", 'callback_data' => "wizwizch"],
            ['text' => "آیدی کاربر", 'callback_data' => 'wizwizch']
        ],
        [
            ['text' => $first_name ?? " ", 'callback_data' => "wizwizch"],
            ['text' => "اسم کاربر", 'callback_data' => 'wizwizch']
        ],
        [
            ['text' => "$title", 'callback_data' => 'wizwizch'],
            ['text' => "عنوان", 'callback_data' => 'wizwizch']
        ],
        [
            ['text' => "$category", 'callback_data' => 'wizwizch'],
            ['text' => "دسته بندی", 'callback_data' => 'wizwizch']
        ],
    ]]);
    sendMessage("☑️| تیکت توسط کاربر بسته شد", $keys, "HTML", $admin);
}
if (preg_match('/^replySupport_(.*)/', $data, $match)) {
    delMessage();
    sendMessage("💠لطفا متن پیام خود را بصورت ساده و مختصر ارسال کنید!", $cancelKey);
    setUser("sendMsg_" . $match[1]);
}
if (preg_match('/^sendMsg_(.*)/', $userInfo['step'], $match)  and $text != $cancelText) {
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

    $text = str_replace(["/", "'", "#"], ['\/', "\'", "\#"], $text);
    $stmt = $connection->prepare("INSERT INTO `chats_info` (`chat_id`,`sent_date`,`msg_type`,`text`) VALUES
                (?,?,'USER',?)");
    $stmt->bind_param("iis", $ticketRowId, $time, $text);
    $stmt->execute();
    $stmt->close();

    sendMessage($txt, json_encode(['inline_keyboard' => [
        [['text' => "پاسخ", 'callback_data' => "reply_{$ticketRowId}"]]
    ]]), "HTML", $admin);
    sendMessage("پیام شما با موفقیت ثبت شد", $mainKeys, "HTML");
    setUser("none");
}
if (preg_match("/^rate_+([0-9])+_+([0-9])/", $data, $match)) {
    $rowChatId = $match[1];
    $rate = $match[2];

    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $rowChatId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $title = $ticketInfo['title'];
    $category = $ticketInfo['category'];


    $stmt = $connection->prepare("UPDATE `chats` SET `rate` = $rate WHERE `id` = ?");
    $stmt->bind_param("i", $rowChatId);
    $stmt->execute();
    $stmt->close();
    editText($message_id, "✅");

    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => "رای تیکت", 'callback_data' => "wizwizch"]
        ],
    ]]);

    sendMessage("
📨|رأی به تیکت 

👤 آیدی عددی: $from_id
❕نام کاربر: $first_name
❗️نام کاربری: $username
〽️ عنوان: $title
⚜️ دسته بندی: $category
❤️ رای: $rate
 ⁮⁮
    ", $keys, "HTML", $admin);
}
if ($data == "ticketsList" and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $ticketSection = json_encode(['inline_keyboard' => [
        [
            ['text' => "تیکت های باز", 'callback_data' => "openTickets"],
            ['text' => "تیکت های جدید", 'callback_data' => "newTickets"]
        ],
        [
            ['text' => "همه ی تیکت ها", 'callback_data' => "allTickets"],
            ['text' => "دسته بندی تیکت ها", 'callback_data' => "ticketsCategory"]
        ],
        [['text' => "↪ برگشت", 'callback_data' => "managePanel"]]
    ]]);
    editText($message_id, "به بخش تیکت ها خوش اومدید، 
    
🚪 /start
    ", $ticketSection);
}
if ($data == 'ticketsCategory' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'TICKETS_CATEGORY'");
    $stmt->execute();
    $ticketCategory = $stmt->get_result();
    $stmt->close();
    $keys = array();
    $keys[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "دسته بندی", 'callback_data' => "wizwizch"]];

    if ($ticketCategory->num_rows > 0) {
        while ($row = $ticketCategory->fetch_assoc()) {
            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text' => "❌", 'callback_data' => "delTicketCat_$rowId"], ['text' => $ticketName, 'callback_data' => "wizwizch"]];
        }
    } else {
        $keys[] = [['text' => "دسته بندی یافت نشد", 'callback_data' => "wizwizch"]];
    }
    $keys[] = [['text' => "افزودن دسته بندی", 'callback_data' => "addTicketCategory"]];
    $keys[] = [['text' => "↩️ برگشت", 'callback_data' => "ticketsList"]];

    $keys =  json_encode(['inline_keyboard' => $keys]);
    editText($message_id, "دسته بندی تیکت ها", $keys);
}
if ($data == "addTicketCategory" and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser('addTicketCategory');
    editText($message_id, "لطفا اسم دسته بندی را وارد کنید");
}
if ($userInfo['step'] == "addTicketCategory" and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
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
    $keys[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "دسته بندی", 'callback_data' => "wizwizch"]];

    if ($ticketCategory->num_rows > 0) {
        while ($row = $ticketCategory->fetch_assoc()) {

            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text' => "❌", 'callback_data' => "delTicketCat_$rowId"], ['text' => $ticketName, 'callback_data' => "wizwizch"]];
        }
    } else {
        $keys[] = [['text' => "دسته بندی یافت نشد", 'callback_data' => "wizwizch"]];
    }
    $keys[] = [['text' => "افزودن دسته بندی", 'callback_data' => "addTicketCategory"]];
    $keys[] = [['text' => "↩️ برگشت", 'callback_data' => "ticketsList"]];

    $keys =  json_encode(['inline_keyboard' => $keys]);
    sendMessage("دسته بندی تیکت ها", $keys);
}
if (preg_match("/^delTicketCat_(\d+)/", $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
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
    $keys[] = [['text' => "حذف", 'callback_data' => "wizwizch"], ['text' => "دسته بندی", 'callback_data' => "wizwizch"]];

    if ($ticketCategory->num_rows > 0) {
        while ($row = $ticketCategory->fetch_assoc()) {

            $rowId = $row['id'];
            $ticketName = $row['value'];
            $keys[] = [['text' => "❌", 'callback_data' => "delTicketCat_$rowId"], ['text' => $ticketName, 'callback_data' => "wizwizch"]];
        }
    } else {
        $keys[] = [['text' => "دسته بندی یافت نشد", 'callback_data' => "wizwizch"]];
    }
    $keys[] = [['text' => "افزودن دسته بندی", 'callback_data' => "addTicketCategory"]];
    $keys[] = [['text' => "↩️ برگشت", 'callback_data' => "ticketsList"]];

    $keys =  json_encode(['inline_keyboard' => $keys]);
    editText($message_id, "دسته بندی تیکت ها", $keys);
}
if (($data == "openTickets" or $data == "newTickets" or $data == "allTickets")  and  $from_id == $admin) {
    if ($data == "openTickets") {
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 ORDER BY `state` ASC, `create_date` DESC");
        $type = 2;
    } elseif ($data == "newTickets") {
        $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` = 0 ORDER BY `create_date` DESC");
        $type = 0;
    } elseif ($data == "allTickets") {
        $stmt = $connection->prepare("SELECT * FROM `chats` ORDER BY `state` ASC, `create_date` DESC");
        $type = "all";
    }
    $stmt->execute();
    $ticketList = $stmt->get_result();
    $stmt->close();
    $allList = $ticketList->num_rows;
    $cont = 5;
    $current = 0;
    $keys = array();
    if ($allList > 0) {
        while ($row = $ticketList->fetch_assoc()) {
            $current++;

            $rowId = $row['id'];
            $admin = $row['user_id'];
            $title = $row['title'];
            $category = $row['category'];
            $state = $row['state'];
            $username = bot('getChat', ['chat_id' => $admin])->result->first_name ?? " ";

            $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC");
            $stmt->bind_param("i", $rowId);
            $stmt->execute();
            $ticketInfo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $lastmsg = $ticketInfo['text'];
            $sentType = $ticketInfo['msg_type'] == "USER" ? "کاربر" : "ادمین";

            if ($state != 2) {
                $keys = [
                    [['text' => "بستن تیکت", 'callback_data' => "closeTicket_$rowId"], ['text' => "پاسخ", 'callback_data' => "reply_{$rowId}"]],
                    [['text' => "آخرین پیام ها", 'callback_data' => "latestMsg_$rowId"]]
                ];
            } else {
                $keys = [[['text' => "آخرین پیام ها", 'callback_data' => "latestMsg_$rowId"]]];
                $rate = "\nرأی: " . $row['rate'];
            }

            sendMessage(
                "آیدی کاربر: $admin\nنام کاربر: $username\nدسته بندی: $category $rate\n\nموضوع: $title\nآخرین پیام:\n[$sentType] $lastmsg",
                json_encode(['inline_keyboard' => $keys]),
                "html"
            );

            if ($current >= $cont) {
                break;
            }
        }

        if ($allList > $cont) {
            $keys = json_encode(['inline_keyboard' => [
                [['text' => "دریافت", 'callback_data' => "moreTicket_{$type}_{$cont}"]]
            ]]);
            sendMessage("موارد بیشتر", $keys, "html");
        }
    } else {
        alert("تیکتی یافت نشد");
    }
}
if (preg_match('/^moreTicket_(.+)_(.+)/', $data, $match) and  ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    editText($message_id, "لطفا منتظر باشید");
    $type = $match[1];
    $offset = $match[2];
    if ($type == "2") $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` != 2 ORDER BY `state` ASC, `create_date` DESC");
    elseif ($type == "0") $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `state` = 0 ORDER BY `create_date` DESC");
    elseif ($type == "all") $stmt = $connection->prepare("SELECT * FROM `chats` ORDER BY `state` ASC, `create_date` DESC");

    $stmt->execute();
    $ticketList = $stmt->get_result();
    $stmt->close();

    $allList = $ticketList->num_rows;
    $cont = 5 + $offset;
    $current = 0;
    $keys = array();
    $rowCont = 0;
    if ($allList > 0) {
        while ($row = $ticketList->fetch_assoc()) {
            $rowCont++;
            if ($rowCont > $offset) {
                $current++;

                $rowId = $row['id'];
                $admin = $row['user_id'];
                $title = $row['title'];
                $category = $row['category'];
                $state = $row['state'];
                $username = bot('getChat', ['chat_id' => $admin])->result->first_name ?? " ";

                $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC");
                $stmt->bind_param("i", $rowId);
                $stmt->execute();
                $ticketInfo = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $lastmsg = $ticketInfo['text'];
                $sentType = $ticketInfo['msg_type'] == "USER" ? "کاربر" : "ادمین";

                if ($state != 2) {
                    $keys = [
                        [['text' => "بستن تیکت", 'callback_data' => "closeTicket_$rowId"], ['text' => "پاسخ", 'callback_data' => "reply_{$rowId}"]],
                        [['text' => "آخرین پیام ها", 'callback_data' => "latestMsg_$rowId"]]
                    ];
                } else {
                    $keys = [[['text' => "آخرین پیام ها", 'callback_data' => "latestMsg_$rowId"]]];
                    $rate = "\nرأی: " . $row['rate'];
                }

                sendMessage(
                    "آیدی کاربر: $admin\nنام کاربر: $username\nدسته بندی: $category $rate\n\nموضوع: $title\nآخرین پیام:\n[$sentType] $lastmsg",
                    json_encode(['inline_keyboard' => $keys]),
                    "html"
                );


                if ($current >= $cont) {
                    break;
                }
            }
        }

        if ($allList > $cont) {
            $keys = json_encode(['inline_keyboard' => [
                [['text' => "دریافت", 'callback_data' => "moreTicket_{$type}_{$cont}"]]
            ]]);
            sendMessage("موارد بیشتر", $keys);
        }
    } else {
        alert("تیکتی یافت نشد");
    }
}
if (preg_match('/^closeTicket_(\d+)/', $data, $match) and  ($from_id == $admin || $userInfo['isAdmin'] == true)) {
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

    $keys = json_encode(['inline_keyboard' => [
        [['text' => "بسیار بد 😠", 'callback_data' => "rate_{$chatRowId}_1"]],
        [['text' => "بد 🙁", 'callback_data' => "rate_{$chatRowId}_2"]],
        [['text' => "خوب 😐", 'callback_data' => "rate_{$chatRowId}_3"]],
        [['text' => "بسیار خوب 😃", 'callback_data' => "rate_{$chatRowId}_4"]],
        [['text' => "عالی 🤩", 'callback_data' => "rate_{$chatRowId}_5"]]
    ]]);
    sendMessage($ticketClosed, $keys, 'html', $userId);
    bot('editMessageReplyMarkup', ['chat_id' => $from_id, 'message_id' => $message_id, 'reply_markup' => json_encode(['inline_keyboard' => [
        [['text' => "تیکت بسته شد", 'callback_data' => "wizwizch"]]
    ]])]);
}
if (preg_match('/^latestMsg_(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `chats_info` WHERE `chat_id` = ? ORDER BY `sent_date` DESC LIMIT 10");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $chatList = $stmt->get_result();
    $stmt->close();
    $output = "";
    while ($row = $chatList->fetch_assoc()) {
        $type = $row['msg_type'] == "USER" ? "کاربر" : "ادمین";
        $text = $row['text'];

        $output .= "<i>[$type]</i>\n$text\n\n";
    }
    sendMessage($output, null, "html");
}
if ($data == "banUser" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("😡 | کی باز شلوغی کرده آیدی عددی شو بفرس تا برم ...... آرهههه:", $cancelKey);
    setUser($data);
}
if ($data == "unbanUser" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("آیدی عددیشو بفرست تا آزادش کنم", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "banUser" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    if (is_numeric($text)) {

        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $usersList = $stmt->get_result();
        $stmt->close();

        if ($usersList->num_rows > 0) {
            $userState = $usersList->fetch_assoc();
            if ($userState['step'] != "banned") {
                $stmt = $connection->prepare("UPDATE `users` SET `step` = 'banned' WHERE `userid` = ?");
                $stmt->bind_param("i", $text);
                $stmt->execute();
                $stmt->close();

                sendMessage("❌ | خب خب برید کنار که مسدودش کردم 😎😂", $removeKeyboard);
            } else {
                sendMessage("☑️ | این کاربر که از قبل مسدود بود چیکارش داری بدبخت و 😂🤣", $removeKeyboard);
            }
        } else sendMessage("کاربری با این آیدی یافت نشد");
        setUser();
        sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $adminKeys);
    } else {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if ($data == "mainMenuButtons" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    editText($message_id, "مدیریت دکمه های صفحه اصلی", getMainMenuButtonsKeys());
}
if (preg_match('/^delMainButton(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `setting` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    alert("با موفقیت حذف شد");
    editText($message_id, "مدیریت دکمه های صفحه اصلی", getMainMenuButtonsKeys());
}
if ($data == "addNewMainButton" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("لطفا اسم دکمه را وارد کنید", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "addNewMainButton" && $text != $cancelText) {
    if (!isset($update->message->text)) {
        sendMessage("لطفا فقط متن بفرستید");
        exit();
    }
    sendMessage("لطفا پاسخ دکمه را وارد کنید");
    setUser("setMainButtonAnswer" . $text);
}
if (preg_match('/^setMainButtonAnswer(.*)/', $userInfo['step'], $match)) {
    if (!isset($update->message->text)) {
        sendMessage("لطفا فقط متن بفرستید");
        exit();
    }
    setUser();

    $stmt = $connection->prepare("INSERT INTO `setting` (`type`, `value`) VALUES (?, ?)");
    $btn = "MAIN_BUTTONS" . $match[1];
    $stmt->bind_param("ss", $btn, $text);
    $stmt->execute();
    $stmt->close();

    sendMessage("مدیریت دکمه های صفحه اصلی", getMainMenuButtonsKeys());
}
if ($userInfo['step'] == "unbanUser" && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `userid` = ?");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $usersList = $stmt->get_result();
        $stmt->close();

        if ($usersList->num_rows > 0) {
            $userState = $usersList->fetch_assoc();
            if ($userState['step'] == "banned") {
                $stmt = $connection->prepare("UPDATE `users` SET `step` = 'none' WHERE `userid` = ?");
                $stmt->bind_param("i", $text);
                $stmt->execute();
                $stmt->close();

                sendMessage("✅ | آزاد شدم خوشحالم ننه ، ایشالا آزادی همه 😂", $removeKeyboard);
            } else {
                sendMessage("☑️ | این کاربری که فرستادی از قبل آزاد بود 🙁", $removeKeyboard);
            }
        } else sendMessage("کاربری با این آیدی یافت نشد");
        setUser();
        sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $adminKeys);
    } else {
        sendMessage("😡 | مگه نمیگم فقط عدد بفرس نمیفهمی؟ یا خودتو زدی به نفهمی؟");
    }
}
if (preg_match("/^reply_(.*)/", $data, $match) and  ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    setUser("answer_" . $match[1]);
    sendMessage("لطفا پیام خود را ارسال کنید", $cancelKey);
}
if (preg_match('/^answer_(.*)/', $userInfo['step'], $match) and  $from_id == $admin  and $text != $cancelText) {
    $chatRowId = $match[1];
    $stmt = $connection->prepare("SELECT * FROM `chats` WHERE `id` = ?");
    $stmt->bind_param("i", $chatRowId);
    $stmt->execute();
    $ticketInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $userId = $ticketInfo['user_id'];
    $ticketTitle = $ticketInfo['title'];
    $ticketCat = $ticketInfo['category'];

    sendMessage("\[$ticketTitle] _{$ticketCat}_\n\n" . $text, json_encode(['inline_keyboard' => [
        [
            ['text' => 'پاسخ به تیکت 📝', 'callback_data' => "replySupport_$chatRowId"],
            ['text' => "بستن تیکت 🗳", 'callback_data' => "closeTicket_$chatRowId"]
        ]
    ]]), "MarkDown", $userId);
    $time = time();

    $ticketTitle = str_replace(["/", "'", "#"], ['\/', "\'", "\#"], $ticketTitle);
    $text = str_replace(["/", "'", "#"], ['\/', "\'", "\#"], $text);
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
    sendMessage("پیام شما با موفقیت ارسال شد ✅", $removeKeyboard);
}
if (preg_match('/freeTrial(\d+)/', $data, $match)) {
    $id = $match[1];

    if ($userInfo['freetrial'] == 'used' and !($from_id == $admin)) {
        alert('⚠️شما قبلا هدیه رایگان خود را دریافت کردید');
        exit;
    }
    delMessage();
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
    $rahgozar = $file_detail['rahgozar'];

    if ($acount == 0 and $inbound_id != 0) {
        alert('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if ($inbound_id == 0) {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($server_info['ucount'] != 0) {
            $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $server_id);
            $stmt->execute();
            $stmt->close();
        } else {
            alert('ظرفیت این سرور پر شده است');
            exit;
        }
    } else {
        if ($acount != 0) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `acount` = `acount` - 1 WHERE `id`=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $uniqid = generateRandomString(42, $protocol);

    $savedinfo = file_get_contents('settings/temp.txt');
    $savedinfo = explode('-', $savedinfo);
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

    $rnd = rand(1111, 99999);
    $remark = "{$srv_remark}-{$from_id}-{$rnd}";

    if ($portType == "auto") {
        file_put_contents('settings/temp.txt', $port . '-' . $last_num);
    } else {
        $port = rand(1111, 65000);
    }
    if ($inbound_id == 0) {
        $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $id);
        if (!$response->success) {
            $response = addUser($server_id, $uniqid, $protocol, $port, $expire_microdate, $remark, $volume, $netType, 'none', $rahgozar, $id);
        }
    } else {
        $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $id);
        if (!$response->success) {
            $response = addInboundAccount($server_id, $uniqid, $inbound_id, $expire_microdate, $remark, $volume, $limitip, null, $id);
        }
    }
    if (is_null($response)) {
        alert('❌ | 🥺  اتصال به سرور برقرار نیست لطفا مدیر رو در جریان بزار ...');
        exit;
    }
    if ($response == "inbound not Found") {
        alert("❌ | 🥺 سطر (inbound) با آیدی $inbound_id تو این سرور وجود نداره ، مدیر رو در جریان بزار ...");
        exit;
    }
    if (!$response->success) {
        alert('❌ | 😮 وای خطا داد لطفا سریع به مدیر بگو ...');
        exit;
    }
    alert('🚀 | 😍 در حال ارسال کانفیگ به مشتری ...');
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, $inbound_id, $rahgozar);
    include 'phpqrcode/qrlib.php';
    $token = RandomString(30);
    $subLink = $botUrl . "settings/subLink.php?token=" . $token;
    foreach ($vraylink as $vray_link) {
        $acc_text = "
💎 سفارش شما آماده شد
📡 پروتکل: $protocol
🔮 نام سرویس: $remark
🔋حجم سرویس: $volume گیگ
⏰ مدت سرویس: $days روز
⁮⁮ ⁮⁮
💝 config : <code>$vray_link</code>";
        if ($botState['subLinkState'] == "on") $acc_text .= "

\n🌐 subscription : <code>$subLink</code>";

        $file = RandomString() . ".png";
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;
        QRcode::png($vray_link, $file, $ecc, $pixel_Size, $frame_size);
        addBorderImage($file);
        sendPhoto($botUrl . $file, $acc_text, json_encode(['inline_keyboard' => [[['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]]]]), "HTML");
        unlink($file);
    }

    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("INSERT INTO `orders_list` 
	    (`userid`, `token`, `transid`, `fileid`, `server_id`, `inbound_id`, `remark`, `protocol`, `expire_date`, `link`, `amount`, `status`, `date`, `notif`, `rahgozar`)
	    VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?,1, ?, 0, ?);");

    $stmt->bind_param("isiiissisiii", $from_id, $token, $id, $server_id, $inbound_id, $remark, $protocol, $expire_date, $vray_link, $price, $date, $rahgozar);
    $stmt->execute();
    $order = $stmt->get_result();
    $stmt->close();

    setUser('used', 'freetrial');
}
if (preg_match('/^showMainButtonAns(\d+)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    editText($message_id, $info['value'], json_encode(['inline_keyboard' => [
        [['text' => "برگشت 🔙", 'callback_data' => "mainMenu"]]
    ]]));
}
if ($data == "showUUIDLeft" && ($botState['searchState'] == "on" || $from_id == $admin)) {
    delMessage();
    sendMessage("❗️| لینک کانفیگ یا uuid رو برام بفرس اطلاعات کامل رو تحویلت بدم 🤭", $cancelKey);
    setUser('showAccount');
}
if ($userInfo['step'] == "showAccount" and $text != $cancelText) {
    if (preg_match('/^vmess:\/\/(.*)/', $text, $match)) {
        $jsonDecode = json_decode(base64_decode($match[1]), true);
        $text = $jsonDecode['id'];
    } elseif (preg_match('/^vless:\/\/(.*?)\@/', $text, $match)) {
        $text = $match[1];
    } elseif (preg_match('/^trojan:\/\/(.*?)\@/', $text, $match)) {
        $text = $match[1];
    } elseif (!preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $text)) {
        sendMessage("متن وارد شده معتبر نمی باشد");
        exit();
    }

    sendMessage(" لطفا یکم منتظر بمون ...", $removeKeyboard);
    $stmt = $connection->prepare("SELECT * FROM `server_config`");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();
    $found = false;
    while ($row = $serversList->fetch_assoc()) {
        $serverId = $row['id'];

        $response = getJson($serverId);
        if ($response->success) {

            $list = json_encode($response->obj);

            if (strpos($list, $text)) {
                setUser();
                $found = true;
                $list = $response->obj;
                if (!isset($list[0]->clientStats)) {
                    foreach ($list as $keys => $packageInfo) {
                        if (strpos($packageInfo->settings, $text) != false) {
                            $remark = $packageInfo->remark;
                            $upload = sumerize($packageInfo->up);
                            $download = sumerize($packageInfo->down);
                            $state = $packageInfo->enable == true ? "فعال 🟢" : "غیر فعال 🔴";
                            $totalUsed = sumerize($packageInfo->up + $packageInfo->down);
                            $total = $packageInfo->total != 0 ? sumerize($packageInfo->total) : "نامحدود";
                            $expiryTime = $packageInfo->expiryTime != 0 ? jdate("Y-m-d H:i:s", substr($packageInfo->expiryTime, 0, -3)) : "نامحدود";
                            $leftMb = $packageInfo->total != 0 ? sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down) : "نامحدود";
                            $expiryDay = $packageInfo->expiryTime != 0 ?
                                floor(
                                    (substr($packageInfo->expiryTime, 0, -3) - time()) / (60 * 60 * 24)
                                )
                                :
                                "نامحدود";
                            if (is_numeric($expiryDay)) {
                                if ($expiryDay < 0) $expiryDay = 0;
                            }
                            break;
                        }
                    }
                } else {
                    $keys = -1;
                    $settings = array_column($list, 'settings');
                    foreach ($settings as $key => $value) {
                        if (strpos($value, $text) != false) {
                            $keys = $key;
                            break;
                        }
                    }
                    if ($keys == -1) {
                        $found = false;
                        break;
                    }
                    $clientsSettings = json_decode($list[$keys]->settings, true)['clients'];
                    if (!is_array($clientsSettings)) {
                        sendMessage("با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                        exit();
                    }
                    $settingsId = array_column($clientsSettings, 'id');
                    $settingKey = array_search($text, $settingsId);

                    if (!isset($clientsSettings[$settingKey]['email'])) {
                        $packageInfo = $list[$keys];
                        $remark = $packageInfo->remark;
                        $upload = sumerize($packageInfo->up);
                        $download = sumerize($packageInfo->down);
                        $state = $packageInfo->enable == true ? "فعال 🟢" : "غیر فعال 🔴";
                        $totalUsed = sumerize($packageInfo->up + $packageInfo->down);
                        $total = $packageInfo->total != 0 ? sumerize($packageInfo->total) : "نامحدود";
                        $expiryTime = $packageInfo->expiryTime != 0 ? jdate("Y-m-d H:i:s", substr($packageInfo->expiryTime, 0, -3)) : "نامحدود";
                        $leftMb = $packageInfo->total != 0 ? sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down) : "نامحدود";
                        if (is_numeric($leftMb)) {
                            if ($leftMb < 0) {
                                $leftMb = 0;
                            } else {
                                $leftMb = sumerize($packageInfo->total - $packageInfo->up - $packageInfo->down);
                            }
                        }


                        $expiryDay = $packageInfo->expiryTime != 0 ?
                            floor(
                                (substr($packageInfo->expiryTime, 0, -3) - time()) / (60 * 60 * 24)
                            ) :
                            "نامحدود";
                        if (is_numeric($expiryDay)) {
                            if ($expiryDay < 0) $expiryDay = 0;
                        }
                    } else {
                        $email = $clientsSettings[$settingKey]['email'];
                        $clientState = $list[$keys]->clientStats;
                        $emails = array_column($clientState, 'email');
                        $emailKey = array_search($email, $emails);


                        if ($clientState[$emailKey]->total != 0 || $clientState[$emailKey]->up != 0  ||  $clientState[$emailKey]->down != 0 || $clientState[$emailKey]->expiryTime != 0) {
                            $upload = sumerize($clientState[$emailKey]->up);
                            $download = sumerize($clientState[$emailKey]->down);
                            $total = $clientState[$emailKey]->total == 0 && $list[$keys]->total != 0 ? $list[$keys]->total : $clientState[$emailKey]->total;
                            $leftMb = $total != 0 ? ($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down) : "نامحدود";
                            if (is_numeric($leftMb)) {
                                if ($leftMb < 0) {
                                    $leftMb = 0;
                                } else {
                                    $leftMb = sumerize($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down);
                                }
                            }
                            $totalUsed = sumerize($clientState[$emailKey]->up + $clientState[$emailKey]->down);
                            $total = $total != 0 ? sumerize($total) : "نامحدود";
                            $expTime = $clientState[$emailKey]->expiryTime == 0 && $list[$keys]->expiryTime ? $list[$keys]->expiryTime : $clientState[$emailKey]->expiryTime;
                            $expiryTime = $expTime != 0 ? jdate("Y-m-d H:i:s", substr($expTime, 0, -3)) : "نامحدود";
                            $expiryDay = $expTime != 0 ?
                                floor(
                                    ((substr($expTime, 0, -3) - time()) / (60 * 60 * 24))
                                ) :
                                "نامحدود";
                            if (is_numeric($expiryDay)) {
                                if ($expiryDay < 0) $expiryDay = 0;
                            }
                            $state = $clientState[$emailKey]->enable == true ? "فعال 🟢" : "غیر فعال 🔴";
                            $remark = $email;
                        } elseif ($list[$keys]->total != 0 || $list[$keys]->up != 0  ||  $list[$keys]->down != 0 || $list[$keys]->expiryTime != 0) {
                            $upload = sumerize($list[$keys]->up);
                            $download = sumerize($list[$keys]->down);
                            $leftMb = $list[$keys]->total != 0 ? ($list[$keys]->total - $list[$keys]->up - $list[$keys]->down) : "نامحدود";
                            if (is_numeric($leftMb)) {
                                if ($leftMb < 0) {
                                    $leftMb = 0;
                                } else {
                                    $leftMb = sumerize($list[$keys]->total - $list[$keys]->up - $list[$keys]->down);
                                }
                            }
                            $totalUsed = sumerize($list[$keys]->up + $list[$keys]->down);
                            $total = $list[$keys]->total != 0 ? sumerize($list[$keys]->total) : "نامحدود";
                            $expiryTime = $list[$keys]->expiryTime != 0 ? jdate("Y-m-d H:i:s", substr($list[$keys]->expiryTime, 0, -3)) : "نامحدود";
                            $expiryDay = $list[$keys]->expiryTime != 0 ?
                                floor(
                                    ((substr($list[$keys]->expiryTime, 0, -3) - time()) / (60 * 60 * 24))
                                ) :
                                "نامحدود";
                            if (is_numeric($expiryDay)) {
                                if ($expiryDay < 0) $expiryDay = 0;
                            }
                            $state = $list[$keys]->enable == true ? "فعال 🟢" : "غیر فعال 🔴";
                            $remark = $list[$keys]->remark;
                        }
                    }
                }

                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => $state ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "🔘 وضعیت اکانت 🔘", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $remark ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "« نام اکانت »", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $upload ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "√ آپلود √", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $download ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "√ دانلود √", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $total ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "† حجم کلی †", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $leftMb ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "~ حجم باقیمانده ~", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $expiryTime ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "تاریخ اتمام", 'callback_data' => "wizwizch"],
                    ],
                    [
                        ['text' => $expiryDay ?? " ", 'callback_data' => "wizwizch"],
                        ['text' => "تعداد روز باقیمانده", 'callback_data' => "wizwizch"],
                    ],
                    [['text' => "صفحه اصلی", 'callback_data' => "mainMenu"]]
                ]]);
                sendMessage("🔰مشخصات حسابت:", $keys, "MarkDown");
                break;
            }
        }
    }
    if (!$found) {
        sendMessage("، اطلاعاتت اشتباهه 😔", $cancelKey);
    }
}
if (($data == 'addNewPlan' || $data == "addNewRahgozarPlan") and (($from_id == $admin || $userInfo['isAdmin'] == true))) {
    setUser($data);
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();
    if ($data == "addNewPlan") {
        $sql = "INSERT INTO `server_plans` (`fileid`, `catid`, `server_id`, `inbound_id`, `acount`, `limitip`, `title`, `protocol`, `days`, `volume`, `type`, `price`, `descr`, `pic`, `active`, `step`, `date`)
                                            VALUES ('', 0,0,0,0, 1, '', '', 0, 0, '', 0, '', '',0,1, ?);";
    } elseif ($data == "addNewRahgozarPlan") {
        $sql = "INSERT INTO `server_plans` (`fileid`, `catid`, `server_id`, `inbound_id`, `acount`, `limitip`, `title`, `protocol`, `days`, `volume`, `type`, `price`, `descr`, `pic`, `active`, `step`, `date`, `rahgozar`)
                    VALUES ('', 0,0,0,0, 1, '', '', 0, 0, '', 0, '', '',0,1, ?, 1);";
    }
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $time);
    $stmt->execute();
    $stmt->close();
    delMessage();
    $msg = '❗️یه عنوان برا پلن انتخاب کن:';
    sendMessage($msg, $cancelKey);
    exit;
}
if (preg_match('/(addNewRahgozarPlan|addNewPlan)/', $userInfo['step']) and $text != $cancelText) {
    $catkey = [];
    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `parent` =0 and `active`=1");
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();

    while ($cat = $cats->fetch_assoc()) {
        $id = $cat['id'];
        $name = $cat['title'];
        $catkey[] = ["$id - $name"];
    }
    $catkey[] = [$cancelText];

    $step = checkStep('server_plans');

    if ($step == 1 and $text != $cancelText) {
        $msg = '🔰 لطفا قیمت پلن رو به تومان وارد کنید!';
        if (strlen($text) > 1) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `title`=?,`step`=2 WHERE `active`=0 and `step`=1");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $stmt->close();
            sendMessage($msg, $cancelKey);
        }
    }
    if ($step == 2 and $text != $cancelText) {
        $msg = '🔰لطفا یه دسته از لیست زیر برا پلن انتخاب کن ';
        if (is_numeric($text)) {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `price`=?,`step`=3 WHERE `active`=0");
            $stmt->bind_param("s", $text);
            $stmt->execute();
            $stmt->close();
            sendMessage($msg, json_encode(['keyboard' => $catkey]));
        } else {
            $msg = '‼️ لطفا یک مقدار عددی وارد کنید';
            sendMessage($msg, $cancelKey);
        }
    }
    if ($step == 3 and $text != $cancelText) {
        $srvkey = [];
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1");
        $stmt->execute();
        $srvs = $stmt->get_result();
        $stmt->close();
        sendMessage("لطفا منتظر باشید", $cancelKey);
        while ($srv = $srvs->fetch_assoc()) {
            $id = $srv['id'];
            $title = $srv['title'];
            $srvkey[] = ['text' => "$title", 'callback_data' => "selectNewPlanServer$id"];
        }
        $srvkey = array_chunk($srvkey, 2);
        sendMessage("لطفا یکی از سرورها رو انتخاب کن 👇 ", json_encode([
            'inline_keyboard' => $srvkey
        ]), "HTML");
        $inarr = 0;
        foreach ($catkey as $op) {
            if (in_array($text, $op) and $text != $cancelText) {
                $inarr = 1;
            }
        }
        if ($inarr == 1) {
            $input = explode(' - ', $text);
            $catid = $input[0];
            $stmt = $connection->prepare("UPDATE `server_plans` SET `catid`=?,`step`=50 WHERE `active`=0");
            $stmt->bind_param("i", $catid);
            $stmt->execute();
            $stmt->close();

            sendMessage($msg, $cancelKey);
        } else {
            $msg = '‼️ لطفا فقط یکی از گزینه های پیشنهادی زیر را انتخاب کنید';
            sendMessage($msg, $catkey);
        }
    }
    if ($step == 50 and $text != $cancelText and preg_match('/selectNewPlanServer(\d+)/', $data, $match)) {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `server_id`=?,`step`=51 WHERE `active`=0");
        $stmt->bind_param("i", $match[1]);
        $stmt->execute();
        $stmt->close();

        $keys = json_encode(['inline_keyboard' => [
            [['text' => "🎖پورت اختصاصی", 'callback_data' => "withSpecificPort"]],
            [['text' => "🎗پورت اشتراکی", 'callback_data' => "withSharedPort"]]
        ]]);
        editText($message_id, "لطفا نوعیت پورت پنل رو انتخاب کنید", $keys);
    }
    if ($step == 51 and $text != $cancelText and preg_match('/^with(Specific|Shared)Port/', $data, $match)) {
        if ($userInfo['step'] == "addNewRahgozarPlan") $msg =  "📡 | لطفا پروتکل پلن مورد نظر را وارد کنید (vless | vmess)";
        else $msg =  "📡 | لطفا پروتکل پلن مورد نظر را وارد کنید (vless | vmess | trojan)";
        editText($message_id, $msg);
        if ($match[1] == "Shared") {
            $stmt = $connection->prepare("UPDATE `server_plans` SET `step`=60 WHERE `active`=0");
            $stmt->execute();
            $stmt->close();
        } elseif ($match[1] == "Specific") {
            $stmt = $connection->prepare("UPDATE server_plans SET step=52 WHERE active=0");
            $stmt->execute();
            $stmt->close();
        }
    }
    if ($step == 60 and $text != $cancelText) {
        if ($text != "vless" && $text != "vmess" && $text != "trojan" && $userInfo['step'] == "addNewPlan") {
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید", $cancelKey);
            exit();
        } elseif ($text != "vless" && $text != "vmess" && $userInfo['step'] == "addNewRahgozarPlan") {
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید", $cancelKey);
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `protocol`=?,`step`=61 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();
        sendMessage("📅 | لطفا تعداد روز های اعتبار این پلن را وارد کنید:");
    }
    if ($step == 61 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `days`=?,`step`=62 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🔋 | لطفا مقدار حجم به GB این پلن را وارد کنید:");
    }
    if ($step == 62 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `volume`=?,`step`=63 WHERE `active`=0");
        $stmt->bind_param("d", $text);
        $stmt->execute();
        $stmt->close();
        sendMessage("🛡 | لطفا آیدی سطر کانکشن در پنل را وارد کنید:");
    }
    if ($step == 63 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `inbound_id`=?,`step`=64 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("لطفا ظرفیت تعداد اکانت رو پورت مورد نظر را وارد کنید");
    }
    if ($step == 64 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `acount`=?,`step`=65 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🧲 | لطفا تعداد چند کاربره این پلن را وارد کنید ( 0 نامحدود است )");
    }
    if ($step == 65 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }
        $stmt = $connection->prepare("UPDATE `server_plans` SET `limitip`=?,`step`=4 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        $msg = '🔻یه توضیح برای پلن مورد نظرت بنویس:';
        sendMessage($msg, $cancelKey);
    }
    if ($step == 52 and $text != $cancelText) {
        if ($userInfo['step'] == "addNewPlan" && $text != "vless" && $text != "vmess" && $text != "trojan") {
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید", $cancelKey);
            exit();
        } elseif ($userInfo['step'] == "addNewRahgozarPlan" && $text != "vless" && $text != "vmess") {
            sendMessage("لطفا فقط پروتکل های vless و vmess را وارد کنید", $cancelKey);
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `protocol`=?,`step`=53 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("📅 | لطفا تعداد روز های اعتبار این پلن را وارد کنید:");
    }
    if ($step == 53 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        $stmt = $connection->prepare("UPDATE `server_plans` SET `days`=?,`step`=54 WHERE `active`=0");
        $stmt->bind_param("i", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage("🔋 | لطفا مقدار حجم به GB این پلن را وارد کنید:");
    }
    if ($step == 54 and $text != $cancelText) {
        if (!is_numeric($text)) {
            sendMessage("لطفا فقط عدد وارد کنید");
            exit();
        }

        if ($userInfo['step'] == "addNewPlan") {
            $sql = ("UPDATE `server_plans` SET `volume`=?,`step`=55 WHERE `active`=0");
            $msg = "🔉 | لطفا نوع شبکه این پلن را در انتخاب کنید  (ws | tcp | grpc) :";
        } elseif ($userInfo['step'] == "addNewRahgozarPlan") {
            $sql = ("UPDATE `server_plans` SET `volume`=?, `type`='ws', `step`=4 WHERE `active`=0");
            $msg = '🔻یه توضیح برای پلن مورد نظرت بنویس:';
        }
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("d", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage($msg);
    }
    if ($step == 55 and $text != $cancelText) {
        if ($text != "tcp" && $text != "ws" && $text != "grpc") {
            sendMessage("لطفا فقط نوع (ws | tcp | grpc) را وارد کنید");
            exit();
        }
        $stmt = $connection->prepare("UPDATE `server_plans` SET `type`=?,`step`=4 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();


        $msg = '🔻یه توضیح برای پلن مورد نظرت بنویس:';
        sendMessage($msg, $cancelKey);
    }

    if ($step == 4 and $text != $cancelText) {
        $imgtxt = '☑️ | پنل با موفقیت ثبت و ایجاد شد ( لذت ببرید ) ';
        $stmt = $connection->prepare("UPDATE `server_plans` SET `descr`=?, `active`=1,`step`=10 WHERE `step`=4");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();

        sendMessage($imgtxt, $removeKeyboard);
        sendMessage("خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start", $adminKeys);
        setUser();
    }
}
if ($data == 'backplan' and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active`=1");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    $keyboard = [];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['title'];
        $keyboard[] = ['text' => "$title", 'callback_data' => "plansList$id"];
    }
    $keyboard = array_chunk($keyboard, 2);
    $keyboard[] = [['text' => "➖➖➖", 'callback_data' => "wizwizch"]];
    $keyboard[] = [['text' => '➕ افزودن پلن اختصاصی و اشتراکی', 'callback_data' => "addNewPlan"]];
    $keyboard[] = [['text' => '➕ افزودن پلن رهگذر', 'callback_data' => "addNewRahgozarPlan"]];
    $keyboard[] = [['text' => '➕ افزودن پلن حجمی', 'callback_data' => "volumePlanSettings"], ['text' => '➕ افزودن پلن زمانی', 'callback_data' => "dayPlanSettings"]];
    $keyboard[] = [['text' => "➕ افزودن پلن دلخواه", 'callback_data' => "editCustomPlan"]];
    $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "managePanel"]];

    $msg = ' ☑️ مدیریت پلن ها:';

    if (isset($data) and $data == 'backplan') {
        editText($message_id, $msg, json_encode(['inline_keyboard' => $keyboard]));
    } else {
        sendAction('typing');
        sendmessage($msg, json_encode(['inline_keyboard' => $keyboard]));
    }


    exit;
}
if (($data == "editCustomPlan" || preg_match('/^editCustom(gbPrice|dayPrice)/', $userInfo['step'], $match)) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    if (!isset($data)) {
        if (is_numeric($text)) {
            $botState[$match[1]] = $text;

            $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
            $stmt->execute();
            $isExists = $stmt->get_result();
            $stmt->close();
            if ($isExists->num_rows > 0) $query = "UPDATE `setting` SET `value` = ? WHERE `type` = 'BOT_STATES'";
            else $query = "INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', ?)";
            $newData = json_encode($botState);

            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $newData);
            $stmt->execute();
            $stmt->close();

            sendMessage("✅ | با موفقیت ذخیره شد", $removeKeyboard);
        } else {
            sendMessage("فقط عدد ارسال کن");
            exit();
        }
    }
    $gbPrice = number_format($botState['gbPrice'] ?? 0) . " تومان";
    $dayPrice = number_format($botState['dayPrice'] ?? 0) . " تومان";

    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => $gbPrice, 'callback_data' => "editCustomgbPrice"],
            ['text' => "هزینه هر گیگ", 'callback_data' => "wizwizch"]
        ],
        [
            ['text' => $dayPrice, 'callback_data' => "editCustomdayPrice"],
            ['text' => "هزینه هر روز", 'callback_data' => "wizwizch"]
        ],
        [
            ['text' => "برگشت 🔙", 'callback_data' => "backplan"]
        ]

    ]]);
    if (!isset($data)) {
        sendMessage("تنظیمات پلن دلخواه", $keys);
        setUser();
    } else {
        editText($message_id, "تنظیمات پلن دلخواه", $keys);
    }
}
if (preg_match('/^editCustom(gbPrice|dayPrice)/', $data, $match)) {
    delMessage();
    $title = $match[1] == "dayPrice" ? "هر روز" : "هر گیگ";
    sendMessage("لطفا هزینه " . $title . " را به تومان وارد کنید", $cancelKey);
    setUser($data);
}
if (preg_match('/plansList(\d+)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `server_id`=? ORDER BY`id` ASC");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        alert("متاسفانه، هیچ پلنی براش انتخاب نکردی 😑");
        exit;
    } else {
        $keyboard = [];
        while ($cat = $res->fetch_assoc()) {
            $id = $cat['id'];
            $title = $cat['title'];
            $keyboard[] = ['text' => "#$id $title", 'callback_data' => "planDetails$id"];
        }
        $keyboard = array_chunk($keyboard, 2);
        $keyboard[] = [['text' => "↪ برگشت", 'callback_data' => "backplan"],];
        $msg = ' ▫️ یه پلن رو انتخاب کن بریم برای ادیت:';
        editText($message_id, $msg, json_encode(['inline_keyboard' => $keyboard]), "HTML");
    }
    exit();
}
if (preg_match('/planDetails(\d+)/', $data, $match)) {
    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else editText($message_id, "ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^wizwizplanacclist(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `status`=1 AND `fileid`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        alert('لیست خالی است');
        exit;
    }
    $txt = '';
    while ($order = $res->fetch_assoc()) {
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
        $sold = " 🚀 " . $uname . " ($date)";
        $accid = $order['id'];
        $orderLink = json_decode($order['link'], true);
        $txt = "$sold \n  ☑️ $remark ";
        foreach ($orderLink as $link) {
            $txt .= "<code>" . $link . "</code> \n";
        }
        $txt .= "\n ❗ $channelLock \n";
        sendMessage($txt, null, "HTML");
    }
}
if (preg_match('/^wizwizplandelete(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    alert("پلن رو برات حذفش کردم ☹️☑️");

    editText($message_id, "لطفا یکی از کلید های زیر را انتخاب کنید", $mainKeys);
}
if (preg_match('/^wizwizplanname(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🔅 یه اسم برا پلن جدید انتخاب کن:", $cancelKey);
    exit;
}
if (preg_match('/^wizwizplanname(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_plans` SET `title`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys);
}
if (preg_match('/^wizwizplanslimit(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🔅 ظرفیت جدید برای پلن انتخاب کن:", $cancelKey);
    exit;
}
if (preg_match('/^wizwizplanslimit(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_plans` SET `acount`=? WHERE `id`=?");
    $stmt->bind_param("ii", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^wizwizplansinobundid(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🔅 سطر جدید برای پلن انتخاب کن:", $cancelKey);
    exit;
}
if (preg_match('/^wizwizplansinobundid(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_plans` SET `inbound_id`=? WHERE `id`=?");
    $stmt->bind_param("ii", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^wizwizplaneditdes(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🎯 توضیحاتت رو برام وارد کن:", $cancelKey);
    exit;
}
if (preg_match('/^wizwizplaneditdes(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_plans` SET `descr`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();


    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^editDestName(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🎯 dest رو برام وارد کن:\nبرای حذف کردن متن /empty رو وارد کن", $cancelKey);
    exit;
}
if (preg_match('/^editDestName(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if ($text == "/empty") {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `dest` = NULL WHERE `id`=?");
        $stmt->bind_param("i", $match[1]);
    } else {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `dest`=? WHERE `id`=?");
        $stmt->bind_param("si", $text, $match[1]);
    }
    $stmt->execute();
    $stmt->close();


    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^editSpiderX(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🎯 spiderX رو برام وارد کن\nبرای حذف کردن متن /empty رو وارد کن", $cancelKey);
    exit;
}
if (preg_match('/^editSpiderX(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if ($text == "/empty") {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `spiderX`=NULL WHERE `id`=?");
        $stmt->bind_param("s", $match[1]);
    } else {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `spiderX`=? WHERE `id`=?");
        $stmt->bind_param("si", $text, $match[1]);
    }
    $stmt->execute();
    $stmt->close();


    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^editServerNames(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🎯 serverNames رو به صورت زیر برام وارد کن:\n
`[
  \"yahoo.com\",
  \"www.yahoo.com\"
]`
    \n\nبرای حذف کردن متن /empty رو وارد کن", $cancelKey);
    exit;
}
if (preg_match('/^editServerNames(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if ($text == "/empty") {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `serverNames`=NULL WHERE `id`=?");
        $stmt->bind_param("s", $match[1]);
    } else {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `serverNames`=? WHERE `id`=?");
        $stmt->bind_param("si", $text, $match[1]);
    }
    $stmt->execute();
    $stmt->close();


    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^editFlow(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    $keys = json_encode(['inline_keyboard' => [
        [['text' => "None", 'callback_data' => "editPFlow" . $match[1] . "_None"]],
        [['text' => "xtls-rprx-vision", 'callback_data' => "editPFlow" . $match[1] . "_xtls-rprx-vision"]],
    ]]);
    sendMessage("🎯 لطفا یکی از موارد زیر رو انتخاب کن", $keys);
    exit;
}
if (preg_match('/^editPFlow(\d+)_(.*)/', $data, $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_plans` SET `flow`=? WHERE `id`=?");
    $stmt->bind_param("si", $match[2], $match[1]);
    $stmt->execute();
    $stmt->close();

    alert("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    $keys = getPlanDetailsKeys($match[1]);
    editText($message_id, "ویرایش تنظیمات پلن", $keys, "HTML");
}
if (preg_match('/^wizwizplanrial(\d+)/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("🎯 شیطون قیمت و گرون کردی 😂 ، خب قیمت جدید و بزن ببینم :", $cancelKey);
    exit;
}
if (preg_match('/^wizwizplanrial(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if (is_numeric($text)) {
        $stmt = $connection->prepare("UPDATE `server_plans` SET `price`=? WHERE `id`=?");
        $stmt->bind_param("ii", $text, $match[1]);
        $stmt->execute();
        $stmt->close();

        sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
        setUser();

        $keys = getPlanDetailsKeys($match[1]);
        if ($keys == null) {
            alert("موردی یافت نشد");
            exit;
        } else sendMessage("ویرایش تنظیمات پلن", $keys, "HTML");
    } else {
        sendMessage("بهت میگم قیمت وارد کن برداشتی یه چیز دیگه نوشتی 🫤 ( عدد وارد کن ) عجبا");
    }
}
if (($data == 'mySubscriptions' or preg_match('/changeOrdersPage(\d+)/', $data, $match)) && ($botState['sellState'] == "on" || $from_id == $admin)) {
    $results_per_page = 50;
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `status`=1");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $number_of_result = $stmt->get_result()->num_rows;
    $stmt->close();

    $number_of_page = ceil($number_of_result / $results_per_page);
    $page = $match[1] ?? 1;
    $page_first_result = ($page - 1) * $results_per_page;

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid`=? AND `status`=1 ORDER BY `id` DESC LIMIT ?, ?");
    $stmt->bind_param("iii", $from_id, $page_first_result, $results_per_page);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();


    if ($orders->num_rows == 0) {
        alert('عزیزم هیچ سفارشی نداری 🙁 باید یه کانفیگ خریداری کنی');
        exit;
    }
    $keyboard = [];
    while ($cat = $orders->fetch_assoc()) {
        $id = $cat['id'];
        $remark = $cat['remark'];
        $keyboard[] = ['text' => "$remark", 'callback_data' => "orderDetails$id"];
    }
    $keyboard = array_chunk($keyboard, 2);

    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($number_of_page / $results_per_page);
    $lpm1 = $lastpage - 1;

    $buttons = [];
    if ($prev > 0) $buttons[] = ['text' => "◀", 'callback_data' => "changeOrdersPage$prev"];

    if ($next > 0 and $page != $number_of_page) $buttons[] = ['text' => "➡", 'callback_data' => "changeOrdersPage$next"];
    $keyboard[] = $buttons;
    $keyboard[] = [['text' => "⤵️ برگرد صفحه قبلی ", 'callback_data' => "mainMenu"]];

    $msg = ' 🔅 یکی از سرویس هاتو انتخاب کن و مشخصات کاملش رو ببین :';

    if (isset($data)) {
        editText($message_id, $msg, json_encode(['inline_keyboard' => $keyboard]));
    } else {
        sendAction('typing');
        sendMessage($msg, json_encode(['inline_keyboard' => $keyboard]));
    }
    exit;
}
if (preg_match('/orderDetails(\d+)/', $data, $match) && ($botState['sellState'] == "on" || ($from_id == $admin || $userInfo['isAdmin'] == true))) {
    $keys = getOrderDetailKeys($from_id, $match[1]);
    if ($keys == null) {
        alert("موردی یافت نشد");
        exit;
    } else $res = editText($message_id, $keys['msg'], $keys['keyboard'], "HTML");
}
if ($data == "cantEditGrpc") {
    alert("نوعیت این کانفیگ رو تغییر داده نمیتونید!");
    exit();
}
if (preg_match('/changeNetworkType(\d+)_(\d+)/', $data, $match)) {
    $fid = $match[1];
    $oid = $match[2];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();


    if ($respd) {
        $respd = $respd->fetch_assoc();
        $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
        $stmt->bind_param("i", $respd['catid']);
        $stmt->execute();
        $cadquery = $stmt->get_result();
        $stmt->close();


        if ($cadquery) {
            $catname = $cadquery->fetch_assoc()['title'];
            $name = $catname . " " . $respd['title'];
        } else $name = "$oid";
    } else $name = "$oid";

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id`=?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $date = jdate("Y-m-d H:i", $order['date']);
    $expire_date = jdate("Y-m-d H:i", $order['expire_date']);
    $remark = $order['remark'];
    $acc_link = $order['link'];
    $protocol = $order['protocol'];
    $server_id = $order['server_id'];
    $price = $order['amount'];

    $response = getJson($server_id)->obj;
    foreach ($response as $row) {
        if ($row->remark == $remark) {
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $port = $row->port;
            $uniqid = ($protocol == 'trojan') ? json_decode($row->settings)->clients[0]->password : json_decode($row->settings)->clients[0]->id;
            $netType = json_decode($row->streamSettings)->network;
            $security = json_decode($row->streamSettings)->security;
            $netType = ($netType == 'tcp') ? 'ws' : 'tcp';
            break;
        }
    }

    if ($protocol == 'trojan') $netType = 'tcp';

    $update_response = editInbound($server_id, $uniqid, $remark, $protocol, $netType);
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType);

    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("UPDATE `orders_list` SET `protocol`=?,`link`=? WHERE `id`=?");
    $stmt->bind_param("ssi", $protocol, $vray_link, $oid);
    $stmt->execute();
    $stmt->close();

    $keys = getOrderDetailKeys($from_id, $oid);
    editText($message_id, $keys['msg'], $keys['keyboard'], "HTML");
}
if ($data == "changeProtocolIsDisable") {
    alert("تغییر پروتکل غیر فعال است");
}
if (preg_match('/changeAccProtocol(\d+)_(\d+)_(.*)/', $data, $match)) {
    $fid = $match[1];
    $oid = $match[2];
    $protocol = $match[3];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=? AND `active`=1");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();


    if ($respd) {
        $respd = $respd->fetch_assoc();
        $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `id`=?");
        $stmt->bind_param("i", $respd['catid']);
        $stmt->execute();
        $cadquery = $stmt->get_result();
        $stmt->close();


        if ($cadquery) {
            $catname = $cadquery->fetch_assoc()['title'];
            $name = $catname . " " . $respd['title'];
        } else $name = "$id";
    } else $name = "$id";

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id`=?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    $date = jdate("Y-m-d H:i", $order['date']);
    $expire_date = jdate("Y-m-d H:i", $order['expire_date']);
    $remark = $order['remark'];
    $acc_link = $order['link'];
    $server_id = $order['server_id'];
    $price = $order['amount'];
    $rahgozar = $order['rahgozar'];


    $response = getJson($server_id)->obj;
    foreach ($response as $row) {
        if ($row->remark == $remark) {
            $total = $row->total;
            $up = $row->up;
            $down = $row->down;
            $port = $row->port;
            $netType = json_decode($row->streamSettings)->network;
            $security = json_decode($row->streamSettings)->security;
            break;
        }
    }
    if ($protocol == 'trojan') $netType = 'tcp';
    $uniqid = generateRandomString(42, $protocol);
    $leftgb = round(($total - $up - $down) / 1073741824, 2) . " GB";
    $update_response = editInbound($server_id, $uniqid, $remark, $protocol, $netType, $security, $rahgozar);
    $vraylink = getConnectionLink($server_id, $uniqid, $protocol, $remark, $port, $netType, 0, $rahgozar);

    $vray_link = json_encode($vraylink);
    $stmt = $connection->prepare("UPDATE `orders_list` SET `protocol`=?,`link`=? WHERE `id`=?");
    $stmt->bind_param("ssi", $protocol, $vray_link, $oid);
    $stmt->execute();
    $stmt->close();
    $keys = getOrderDetailKeys($from_id, $oid);
    editText($message_id, $keys['msg'], $keys['keyboard'], "HTML");
}
if (preg_match('/^discountRenew(\d+)_(\d+)/', $userInfo['step'], $match) || preg_match('/renewAccount(\d+)/', $data, $match) && $text != $cancelText) {
    if (preg_match('/^discountRenew/', $userInfo['step'])) {
        $rowId = $match[2];

        $time = time();
        $stmt = $connection->prepare("SELECT * FROM `discounts` WHERE (`expire_date` > $time OR `expire_date` = 0) AND (`expire_count` > 0 OR `expire_count` = -1) AND `hash_id` = ?");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $list = $stmt->get_result();
        $stmt->close();

        $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `id` = ?");
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $payInfo = $stmt->get_result()->fetch_assoc();
        $hash_id = $payInfo['hash_id'];
        $afterDiscount = $payInfo['price'];
        $stmt->close();

        if ($list->num_rows > 0) {
            $discountInfo = $list->fetch_assoc();
            $amount = $discountInfo['amount'];
            $type = $discountInfo['type'];
            $count = $discountInfo['expire_count'];
            $usedBy = !is_null($discountInfo['used_by']) ? json_decode($discountInfo['used_by'], true) : array();
            if (!in_array($from_id, $usedBy)) {
                $usedBy[] = $from_id;
                $encodeUsedBy = json_encode($usedBy);

                if ($count != -1) $query = "UPDATE `discounts` SET `expire_count` = `expire_count` - 1, `used_by` = ? WHERE `id` = ?";
                else $query = "UPDATE `discounts` SET `used_by` = ? WHERE `id` = ?";

                $stmt = $connection->prepare($query);
                $stmt->bind_param("si", $encodeUsedBy, $discountInfo['id']);
                $stmt->execute();
                $stmt->close();

                if ($type == "percent") {
                    $discount = $afterDiscount * $amount / 100;
                    $afterDiscount -= $discount;
                    $discount = number_format($discount) . " تومان";
                } else {
                    $afterDiscount -= $amount;
                    $discount = number_format($amount) . " تومان";
                }
                if ($afterDiscount < 0) $afterDiscount = 0;

                $stmt = $connection->prepare("UPDATE `pays` SET `price` = ? WHERE `id` = ?");
                $stmt->bind_param("ii", $afterDiscount, $rowId);
                $stmt->execute();
                $stmt->close();
                sendMessage(" ✅|کد تخفیف با موفقیت استفاده شد\nمقدار تخفیف $discount");
                $keys = json_encode(['inline_keyboard' => [
                    [
                        ['text' => "❤️", "callback_data" => "wizwizch"]
                    ],
                ]]);
                sendMessage("
 ☑️|🎁 کد تخفیف استفاده شد

🔰آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎁 کد تخفیف: $text
💰مقدار تخفیف: $discount
⁮⁮ ⁮⁮
                ", $keys, null, $admin);
            } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
        } else sendMessage("😔|کد تخفیفی که وارد کردی معتبر نیس");
        setUser();
    } else delMessage();

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

    if (!preg_match('/^discountRenew/', $userInfo['step'])) {
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
        $rowId = $stmt->insert_id;
        $stmt->close();
    } else $price = $afterDiscount;


    $keyboard = array();
    $temp = array();
    if ($botState['cartToCartState'] == "on") {
        $temp[] = ['text' => "💳 کارت به کارت مبلغ $price تومان ",  'callback_data' => "payRenewWithCartToCart$hash_id"];
    }
    if ($botState['nowPaymentOther'] == "on") {
        $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['zarinpal'] == "on") {
        $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['nextpay'] == "on") {
        $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['weSwapState'] == "on") {
        $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
    }

    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['walletState'] == "on") {
        $temp[] = ['text' => "پرداخت با موجودی مبلغ $price تومان 💰",  'callback_data' => "payRenewWithWallet$hash_id"];
    }
    array_push($keyboard, $temp);
    if (!preg_match('/^discountRenew/', $userInfo['step'])) $keyboard[] = [['text' => " 🎁 نکنه کد تخفیف داری؟ ",  'callback_data' => "haveDiscountRenew_" . $match[1] . "_" . $rowId]];

    $keyboard[] = [['text' => $cancelText, 'callback_data' => "mainMenu"]];



    sendMessage("لطفا با یکی از روش های زیر اکانت خود را تمدید کنید :", json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/payRenewWithCartToCart(.*)/', $data, $match)) {
    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

", $cancelKey, "html");
    exit;
}
if (preg_match('/payRenewWithCartToCart(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $hash_id = $payInfo['hash_id'];
    $stmt->close();

    $oid = $payInfo['plan_id'];

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $fid = $order['fileid'];
    $remark = $order['remark'];
    $uid = $order['userid'];
    $userName = $userInfo['username'];
    $uname = $userInfo['name'];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id` = ? AND `active` = 1");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $respd = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $payInfo['price'];
    $msg = "
✅| دوست عزیز ، درخواستت با موفقیت ثبت شد، بعد از بررسی و تمدید ادمین کانفیگ رو برات میفرستم ممنون از صبوریت 

🚪 /start
";
    sendMessage($msg, $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);
    // notify admin
    $msg = "
♻️ تمدید سرویس ( کارت به کارت )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
";

    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approveRenewAcc$hash_id"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decRenewAcc$hash_id"]
            ]
        ]
    ]);

    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\n\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
    setUser();
}
if (preg_match('/approveRenewAcc(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $hash_id = $payInfo['hash_id'];
    $stmt->close();

    $uid = $payInfo['user_id'];
    $oid = $payInfo['plan_id'];
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
    $price = $payInfo['price'];


    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => "✅", 'callback_data' => "wizwizch"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);



    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);


    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, $days);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, $days);

    if (is_null($response)) {
        alert('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید', true);
        exit;
    }
    $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
    $newExpire = $expire_date + $days * 86400;
    $stmt->bind_param("ii", $newExpire, $oid);
    $stmt->execute();
    $stmt->close();
    $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("iiisii", $uid, $server_id, $inbound_id, $remark, $price, $time);
    $stmt->execute();
    $stmt->close();
    sendMessage("✅سرویس $remark با موفقیت تمدید شد", null, null, $uid);
    exit;
}
if (preg_match('/decRenewAcc(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $hash_id = $payInfo['hash_id'];
    $stmt->close();

    $uid = $payInfo['user_id'];
    $oid = $payInfo['plan_id'];

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


    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => '❌', 'callback_data' => "dontsendanymore"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);

    sendMessage("😖|تمدید سرویس $remark لغو شد", null, null, $uid);
    exit;
}
if (preg_match('/payRenewWithWallet(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ?");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result()->fetch_assoc();
    $hash_id = $payInfo['hash_id'];
    $stmt->close();

    $oid = $payInfo['plan_id'];
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
    $price = $payInfo['price'];

    $userwallet = $userInfo['wallet'];

    if ($userwallet < $price) {
        $needamount = $price - $userwallet;
        alert("💡موجودی کیف پول (" . number_format($userwallet) . " تومان) کافی نیست لطفا به مقدار " . number_format($needamount) . " تومان شارژ کنید ", true);
        exit;
    }

    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, $days);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, $days);

    if (is_null($response)) {
        alert('🔻مشکل فنی در اتصال به سرور. لطفا به مدیریت اطلاع بدید', true);
        exit;
    }
    $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = ?, `notif` = 0 WHERE `id` = ?");
    $newExpire = $expire_date + $days * 86400;
    $stmt->bind_param("ii", $newExpire, $oid);
    $stmt->execute();
    $stmt->close();
    $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("iiisii", $from_id, $server_id, $inbound_id, $remark, $price, $time);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
    $stmt->bind_param("ii", $price, $from_id);
    $stmt->execute();
    $stmt->close();
    editText($message_id, "✅سرویس $remark با موفقیت تمدید شد", $mainKeys);
    $keys = json_encode(['inline_keyboard' => [
        [
            ['text' => "به به تمدید 😍", 'callback_data' => "mainMenu"]
        ],
    ]]);

    sendMessage("
♻️ تمدید سرویس ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
💰مبلغ پرداختی: $price تومان
✏️ نام سرویس: $remark
⁮⁮ ⁮⁮
", $keys, "html", $admin);
    exit;
}
if (preg_match('/switchLocation(.+)_(.+)_(.+)_(.+)/', $data, $match)) {
    $order_id = $match[1];
    $server_id = $match[2];
    $leftgp = $match[3];
    $expire = $match[4];
    if ($expire < time() or $leftgp <= 0) {
        alert("سرویس شما غیرفعال است.لطفا ابتدا آن را تمدید کنید", true);
        exit;
    }
    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `active` = 1 and ucount > 0 AND `id` != ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();
    if ($respd->num_rows == 0) {
        alert('در حال حاضر هیچ سرور فعالی برای تغییر لوکیشن وجود ندارد', true);
        exit;
    }
    $keyboard = [];
    while ($cat = $respd->fetch_assoc()) {
        $sid = $cat['id'];
        $name = $cat['title'];
        $keyboard[] = ['text' => "$name", 'callback_data' => "switchServer{$sid}_{$order_id}"];
    }
    $keyboard = array_chunk($keyboard, 2);
    $keyboard[] = [['text' => '🔙 بازگشت', 'callback_data' => "mainMenu"]];
    editText($message_id, ' 📍 لطفا برای تغییر لوکیشن سرویس فعلی, یکی از سرورها را انتخاب کنید👇', json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/switchServer(.+)_(.+)/', $data, $match)) {
    $sid = $match[1];
    $oid = $match[2];
    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
    $stmt->bind_param("i", $oid);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $inbound_id = $order['inbound_id'];
    $server_id = $order['server_id'];
    $remark = $order['remark'];
    $fid = $order['fileid'];
    $protocol = $order['protocol'];
    $link = json_decode($order['link'])[0];

    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $flow = $file_detail['flow'] == "None" ? "" : $file_detail['flow'];

    $stmt = $connection->prepare("SELECT * FROM server_config WHERE id=?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $reality = $server_info['reality'];
    $serverType = $server_info['type'];


    if (preg_match('/vmess/', $link)) {
        $link_info = json_decode(base64_decode(str_replace('vmess://', '', $link)));
        $uniqid = $link_info->id;
        $port = $link_info->port;
        $netType = $link_info->net;
    } else {
        $link_info = parse_url($link);
        $panel_ip = $link_info['host'];
        $uniqid = $link_info['user'];
        $protocol = $link_info['scheme'];
        $port = $link_info['port'];
        $netType = explode('type=', $link_info['query'])[1];
        $netType = explode('&', $netType)[0];
    }

    if ($inbound_id > 0) {
        $remove_response = deleteClient($server_id, $inbound_id, $remark);
        if (is_null($remove_response)) {
            alert('🔻اتصال به سرور برقرار نیست. لطفا به مدیریت اطلاع بدید', true);
            exit;
        }
        if ($remove_response) {
            $total = $remove_response['total'];
            $up = $remove_response['up'];
            $down = $remove_response['down'];
            $id_label = $protocol == 'trojan' ? 'password' : 'id';
            if ($serverType == "sanaei" || $serverType == "alireza") {
                if ($reality == "true") {
                    $newArr = [
                        "$id_label" => $uniqid,
                        "email" => $remark,
                        "flow" => $flow,
                        "limitIp" => $remove_response['limitIp'],
                        "totalGB" => $total - $up - $down,
                        "expiryTime" => $remove_response['expiryTime']
                    ];
                } else {
                    $newArr = [
                        "$id_label" => $uniqid,
                        "email" => $remark,
                        "limitIp" => $remove_response['limitIp'],
                        "totalGB" => $total - $up - $down,
                        "expiryTime" => $remove_response['expiryTime']
                    ];
                }
            } else {
                $newArr = [
                    "$id_label" => $uniqid,
                    "flow" => $remove_response['flow'],
                    "email" => $remark,
                    "limitIp" => $remove_response['limitIp'],
                    "totalGB" => $total - $up - $down,
                    "expiryTime" => $remove_response['expiryTime']
                ];
            }

            $response = addInboundAccount($sid, '', $inbound_id, 1, $remark, 0, 1, $newArr);
            if (is_null($response)) {
                alert('🔻اتصال به سرور برقرار نیست. لطفا به مدیریت اطلاع بدید', true);
                exit;
            }
            if ($response == "inbound not Found") {
                alert("🔻سطر (inbound) با آیدی $inbound_id در این سرور یافت نشد. لطفا به مدیریت اطلاع بدید", true);
                exit;
            }
            if (!$response->success) {
                alert('🔻خطا در ساخت کانفیگ. لطفا به مدیریت اطلاع بدید', true);
                exit;
            }
            $vray_link = getConnectionLink($sid, $uniqid, $protocol, $remark, $port, $netType, $inbound_id);
            deleteClient($server_id, $inbound_id, $remark, 1);
        }
    } else {
        $response = deleteInbound($server_id, $remark);
        if (is_null($response)) {
            alert('🔻اتصال به سرور برقرار نیست. لطفا به مدیریت اطلاع بدید', true);
            exit;
        }
        if ($response) {
            $res = addUser($sid, $response['uniqid'], $response['protocol'], $response['port'], $response['expiryTime'], $remark, $response['volume'] / 1073741824, $response['netType'], $response['security']);
            $vray_link = getConnectionLink($sid, $response['uniqid'], $response['protocol'], $remark, $response['port'], $response['netType'], $inbound_id);
            deleteInbound($server_id, $remark, 1);
        }
    }
    $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` + 1 WHERE `id` = ?");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE `server_info` SET `ucount` = `ucount` - 1 WHERE `id` = ?");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $stmt->close();

    $vray_link = json_encode($vray_link);
    $stmt = $connection->prepare("UPDATE `orders_list` SET `server_id` = ?, `link`=? WHERE `id` = ?");
    $stmt->bind_param("isi", $sid, $vray_link, $oid);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id` = ?");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $server_title = $stmt->get_result()->fetch_assoc()['title'];
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `userid` = ? AND `status` = 1 ORDER BY `id` DESC");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();

    $keyboard = [];
    while ($cat = $orders->fetch_assoc()) {
        $id = $cat['id'];
        $cremark = $cat['remark'];
        $keyboard[] = ['text' => "$cremark", 'callback_data' => "orderDetails$id"];
    }
    $keyboard = array_chunk($keyboard, 2);
    $keyboard[] = [['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]];
    $msg = " 📍لوکیشن سرویس $remark به $server_title تغییر یافت.\n لطفا برای مشاهده مشخصات, روی آن بزنید👇";

    editText($message_id, $msg, json_encode([
        'inline_keyboard' => $keyboard
    ]));
    exit();
}
if (preg_match('/increaseADay(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `increase_day`");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    if ($res->num_rows == 0) {
        alert("در حال حاضر هیچ پلنی برای افزایش مدت زمان سرویس وجود ندارد");
        exit;
    }
    $keyboard = [];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']);
        $keyboard[] = ['text' => "$title روز $price تومان", 'callback_data' => "selectPlanDayIncrease{$match[1]}_$id"];
    }
    $keyboard = array_chunk($keyboard, 2);
    $keyboard[] = [['text' => "صفحه اصلی 🏘", 'callback_data' => "mainMenu"]];
    editText($message_id, "لطفا یکی از پلن های افزایشی را انتخاب کنید :", json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/selectPlanDayIncrease(.+)_(.+)_(.+)_(.+)/', $data, $match)) {
    $data = str_replace('selectPlanDayIncrease', '', $data);
    $pid = $match[4];
    $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $planprice = $res['price'];



    $hash_id = RandomString();
    $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` LIKE '%INCREASE_DAY%' AND `state` = 'pending'");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $stmt->close();

    $time = time();
    $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                VALUES (?, ?, ?, '0', '0', '0', ?, ?, 'pending')");
    $type = "INCREASE_DAY_$data";
    $stmt->bind_param("sisii", $hash_id, $from_id, $type, $planprice, $time);
    $stmt->execute();
    $stmt->close();


    $keyboard = array();
    $temp = array();
    if ($botState['cartToCartState'] == "on") {
        $temp[] = ['text' => "💳 کارت به کارت ",  'callback_data' => "payIncreaseDayWithCartToCart$hash_id"];
    }
    if ($botState['nowPaymentOther'] == "on") {
        $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['zarinpal'] == "on") {
        $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['nextpay'] == "on") {
        $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['weSwapState'] == "on") {
        $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['walletState'] == "on") {
        $temp[] = ['text' => "💰پرداخت با موجودی ",  'callback_data' => "payIncraseDayWithWallet$hash_id"];
    }
    array_push($keyboard, $temp);
    $keyboard[] = [['text' => $cancelText, 'callback_data' => "mainMenu"]];
    editText($message_id, "لطفا با یکی از روش های زیر پرداخت خود را تکمیل کنید :", json_encode(['inline_keyboard' => $keyboard]));
}
if (preg_match('/payIncreaseDayWithCartToCart(.*)/', $data, $match)) {
    delMessage();
    setUser($data);
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

", $cancelKey, "html");
    exit;
}
if (preg_match('/payIncreaseDayWithCartToCart(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    setUser();
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];


    $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $payParam['price'];
    $volume = $res['volume'];

    $msg = "
✅| دوست عزیز ، درخواستت با موفقیت ثبت شد، بعد از بررسی و تمدید ادمین کانفیگ رو برات میفرستم ممنون از صبوریت 

🚪 /start
";
    sendMessage($msg, $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);

    // notify admin
    $msg = "
⏰ درخواست افزایش ( زمان سرویس )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
🔋مدت افزایش: $volume روز
💰قیمت: $price تومان
";

    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approveIncreaseDay{$match[1]}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decIncreaseDay{$match[1]}"]
            ]
        ]
    ]);


    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
    setUser();
}
if (preg_match('/approveIncreaseDay(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];


    $uid = $payParam['user_id'];

    $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $res['price'];
    $volume = $res['volume'];

    $acctxt = '';


    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => '✅', 'callback_data' => "dontsendanymore"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);


    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, 0, $volume);
    else
        $response = editInboundTraffic($server_id, $remark, 0, $volume);
    if ($response->success) {
        $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ?, `notif` = 0 WHERE `remark` = ?");
        $newVolume = $volume * 86400;
        $stmt->bind_param("is", $newVolume, $remark);
        $stmt->execute();
        $stmt->close();

        $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
        $newVolume = $volume * 86400;
        $stmt->bind_param("iiisii", $uid, $server_id, $inbound_id, $remark, $price, $time);
        $stmt->execute();
        $stmt->close();
        sendMessage("✅$volume روز به مدت زمان سرویس شما اضافه شد", null, null, $uid);
    } else {
        alert("مشکل فنی در ارتباط با سرور. لطفا سلامت سرور را بررسی کنید", true);
        exit;
    }
}
if (preg_match('/payIncraseDayWithWallet(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];



    $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $payParam['price'];
    $volume = $res['volume'];

    $userwallet = $userInfo['wallet'];

    if ($userwallet < $price) {
        $needamount = $price - $userwallet;
        alert("💡موجودی کیف پول (" . number_format($userwallet) . " تومان) کافی نیست لطفا به مقدار " . number_format($needamount) . " تومان شارژ کنید ", true);
        exit;
    }



    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, 0, $volume);
    else
        $response = editInboundTraffic($server_id, $remark, 0, $volume);

    if ($response->success) {
        $stmt = $connection->prepare("UPDATE `orders_list` SET `expire_date` = `expire_date` + ?, `notif` = 0 WHERE `remark` = ?");
        $newVolume = $volume * 86400;
        $stmt->bind_param("is", $newVolume, $remark);
        $stmt->execute();
        $stmt->close();

        $stmt = $connection->prepare("INSERT INTO `increase_order` VALUES (NULL, ?, ?, ?, ?, ?, ?);");
        $newVolume = $volume * 86400;
        $stmt->bind_param("iiisii", $from_id, $server_id, $inbound_id, $remark, $price, $time);
        $stmt->execute();
        $stmt->close();

        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $price, $from_id);
        $stmt->execute();
        $stmt->close();
        editText($message_id, "✅$volume روز به مدت زمان سرویس شما اضافه شد", $mainKeys);

        $keys = json_encode(['inline_keyboard' => [
            [
                ['text' => "اخیش یکی زمان زد 😁", 'callback_data' => "wizwizch"]
            ],
        ]]);
        sendMessage("
🔋|💰 افزایش زمان با ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
⏰ مدت افزایش: $volume روز
💰قیمت: $price تومان
⁮⁮ ⁮⁮
        ", $keys, "html", $admin);

        exit;
    } else {
        alert("به دلیل مشکل فنی امکان افزایش حجم نیست. لطفا به مدیریت اطلاع بدید یا 5دقیقه دیگر دوباره تست کنید", true);
        exit;
    }
}
if (preg_match('/^increaseAVolume(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `increase_plan`");
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows == 0) {
        alert("در حال حاضر هیچ پلن حجمی وجود ندارد");
        exit;
    }
    $keyboard = [];
    while ($cat = $res->fetch_assoc()) {
        $id = $cat['id'];
        $title = $cat['volume'];
        $price = number_format($cat['price']);
        $keyboard[] = ['text' => "$title گیگ $price تومان", 'callback_data' => "increaseVolumePlan{$match[1]}_{$id}"];
    }
    $keyboard = array_chunk($keyboard, 2);
    $keyboard[] = [['text' => "صفحه ی اصلی 🏘", 'callback_data' => "mainMenu"]];
    editText($message_id, "لطفا یکی از پلن های حجمی را انتخاب کنید :", json_encode([
        'inline_keyboard' => $keyboard
    ]));
}
if (preg_match('/increaseVolumePlan(.+)_(.+)_(.+)_(.+)/', $data, $match)) {
    $data = str_replace('increaseVolumePlan', '', $data);
    $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $match[4]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $planprice = $res['price'];
    $plangb = $res['volume'];


    $hash_id = RandomString();
    $stmt = $connection->prepare("DELETE FROM `pays` WHERE `user_id` = ? AND `type` LIKE '%INCREASE_VOLUME%' AND `state` = 'pending'");
    $stmt->bind_param("i", $from_id);
    $stmt->execute();
    $stmt->close();

    $time = time();
    $stmt = $connection->prepare("INSERT INTO `pays` (`hash_id`, `user_id`, `type`, `plan_id`, `volume`, `day`, `price`, `request_date`, `state`)
                                VALUES (?, ?, ?, '0', '0', '0', ?, ?, 'pending')");
    $type = "INCREASE_VOLUME_$data";
    $stmt->bind_param("sisii", $hash_id, $from_id, $type, $planprice, $time);
    $stmt->execute();
    $stmt->close();

    $keyboard = array();
    $temp = array();
    if ($botState['cartToCartState'] == "on") {
        $temp[] = ['text' => "💳 کارت به کارت " . number_format($planprice) . " تومان",  'callback_data' => "payIncreaseWithCartToCart$hash_id"];
    }
    if ($botState['nowPaymentOther'] == "on") {
        $temp[] = ['text' => "💳 درگاه NowPayment ",  'url' => $botUrl . "pay/?nowpayment&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['zarinpal'] == "on") {
        $temp[] = ['text' => "💳 درگاه زرین پال ",  'url' => $botUrl . "pay/?zarinpal&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['nextpay'] == "on") {
        $temp[] = ['text' => "💳 درگاه نکست پی ",  'url' => $botUrl . "pay/?nextpay&hash_id=" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['weSwapState'] == "on") {
        $temp[] = ['text' => "💳 درگاه وی سواپ ",  'callback_data' => "payWithWeSwap" . $hash_id];
    }
    if (count($temp) == 2) {
        array_push($keyboard, $temp);
        $temp = array();
    }
    if ($botState['walletState'] == "on") {
        $temp[] = ['text' => "💰پرداخت با موجودی  " . number_format($planprice) . " تومان",  'callback_data' => "payIncraseWithWallet$hash_id"];
    }
    array_push($keyboard, $temp);
    $keyboard[] = [['text' => $cancelText, 'callback_data' => "mainMenu"]];
    editText($message_id, "لطفا با یکی از روش های زیر پرداخت خود را تکمیل کنید :", json_encode(['inline_keyboard' => $keyboard]));
}
if (preg_match('/payIncreaseWithCartToCart(.*)/', $data)) {
    setUser($data);
    delMessage();
    sendMessage("♻️ عزیزم یه تصویر از فیش واریزی یا شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده رو در یک پیام برام ارسال کن :

🔰 <code>{$paymentKeys['bankAccount']}</code> - {$paymentKeys['holderName']}

", $cancelKey, "html");
    exit;
}
if (preg_match('/payIncreaseWithCartToCart(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];


    $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $payParam['price'];
    $volume = $res['volume'];
    $state = str_replace('payIncreaseWithCartToCart', '', $userInfo['step']);
    $msg = "
✅| دوست عزیز ، درخواستت با موفقیت ثبت شد، بعد از بررسی و تمدید ادمین کانفیگ رو برات میفرستم ممنون از صبوریت 

🚪 /start
";
    sendMessage($msg, $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);

    // notify admin
    $msg = "
🔋درخواست افزایش ( حجم سرویس )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
⏰ مدت افزایش: $volume گیگ
💰قیمت: $price تومان
";

    $keyboard = json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'تایید ✅', 'callback_data' => "approveIncreaseVolume{$match[1]}"],
                ['text' => 'عدم تایید ❌', 'callback_data' => "decIncreaseVolume{$match[1]}"]
            ]
        ]
    ]);

    if (isset($update->message->photo)) {
        sendPhoto($fileid, $msg, $keyboard, "HTML", $admin);
    } else {
        $msg .= "\nاطلاعات واریز: $text";
        sendMessage($msg, $keyboard, "HTML", $admin);
    }
    setUser();
}
if (preg_match('/approveIncreaseVolume(.*)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];

    $uid = $payParam['user_id'];
    $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $res['price'];
    $volume = $res['volume'];

    $acctxt = '';

    unset($markup[count($markup) - 1]);
    $markup[] = [['text' => '✅', 'callback_data' => "dontsendanymore"]];
    $keys = json_encode(['inline_keyboard' => array_values($markup)], 488);

    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => $keys
    ]);


    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, 0);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, 0);
    if ($response->success) {
        $stmt = $connection->prepare("UPDATE `orders_list` SET `notif` = 0 WHERE `remark` = ?");
        $stmt->bind_param("s", $remark);
        $stmt->execute();
        $stmt->close();
        sendMessage("✅$volume گیگ به حجم سرویس شما اضافه شد", null, null, $uid);
    } else {
        alert("مشکل فنی در ارتباط با سرور. لطفا سلامت سرور را بررسی کنید", true);
        exit;
    }
}
if (preg_match('/decIncreaseVolume(.*)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];

    $uid = $payParam['user_id'];
    $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $res['price'];
    $volume = $res['volume'];

    $acctxt = '';
    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => "لغو شد ❌", 'callback_data' => "wizwizch"]]
        ]])
    ]);

    sendMessage("افزایش حجم $volume گیگ اشتراک $remark لغو شد", null, null, $uid);
}
if (preg_match('/decIncreaseDay(.*)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];

    $uid = $payParam['user_id'];
    $stmt = $connection->prepare("SELECT * FROM `increase_day` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $res['price'];
    $volume = $res['volume'];

    $acctxt = '';
    bot('editMessageReplyMarkup', [
        'chat_id' => $from_id,
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => "لغو شد ❌", 'callback_data' => "wizwizch"]]
        ]])
    ]);


    sendMessage("افزایش زمان $volume روز اشتراک $remark لغو شد", null, null, $uid);
}
if (preg_match('/payIncraseWithWallet(.*)/', $data, $match)) {
    $stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
    $stmt->bind_param("s", $match[1]);
    $stmt->execute();
    $payInfo = $stmt->get_result();
    $stmt->close();

    $payParam = $payInfo->fetch_assoc();
    $payType = $payParam['type'];


    preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/', $payType, $increaseInfo);
    $server_id = $increaseInfo[1];
    $inbound_id = $increaseInfo[2];
    $remark = $increaseInfo[3];
    $planid = $increaseInfo[4];

    $stmt = $connection->prepare("SELECT * FROM `increase_plan` WHERE `id` = ?");
    $stmt->bind_param("i", $planid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $price = $payParam['price'];
    $volume = $res['volume'];

    $userwallet = $userInfo['wallet'];

    if ($userwallet < $price) {
        $needamount = $price - $userwallet;
        alert("💡موجودی کیف پول (" . number_format($userwallet) . " تومان) کافی نیست لطفا به مقدار " . number_format($needamount) . " تومان شارژ کنید ", true);
        exit;
    }

    if ($inbound_id > 0)
        $response = editClientTraffic($server_id, $inbound_id, $remark, $volume, 0);
    else
        $response = editInboundTraffic($server_id, $remark, $volume, 0);

    if ($response->success) {
        $stmt = $connection->prepare("UPDATE `users` SET `wallet` = `wallet` - ? WHERE `userid` = ?");
        $stmt->bind_param("ii", $price, $from_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $connection->prepare("UPDATE `orders_list` SET `notif` = 0 WHERE `remark` = ?");
        $stmt->bind_param("s", $remark);
        $stmt->execute();
        $stmt->close();
        $keys = json_encode(['inline_keyboard' => [
            [
                ['text' => "اخیش یکی حجم زد 😁", 'callback_data' => "wizwizch"]
            ],
        ]]);
        sendMessage("
🔋|💰 افزایش حجم با ( کیف پول )

▫️آیدی کاربر: $from_id
👨‍💼اسم کاربر: $first_name
⚡️ نام کاربری: $username
🎈 نام سرویس: $remark
⏰ مدت افزایش: $volume گیگ
💰قیمت: $price تومان
⁮⁮ ⁮⁮
        ", $keys, "html", $admin);
        editText($message_id, "✅$volume گیگ به حجم سرویس شما اضافه شد", $mainKeys);
        exit;
    } else {
        alert("به دلیل مشکل فنی امکان افزایش حجم نیست. لطفا به مدیریت اطلاع بدید یا 5دقیقه دیگر دوباره تست کنید", true);
        exit;
    }
}
if ($data == 'cantEditTrojan') {
    alert("پروتکل تروجان فقط نوع شبکه TCP را دارد");
    exit;
}
if (($data == 'categoriesSetting' || preg_match('/^nextCategoryPage(\d+)/', $data, $match)) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (isset($match[1])) $keys = getCategoriesKeys($match[1]);
    else $keys = getCategoriesKeys();

    editText($message_id, "☑️ مدیریت دسته ها:", $keys);
}
if ($data == 'addNewCategory' and (($from_id == $admin || $userInfo['isAdmin'] == true))) {
    setUser($data);
    delMessage();
    $stmt = $connection->prepare("DELETE FROM `server_categories` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();


    $sql = "INSERT INTO `server_categories` VALUES (NULL, 0, '', 0,2,0);";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $stmt->close();


    $msg = '▪️یه اسم برای دسته بندی وارد کن:';
    sendMessage($msg, $cancelKey);
    exit;
}
if (preg_match('/^addNewCategory/', $userInfo['step']) and $text != $cancelText) {
    $step = checkStep('server_categories');
    if ($step == 2 and $text != $cancelText) {

        $stmt = $connection->prepare("UPDATE `server_categories` SET `title`=?,`step`=4,`active`=1 WHERE `active`=0");
        $stmt->bind_param("s", $text);
        $stmt->execute();
        $stmt->close();


        $msg = 'یه دسته بندی جدید برات ثبت کردم 🙂☑️';
        sendMessage($msg, $removeKeyboard);
        sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', getCategoriesKeys());
    }
}
if (preg_match('/^wizwizcategorydelete(\d+)_(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `server_categories` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    alert("دسته بندی رو برات حذفش کردم ☹️☑️");

    $stmt = $connection->prepare("SELECT * FROM `server_categories` WHERE `active`=1 AND `parent`=0");
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();

    $keys = getCategoriesKeys($match[2]);
    editText($message_id, "☑️ مدیریت دسته ها:", $keys);
}
if (preg_match('/^wizwizcategoryedit/', $data) and ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    setUser($data);
    delMessage();
    sendMessage("〽️ یه اسم جدید برا دسته بندی انتخاب کن:", $cancelKey);
    exit;
}
if (preg_match('/wizwizcategoryedit(\d+)_(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_categories` SET `title`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();

    sendMessage("با موفقیت برات تغییر دادم ☺️☑️");
    setUser();

    sendMessage("☑️ مدیریت دسته ها:", getCategoriesKeys($match[2]));
}
if (($data == 'serversSetting' || preg_match('/^nextServerPage(\d+)/', $data, $match)) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (isset($match[1])) $keys = getServerListKeys($match[1]);
    else $keys = getServerListKeys();

    editText($message_id, "☑️ مدیریت سرور ها:", $keys);
}
if (preg_match('/^toggleServerState(\d+)_(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("UPDATE `server_info` SET `state` = IF(`state` = 0,1,0) WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $cats = $stmt->get_result();
    $stmt->close();

    alert("وضعیت سرور با موفقیت تغییر کرد");

    $keys = getServerListKeys($match[2]);
    editText($message_id, "☑️ مدیریت سرور ها:", $keys);
}
if (preg_match('/^showServerSettings(\d+)_(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $keys = getServerConfigKeys($match[1], $match[2]);
    editText($message_id, "☑️ مدیریت سرور ها: $cname", $keys);
}
if (preg_match('/^changesServerIp(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("SELECT * FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $serverIp = $stmt->get_result()->fetch_assoc()['ip'] ?? "اطلاعاتی یافت نشد";
    $stmt->close();

    delMessage();
    sendMessage("لیست آیپی های فعلی: \n$serverIp\nلطفا آیپی های جدید را در خط های جدا بفرستید\n\nبرای خالی کردن متن /empty را وارد کنید", $cancelKey, null, null, null);
    setUser($data);
    exit();
}
if (preg_match('/^changesServerIp(\d+)/', $userInfo['step'], $match) && ($from_id == $admin || $userInfo['isAdmin'] == true) && $text != $cancelText) {
    $stmt = $connection->prepare("UPDATE `server_config` SET `ip` = ? WHERE `id`=?");
    if ($text == "/empty") $text = "";
    $stmt->bind_param("si", $text, $match[1]);
    $stmt->execute();
    $stmt->close();
    sendMessage("☑️ | 😁 با موفقیت ذخیره شد", $removeKeyboard);
    setUser();

    $keys = getServerConfigKeys($match[1]);
    sendMessage("☑️ مدیریت سرور ها: $cname", $keys);
    exit();
}
if (preg_match('/^changePortType(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("UPDATE `server_config` SET `port_type` = IF(`port_type` = 'auto', 'random', 'auto') WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();
    alert("نوعیت پورت سرور مورد نظر با موفقیت تغییر کرد");

    $keys = getServerConfigKeys($match[1]);
    editText($message_id, "☑️ مدیریت سرور ها: $cname", $keys);

    exit();
}
if (preg_match('/^changeRealityState(\d+)/', $data, $match)) {
    $stmt = $connection->prepare("UPDATE `server_config` SET `reality` = IF(`reality` = 'true', 'false', 'true') WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    $keys = getServerConfigKeys($match[1]);
    editText($message_id, "☑️ مدیریت سرور ها: $cname", $keys);

    exit();
}
if (preg_match('/^changeServerType(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    editText($message_id, "
    
🔰 نکته مهم: ( پنل x-ui خود را به آخرین نسخه آپدیت کنید ) 

❤️ اگر از پنل سنایی استفاده میکنید لطفا نوع پنل را ( سنایی ) انتخاب کنید
🧡 اگر از پنل علیرضا استفاده میکنید لطفا نوع پنل را ( علیرضا ) انتخاب کنید
💚 اگر از پنل نیدوکا استفاده میکنید لطفا نوع پنل را ( ساده ) انتخاب کنید 
💙 اگر از پنل چینی استفاده میکنید لطفا نوع پنل را ( ساده ) انتخاب کنید 
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
📣 حتما نوع پنل را انتخاب کنید وگرنه براتون مشکل ساز میشه !
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
", json_encode(['inline_keyboard' => [
        [['text' => "ساده", 'callback_data' => "chhangeServerTypenormal_" . $match[1]], ['text' => "سنایی", 'callback_data' => "chhangeServerTypesanaei_" . $match[1]]],
        [['text' => "علیرضا", 'callback_data' => "chhangeServerTypealireza_" . $match[1]]]
    ]]));
    exit();
}
if (preg_match('/^chhangeServerType(\w+)_(\d+)/', $data, $match) && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    alert("☑️ | 😁 با موفقیت ذخیره شد");
    $stmt = $connection->prepare("UPDATE `server_config` SET `type` = ? WHERE `id`=?");
    $stmt->bind_param("si", $match[1], $match[2]);
    $stmt->execute();
    $stmt->close();

    $keys = getServerConfigKeys($match[2]);
    editText($message_id, "☑️ مدیریت سرور ها: $cname", $keys);
}
if ($data == 'addNewServer' and (($from_id == $admin || $userInfo['isAdmin'] == true))) {
    delMessage();
    setUser('addserverName');
    sendMessage("مرحله اول: 
▪️یه اسم برا سرورت انتخاب کن:", $cancelKey);
    exit();
}
if ($userInfo['step'] == 'addserverName' and $text != $cancelText) {
    sendMessage('مرحله دوم: 
▪️ظرفیت تعداد ساخت کانفیگ رو برای سرورت مشخص کن ( عدد باشه )');
    $data = array();
    $data['title'] = $text;

    setUser('addServerUCount' . json_encode($data, JSON_UNESCAPED_UNICODE));
    exit();
}
if (preg_match('/^addServerUCount(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['ucount'] = $text;

    sendMessage("مرحله سوم: 
▪️یه اسم ( ریمارک ) برا کانفیگ انتخاب کن:
 ( به صورت انگیلیسی و بدون فاصله )
");
    setUser('addServerRemark' . json_encode($data, JSON_UNESCAPED_UNICODE));
    exit();
}
if (preg_match('/^addServerRemark(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['remark'] = $text;

    sendMessage("مرحله چهارم:
▪️لطفا یه ( ایموجی پرچم 🇮🇷 ) برا سرورت انتخاب کن:");
    setUser('addServerFlag' . json_encode($data, JSON_UNESCAPED_UNICODE));
    exit();
}
if (preg_match('/^addServerFlag(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['flag'] = $text;

    sendMessage("مرحله پنجم:

▪️لطفا آدرس پنل x-ui رو به صورت مثال زیر وارد کن:

❕https://yourdomain.com:54321
❕https://yourdomain.com:54321/path
❗️http://125.12.12.36:54321
❗️http://125.12.12.36:54321/path

اگر سرور مورد نظر با دامنه و ssl هست از مثال ( ❕) استفاده کنید
اگر سرور مورد نظر با ip و بدون ssl هست از مثال ( ❗️) استفاده کنید
❌ همچنین حتما حتما ویس زیر رو گوش کنید تا جلوتر موقع ثبت سرور با خطا مواجه نشید 👇🏻

⛔️🔗 https://t.me/wizwizch/186

⚠️ نکته مهم ( برای تانل ها ) : اگر از تانل استفاده می کنید لطفا سرور خارجی که پنل روی آن نصب است را به صورت ip در این مرحله وارد کنید ، سپس دامنه ای که ip ایران ست شده است را در مرحله بعدی وارد کنید
⁮⁮ ⁮⁮
");
    setUser('addServerPanelUrl' . json_encode($data, JSON_UNESCAPED_UNICODE));
    exit();
}
if (preg_match('/^addServerPanelUrl(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['panel_url'] = $text;
    setUser('addServerIp' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا ip یا دامنه تانل شده پنل را وارد کنید:

نمونه: 
91.257.142.14
sub.domain.com
❗️در صورتی که میخواید چند دامنه یا ip کانفیگ بگیرید باید زیر هم بنویسید و برای ربات بفرستین:
    \n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerIp(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['panel_ip'] = $text;
    setUser('addServerSni' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا sni پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerSni(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['sni'] = $text;
    setUser('addServerHeaderType' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا header type پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerHeaderType(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['header_type'] = $text;
    setUser('addServerRequestHeader' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا request header پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerRequestHeader(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['request_header'] = $text;
    setUser('addServerResponseHeader' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا response header پنل را وارد کنید\n\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerResponseHeader(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['response_header'] = $text;
    setUser('addServerSecurity' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("🔅 لطفا security پنل را وارد کنید

⚠️ توجه: برای استفاده از tls یا xtls لطفا کلمه tls یا xtls رو تایپ کنید در غیر این صورت 👇
\n🔻برای خالی گذاشتن متن /empty را وارد کنید");
    exit();
}
if (preg_match('/^addServerSecurity(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
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
        . '<b>tls setting:</b> <code>{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}]}</code>' . "\n"
        . '<b>xtls setting:</b> <code>{"serverName": "","certificates": [{"certificateFile": "","keyFile": ""}],"alpn": []}</code>', null, "HTML");

    exit();
}
if (preg_match('/^addServerTlsSetting(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['tls_setting'] = $text;
    setUser('addServerPanelUser' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("مرحله ششم: 
▪️لطفا یوزر پنل را وارد کنید:");

    exit();
}
if (preg_match('/^addServerPanelUser(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['panel_user'] = $text;
    setUser('addServerPanePassword' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("مرحله هفتم: 
▪️لطفا پسورد پنل را وارد کنید:");
    exit();
}
if (preg_match('/^addServerPanePassword(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    sendMessage("⏳ در حال ورود به اکانت ...");
    $data = json_decode($match[1], true);

    $title = $data['title'];
    $ucount = $data['ucount'];
    $remark = $data['remark'];
    $flag = $data['flag'];

    $panel_url = $data['panel_url'];
    $ip = $data['panel_ip'] != "/empty" ? $data['panel_ip'] : "";
    $sni = $data['sni'] != "/empty" ? $data['sni'] : "";
    $header_type = $data['header_type'] != "/empty" ? $data['header_type'] : "none";
    $request_header = $data['request_header'] != "/empty" ? $data['request_header'] : "";
    $response_header = $data['response_header'] != "/empty" ? $data['response_header'] : "";
    $security = $data['security'] != "/empty" ? $data['security'] : "none";
    $tlsSettings = $data['tls_setting'] != "/empty" ? $data['tls_setting'] : "";
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
    $loginResponse = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (!$loginResponse['success']) {
        setUser('addServerPanelUser' . json_encode($data, JSON_UNESCAPED_UNICODE));
        sendMessage("
⚠️ با خطا مواجه شدی ! 

برای رفع این مشکل روی لینک زیر بزن و ویس رو با دقت گوش کن 👇

⛔️🔗 https://t.me/wizwizch/186
⁮⁮ ⁮⁮
        ");
        exit();
    }
    unlink("tempCookie.txt");
    $stmt = $connection->prepare("INSERT INTO `server_info` (`title`, `ucount`, `remark`, `flag`, `active`)
                                                    VALUES (?,?,?,?,1)");
    $stmt->bind_param("siss", $title, $ucount, $remark, $flag);
    $stmt->execute();
    $rowId = $stmt->insert_id;
    $stmt->close();


    $stmt = $connection->prepare("INSERT INTO `server_config` (`id`, `panel_url`, `ip`, `sni`, `header_type`, `request_header`, `response_header`, `security`, `tlsSettings`, `username`, `password`)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $rowId, $panel_url, $ip, $sni, $header_type, $request_header, $response_header, $security, $tlsSettings, $serverName, $serverPass);
    $stmt->execute();
    $rowId = $stmt->insert_id;
    $stmt->close();

    sendMessage(" تبریک ; سرورت رو ثبت کردی 🥹", $removeKeyboard);

    sendMessage("
    
🔰 نکته مهم: ( پنل x-ui خود را به آخرین نسخه آپدیت کنید ) 

❤️ اگر از پنل سنایی استفاده میکنید لطفا نوع پنل را ( سنایی ) انتخاب کنید
🧡 اگر از پنل علیرضا استفاده میکنید لطفا نوع پنل را ( علیرضا ) انتخاب کنید
💚 اگر از پنل نیدوکا استفاده میکنید لطفا نوع پنل را ( ساده ) انتخاب کنید 
💙 اگر از پنل چینی استفاده میکنید لطفا نوع پنل را ( ساده ) انتخاب کنید 
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
📣 حتما نوع پنل را انتخاب کنید وگرنه براتون مشکل ساز میشه !
⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮
    ", json_encode(['inline_keyboard' => [
        [['text' => "ساده", 'callback_data' => "chhangeServerTypenormal_" . $rowId], ['text' => "سنایی", 'callback_data' => "chhangeServerTypesanaei_" . $rowId]],
        [['text' => "علیرضا", 'callback_data' => "chhangeServerTypealireza_" . $rowId]]
    ]]));
    setUser();
    exit();
}
if (preg_match('/^changesServerLoginInfo(\d+)/', $data, $match)) {
    delMessage();
    setUser($data);
    sendMessage("▪️لطفا آدرس پنل را وارد کنید:", $cancelKey);
}
if (preg_match('/^changesServerLoginInfo(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    $data = array();
    $data['rowId'] = $match[1];
    $data['panel_url'] = $text;
    setUser('editServerPaneUser' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("▪️لطفا یوزر پنل را وارد کنید:", $cancelKey);
    exit();
}
if (preg_match('/^editServerPaneUser(.*)/', $userInfo['step'], $match) && $text != $cancelText) {
    $data = json_decode($match[1], true);
    $data['panel_user'] = $text;
    setUser('editServerPanePassword' . json_encode($data, JSON_UNESCAPED_UNICODE));
    sendMessage("▪️لطفا پسورد پنل را وارد کنید:");
    exit();
}
if (preg_match('/^editServerPanePassword(.*)/', $userInfo['step'], $match) and $text != $cancelText) {
    sendMessage("⏳ در حال ورود به اکانت ...");
    $data = json_decode($match[1], true);

    $rowId = $data['rowId'];
    $panel_url = $data['panel_url'];
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
    $loginResponse = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (!$loginResponse['success']) sendMessage("اطلاعاتی که وارد کردی اشتباهه 😂");
    else {
        $stmt = $connection->prepare("UPDATE `server_config` SET `panel_url` = ?, `username` = ?, `password` = ? WHERE `id` = ?");
        $stmt->bind_param("sssi", $panel_url, $serverName, $serverPass, $rowId);
        $stmt->execute();
        $stmt->close();

        sendMessage("اطلاعات ورود سرور با موفقیت عوض شد", $removeKeyboard);
    }
    unlink("tempCookie.txt");

    $keys = getServerConfigKeys($rowId);
    sendMessage('☑️ مدیریت سرور ها:', $keys);
    setUser();
}
if (preg_match('/^wizwizdeleteserver(\d+)/', $data, $match) and ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $stmt = $connection->prepare("DELETE FROM `server_info` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("DELETE FROM `server_config` WHERE `id`=?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    alert("🙂 سرور رو چرا حذف کردی اخه ...");


    $keys = getServerListKeys();
    if ($keys == null) editText($message_id, "موردی یافت نشد");
    else editText($message_id, "☑️ مدیریت سرور ها:", $keys);
}
if (preg_match('/^editServer(\D+)(\d+)/', $data, $match) && $text != $cancelText) {
    switch ($match[1]) {
        case "Name":
            $txt = "اسم";
            break;
        case "Max":
            $txt = "ظرفیت";
            break;
        case "Remark":
            $txt = "ریمارک";
            break;
        case "Flag":
            $txt = "پرچم";
            break;
        default:
            $txt = str_replace("_", " ", $match[1]);
            $end = "برای خالی کردن متن /empty را وارد کنید";
            break;
    }
    delMessage();
    sendMessage("🔘|لطفا " . $txt . " جدید را وارد کنید" . $end, $cancelKey);
    setUser($data);
    exit();
}
if (preg_match('/^editServer(\D+)(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    switch ($match[1]) {
        case "Name":
            $txt = "title";
            break;
        case "Max":
            $txt = "ucount";
            break;
        case "Remark":
            $txt = "remark";
            break;
        case "Flag":
            $txt = "flag";
            break;
        default:
            $txt = $match[1];
            break;
    }

    if ($text == "/empty") {
        $stmt = $connection->prepare("UPDATE `server_info` SET `$txt` IS NULL WHERE `id`=?");
        $stmt->bind_param("i", $match[2]);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $connection->prepare("UPDATE `server_info` SET `$txt`=? WHERE `id`=?");
        $stmt->bind_param("si", $text, $match[2]);
        $stmt->execute();
        $stmt->close();
    }

    sendMessage("☑️ | 😁 با موفقیت ذخیره شد", $removeKeyboard);
    setUser();

    $keys = getServerConfigKeys($match[2]);
    sendMessage("مدیریت سرور $cname", $keys);
    exit();
}
if (preg_match('/^editsServer(\D+)(\d+)/', $data, $match) && $text != $cancelText) {
    $txt = str_replace("_", " ", $match[1]);
    delMessage();
    sendMessage("🔘|لطفا " . $txt . " جدید را وارد کنید\nبرای خالی کردن متن /empty را وارد کنید", $cancelKey);
    setUser($data);
    exit();
}
if (preg_match('/^editsServer(\D+)(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    if ($text == "/empty") {
        if ($match[1] == "header_type" || $match[1] == "security") {
            $stmt = $connection->prepare("UPDATE `server_config` SET `{$match[1]}` = 'none' WHERE `id`=?");
            $stmt->bind_param("i", $match[2]);
        } else {
            $stmt = $connection->prepare("UPDATE `server_config` SET `{$match[1]}` = '' WHERE `id`=?");
            $stmt->bind_param("i", $match[2]);
        }
    } else {
        if ($match[1] == "header_type" && $text != "http" && $text != "none") {
            sendMessage("برای نوع header type فقط none و یا http مجاز است");
            exit();
        } elseif ($match[1] == "security" && $text != "tls" && $text != "none" && $text != "xtls") {
            sendMessage("برای نوع security فقط tls یا xtls و یا هم none مجاز است");
            exit();
        }
        $stmt = $connection->prepare("UPDATE `server_config` SET `{$match[1]}`=? WHERE `id`=?");
        $stmt->bind_param("si", $text, $match[2]);
    }
    $stmt->execute();
    $stmt->close();

    sendMessage("☑️ | 😁 با موفقیت ذخیره شد", $removeKeyboard);
    setUser();

    $keys = getServerConfigKeys($match[2]);
    sendMessage("مدیریت سرور $cname", $keys);
    exit();
}
if (preg_match('/^editServer(\D+)(\d+)/', $data, $match) && $text != $cancelText) {
    switch ($match[1]) {
        case "Name":
            $txt = "اسم";
            break;
        case "Max":
            $txt = "ظرفیت";
            break;
        case "Remark":
            $txt = "ریمارک";
            break;
        case "Flag":
            $txt = "پرچم";
            break;
    }
    delMessage();
    sendMessage("🔘|لطفا " . $txt . " جدید را وارد کنید", $cancelKey);
    setUser($data);
}
if (preg_match('/^editServer(\D+)(\d+)/', $userInfo['step'], $match) && $text != $cancelText) {
    switch ($match[1]) {
        case "Name":
            $txt = "title";
            break;
        case "Max":
            $txt = "ucount";
            break;
        case "Remark":
            $txt = "remark";
            break;
        case "Flag":
            $txt = "flag";
            break;
    }

    $stmt = $connection->prepare("UPDATE `server_info` SET `$txt`=? WHERE `id`=?");
    $stmt->bind_param("si", $text, $match[2]);
    $stmt->execute();
    $stmt->close();

    sendMessage("☑️ | 😁 با موفقیت ذخیره شد", $removeKeyboard);
    setUser();

    $keys = getServerConfigKeys($match[2]);
    sendMessage("مدیریت سرور $cname", $keys);
}
if ($data == "discount_codes" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    editText($message_id, "مدیریت کد های تخفیف", getDiscountCodeKeys());
}
if ($data == "addDiscountCode" && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    delMessage();
    sendMessage("🔘|لطفا مقدار تخفیف را وارد کنید\nبرای درصد علامت % را در کنار عدد وارد کنید در غیر آن مقدار تخفیف به تومان محاسبه میشود", $cancelKey);
    setUser($data);
}
if ($userInfo['step'] == "addDiscountCode" && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    $dInfo = array();
    $dInfo['type'] = 'amount';
    if (strpos($text, "%")) $dInfo['type'] = 'percent';
    $text = trim(str_replace("%", "", $text));
    if (is_numeric($text)) {
        $dInfo['amount'] = $text;
        setUser("addDiscountDate" . json_encode($dInfo, JSON_UNESCAPED_UNICODE));
        sendMessage("🔘|لطفا مدت زمان این تخفیف را به روز وارد کنید\nبرای نامحدود بودن 0 وارد کنید");
    } else sendMessage("🔘|لطفا فقط عدد و یا درصد بفرستید");
}
if (preg_match('/^addDiscountDate(.*)/', $userInfo['step'], $match) && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (is_numeric($text)) {
        $dInfo = json_decode($match[1], true);
        $dInfo['date'] = $text != 0 ? time() + ($text * 24 * 60 * 60) : 0;

        setUser("addDiscountCount" . json_encode($dInfo, JSON_UNESCAPED_UNICODE));
        sendMessage("🔘|لطفا تعداد استفاده این تخفیف را وارد کنید\nبرای نامحدود بودن 0 وارد کنید");
    } else sendMessage("🔘|لطفا فقط عدد بفرستید");
}
if (preg_match('/^addDiscountCount(.*)/', $userInfo['step'], $match) && $text != $cancelText && ($from_id == $admin || $userInfo['isAdmin'] == true)) {
    if (is_numeric($text)) {
        $dInfo = json_decode($match[1], true);
        $dInfo['count'] = $text > 0 ? $text : -1;
        $hashId = RandomString();

        $stmt = $connection->prepare("INSERT INTO `discounts` (`hash_id`, `type`, `amount`, `expire_date`, `expire_count`)
                                        VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssiii", $hashId, $dInfo['type'], $dInfo['amount'], $dInfo['date'], $dInfo['count']);
        $stmt->execute();
        $stmt->close();
        sendMessage("کد تخفیف جدید (<code>$hashId</code>) با موفقیت ساخته شد", $removeKeyboard, "HTML");
        setUser();
        sendMessage("مدیریت کد های تخفیف", getDiscountCodeKeys());
    } else sendMessage("🔘|لطفا فقط عدد بفرستید");
}
if (preg_match('/^delDiscount(\d+)/', $data, $match)) {
    $stmt = $connection->prepare("DELETE FROM `discounts` WHERE `id` = ?");
    $stmt->bind_param("i", $match[1]);
    $stmt->execute();
    $stmt->close();

    alert("کد تخفیف مورد نظر با موفقیت حذف شد");
    editText($message_id, "مدیریت کد های تخفیف", getDiscountCodeKeys());
}
if (preg_match('/^copyHash(.*)/', $data, $match)) {
    sendMessage("<code>" . $match[1] . "</code>", null, "HTML");
}
if ($data == "managePanel" and (($from_id == $admin || $userInfo['isAdmin'] == true))) {

    setUser();
    $msg = "
👤 عزیزم به بخش مدیریت خوشومدی 
🤌 هرچی نیاز داشتی میتونی اینجا طبق نیازهات اضافه و تغییر بدی ، عزیزم $first_name جان اگه از فروش ربات درآمد داری از من حمایت کن تا پروژه همیشه آپدیت بمونه !

🆔 @wizwizch

🚪 /start
";
    editText($message_id, $msg, $adminKeys);
}
if ($data == 'reciveApplications') {
    $stmt = $connection->prepare("SELECT * FROM `needed_sofwares` WHERE `status`=1");
    $stmt->execute();
    $respd = $stmt->get_result();
    $stmt->close();

    $keyboard = [];
    while ($file =  $respd->fetch_assoc()) {
        $link = $file['link'];
        $title = $file['title'];
        $keyboard[] = ['text' => "$title", 'url' => $link];
    }
    $keyboard[] = ['text' => "⤵️ برگرد صفحه قبلی ", 'callback_data' => "mainMenu"];
    $keyboard = array_chunk($keyboard, 1);
    editText($message_id, "
🔸می توانید به راحتی همه فایل ها را (به صورت رایگان) دریافت کنید
📌 شما میتوانید برای راهنمای اتصال به سرویس کانال رسمی مارا دنبال کنید و همچنین از دکمه های زیر میتوانید برنامه های مورد نیاز هر سیستم عامل را دانلود کنید

✅ پیشنهاد ما برنامه V2rayng است زیرا کار با آن ساده است و برای تمام سیستم عامل ها قابل اجرا است، میتوانید به بخش سیستم عامل مورد نظر مراجعه کنید و لینک دانلود را دریافت کنید
", json_encode(['inline_keyboard' => $keyboard]));
}
if ($text == $cancelText) {
    setUser();
    $stmt = $connection->prepare("DELETE FROM `server_plans` WHERE `active`=0");
    $stmt->execute();
    $stmt->close();

    sendMessage('⏳ در حال انتظار ...', $removeKeyboard);
    sendMessage('خب برگشتم عقب اگه کاری داری بگو 😉 | اگه خواستی یکی از گزینه هارو انتخاب کن که کارتو انجام بدم

🚪 /start', $mainKeys);
}
