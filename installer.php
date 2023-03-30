<?php
session_start();
error_reporting(0);
const VERSION = '0.1';
const TITLE = 'نصب آسان ربات WizWiz';

const CONFIG_FILE = 'baseInfo.php';
const SQL_FILE = 'wizwiz.sql';


$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . str_replace('installer.php', '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

function go($url = ''): void
{

    if (!headers_sent()) {
        header('Location: ' . $url);
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
    }
    exit;
}

if (file_exists('.install')) {
    die("<h1 style='font-size: 24px;padding-top: 10px'>The configuration is already done.</h1>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $configContent = '
    <?php
    error_reporting(0);
	$botToken = "<TOKEN>";
	$dbUserName = "<DBUSER>";
	$dbPassword = "<DBPASS>";
	$dbName = "<DBNAME>"; 
	$admin = <ADMIN>;  
	$channelLock = "<CHANNEL>";
	$botUrl = "<URL>";
	$walletwizwiz = "<WALLET>";
    ?>';


    $sql = file_get_contents(SQL_FILE);

    $Inputs = ['token', 'adminID', 'channelID', 'url', 'wallet', 'dbuser', 'dbname'];

    foreach ($Inputs as $Input) {
        if (empty($_POST[$Input])) {
            $_SESSION['msg'] = ['type' => 'danger', 'message' => "لطفا تمام فیلد های بالا را پر کنید !"];
            go('./installer.php');
            exit('val');
        }
        $$Input = $_POST[$Input];
    }

    $dbpass = $_POST['dbpass'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=" . $dbname, $dbuser, $dbpass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = file_get_contents(SQL_FILE);
        $qr = $conn->exec($sql);
    } catch (PDOException $e) {
        $_SESSION['msg'] = ['type' => 'danger', 'message' => "خطا در اتصال به دیتابیس: \n" . $e->getMessage()];
        go('./installer.php');
        exit('db');
    }

    $configContent = str_replace('<TOKEN>', $token, $configContent);
    $configContent = str_replace('<DBUSER>', $dbuser, $configContent);
    $configContent = str_replace('<DBPASS>', $dbpass, $configContent);
    $configContent = str_replace('<DBNAME>', $dbname, $configContent);
    $configContent = str_replace('<ADMIN>', $adminID, $configContent);
    $configContent = str_replace('<CHANNEL>', $channelID, $configContent);
    $configContent = str_replace('<URL>', $url, $configContent);
    $configContent = str_replace('<WALLET>', $wallet, $configContent);
    file_put_contents(CONFIG_FILE, $configContent);



    $url = "https://api.telegram.org/bot" . $token . "/setWebhook?url=" . $url . "bot.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        $setWebHook = json_decode($res);
    }



    if ($setWebHook->ok) {
        $_SESSION['msg'] = ['type' => 'success', 'message' => "ربات با موفقیت فعال شد! <br> " . json_encode($setWebHook) . "<br><br> در قسمت Common Settings حالت Once Per Minute(* * * * *) را انتخاب کنید<br>
         - در قسمت Command لطفا ادرس زیر را وارد کنید:
         <br>
         <code class='ltr'>
         /usr/bin/php -q " . __DIR__ . "/messagewizwiz.php >/dev/null 2>&1
         </code>
         <br>
         - همین مراحل را برای فایل warnUsage.php تکرار کنید:
         <br>
         <code class='ltr'>
         /usr/bin/php -q " . __DIR__ . "/warnUsage.php >/dev/null 2>&1
         </code>
         "];
        go('./installer.php');
    } else {
        $_SESSION['msg'] = ['type' => 'danger', 'message' => "خطا در فعال سازی ربات! \n " . json_encode($setWebHook)];
        go('./installer.php');
    }
}
?>

<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
        }

        .ltr {
            direction: ltr;
        }
        .mytitle {
            color: #6c757d;
        }
        .mytitle::after{
            content: "— ";
        }
        .mytitle::before{
            content: " —";
        }
    </style>
    <title><?= TITLE ?></title>
</head>

<body class="">

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1><?= TITLE ?></h1>
            </div>

            <div class="col-12 box-step rounded">
                <form method="POST" action="">
              
                    <div class="mb-3">
                        <label for="token" class="form-label">توکن ربات</label>
                        <textarea class="form-control ltr" id="token" name="token" rows="2" placeholder="1238767123:AAFyJRuJAq0W61pys7IuDHf9Y5kGX1PhnqU : مانند نمونه " required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="adminID" class="form-label">آیدی عددی مدیر ربات</label>
                        <input type="number" class="form-control" id="adminID" name="adminID" aria-describedby="adminIDA" placeholder="123456789" required>
                        <div id="adminIDA" class="form-text"> آیدی عددی یا شناسه کاربری اکانت ادمین را از این ربات بگیرید : <a href="https://t.me/userinfobot">@UserInfoBot</a></div>
                    </div>
                    <div class="mb-3">
                        <label for="channel" class="form-label">آیدی کانال (عضویت اجباری) <span class="text-danger">با @</span></label>
                        <input type="text" class="form-control ltr" id="channel" name="channelID" placeholder="@MYchannel : مانند نمونه " required>
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label">آدرس دامنه</label>
                        <input type="text" class="form-control ltr" id="url" name="url" placeholder="https://yourdomain.com/ : مانند نمونه " value="<?= $actual_link ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="wallet" class="form-label">شماره کارت یا آدرس کیف پول</label>
                        <input type="text" class="form-control ltr" name="wallet" id="wallet" placeholder="TNGhLBmiTHnLsUyNDQUxga4sDKA96dM2S3 : برای مثال " required>
                    </div>
                   
                    <h1 class="text-center mytitle mt-3" style="font-size: 24px;">
                        کانفیگ <cite title="Source Title">اتصال به دیتابیس</cite>
                    </h1>
                    <div class="mb-3">
                        <label for="dbuser" class="form-label">نام کاربری دیتابیس</label>
                        <input type="text" class="form-control ltr" name="dbuser" id="dbuser" placeholder="db-wizwizbot  : برای مثال " required>
                    </div>
                    <div class="mb-3">
                        <label for="dbpass" class="form-label">پسورد دیتابیس</label>
                        <input type="text" class="form-control ltr" name="dbpass" id="dbpass" placeholder="@ABC-123-@#$%  : برای مثال ">
                    </div>
                    <div class="mb-3">
                        <label for="dbname" class="form-label">نام دیتابیس</label>
                        <input type="text" class="form-control ltr" name="dbname" id="dbname" placeholder="wizwiz  : برای مثال " required>
                    </div>

                    <div class="d-grid gap-2 col-6 mx-auto mt-4">
                        <button type="submit" class="btn btn-primary">نصب ربات</button>
                    </div>

                    <?php
                    if (isset($_SESSION['msg'])) {
                        if ($_SESSION['msg']['type'] == 'success') {
                            file_put_contents('.install', 'success');
                        }
                        echo '<div class="alert alert-' . $_SESSION['msg']['type'] . ' mt-3" role="alert">' . $_SESSION['msg']['message'] . '</div>';
                        session_destroy();
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>

    <footer class="container mt-5">
        <p class="float-end">Powered By Salir</p>
        <p><?= date('Y') ?> &copy; </p>
    </footer>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</html>
