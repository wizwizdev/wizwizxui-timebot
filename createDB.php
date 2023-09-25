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


$connection->query("CREATE TABLE `discounts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `amount` int(255) NOT NULL,
  `expire_date` int(255) NOT NULL,
  `expire_count` int(255) NOT NULL,
  `used_by` text DEFAULT NULL,
  `can_use` int(255) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)");

$connection->query("CREATE TABLE `gift_list` (
  `id` int(255) NOT NULL,
  `server_id` int(255) NOT NULL,
  `volume` int(255) NOT NULL,
  `day` int(255) NOT NULL,
  `offset` int(255) DEFAULT 0,
  `server_offset` int(255) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
");

$connection->query("CREATE TABLE `increase_day` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `volume` float NOT NULL,
  `price` int(255) NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("CREATE TABLE `increase_order` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userid` varchar(30) NOT NULL,
  `server_id` int(255) NOT NULL,
  `inbound_id` int(255) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `amount` int(255) NOT NULL,
  `date` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
)");


$connection->query("CREATE TABLE `increase_plan` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `volume` float NOT NULL,
  `price` int(255) NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("CREATE TABLE `needed_sofwares` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
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
(7, 'windows v2rayng', 'https://google.com', 1),
(8, 'mac fair', 'https://apps.apple.com/us/app/fair-vpn/id1533873488', 1);
");


$connection->query("CREATE TABLE `orders_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(30) NOT NULL,
  `token` varchar(100) NOT NULL,
  `transid` varchar(150) NOT NULL,
  `fileid` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `inbound_id` int(11) NOT NULL DEFAULT 0,
  `remark` varchar(100) NOT NULL,
  `uuid` varchar(100) NOT NULL,
  `protocol` varchar(20) NOT NULL,
  `expire_date` int(11) NOT NULL,
  `link` text NOT NULL,
  `amount` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` varchar(50) NOT NULL,
  `notif` int(11) NOT NULL DEFAULT 0,
  `rahgozar` int(10) DEFAULT 0,
  `agent_bought` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE IF NOT EXISTS `pays` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `hash_id` varchar(1000) NOT NULL,
    `description` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
    `payid` varchar(500) DEFAULT NULL,
    `user_id` bigint(10) NOT NULL,
    `type` varchar(100),
    `plan_id` int(255),
    `volume` int(255),
    `day` int(255),
    `price` int(255) NOT NULL,
    `request_date` int(255) NOT NULL,
    `state` varchar(255) NOT NULL,
    `agent_bought` int(1) NOT NULL DEFAULT 0,
    `agent_count` int(255) NOT NULL DEFAULT 0,
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
  `type` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `port_type` varchar(10) DEFAULT 'auto',
  `reality` varchar(10) DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci");

$connection->query("CREATE TABLE `server_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ucount` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `remark` varchar(100) NOT NULL,
  `flag` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `state` int(255) NOT NULL DEFAULT 1,
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
  `rahgozar` int(10) DEFAULT 0,
  `dest` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `serverNames` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `spiderX` varchar(500) DEFAULT NULL,
  `flow` varchar(50) NOT NULL DEFAULT 'None',
  `custom_path` int(10) DEFAULT 1,
  `custom_port` int(255) NOT NULL DEFAULT 0,
  `custom_sni` varchar(500)  CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE `setting` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("INSERT INTO `setting` (`id`, `type`, `value`) VALUES
(1, 'TICKETS_CATEGORY', 'شکایت'),
(2, 'INVITE_BANNER_AMOUNT', '3000'),
(3, 'INVITE_BANNER_TEXT', '{\"type\":\"photo\",\"caption\":\"\\ud83d\\udd30\\u0628\\u0631\\u062a\\u0631\\u06cc\\u0646 \\u0648 \\u0628\\u0647\\u062a\\u0631\\u06cc\\u0646 \\u0631\\u0628\\u0627\\u062a vpn \\u0628\\u0627 \\u06a9\\u0627\\u0646\\u06a9\\u0634\\u0646 \\u0647\\u0627\\u06cc \\u0631\\u0627\\u06cc\\u06af\\u0627\\u0646\\n\\u2705 \\u062d\\u062a\\u0645\\u0627 \\u0639\\u0636\\u0648 \\u0631\\u0628\\u0627\\u062a \\u0628\\u0634\\u06cc\\u062f \\u0648 \\u0627\\u0632 \\u062a\\u062e\\u0641\\u06cc\\u0641 \\u0647\\u0627\\u06cc \\u0648\\u06cc\\u0698\\u0647 \\u0644\\u0630\\u062a \\u0628\\u0628\\u0631\\u06cc\\u0646\\n\\n\\ud83d\\udd17 LINK\",\"file_id\":\"AgACAgQAAxkBAAJRKWRtX3wObRa3qAR_gkJgyKDdkHZsAAKAuzEbRaBpU3QQ2kLLt7MVAQADAgADeAADLwQ\"}'),
(4, 'PAYMENT_KEYS', '{\"nowpayment\":\"cccc-cccc-cccc-cccc\",\"zarinpal\":\"aaaa-aaaa-aaaa-aaaa\",\"nextpay\":\"bbbb-bbbb-bbbb-bbbb\",\"bankAccount\":\"6104-6104-6104-6104\",\"holderName\":\"\\u0648\\u06cc\\u0632\\u0648\\u06cc\\u0632\"}'),
(5, 'BOT_STATES', '{\"requirePhone\":\"off\",\"requireIranPhone\":\"off\",\"sellState\":\"on\",\"botState\":\"on\",\"searchState\":\"on\",\"rewaredTime\":\"3\",\"cartToCartState\":\"on\",\"nextpay\":\"on\",\"zarinpal\":\"on\",\"nowPaymentWallet\":\"on\",\"nowPaymentOther\":\"on\",\"walletState\":\"on\",\"rewardChannel\":\"@wizwizdev\",\"lockChannel\":\"@wizwizch\",\"changeProtocolState\":null,\"renewAccountState\":null,\"switchLocationState\":\"on\",\"increaseTimeState\":\"on\",\"increaseVolumeState\":\"on\",\"gbPrice\":\"100\",\"dayPrice\":\"100\",\"subLinkState\":\"on\",\"plandelkhahState\":\"off\",\"weSwapState\":\"on\"}');
");



$connection->query("CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `refcode` varchar(50) NOT NULL,
  `wallet` int(11) NOT NULL DEFAULT 0,
  `date` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `refered_by` bigint(10) DEFAULT NULL,
  `step` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `freetrial` varchar(10) DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `first_start` varchar(10) DEFAULT NULL,
  `temp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_agent` int(1) NOT NULL DEFAULT 0,
  `discount_percent` int(255) NOT NULL DEFAULT 0,
  `agent_date` int(255) NOT NULL DEFAULT 0,
  `spam_info` varchar(500),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");


$connection->query("CREATE TABLE `admins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `backupchannel` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lang` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
)");

$connection->query("INSERT INTO `admins` (`id`, `username`, `password`, `backupchannel`, `lang`) VALUES
(1, 'admin', 'admin', '-1002545458541', 'en');
");



$connection->query("CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(200) NOT NULL,
  `port` int(10) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `panel` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci");



?>
