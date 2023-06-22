<?php
ob_start();
session_start();
include 'includ/db.php';
include 'includ/jdf.php';
include 'includ/notif.php';
include 'includ/session.php';
include 'includ/fun.php';
//include 'langs/lang_en.php';
//include 'langs/lang_fa.php';
session_login_wizwiz();
?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>wizwiz</title>
    <script src="./assets/js/tailwind.js"></script>
    <link rel="stylesheet" href="./assets/css/tailwind.output.css"/>
    <link rel="stylesheet" href="./assets/css/talwind.min.css"/>
    <link rel="stylesheet" href="./monitor/gauge/css/asPieProgress.css">
    <script src="./assets/js/alpine.js"></script>
    <script src="./assets/js/alpine.js"></script>
    <script src="./assets/js/init-alpine.js"></script>
    <script src="./assets/js/jquery.js"></script>
    <script src="./assets/wizwiz.js"></script>
    <style>
        @font-face {
            font-family: IRANSans;
            src: url('./assets/fonts/IRANSans.ttf'); format('truetype');
            font-weight: normal;
        }
        ::-webkit-scrollbar {
            width: 7px;
            background-color: #f2f2f2;
        }

        ::-webkit-scrollbar-thumb {
            height: 50px;
            background-color: #e7cef1;
        }
    </style>
</head>
<body style="font-family: IRANSans !important;" id="result">

<div
    class="flex h-screen bg-gray-50 dark:bg-gray-900"
    :class="{ 'overflow-hidden': isSideMenuOpen}"
>
    <!-- Desktop sidebar -->
    <aside
        class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0 border-2 dark:border-gray-700"
    >
        <div class="py-3 text-gray-500 dark:text-gray-400">
            <div class="ml-2 flex justify-start items-center">
            <img width="40px" src="./icons/wizwiz.png">
            <a class=" text-lg font-bold text-gray-800 dark:text-gray-200" href="index.php" > WizWiz <span class="px-1 ml-1 rounded" style="font-size: 10px;background-color: #e7cef1;color:#45013c !important;"> v 7.5.3</span></a>
            </div>
