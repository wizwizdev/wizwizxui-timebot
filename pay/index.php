<?php 
//==================================================
include '../baseInfo.php';
include '../config.php';

//====================//  Get  //==============================
$hash_id = $_GET['hash_id'];
if(!isset($_GET['zarinpal']) && !isset($_GET['nowpayment']) && !isset($_GET['nextpay'])){
    showForm("درگاه پرداخت شناسایی نشد!");
    exit();
}

$stmt = $connection->prepare("SELECT * FROM `pays` WHERE `hash_id` = ? AND `state` = 'pending'");
$stmt->bind_param("s", $hash_id);
$stmt->execute();
$payInfo = $stmt->get_result();
$stmt->close();
if(mysqli_num_rows($payInfo)==0){
    showForm("کد پرداخت یافت نشد");
}else{
    $payParam = $payInfo->fetch_assoc();
    
    $fid = $payParam['plan_id'];
    
    $stmt = $connection->prepare("SELECT * FROM `server_plans` WHERE `id`=?");
    $stmt->bind_param("i", $fid);
    $stmt->execute();
    $file_detail = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $server_id = $file_detail['server_id'];
    $acount = $file_detail['acount'];
    $inbound_id = $file_detail['inbound_id'];
    
    
    $orderId= $payParam['id'];
    $amount = $payParam['price'];
    $payType = $payParam['type'];
    //========================== // config // ==============================
    
    
    if($acount == 0 and $inbound_id != 0 && $payType == "BUY_SUB"){
        showForm('ظرفیت این کانکشن پر شده است');
        exit;
    }
    if($inbound_id == 0 && $payType == "BUY_SUB") {
        $stmt = $connection->prepare("SELECT * FROM `server_info` WHERE `id`=?");
        $stmt->bind_param("i", $server_id);
        $stmt->execute();
        $server_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($server_info['ucount'] == 0) {
            showForm('ظرفیت این سرور پر شده است');
            exit;
        }
    }elseif($payType == "BUY_SUB"){
        if($acount != 0 && $acount < $text){
            showForm("روی این پلن فقط $acount اکانت میشه ساخت");
            exit();
        }
    }
    
    if($payType == "BUY_SUB") $type = "خرید اکانت";
    elseif($payType == "RENEW_ACCOUNT"){
        $type = "تمدید اکانت";
        $oid = $payParam['plan_id'];
        $stmt = $connection->prepare("SELECT * FROM `orders_list` WHERE `id` = ?");
        $stmt->bind_param("i", $oid);
        $stmt->execute();
        $order = $stmt->get_result();
        $stmt->close();
        if($order->num_rows == 0){
            showForm($mainValues['config_not_found']);
            exit();
        }

    }
    elseif($payType == "RENEW_SCONFIG") $type = "تمدید اکانت";
    elseif($payType == "INCREASE_WALLET") $type ="شارژ کیف پول";
    elseif(preg_match('/^INCREASE_DAY_(\d+)_(\d+)/',$payType)) $type = "افزایش زمان اکانت";
    elseif(preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)/',$payType)) $type = "افزایش حجم اکانت";
    
    
    
    $stmt = $connection->prepare("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    $stmt->execute();
    $paymentKeys = $stmt->get_result()->fetch_assoc()['value'];
    if(!is_null($paymentKeys)) $paymentKeys = json_decode($paymentKeys,true);
    else $paymentKeys = array();
    $stmt->close();
    
    if(isset($_GET['nowpayment'])){
        $dollarPrice = json_decode(file_get_contents('https://api.tetherland.com/currencies'),true)['data']['currencies']['USDT']['price'];
        $base_url = 'https://api.nowpayments.io/v1/invoice';
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $paymentKeys['nowpayment'], 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'price_amount' => ($amount / $dollarPrice),
            'price_currency' => 'usd',
            'order_id' => $hash_id,
            'order_description' => $type,
            'success_url' => $botUrl . 'pay/back.php?nowpayment',
            'is_fee_paid_by_user' => true
        ]));
        curl_setopt($ch, CURLOPT_URL, $base_url);
        $res = json_decode(curl_exec($ch));
        $payid = $res->id;
        
        $stmt = $connection->prepare("UPDATE `pays` SET `payid` = ? WHERE `hash_id` = ?");
        $stmt->bind_param("is", $payid, $hash_id);
        $stmt->execute();
        $stmt->close();
        header('Location: '.$res->invoice_url);
    }
    elseif(isset($_GET['zarinpal'])){
        $CallbackURL = $botUrl . "pay/back.php?zarinpal&hash_id=$hash_id";
        $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
        $result = $client->PaymentRequest([
        'MerchantID' => $paymentKeys['zarinpal'],
        'Amount' => $amount,
        'Description' => "خرید اکانت",
        'Email' => $Email,
        'Mobile' => $Mobile,
        'CallbackURL' => $CallbackURL,
        ]);
        //==============================================================
        Header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority.'/ZarinGate');
    }
    elseif(isset($_GET['nextpay'])){        
        $Description = "خرید اشتراک";
        $CallbackURL = $botUrl . "pay/back.php?nextpay&hash_id=$hash_id";
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://nextpay.org/nx/gateway/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'api_key='.$paymentKeys['nextpay'] .'&amount='.$amount.'&order_id='.$orderId.'&currency=IRT&callback_uri='.$CallbackURL,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        if ($response->code == '-1'){
            $startGateWayUrl = "https://nextpay.org/nx/gateway/payment/".$response->trans_id;
            $transId = $response->trans_id;            
            $stmt = $connection->prepare("UPDATE `pays` SET `payid` = ? WHERE `hash_id` = ?");
            $stmt->bind_param("ss", $transId, $hash_id);
            $stmt->execute();
            $stmt->close();
            header('location: '.$startGateWayUrl);
        } else {
            showForm("تراکنش با خطا مواجه شده است");
        }
        
        
    }
}


function showForm($msg){
    ?>
    <html dir="rtl">
        <head>
            <script>
          (function(w,d,s,l,i){w[l]=w[l]||[];
            w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js', });
            var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+'&gtm_auth=&gtm_preview=&gtm_cookies_win=x';
            f.parentNode.insertBefore(j,f);
          })(window,document,'script','dataLayer','GTM-MSN6P6G');</script>
          <meta charset="utf-8"><meta name="viewport" content="width=device-width">
    		<title><?php echo $msg;?></title>
            <meta name="next-head-count" content="4">
            <link rel="stylesheet" href="../assets/20bb620751bbea45.css">
            <noscript data-n-css=""></noscript>
    
        </head>
        <body>
            <div id="__next">
                <section class="ant-layout ant-layout-rtl PayPing-layout background--primary justify-center" style="min-height:100vh">
                    <header class="ant-layout-header PayPing-header-logo justify-center align-center"></header>
                    <main class="ant-layout-content justify-center align-center flex-column">
                        <div class="ant-row ant-row-center ant-row-rtl PayPing-row w-100">
                            <div class="ant-col PayPing-col PayPing-error-card ant-col-xs-23 ant-col-rtl ant-col-sm-20 ant-col-md-16 ant-col-lg-12 ant-col-xl-8 ant-col-xxl-6">
                                <div class="py-2 align-center color--danger flex-column">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="PayPing-icon" stroke-width="1" width="100">
                                        <circle cx="12" cy="12" r="11"></circle>
                                        <path d="M15.3 8.7l-6.6 6.6M8.7 8.7l6.6 6.6"></path>
                                    </svg>
                                    <div class="py-2"><?php echo $msg; ?></div>
                                </div>
                            </div>
                        </div>
                    </main>
                </section>
            </div>
        </body>
    </html>
<?php
}
?>
