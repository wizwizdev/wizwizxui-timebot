<?php
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on'){
    form("ربات باید روی دامنه ی دارای ssl فعال نصب بشه!");
    exit();
}
if(!file_exists("../createDB.php") || !file_exists("../baseInfo.php") || !file_exists("../bot.php") || !file_exists("../config.php")){
    form("فایل های مورد نیاز ربات یافت نشد");
    exit();
}

$fileAddress = str_replace(["install.php","?webhook", "?install", "?updateBot", "?update", "/install"],"", "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$botUrl = $fileAddress . "bot.php";

    if(isset($_REQUEST['webhook'])){
    	$botToken = $_POST['bottoken'];
    	$adminId = $_POST['adminid'];
    	$dbName = $_POST['dbname'];
    	$dbUser = $_POST['dbuser'];
    	$dbPassword = $_POST['dbpassword'];
    	$channelLock = $_POST['channelLock'];
    	$walletMerchant = $_POST['walletmerchant'];
    	$pursant = $_POST['pursant'];
    	$nowPaymentKey= $_POST['nowpaymentkey'];
    	$zarinpalKey = $_POST['zarinpalkey'];

    	
    	$connection = new mysqli('localhost',$dbUser,$dbPassword,$dbName);
    	if($connection->connect_error){
    	    form("خطای دیتابیس: " . $connection->connect_error);
    	    exit();
    	}
        $checkBot = json_decode(file_get_contents("https://api.telegram.org/bot" . $botToken . "/getwebhookinfo"));
        if($checkBot->ok){
            if($checkBot->result->url != ""){
                form("این ربات قبلاً نصب شده");
                exit();
            }
        }else{
            form("رباتی با این توکن یافت نشد");
            exit();
        }

     	$baseInfo = file_get_contents("../baseInfo.php");
     	$baseInfo = str_replace(['[NOWPAYMENTKEY]','[ZARINPALKEY]','[BOTTOKEN]','[DBUSERNAME]','[DBPASSWORD]','[DBNAME]','[ADMIN]','[CHANNELLOCK]','[BOTURL]','[WALLET]','[PURSANT]'],
     	              [$nowPaymentKey, $zarinpalKey, $botToken, $dbUser, $dbPassword, $dbName, $adminId, $channelLock, $fileAddress, $walletMerchant, $pursant],
     	                        $baseInfo);
        file_put_contents("../baseInfo.php", $baseInfo);
        file_get_contents($fileAddress. "createDB.php");
        $response = json_decode(file_get_contents("https://api.telegram.org/bot" . $botToken . "/setWebhook?url=" . $botUrl));
        if($response->ok){
            file_get_contents("https://api.telegram.org/bot" . $botToken . "/sendMessage?chat_id=" . $adminId . "&text=✅| ربات ویزویز با موفقیت نصب شد");
            form("ربات با موفقیت نصب شد", false);
        }
    }elseif(isset($_REQUEST['install'])){
        $baseInfo = file_get_contents("../baseInfo.php");
        if(!strstr($baseInfo, '[NOWPAYMENTKEY]') || !strstr($baseInfo, '[ZARINPALKEY]') 
                        || !strstr($baseInfo, '[BOTTOKEN]') || !strstr($baseInfo, '[DBUSERNAME]')
                        || !strstr($baseInfo, '[DBPASSWORD]') || !strstr($baseInfo, '[DBNAME]')
                        || !strstr($baseInfo, '[ADMIN]') || !strstr($baseInfo, '[CHANNELLOCK]')
                        || !strstr($baseInfo, '[BOTURL]') || !strstr($baseInfo,'[WALLET]') || !strstr($baseInfo, '[PURSANT]')){
            form('روی این سورس قبلاً رباتی نصب شده!');
            exit();
        }
        showForm("install");
    }elseif(isset($_REQUEST['update'])){
        showForm("update");
    }
    elseif(isset($_REQUEST['updateBot'])){
        if (!file_exists("update.php")){
            echo '<script type="text/javascript">alert("فایل آپدیت یافت نشد");history.go(-1)</script>';
            exit();
        }
    	require "update.php";
    	require "baseInfo.php";
    	
    	
    	$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
    	
    	if($connection->connect_error){
    	    form("خطای دیتابیس: " . $connection->connect_error);
    	    exit();
    	}
        
        foreach($arrays as $query){
            $connection->query($query);
        }
        form("ربات با موفقیت آپدیت شد",false);
    }
    else{
        showForm("unknown");
    }
?>
<?php 
function showForm($type){
?>
    <html lang="en">
        <head>
          <script>
          (function(w,d,s,l,i){w[l]=w[l]||[];
            w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js', });
            var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+'&gtm_auth=&gtm_preview=&gtm_cookies_win=x';
            f.parentNode.insertBefore(j,f);
          })(window,document,'script','dataLayer','GTM-MSN6P6G');</script>
          <meta charset="utf-8"><meta name="viewport" content="width=device-width">
    		<title><?php if($type=="unknown") echo "نصب و آپدیت خودکار ویزویز";
    		elseif ($type=="install") echo "نصب ربات";
    		elseif ($type=="update") echo "آپدیت ربات";
    		?></title>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link type="text/css" href="css/style.css" rel="stylesheet" />
            <style>
                body{
                    direction: rtl;
                    padding-top: 15px;
                    text-align: center;
                    background: #345987;
                }

                button{
                    cursor: pointer;
                    font-size: 18px;
                    width: 90%;
                    margin-bottom: 10px;
                    border-radius: 10px;
                    padding: 10px;
                    border: 1px #eae1e1 solid;
                    font-family: iransans !important;
                }
                 type[input]{
                    direction: rtl;
                }
            </style>
        </head>
        <body>
                                <?php if ($type=="unknown"){?>

                                    <div>
                                        <img src="image/logo.png" width="200px">
                                    </div>
                                    <br>
                                <div>
        							<a href="./install.php?install">
                                        <button style="background-color: #e0eeee;
                                        border: none;font-weight: 600;color: #000509" type="button">
                                            <span>نصب ربات</span>
                                        </button>
                                    </a>
        							<a href="./install.php?update">
                                        <button style="margin: 5px;background-color: #eccfde;
                                        border: none;font-weight: 600;color: #620738">
                                            <span>آپدیت ربات</span>
                                        </button>
                                    </a>
                                </div>
								<div style="margin-top: 5px">
                                    <span>
                                        <a target="_blank" href="https://t.me/wizwizch">
                                        <button style="width: 150px;margin: 5px;padding: 3px;font-size: 16px">
                                            <span>کانال تلگرام</span>
                                        </button>
                                    </a>
                                    </span>
                                        <span>
                                        <a target="_blank" href="https://t.me/wizwizdev">
                                        <button style="width: 150px;margin: 5px;padding: 3px;font-size: 16px">
                                            <span>گروه تلگرام</span>
                                        </button>
                                    </a>
                                    </span>
                                </div>
                                <?php }elseif($type=="update"){
                                    ?>
                                    <h1 style="font-size: 25px">لطفا فایل آپدیت رو درون پوشه ی نصب قرار داده و دکمه زیر رو بزنید</h1>
                                    <a href="./install.php?updateBot">
                                        <button type="button" class="ant-btn ant-btn-primary ant-btn-block ant-btn-rtl PayPing-button always-white">
                                            <span>آپدیت ربات</span>
                                        </button>
                                    </a>
                                    <?php
                                }
                                elseif($type=="install"){ ?>
                                
                                <h1>نصب ربات ویزویز</h1>
                                    <div class="container">
                                        <form id="contact" action="install.php?webhook" method="post">
                                    <h3>لطفا اطلاعات خواسته شده را وارد کنید</h3>
                                    <h4>نظرات و پیشنهادات خود را در گروه تلگرامی wizwizdev ارسال کنید</h4>
                                            <fieldset>
                                                <input placeholder="توکن ربات" type="text" name="bottoken" autocomplete="off" required >
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="آیدی عددی ادمین" type="text" name="adminid" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="اسم دیتابیس" type="text" name="dbname" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="یوزرنیم دیتابیس" type="text" name="dbuser" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="پسورد دیتابیس" type="text" name="dbpassword" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="آیدی کانال با @" type="text" name="channelLock" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="شماره کارت یا ولت" type="text" name="walletmerchant" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="کلید درگاه NowPayment ( در صورت نداشتن عدد 0 وارد کنید )" type="text" name="nowpaymentkey" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="مرچنت کد درگاه زرین پال ( در صورت نداشتن عدد 0 وارد کنید )" type="text" name="zarinpalkey" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <input placeholder="پورسانت لطفا عدد 10 وارد کنید ( به زودی )" type="text" name="pursant" autocomplete="off" required>
                                            </fieldset>
                                            <fieldset>
                                                <button class="btninstall" type="submit">نصب ربات</button>
                                            </fieldset>
											<p style="font-size:13px">Made with 🖤 in <a target="_blank" href="https://github.com/wizwizdev/wizwizxui-timebot">wizwiz</a></p>
                                        </form>
                                    </div>
                                    <br>
                                    <br>

                                <?php } ?>
        </body>
    </html>
<?php
}
function form($msg, $error = true){
    ?>
    
        <html dir="rtl">
        <head>
            <script type="text/javascript" async="" src="https://www.googletagmanager.com/gtag/js?id=G-67LWNZSW5B&amp;l=dataLayer&amp;cx=c"></script>
            <script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script>
            <script async="" src="https://www.googletagmanager.com/gtm.js?id=GTM-MSN6P6G&amp;gtm_auth=&amp;gtm_preview=&amp;gtm_cookies_win=x"></script>
            <script>
          (function(w,d,s,l,i){w[l]=w[l]||[];
            w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js', });
            var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+'&gtm_auth=&gtm_preview=&gtm_cookies_win=x';
            f.parentNode.insertBefore(j,f);
          })(window,document,'script','dataLayer','GTM-MSN6P6G');</script>
          <meta charset="utf-8"><meta name="viewport" content="width=device-width">
            <title>نصب ربات ویزویز</title>
            <meta name="next-head-count" content="4">
            <link rel="stylesheet" href="css/20bb620751bbea45.css" data-n-g="">
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
                                    <?php if ($error == true){ ?> <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="PayPing-icon" stroke-width="1" width="100">
                                        <circle cx="12" cy="12" r="11"></circle>
                                        <path d="M15.3 8.7l-6.6 6.6M8.7 8.7l6.6 6.6"></path>
                                    </svg>
                                    <?php }?>
                                    <div class="py-2"> <?php echo $msg ?></div>
                                </div>
                            </div>
                        </div>
                        <footer class="ant-layout-footer PayPing-footer">
                            <span class="ant-typography ant-typography-rtl PayPing-typography PayPing-typography-text PayPing-text-body2 white PayPing-footer-securePay" direction="rtl">ویزویز</span>
                        </footer>
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