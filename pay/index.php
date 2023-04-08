<?php 
//==================================================
include '../baseInfo.php';
include '../config.php';

//====================//  Get  //==============================
$hash_id = $_GET['hash_id'];
if(!isset($_GET['zarinpal']) && !isset($_GET['nowpayment'])){
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
    $orderId= $payParam['id'];
    $amount = $payParam['price'];
    $payType = $payParam['type'];
    //========================== // config // ==============================
    
    if($payType == "BUY_SUB") $type = "خرید اشتراک";
    elseif($payType == "RENEW_ACCOUNT") $type = "تمدید اکانت";
    elseif($payType == "INCREASE_WALLET") $type ="شارژ کیف پول";
    elseif(preg_match('/^INCREASE_DAY_(\d+)_(\d+)_(.+)_(\d+)/',$payType)) $type = "افزایش زمان اکانت";
    elseif(preg_match('/^INCREASE_VOLUME_(\d+)_(\d+)_(.+)_(\d+)/',$payType)) $type = "افزایش حجم اکانت";
    
    
    if(isset($_GET['nowpayment'])){
        $dollarPrice = json_decode(file_get_contents('https://api.tetherland.com/currencies'),true)['data']['currencies']['USDT']['price'];
        $base_url = 'https://api.nowpayments.io/v1/invoice';
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $nowPaymentKey, 'Content-Type: application/json']);
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
    }elseif(isset($_GET['zarinpal'])){
        $CallbackURL = $botUrl . "pay/back.php?zarinpal&hash_id=$hash_id";
        $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
        $result = $client->PaymentRequest([
        'MerchantID' => $zarinpalId,
        'Amount' => $amount,
        'Description' => "خرید اشتراک",
        'Email' => $Email,
        'Mobile' => $Mobile,
        'CallbackURL' => $CallbackURL,
        ]);
        //==============================================================
        Header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority.'/ZarinGate');
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
            <link rel="preload" href="../install/css/20bb620751bbea45.css" as="style">
            <link rel="stylesheet" href="../install/css/20bb620751bbea45.css" data-n-g="">
            <noscript data-n-css=""></noscript>
        </head>
        <body>
            <noscript>
                <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MSN6P6G&gtm_auth=&gtm_preview=&gtm_cookies_win=x"
                height="0" width="0" style="display:none;visibility:hidden" id="tag-manager"></iframe>
            </noscript>
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
                    <footer class="ant-layout-footer PayPing-footer">
                        <div class="ant-row ant-row-center ant-row-rtl PayPing-row w-100">
                            <div class="ant-col PayPing-col PayPing-footer-links ant-col-xs-23 ant-col-rtl ant-col-sm-22 ant-col-md-20 ant-col-lg-18 ant-col-xl-14 ant-col-xxl-10"></div>
                        </div>
                    </footer>
                </section>
            </div>
        </body>
    </html>
<?php
}
?>