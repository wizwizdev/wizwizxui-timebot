<?php

require 'config.php';

$connection = mysqli_connect('localhost', $Database['username'], $Database['password'], $Database['dbname']);
// ------------------ { Connect MySQL & Creat Table } ------------------ //
$res = $connection->query("CREATE TABLE IF NOT EXISTS `user` (
    `id` bigint(10) NOT NULL PRIMARY KEY,
    `step` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
    )");


$connection->query("CREATE TABLE IF NOT EXISTS `servers` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `server_ip` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    `user_name` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
    `password` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
    `type` varchar(100),
    PRIMARY KEY (`id`)
    )");

$connection->query("CREATE TABLE IF NOT EXISTS `loged_info` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `user_id` bigint(10) NOT NULL,
    `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
    `uuid` varchar(1000),
    `sub_server` varchar(1000),
    `warned` varchar(100),
    PRIMARY KEY (`id`)
    )");

?>
