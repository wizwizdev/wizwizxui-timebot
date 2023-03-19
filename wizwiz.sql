-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 16, 2023 at 08:15 AM
-- Server version: 5.7.40-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wizwiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(255) NOT NULL,
  `user_id` bigint(10) NOT NULL,
  `create_date` int(255) NOT NULL,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `category` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `state` int(5) NOT NULL,
  `rate` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chats_info`
--

CREATE TABLE `chats_info` (
  `id` int(255) NOT NULL,
  `chat_id` int(255) NOT NULL,
  `sent_date` int(255) NOT NULL,
  `msg_type` varchar(50) DEFAULT NULL,
  `text` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `needed_sofwares`
--

CREATE TABLE `needed_sofwares` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `link` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `needed_sofwares`
--

INSERT INTO `needed_sofwares` (`id`, `title`, `link`, `status`) VALUES
(1, 'ios fair-vpn', 'https://apps.apple.com/us/app/fair-vpn/id1533873488', 1),
(2, 'ios napsternetv', 'https://apps.apple.com/us/app/napsternetv/id1629465476', 1),
(3, 'ios oneclick', 'https://apps.apple.com/us/app/oneclick-safe-easy-fast/id1545555197', 1),
(4, 'android v2rayng', 'https://play.google.com/store/apps/details?id=com.v2ray.ang&hl=en&gl=US', 1),
(5, 'android sagernet', 'https://play.google.com/store/apps/details?id=io.nekohasekai.sagernet&hl=de&gl=US', 1),
(6, 'android onclick', 'https://play.google.com/store/apps/details?id=earth.oneclick', 1),
(7, 'windows v2rayng', 'https://holoo.pro/v2ray-windows/', 1),
(8, 'mac fair', 'https://apps.apple.com/us/app/fair-vpn/id1533873488', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_list`
--

CREATE TABLE `orders_list` (
  `id` int(11) NOT NULL,
  `userid` varchar(30) COLLATE utf8mb4_persian_ci NOT NULL,
  `transid` varchar(150) COLLATE utf8mb4_persian_ci NOT NULL,
  `fileid` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `inbound_id` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `protocol` varchar(20) COLLATE utf8mb4_persian_ci NOT NULL,
  `expire_date` int(11) NOT NULL,
  `link` text COLLATE utf8mb4_persian_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `notif` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_accounts`
--

CREATE TABLE `server_accounts` (
  `id` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `text` text NOT NULL,
  `sold` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `server_categories`
--

CREATE TABLE `server_categories` (
  `id` int(11) NOT NULL,
  `server_id` varchar(20) COLLATE utf8mb4_persian_ci NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `step` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_config`
--

CREATE TABLE `server_config` (
  `id` int(11) NOT NULL,
  `panel_url` varchar(254) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `sni` varchar(254) NOT NULL,
  `header_type` enum('none','http') NOT NULL,
  `request_header` text NOT NULL,
  `response_header` text NOT NULL,
  `security` enum('tls','none') NOT NULL,
  `tlsSettings` text NOT NULL,
  `cookie` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `server_info`
--

CREATE TABLE `server_info` (
  `id` int(11) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `ucount` varchar(20) CHARACTER SET latin1 NOT NULL,
  `remark` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `flag` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_plans`
--

CREATE TABLE `server_plans` (
  `id` int(11) NOT NULL,
  `fileid` varchar(250) COLLATE utf8mb4_persian_ci NOT NULL,
  `catid` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `inbound_id` int(11) NOT NULL DEFAULT '0',
  `acount` bigint(20) NOT NULL,
  `limitip` int(11) NOT NULL DEFAULT '1',
  `title` varchar(150) COLLATE utf8mb4_persian_ci NOT NULL,
  `protocol` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `days` float NOT NULL,
  `volume` float NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `price` int(11) NOT NULL,
  `descr` text COLLATE utf8mb4_persian_ci NOT NULL,
  `pic` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `step` int(11) NOT NULL,
  `date` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(255) NOT NULL,
  `type` varchar(500) NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userid` varchar(40) COLLATE utf8mb4_persian_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_persian_ci NOT NULL,
  `refcode` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `wallet` int(11) NOT NULL DEFAULT '0',
  `date` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats_info`
--
ALTER TABLE `chats_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `needed_sofwares`
--
ALTER TABLE `needed_sofwares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_list`
--
ALTER TABLE `orders_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_accounts`
--
ALTER TABLE `server_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_categories`
--
ALTER TABLE `server_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_config`
--
ALTER TABLE `server_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_info`
--
ALTER TABLE `server_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_plans`
--
ALTER TABLE `server_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats_info`
--
ALTER TABLE `chats_info`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `needed_sofwares`
--
ALTER TABLE `needed_sofwares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders_list`
--
ALTER TABLE `orders_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_accounts`
--
ALTER TABLE `server_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_categories`
--
ALTER TABLE `server_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_config`
--
ALTER TABLE `server_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_info`
--
ALTER TABLE `server_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_plans`
--
ALTER TABLE `server_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
