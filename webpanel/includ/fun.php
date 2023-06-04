<!--
* WizWiz v7.5.3
* https://github.com/wizwizdev/wizwizxui-timebot
* Copyright (c) @wizwizch
-->
<?php

class VolumeInsertionHandler {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleInsertion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'insert_volume_gb') {
            return;
        }

        $postVars = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $priceNames = $postVars["price_save"];
        $volumeNames = $postVars["volume_save"];

        $sqlSaveVolume = "INSERT INTO increase_plan (volume, price) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $sqlSaveVolume);

        if (!$stmt) {
            echo "Error preparing statement: " . mysqli_error($this->conn);
            return;
        }

        mysqli_stmt_bind_param($stmt, 'ss', $volumeNames, $priceNames);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Error updating tables: ". mysqli_stmt_error($stmt);
        } else {
            creatwizwiz();
            header("Location: add-volume.php");
            exit();
        }
    }

    private function sanitizeInput($input) {
        // Remove special characters that can be used for SQL injection attacks
        $sanitized_input = preg_replace("/[!@#\$%\^&\*\(\)_\+><\{\|\}\"':;]/", "", $input);

        // Prevent cross-site scripting (XSS) attacks by escaping HTML entities
        $sanitized_input = htmlspecialchars($sanitized_input);

        return $sanitized_input;
    }



    public function insertVolumeDay() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['action']) || $_POST['action'] !== 'insert_volume_day') {
            return; // requests that don't meet requirements are ignored.
        }

        $price_day_names = $this->sanitizeInput($_POST["price_day_save"]);
        $day_names = $this->sanitizeInput($_POST["days_save"]);

        $sql_save_day = "INSERT INTO increase_day (volume, price) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql_save_day);

        if (!$stmt) {
            echo "Error preparing statement: ". mysqli_error($this->conn);
            return;
        }

        mysqli_stmt_bind_param($stmt, 'ss', $day_names, $price_day_names);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Error updating tables: ". mysqli_stmt_error($stmt);
        } else {
            creatwizwiz();
            header("Location: add-volume.php");
            exit();
        }
    }


    public function handleDeletePlanRequest() {
        if (isset($_GET['deleteplan'])) {
            $id_delete_increase_plan = $_GET['deleteplan'];
            $sql_delete_increase_plan = "DELETE FROM increase_plan WHERE id='$id_delete_increase_plan'";
            $result_delete_increase_plan = mysqli_query($this->conn, $sql_delete_increase_plan);

            if (!$result_delete_increase_plan) {
                die("database error" . mysqli_error($this->conn));
            } else {
                deletewizwiz();
                header("location: add-volume.php");
            }
        }
    }

    public function handleDeleteDayRequest() {
        if (isset($_GET['deleteday'])) {
            $id_delete_increase_day = $_GET['deleteday'];
            $sql_delete_increase_day = "DELETE FROM increase_day WHERE id='$id_delete_increase_day'";
            $result_delete_increase_day = mysqli_query($this->conn, $sql_delete_increase_day);

            if (!$result_delete_increase_day) {
                die("database error" . mysqli_error($this->conn));
            } else {
                deletewizwiz();
                header("location: add-volume.php");
            }
        }
    }
}

function settingsave_state($conn) {
    if (isset($_POST['action']) && $_POST['action'] == 'save_state'){
        foreach($_POST as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[^a-zA-Z0-9\s]/", "", $post_var);
            }
        }
        $requirePhones = $_POST["requirePhone"];
        $requireIranPhones = $_POST["requireIranPhone"];
        $sellStates = $_POST["sellState"];
        $botStates = $_POST["botState"];
        $searchStates = $_POST["searchState"];
        $rewaredTimes = $_POST["rewaredTime"];
        $cartToCartStates = $_POST["cartToCartState"];
        $nextpays = $_POST["nextpay"];
        $zarinpals = $_POST["zarinpal"];
        $nowPaymentWallets = $_POST["nowPaymentWallet"];
        $nowPaymentOthers = $_POST["nowPaymentOther"];
        $walletStates = $_POST["walletState"];
        $rewardChannels = $_POST["rewardChannel"];
        $lockChannels = $_POST["lockChannel"];
        $changeProtocolStates = $_POST["changeProtocolState"];
        $renewAccountStates = $_POST["renewAccountState"];
        $switchLocationStates = $_POST["switchLocationState"];
        $increaseTimeStates = $_POST["increaseTimeState"];
        $increaseVolumeStates = $_POST["increaseVolumeState"];
        $subLinkStates = $_POST["subLinkState"];
        $plandelkhahStates = $_POST["plandelkhahState"];
        $weSwapStates = $_POST["weSwapState"];
        $gbPriceStates = $_POST["gbPrice"];
        $dayPriceStates = $_POST["dayPrice"];
        $BOT_STATES_1 = 'BOT_STATES';
        $lockChannelschange = '@'.$lockChannels;
        $rewardChannelschange = '@'.$rewardChannels;
        $data2 = json_encode(array(
            "requirePhone" => $requirePhones,
            "requireIranPhone" => $requireIranPhones,
            "sellState" => $sellStates,
            "botState" => $botStates,
            "searchState" => $searchStates,
            "rewaredTime" => $rewaredTimes,
            "cartToCartState" => $cartToCartStates,
            "nextpay" => $nextpays,
            "zarinpal" => $zarinpals,
            "nowPaymentWallet" => $nowPaymentWallets,
            "nowPaymentOther" => $nowPaymentOthers,
            "walletState" => $walletStates,
            "rewardChannel" => $rewardChannelschange,
            "lockChannel" => $lockChannelschange,
            "changeProtocolState" => $changeProtocolStates,
            "renewAccountState" => $renewAccountStates,
            "switchLocationState" => $switchLocationStates,
            "increaseTimeState" => $increaseTimeStates,
            "increaseVolumeState" => $increaseVolumeStates,
            "gbPrice" => $gbPriceStates,
            "dayPrice" => $dayPriceStates,
            "subLinkState" => $subLinkStates,
            "plandelkhahState" => $plandelkhahStates,
            "weSwapState" => $weSwapStates
        ));

        $sqlserver_setting2 = "UPDATE setting SET value='$data2',type='$BOT_STATES_1' WHERE id='5'";
        $resultsetting2 = mysqli_query($conn, $sqlserver_setting2);
        if (!$resultsetting2) {
            echo "Error updating tables: " . mysqli_error($conn);

        } else {
            editwizwiz();
            header("location: settings.php");
        }
    }
}

function volume_select_gb($conn){
    $sql_increase_plan = "SELECT * FROM increase_plan ORDER BY id DESC";
    $result_increase_plan = $conn->query($sql_increase_plan);
    while ($row_increase_plan = $result_increase_plan->fetch_assoc()) {
        $result[] = $row_increase_plan;
    }
    return @$result;
}
function volume_select_day($conn){
    $sql_increase_day = "SELECT * FROM increase_day ORDER BY id DESC";
    $result_increase_day = $conn->query($sql_increase_day);
    while ($row_increase_day = $result_increase_day->fetch_assoc()) {
        $result[] = $row_increase_day;
    }
    return @$result;
}



function categories_delete($conn){
    if (isset($_GET['delete'])) {
        $id_delete_categories = $_GET['delete'];
        $sql_delete_categories = "DELETE FROM server_categories WHERE id='$id_delete_categories'";
        $result_delete_categories = mysqli_query($conn, $sql_delete_categories);

        if (!$result_delete_categories) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: category.php");
        }
    }
}

function server_select($conn){
    $sql_categories = "SELECT * FROM server_categories ORDER BY id DESC";
    $result_categories = $conn->query($sql_categories);
    while ($row_categories = $result_categories->fetch_assoc()) {
        $result[] = $row_categories;
    }
    return @$result;
}

function server_insert($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'insert_category') {
        foreach($_POST as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[!@#\$%\^&\*\(\)_\+><\{\|\}\"':;]/", "", $post_var);
            }
        }
        $title_category = $_POST['title_category'];
        $server_ids = '0';
        $parents = '0';
        $steps = '4';
        $actives = '1';
        $insert_category_sql = "INSERT INTO server_categories (server_id,title,parent,step,active) VALUES ('$server_ids','$title_category','$parents','$steps','$actives')";
        $result_category_sql = mysqli_query($conn, $insert_category_sql);
        if (!$result_category_sql) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            creatwizwiz();
            header('Location: category.php');

        }
    }
}


function discounts_select($conn){
    $sql_categories = "SELECT * FROM discounts ORDER BY id DESC";
    $result_categories = $conn->query($sql_categories);
    while ($row_categories = $result_categories->fetch_assoc()) {
        $result[] = $row_categories;
    }
    return @$result;
}
function discounts_delete($conn){
    if (isset($_GET['delete'])) {
        $id_delete_categories = $_GET['delete'];
        $sql_delete_categories = "DELETE FROM discounts WHERE id='$id_delete_categories'";
        $result_delete_categories = mysqli_query($conn, $sql_delete_categories);

        if (!$result_delete_categories) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: discount.php");
        }
    }
}
function discounts_insert($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'insert_discount') {
        foreach($_POST as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[!@#\$%\^&\*\(\)_\+><\{\|\}\"':;]/", "", $post_var);
            }
        }
        $category_discountss = $_POST['category_discounts'];
        $percent_discounts = $_POST['percent_discounts'];
        $discounts_date = $_POST['discounts_date'];
        $discounts_count = $_POST['discounts_count'];
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ'; // string containing all letters of the alphabet
        $hash_ids = substr(str_shuffle($letters), 0, 10); // shuffle the string and take the first 10 characters
        if($discounts_date == '0'){
            $discounts_date = '0';
        }else {
            $discounts_date = time() + ($discounts_date * 24 * 60 * 60);
        }
        $insert_category_sql = "INSERT INTO discounts (hash_id,type,amount,expire_date,expire_count) 
VALUES ('$hash_ids','$category_discountss','$percent_discounts','$discounts_date','$discounts_count')";
        $result_category_sql = mysqli_query($conn, $insert_category_sql);
        if (!$result_category_sql) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            creatwizwiz();
            header('Location: discount.php');

        }
    }
}



function users_insert($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'insert_gift') {
        include '../wizwizxui-timebot/baseInfo.php';
        $chatId = $_POST["id_user"];
        $gifts = $_POST["gift"];
        $urls = $_POST["url"];
        $buttons = $_POST["button"];

        $sql_admins = "SELECT * FROM users where userid='$chatId'";
        $result_admins = $conn->query($sql_admins);
        $row_admins = $result_admins->fetch_assoc();
        $gift_free = ( $gifts + $row_admins['wallet'] );

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $message = '🎁 دوست عزیزم، مبلغ '. $gifts . ' به حساب شما واریز شد';
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => $buttons,
                            'url' => $urls
                        ]
                    ]
                ]
            ])
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);


        $sqlserver_setting = "UPDATE users SET wallet='$gift_free' WHERE userid='$chatId'";
        $resultsetting = mysqli_query($conn, $sqlserver_setting);
        if (!$resultsetting) {
            echo "Error updating tables: " . mysqli_error($conn);

        } else {
            creatwizwiz();
            header("location: gift.php");
        }

    }
}



//index.php
function users_on_off($conn){
    if (isset($_GET['on'])) {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE users SET isAdmin='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($conn, $sql_on_select);
        if (!$res_on_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: index.php");
        }
    }

    if (isset($_GET['off'])) {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE users SET isAdmin='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($conn, $sql_off_select);
        if (!$res_off_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusoffwizwiz();
            header("location: index.php");
        }
    }
}

function users_ban($conn){
    if (isset($_GET['stepon'])) {
        $id_stepon_select = $_GET['stepon'];
        $sql_stepon_select = "UPDATE users SET step='none' WHERE id='$id_stepon_select'";
        $res_stepon_select = mysqli_query($conn, $sql_stepon_select);
        if (!$res_stepon_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: index.php");
        }
    }

    if (isset($_GET['stepoff'])) {
        $id_stepoff_select = $_GET['stepoff'];
        $sql_stepoff_select = "UPDATE users SET step='banned' WHERE id='$id_stepoff_select'";
        $res_stepoff_select = mysqli_query($conn, $sql_stepoff_select);
        if (!$res_stepoff_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusoffwizwiz();
            header("location: index.php");
        }
    }
}
function users_select($conn){
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $results = $conn->query($sql);
    while ($row_users = $results->fetch_assoc()) {
        $result[] = $row_users;
    }
    return @$result;
}
function users_count($conn){
    $user_sum = "SELECT COUNT(*) FROM users";
    $result_users = mysqli_query($conn, $user_sum);
    $row_users = mysqli_fetch_assoc($result_users);
    $show_user = implode($row_users);
    return $show_user;
}
function number_order($conn){
    $amount_order_sum = "SELECT SUM(amount) FROM orders_list";
    $result_amount_order = mysqli_query($conn, $amount_order_sum);
    $row_amount_order = mysqli_fetch_assoc($result_amount_order);
    $show_amount_order = implode($row_amount_order);
    $number_order = number_format($show_amount_order);
    return $number_order;
}
function show_product($conn){
    $product_sum = "SELECT COUNT(*) FROM orders_list";
    $result_product = mysqli_query($conn, $product_sum);
    $row_product = mysqli_fetch_assoc($result_product);
    $show_product = implode($row_product);
    return $show_product;
}
function show_chats_info($conn){
    $chats_info_sum = "SELECT COUNT(*) FROM chats_info";
    $result_chats_info = mysqli_query($conn, $chats_info_sum);
    $row_chats_info = mysqli_fetch_assoc($result_chats_info);
    $show_chats_info = implode($row_chats_info);
    return $show_chats_info;
}
function order_on($conn){
    if (isset($_GET['on'])) {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE orders_list SET status='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($conn, $sql_on_select);
        if (!$res_on_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: orders.php");
        }
    }
}
function order_off($conn){
    if (isset($_GET['off'])) {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE orders_list SET status='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($conn, $sql_off_select);
        if (!$res_off_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusoffwizwiz();
            header("location: orders.php");
        }
    }

}
function order_delete($conn){
    if (isset($_GET['delete'])) {
        $id_delete_orders = $_GET['delete'];
        $sql_delete_orders = "DELETE FROM orders_list WHERE id='$id_delete_orders'";
        $result_delete_orders = mysqli_query($conn, $sql_delete_orders);

        if (!$result_delete_orders) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: orders.php");

        }
    }
}
function orders_list($conn){
    $sql = "SELECT * FROM orders_list ORDER BY id DESC";
    $results = $conn->query($sql);
    if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
            $result[] = $row;
        }
    }
    return @$result;
}
//function orders_list_select($conn){
//        $id_wiw = $_GET['id'];
//        $sql1 = "SELECT * FROM orders_list WHERE server_id='$id_wiw' ORDER BY id DESC ";
//        $result1 = $conn->query($sql1);
//        while ($row1 = $result1->fetch_assoc()) {
//            $result[] = $row1;
//        }
//    return @$result;
//}
function plan_edit($conn){
    if (isset($_GET['edit'])) {
        $id_edit_select = $_GET['edit'];
        $sql12123 = "SELECT * FROM server_plans WHERE id = '$id_edit_select'";
        $result5454 = $conn->query($sql12123);
        while ($row656 = $result5454->fetch_assoc()) {
            $result[] = $row656;
        }
        return @$result;
    }
}
function plan_insert($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'insert_plans') {
        foreach($_POST as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[!@#\$%\^&\*\(\)_\+><\{\|\}\"':;]/", "", $post_var);
            }
        }
        $title_plans = $_POST['title_plan'];
        $protocol_plans = $_POST['protocol_plan'];
        $days_plans = $_POST['days_plan'];
        $volume_plans = $_POST['volume_plan'];
        $type_plans = $_POST['type_plan'];
        $price_plans = $_POST['price_plan'];
        $limitip_plans = $_POST['limitip_plan'];
        $name_category_plans = $_POST['name_category_wizwiz'];
        $name_servers_plans = $_POST['name_servers_wizwiz'];
        $descriptions = $_POST['description'];
        $fileid = '0';
        $pic = '0';
        $active = 1;
        $step = 10;
        $dates = time();
        $rahgozars = 0;
        if ($_POST['inbound_plan']) {
            $inbound_plans = $_POST['inbound_plan'];
        } else {
            $inbound_plans = '0';
        }
        if ($_POST['count_plan']) {
            $count_plans = $_POST['count_plan'];
        } else {
            $count_plans = '0';
        }
        $insert_plans_sql = "INSERT INTO server_plans (fileid,catid,server_id,inbound_id,acount,
                          limitip,title,protocol,days,volume,type,price,descr,pic,active,step,date,rahgozar) 
                          VALUES ('$fileid','$name_category_plans','$name_servers_plans','$inbound_plans','$count_plans',
'$limitip_plans','$title_plans','$protocol_plans','$days_plans','$volume_plans','$type_plans','$price_plans','$descriptions','$pic','$active',
'$step','$dates','$rahgozars')";
        $result_plans_sql = mysqli_query($conn, $insert_plans_sql);
        if (!$result_plans_sql) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            creatwizwiz();
            header('Location: plans.php');

        }
    }
}
function plans($conn) {
    if (isset($_GET['delete'])) {
        $id_delete_server_plans = $_GET['delete'];
        $sql_delete_server_plans = "DELETE FROM server_plans WHERE id='$id_delete_server_plans'";
        $result_delete_server_plans = mysqli_query($conn, $sql_delete_server_plans);

        if (!$result_delete_server_plans) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: plans.php");
        }
    }

    if (isset($_GET['on'])) {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE server_plans SET active='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($conn, $sql_on_select);
        if (!$res_on_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: plans.php");
        }
    }

    if (isset($_GET['off'])) {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE server_plans SET active='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($conn, $sql_off_select);
        if (!$res_off_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusoffwizwiz();
            header("location: plans.php");
        }
    }

}
function rahgozar($conn) {
    if (isset($_GET['delete'])) {
        $id_delete_server_plans = $_GET['delete'];
        $sql_delete_server_plans = "DELETE FROM server_plans WHERE id='$id_delete_server_plans'";
        $result_delete_server_plans = mysqli_query($conn, $sql_delete_server_plans);

        if (!$result_delete_server_plans) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: rahgozar.php");
        }
    }

    if (isset($_GET['on'])) {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE server_plans SET active='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($conn, $sql_on_select);
        if (!$res_on_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: rahgozar.php");
        }
    }

    if (isset($_GET['off'])) {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE server_plans SET active='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($conn, $sql_off_select);
        if (!$res_off_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusoffwizwiz();
            header("location: rahgozar.php");
        }
    }
}

class rahgozar_insert {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function rahgozarRequest() {
        if(isset($_POST['action']) && $_POST['action'] == 'insert_rahgozar') {
            $this->sanitizeInput($_POST);

            $title_plans = $_POST['title_plan'];
            $type_plans = $_POST['type_plan'];
            $protocol_plans = $_POST['protocol_plan'];
            $days_plans = $_POST['days_plan'];
            $volume_plans = $_POST['volume_plan'];
            $price_plans = $_POST['price_plan'];
            $limitip_plans = $_POST['limitip_plan'];
            $name_category_plans = $_POST['name_category_wizwiz'];
            $name_servers_plans = $_POST['name_servers_wizwiz'];
            $descriptions = $_POST['description'];
            $fileid = '0';
            $pic = '0';
            $active = 1;
            $step = 10;
            $dates = time();
            $rahgozar_status = 1;

            if ($_POST['inbound_plan']) {
                $inbound_plans = $_POST['inbound_plan'];
            } else {
                $inbound_plans = '0';
            }

            if ($_POST['acount_plan']) {
                $acount_plans = $_POST['acount_plan'];
            } else {
                $acount_plans = '0';
            }

            $insert_plans_sql = "INSERT INTO server_plans (fileid, catid, server_id, inbound_id, acount,
            limitip, title, protocol, days, volume, type, price, descr, pic, active, step, date, rahgozar)
            VALUES ('$fileid', '$name_category_plans', '$name_servers_plans', '$inbound_plans', '$acount_plans',
            '$limitip_plans', '$title_plans', '$protocol_plans', '$days_plans', '$volume_plans', '$type_plans',
            '$price_plans', '$descriptions', '$pic', '$active', '$step', '$dates', '$rahgozar_status')";

            $result_plans_sql = mysqli_query($this->conn, $insert_plans_sql);

            if (!$result_plans_sql) {
                echo "Error" . die(mysqli_error($this->conn));
            } else {
                creatwizwiz();
                header('Location: rahgozar.php');
            }
        }
    }

    private function sanitizeInput(&$postVar) {
        foreach($postVar as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[!@#\$%\^&\*\(\)_\+><\{\|\}\"':;]/", "", $post_var);
            }
        }
    }

}

class ServerHandler
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function handleRequest()
    {
        if (isset($_GET['on'])) {
            $this->turnOnServer();
        } elseif (isset($_GET['off'])) {
            $this->turnOffServer();
        } elseif (isset($_GET['true'])) {
            $this->setRealityTrue();
        } elseif (isset($_GET['false'])) {
            $this->setRealityFalse();
        } elseif (isset($_GET['port_type_auto'])) {
            $this->setPortTypeRandom();
        } elseif (isset($_GET['port_type_random'])) {
            $this->setPortTypeAuto();
        } elseif (isset($_GET['delete'])) {
            $this->deleteServer();
        }
    }

    private function turnOnServer()
    {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE server_info SET state='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($this->conn, $sql_on_select);
        if (!$res_on_select) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statusonwizwiz();
            header("location: servers.php");
        }
    }

    private function turnOffServer()
    {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE server_info SET state='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($this->conn, $sql_off_select);
        if (!$res_off_select) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statusoffwizwiz();
            header("location: servers.php");
        }
    }

    private function setRealityTrue()
    {
        $id_true_select = $_GET['true'];
        $sql_true_select = "UPDATE server_config SET reality='true' WHERE id='$id_true_select'";
        $res_true_select = mysqli_query($this->conn, $sql_true_select);
        if (!$res_true_select) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statustruewizwiz();
            header("location: servers.php");
        }
    }

    private function setRealityFalse()
    {
        $id_false_select = $_GET['false'];
        $sql_false_select = "UPDATE server_config SET reality='false' WHERE id='$id_false_select'";
        $res_false_select = mysqli_query($this->conn, $sql_false_select);
        if (!$res_false_select) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statusfalsewizwiz();
            header("location: servers.php");
        }
    }

    private function setPortTypeRandom()
    {
        $id_port_type_auto = $_GET['port_type_auto'];
        $sql_port_type_auto = "UPDATE server_config SET port_type='random' WHERE id='$id_port_type_auto'";
        $res_port_type_auto = mysqli_query($this->conn, $sql_port_type_auto);
        if (!$res_port_type_auto) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statusportwizwiz();
            header("location: servers.php");
        }
    }

    private function setPortTypeAuto()
    {
        $id_port_type_random = $_GET['port_type_random'];
        $sql_port_type_random = "UPDATE server_config SET port_type='auto' WHERE id='$id_port_type_random'";
        $res_port_type_random = mysqli_query($this->conn, $sql_port_type_random);
        if (!$res_port_type_random) {
            echo "Error" . die(mysqli_error($this->conn));
        } else {
            statusportwizwiz();
            header("location: servers.php");
        }
    }



    private function deleteServer() {
        $id_delete_orders = $_GET['delete'];
        $tables = array("server_info", "server_config");
        foreach ($tables as $table) {
            $query = "DELETE FROM $table WHERE id='$id_delete_orders'";
            mysqli_query($this->conn, $query);
        }
        if (!$query) {
            die("database error" . mysqli_error($this->conn));
        } else {
            deletewizwiz();
            header("location: servers.php");
        }
    }

    function typesserver($conn) {
        if (isset($_GET['type_normal'])) {
            $id_type_normal = $_GET['type_normal'];
            $sql_type_normal = "UPDATE server_config SET type='alireza' WHERE id='$id_type_normal'";
            $res_type_normal = mysqli_query($conn, $sql_type_normal);
            if (!$res_type_normal) {
                echo "خطا" . die(mysqli_error($conn));
            } else {
                statustypetwizwiz();
                header("location: servers.php");
            }
        }

        if (isset($_GET['type_sanaei'])) {
            $id_type_sanaei = $_GET['type_sanaei'];
            $sql_type_sanaei = "UPDATE server_config SET type='normal' WHERE id='$id_type_sanaei'";
            $res_type_sanaei = mysqli_query($conn, $sql_type_sanaei);
            if (!$res_type_sanaei) {
                echo "خطا" . die(mysqli_error($conn));
            } else {
                statustypetwizwiz();
                header("location: servers.php");
            }
        }
        if (isset($_GET['type_alireza'])) {
            $id_type_alireza = $_GET['type_alireza'];
            $sql_type_alireza = "UPDATE server_config SET type='sanaei' WHERE id='$id_type_alireza'";
            $res_type_alireza = mysqli_query($conn, $sql_type_alireza);
            if (!$res_type_alireza) {
                echo "خطا" . die(mysqli_error($conn));
            } else {
                statustypetwizwiz();
                header("location: servers.php");
            }
        }
    }

}


function server_edit($conn){
    if (isset($_GET['edit'])) {
        $id_edit_select = $_GET['edit'];
        $sql12123 = "SELECT server_info.*, server_config.* FROM server_info INNER JOIN server_config ON server_info.id = server_config.id WHERE server_info.id = '$id_edit_select'";
        $result5454 = $conn->query($sql12123);
        while ($row656 = $result5454->fetch_assoc()) {
            $result[] = $row656;
        }
        return @$result;
    }
}


function settings_select_admin($conn){
    $idadmin = '1';
    $sql_admins = "SELECT * FROM admins where id='$idadmin'";
    $result_admins = $conn->query($sql_admins);
    while ($row_admins = $result_admins->fetch_assoc()){
        $result[] = $row_admins;
    }
    return @$result;


}
function settings_save_admin($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'save_admin') {
        $usernames = $_POST["username"];
        $passwords = $_POST["password"];
        $sqlserver_setting = "UPDATE admins SET username='$usernames',password='$passwords' WHERE id='1'";
        $resultsetting = mysqli_query($conn, $sqlserver_setting);
        if (!$resultsetting) {
            echo "Error updating tables: " . mysqli_error($conn);

        } else {
            editwizwiz();
            header("location: settings.php");
        }

    }
}
function settings_backup_channel($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'save_backup') {
        foreach($_POST as &$post_var) {
            if(is_string($post_var)) {
                $post_var = preg_replace("/[^a-zA-Z0-9\s]/", "", $post_var);
            }
        }
        $backupchannels = $_POST["backupchannel"];


        $dsd = '-'.$backupchannels;
        $sqlserver_setting = "UPDATE admins SET backupchannel='$dsd' WHERE id='1'";
        $resultsetting = mysqli_query($conn, $sqlserver_setting);
        if (!$resultsetting) {
            echo "Error updating tables: ". mysqli_error($conn);
        } else {
            editwizwiz();
            header("location: settings.php");
        }
    }
}
function software($conn){
    if (isset($_GET['on'])) {
        $id_on_select = $_GET['on'];
        $sql_on_select = "UPDATE needed_sofwares SET status='1' WHERE id='$id_on_select'";
        $res_on_select = mysqli_query($conn, $sql_on_select);
        if (!$res_on_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else {
            statusonwizwiz();
            header("location: software.php");
        }
    }

    if (isset($_GET['off'])) {
        $id_off_select = $_GET['off'];
        $sql_off_select = "UPDATE needed_sofwares SET status='0' WHERE id='$id_off_select'";
        $res_off_select = mysqli_query($conn, $sql_off_select);
        if (!$res_off_select) {
            echo "خطا" . die(mysqli_error($conn));
        } else{
            statusoffwizwiz();
            header("location: software.php");
        }
    }


    if (isset($_GET['delete'])) {
        $id_delete_software = $_GET['delete'];
        $sql_delete_software = "DELETE FROM needed_sofwares WHERE id='$id_delete_software'";
        $result_delete_software = mysqli_query($conn, $sql_delete_software);

        if (!$result_delete_software) {
            die("خطای پایگاه داده" . mysqli_error($conn));
        } else {
            deletewizwiz();
            header("location: software.php?remove=1");
        }
    }

}
function software_select($conn){
    $sql_sofwares = "SELECT * FROM needed_sofwares ORDER BY id DESC";
    $result_sofwares = $conn->query($sql_sofwares);
    while ($row_sofwares = $result_sofwares->fetch_assoc()) {
        $result[] = $row_sofwares;
    }
    return @$result;
}
function software_insert($conn){
    if(isset($_POST['action']) && $_POST['action'] == 'insert_software') {
        $title_software = $_POST['title_software'];
        $url_software = $_POST['url_software'];
        $status_s = 1;
        $insert_software_sql = "INSERT INTO needed_sofwares (title,link,status) VALUES ('$title_software','$url_software','$status_s')";
        $result_software_sql = mysqli_query($conn, $insert_software_sql);
        if (!$result_software_sql) {
            echo "خطا" . die(mysqli_error($conn));
        } else {

            header('Location: software.php');

        }
    }
}


function volumes_delete($conn){
    class DeleteCategory {
        private $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        public function delete($id) {
            $sql = "DELETE FROM increase_order WHERE id='$id'";
            $result = mysqli_query($this->conn, $sql);

            if (!$result) {
                die("database error" . mysqli_error($this->conn));
            } else {
                $this->deletewizwiz();
                header("location: volume.php");
            }
        }
    }
    if (isset($_GET['delete'])) {
        $id_delete_categories = $_GET['delete'];
        $delete_category = new DeleteCategory($conn);
        $delete_category->delete($id_delete_categories);
    }
}