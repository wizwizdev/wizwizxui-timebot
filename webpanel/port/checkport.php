<!--
* WizWiz v7.5.3
* https://github.com/wizwizdev/wizwizxui-timebot
* Copyright (c) @wizwizch
-->
<?php
ob_start();
session_start();
//if (!isset($_COOKIE['cookie_username'])) {
if (!isset($_SESSION['username'])) {
    header("location: ../login.php");
    exit();
} else {
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #333;
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .p1 {
            display: inline-block;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #badbcc;
            color: #0f5132;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .p2 {
            display: inline-block;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #842029;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = $_POST["domain_name"];
    $ports = array(80, 443, 21, 22, 23, 8080, 8880, 2052, 2082, 2086, 2095, 2053, 8443, 54321, 3306);
    echo "<h1>Open Ports on {$domain} </h1>";
    foreach ($ports as $port) {
        $connection = @fsockopen($domain, $port, $errno, $errstr, 1);
        if (is_resource($connection)) {
            echo "<p class='p1'>Port {$port} is open on {$domain} </p>";
            fclose($connection);
        } else {
            echo "<p class='p2'>Port {$port} is closed on {$domain} </p>";
        }
    }
}
?>
</body>
</html>