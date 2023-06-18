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
    <title>Open Port Finder</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin: 50px auto;
            width: 500px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        }

        input[type=text] {
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 100%;
            margin-bottom: 20px;
            box-sizing: border-box;
            background-color: #f2f2f2;
            color: #333;
            font-size: 16px;
        }

        input[type=submit] {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #008080;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        input[type=submit]:hover {
            background-color: #006666;
        }

        h1 {
            text-align: center;
            font-size: 36px;
            margin-top: 0;
            color: #008080;
        }

        p {
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<form method="post" action="checkport.php">
    <h1>Open Port Finder</h1>
    <p>Please enter a domain name below to find open ports:</p>
    <input type="text" name="domain_name" placeholder="Enter domain name">
    <input type="submit" name="submit" value="Search">
</form>


</body>
</html>