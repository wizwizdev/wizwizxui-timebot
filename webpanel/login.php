<!--
* WizWiz v7.5.3
* https://github.com/wizwizdev/wizwizxui-timebot
* Copyright (c) @wizwizch
-->
<?php
session_start();
ob_start();
//if (isset($_SESSION['username']) && isset($_COOKIE['cookie_username'])) {
if (isset($_SESSION['username'])) {
    header("Location: index.php");
} else {
}


include_once "includ/db.php";
include 'includ/notif.php';
include 'includ/session.php';


?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Windmill Dashboard</title>
    <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/tailwind.output.css"/>
    <script
            src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
            defer
    ></script>
    <script src="assets/js/init-alpine.js"></script>
</head>
<body style="background-color: #ecf0f1;">

<?php
session_notif_wizwiz();
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
    if (time() - $_SESSION['last_login_attempt'] < 300) {
        die('

      <div class=" h-full max-w-4xl mx-auto rounded-lg dark:bg-gray-800">
        <h1 style="text-align: center;font-size: 30px;margin-top: 50px;color: #5a189a">Too many failed login attempts. Please try again later ...</h1>
        <br>
        <svg id="Layer_1" class="mx-auto" height="40" fill="#5a189a" viewBox="0 0 24 24" width="40" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m9.856 20.743a9 9 0 1 1 11.144-8.743 1.5 1.5 0 0 0 3 0 12 12 0 1 0 -14.856 11.657 1.464 1.464 0 0 0 .356.043 1.5 1.5 0 0 0 .355-2.957z"/><path d="m23.621 15.939a1.5 1.5 0 0 0 -2.121 0l-4.785 4.782-2.133-2.26a1.5 1.5 0 0 0 -2.121-.043 1.5 1.5 0 0 0 -.042 2.121l2.581 2.721a2.362 2.362 0 0 0 1.674.74h.037a2.368 2.368 0 0 0 1.661-.688l5.254-5.252a1.5 1.5 0 0 0 -.005-2.121z"/><path d="m10.5 7.5v3.555l-2.4 1.5a1.5 1.5 0 0 0 -.475 2.068 1.5 1.5 0 0 0 2.068.475l2.869-1.8a2 2 0 0 0 .938-1.7v-4.098a1.5 1.5 0 0 0 -3 0z"/></svg>
    </div>

');
    }
    unset($_SESSION['login_attempts']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $usernames = input($_POST["username"]);
    $passwords = input($_POST["password"]);
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $usernames, $passwords);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$result) {
        die('خطا به وجود اومد' . mysqli_error($conn));
    } else {
    }

    if ($result->num_rows == 1 && $row['username'] == $usernames) {
        $_SESSION['username'] = $row['username'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
//        if ($_POST["check_login"] == '1') {

//            function encryptCookieValue($value, $secretKey)
//            {
//                $iv = openssl_random_pseudo_bytes(150);
//                $encryptedValue = openssl_encrypt($value, 'aes-256-gcm', $secretKey, OPENSSL_RAW_DATA, $iv, $tag);
//                $encodedIv = base64_encode($iv);
//                $encodedTag = base64_encode($tag);
//                $encodedValue = base64_encode($encryptedValue);
//                return $encodedIv . ':' . $encodedTag . ':' . $encodedValue;
//            }
//
//            function decryptCookieValue($cookieValue, $secretKey)
//            {
//                list($encodedIv, $encodedTag, $encodedValue) = explode(':', $cookieValue);
//                $iv = base64_decode($encodedIv);
//                $tag = base64_decode($encodedTag);
//                $encryptedValue = base64_decode($encodedValue);
//                $decryptedValue = openssl_decrypt($encryptedValue, 'aes-256-gcm', $secretKey, OPENSSL_RAW_DATA, $iv, $tag);
//                return $decryptedValue;
//            }

//            $secretKey = 'i1a2b3c4d5e67f8g9h0~!@#$%^&*';
//            $originalValue = $usernames;
//            $encryptedValue = encryptCookieValue($originalValue, $secretKey);
//            setcookie('cookie_username', $encryptedValue, time() + 604800);
//
//            $cookieValue = isset($_COOKIE['cookie_username']) ? $_COOKIE['cookie_username'] : '';
//            $decryptedValue = decryptCookieValue($cookieValue, $secretKey);

//        }

        welcomwizwiz();
        header('Location: index.php');
    } else {
        // Login failed
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
        } else {
            $_SESSION['login_attempts']++;
        }
        $_SESSION['last_login_attempt'] = time();
        die('

      <div class=" h-full max-w-4xl mx-auto rounded-lg dark:bg-gray-800">
        <h1 style="text-align: center;font-size: 30px;margin-top: 50px;color: #c9184a">Invalid username or password</h1>
        <br>
        <svg class="mx-auto" fill="#c9184a" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="40" height="40"><path d="M12,24A12,12,0,1,1,24,12,12.013,12.013,0,0,1,12,24ZM12,2A10,10,0,1,0,22,12,10.011,10.011,0,0,0,12,2Zm5.746,15.667a1,1,0,0,0-.08-1.413A9.454,9.454,0,0,0,12,14a9.454,9.454,0,0,0-5.666,2.254,1,1,0,0,0,1.33,1.494A7.508,7.508,0,0,1,12,16a7.51,7.51,0,0,1,4.336,1.748,1,1,0,0,0,1.41-.081ZM6,10c0,1,.895,1,2,1s2,0,2-1a2,2,0,0,0-4,0Zm8,0c0,1,.895,1,2,1s2,0,2-1a2,2,0,0,0-4,0Z"/></svg>
    </div>

');
    }
    $stmt->close();
    mysqli_close($conn);
//    }
}
function input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<div class="mx-auto flex items-center min-h-screen p-6 bg-gray-50 "
     style="background-color: #ecf0f1;margin-top: -100px">
    <div
            class="flex-1 h-full max-w-4xl mx-auto overflow-hidden rounded-lg "
    >
        <div class="flex flex-col overflow-y-auto ">
            <div class="flex items-center justify-center p-6">
                <div class="" style="width: 65%">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <img src="icons/wizwiz.png" class="mx-auto">
                        <br>
                        <br>
                        <label class="block text-sm">
                            <span class="text-gray-700 dark:text-gray-600">Username</span>
                            <input
                                    class="block w-full mt-1 text-sm dark:border-gray-500 dark:bg-gray-600 focus:border-blue-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-600 dark:focus:shadow-outline-gray form-input"
                                    type="text" name="username" id="username" required/>
                        </label>
                        <label class="block mt-4 text-sm">
                            <span class="text-gray-700 dark:text-gray-600">Password</span>
                            <input class="block w-full mt-1 text-sm dark:border-gray-500 dark:bg-gray-600 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-600 dark:focus:shadow-outline-gray form-input"
                                   type="text" name="password" id="password" required/>
                        </label>
                        <input hidden checked type="checkbox" class="form-check-input btn-outline-success"
                               id="inputcheckbox" value="1"
                               name="check_login" <?php
                        if (isset($_COOKIE['cookie_username']) && isset($_COOKIE['cookie_password'])) {
                            echo 'checked';
                        }
                        ?> >
                        <br>
                        <button value="Submit" type="submit" name="submit" style=" width:60px;border-radius: 50%"
                                class="mx-auto block px-2 py-2 mt-4 text-sm text-center text-white rounded-xl">
                            <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                 viewBox="0 0 24 24" width="40" height="40">
                                <path d="M5.972,22.285a1,1,0,0,1-.515-1.857C9,18.3,9,13.73,9,11a3,3,0,0,1,6,0,1,1,0,0,1-2,0,1,1,0,0,0-2,0c0,2.947,0,8.434-4.514,11.143A1,1,0,0,1,5.972,22.285Zm4.963,1.421c2.282-2.3,3.615-5.534,3.961-9.621A1,1,0,0,0,13.985,13a.983.983,0,0,0-1.081.911c-.311,3.657-1.419,6.4-3.388,8.381a1,1,0,0,0,1.419,1.41Zm5.2-.186a17.793,17.793,0,0,0,1.508-3.181,1,1,0,0,0-1.881-.678,15.854,15.854,0,0,1-1.338,2.821,1,1,0,0,0,1.711,1.038ZM18.5,17.191A31.459,31.459,0,0,0,19,11,7,7,0,0,0,6.787,6.333,1,1,0,1,0,8.276,7.667,5,5,0,0,1,17,11a29.686,29.686,0,0,1-.462,5.809,1,1,0,0,0,.79,1.172.979.979,0,0,0,.193.019A1,1,0,0,0,18.5,17.191ZM7,11a5,5,0,0,1,.069-.833A1,1,0,1,0,5.1,9.833,6.971,6.971,0,0,0,5,11c0,4.645-1.346,7-4,7a1,1,0,0,0,0,2C4.869,20,7,16.8,7,11ZM20.7,23.414A29.76,29.76,0,0,0,23,11a10.865,10.865,0,0,0-1.1-4.794,1,1,0,1,0-1.8.875A8.9,8.9,0,0,1,21,11a27.91,27.91,0,0,1-2.119,11.586,1,1,0,0,0,.5,1.324.984.984,0,0,0,.413.09A1,1,0,0,0,20.7,23.414ZM3,14V11a9.01,9.01,0,0,1,9-9,8.911,8.911,0,0,1,5.4,1.8,1,1,0,0,0,1.2-1.6A10.9,10.9,0,0,0,12,0,11.013,11.013,0,0,0,1,11v3a1,1,0,0,0,2,0Z"/>
                            </svg>
                        </button>
                        <p style="font-size: 14px">wizwiz <span style="color:red;">
                                <svg class="inline-block" xmlns="http://www.w3.org/2000/svg" fill="#d8315b" id="Layer_1"
                                     data-name="Layer 1" viewBox="0 0 24 24" width="15" height="15"><path
                                            d="M17.5.917a6.4,6.4,0,0,0-5.5,3.3A6.4,6.4,0,0,0,6.5.917,6.8,6.8,0,0,0,0,7.967c0,6.775,10.956,14.6,11.422,14.932l.578.409.578-.409C13.044,22.569,24,14.742,24,7.967A6.8,6.8,0,0,0,17.5.917Z"/></svg>
                            </span></p>
                    </form>


                </div>


            </div>
        </div>
    </div>

</div>
<script src="assets/wizwiz.js"></script>
</body>
</html>
<!--
* WizWiz v7.5.3
* https://github.com/wizwizdev/wizwizxui-timebot
* Copyright (c) @wizwizch
-->