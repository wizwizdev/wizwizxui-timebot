<?php

include "baseInfo.php";
$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
if($connection->connect_error){
    exit("error " . $connection->connect_error);  
}
$connection->set_charset("utf8mb4");

$connection->query("CREATE TABLE `chats` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) NOT NULL,
  `create_date` int(255) NOT NULL,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `category` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `state` int(5) NOT NULL,
  `rate` int(5) NOT NULL,
  PRIMARY KEY (`id`)
)");


$connection->query("CREATE TABLE `chats_info` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `chat_id` int(255) NOT NULL,
  `sent_date` int(255) NOT NULL,
  `msg_type` varchar(50) DEFAULT NULL,
  `text` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("CREATE TABLE `needed_sofwares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `link` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)");

$connection->query("INSERT INTO `needed_sofwares` (`id`, `title`, `link`, `status`) VALUES
(1, 'ios fair-vpn', 'https://apps.apple.com/us/app/fair-vpn/id1533873488', 1),
(2, 'ios napsternetv', 'https://apps.apple.com/us/app/napsternetv/id1629465476', 1),
(3, 'ios oneclick', 'https://apps.apple.com/us/app/id1545555197', 1),
(4, 'android v2rayng', 'https://play.google.com/store/apps/details?id=com.v2ray.ang&hl=en&gl=US', 1),
(5, 'android sagernet', 'https://play.google.com/store/apps/details?id=io.nekohasekai.sagernet&hl=de&gl=US', 1),
(6, 'android onclick', 'https://play.google.com/store/apps/details?id=earth.oneclick', 1),
(7, 'windows v2rayng', 'https://holoo.pro/v2ray-windows/', 1),
(8, 'mac fair', 'https://apps.apple.com/us/app/fair-vpn/id1533873488', 1);
");


$connection->query("CREATE TABLE `orders_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(30) NOT NULL,
  `transid` varchar(150) NOT NULL,
  `fileid` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `inbound_id` int(11) NOT NULL DEFAULT 0,
  `remark` varchar(100) NOT NULL,
  `protocol` varchar(20) NOT NULL,
  `expire_date` int(11) NOT NULL,
  `link` text NOT NULL,
  `amount` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` varchar(50) NOT NULL,
  `notif` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE IF NOT EXISTS `pays` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `hash_id` varchar(1000) NOT NULL,
    `payid` bigint(10) NOT NULL DEFAULT 0,
    `user_id` bigint(10) NOT NULL,
    `type` varchar(100),
    `plan_id` int(255),
    `volume` int(255),
    `day` int(255),
    `price` int(255) NOT NULL,
    `request_date` int(255) NOT NULL,
    `state` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);");


$connection->query("CREATE TABLE `server_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `step` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE `server_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `panel_url` varchar(254) NOT NULL,
  `ip` text NOT NULL,
  `sni` varchar(254) NOT NULL,
  `header_type` enum('none','http') NOT NULL,
  `request_header` text NOT NULL,
  `response_header` text NOT NULL,
  `security` enum('xtls', 'tls','none') NOT NULL,
  `tlsSettings` text NOT NULL,
  `cookie` text NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `port_type` varchar(10) DEFAULT 'auto',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci");

$connection->query("CREATE TABLE `server_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `ucount` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `remark` varchar(100) NOT NULL,
  `flag` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");



$connection->query("CREATE TABLE `server_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileid` varchar(250) NOT NULL,
  `catid` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `inbound_id` int(11) NOT NULL DEFAULT 0,
  `acount` bigint(20) NOT NULL,
  `limitip` int(11) NOT NULL DEFAULT 1,
  `title` varchar(150) NOT NULL,
  `protocol` varchar(100) NOT NULL,
  `days` float NOT NULL,
  `volume` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `descr` text NOT NULL,
  `pic` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `step` int(11) NOT NULL,
  `date` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE `setting` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("INSERT INTO `setting` (`id`, `type`, `value`) VALUES
(1, 'TICKETS_CATEGORY', 'شکایت');
");


$connection->query("CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `refcode` varchar(50) NOT NULL,
  `wallet` int(11) NOT NULL DEFAULT 0,
  `date` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");




?>