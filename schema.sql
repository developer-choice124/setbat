-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 28, 2019 at 03:58 PM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cricket`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `featured` varchar(255) NOT NULL DEFAULT 'no',
  `top_category` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `chip_setting`
--

CREATE TABLE `chip_setting` (
  `id` int(11) NOT NULL,
  `chip_name_1` varchar(20) NOT NULL,
  `chip_value_1` float(10,2) NOT NULL,
  `chip_name_2` varchar(20) NOT NULL,
  `chip_value_2` float(10,2) NOT NULL,
  `chip_name_3` varchar(20) NOT NULL,
  `chip_value_3` float(10,2) NOT NULL,
  `chip_name_4` varchar(20) NOT NULL,
  `chip_value_4` float(10,2) NOT NULL,
  `chip_name_5` varchar(20) NOT NULL,
  `chip_value_5` float(10,2) NOT NULL,
  `chip_name_6` varchar(20) NOT NULL,
  `chip_value_6` float(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `chip_setting`
--

INSERT INTO `chip_setting` (`id`, `chip_name_1`, `chip_value_1`, `chip_name_2`, `chip_value_2`, `chip_name_3`, `chip_value_3`, `chip_name_4`, `chip_value_4`, `chip_name_5`, `chip_value_5`, `chip_name_6`, `chip_value_6`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '1K', 1000.00, '5K', 5000.00, '25K', 25000.00, '50K', 50000.00, '100K', 100000.00, '500K', 500000.00, 8, '2019-02-08 00:00:00', '2019-02-08 11:24:47'),
(2, '1K', 1000.00, '5K', 5000.00, '25K', 25000.00, '50K', 50000.00, '100K', 100000.00, '500K', 500000.00, 2332, '2019-02-08 00:00:00', '2019-02-08 11:24:47');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('sp1edb3qb8v3badhjl9c8umpa8lue9mi', '::1', 1549724336, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732343332353b6d6573736167657c733a32323a223c703e496e636f7272656374204c6f67696e3c2f703e223b5f5f63695f766172737c613a313a7b733a373a226d657373616765223b733a333a226f6c64223b7d),
('3mmil7ttdekd4tbo7hok05l7ejr161ig', '::1', 1549802156, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393830323135363b6d6573736167657c733a33303a223c703e4c6f67676564204f7574205375636365737366756c6c793c2f703e223b5f5f63695f766172737c613a313a7b733a373a226d657373616765223b733a333a226f6c64223b7d),
('mfc1r37ipepf2lfc2l9jt8iboe79s4g5', '::1', 1549874303, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393837343234343b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439383032313237223b6c6173745f636865636b7c693a313534393837343235363b),
('ha0bprd8578i6386uh4ma540u72kjmgu', '::1', 1549723778, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732333737383b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b6d6573736167657c733a37373a223c703e546865204e65772050617373776f7264206669656c6420646f6573206e6f74206d617463682074686520436f6e6669726d204e65772050617373776f7264206669656c642e3c2f703e0a223b5f5f63695f766172737c613a313a7b733a373a226d657373616765223b733a333a226f6c64223b7d),
('ep60cab7qis6j9qriucot05nb0bk513n', '::1', 1549723362, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732333336323b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b6d6573736167657c733a3133373a223c703e546865204f6c642050617373776f7264206669656c642069732072657175697265642e3c2f703e0a3c703e546865204e65772050617373776f7264206669656c642069732072657175697265642e3c2f703e0a3c703e54686520436f6e6669726d204e65772050617373776f7264206669656c642069732072657175697265642e3c2f703e0a223b5f5f63695f766172737c613a313a7b733a373a226d657373616765223b733a333a226f6c64223b7d),
('ne8s9tfumdr2546gsh0runnr9rt61k67', '::1', 1549723044, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732333034343b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b),
('4rkun66gacfo7p7v4f4sluf1v7gqc6b4', '::1', 1549716277, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393731363237373b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b),
('bkc5o3nj7ac3gr3aii1b776jflptggk2', '::1', 1549719815, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393731393831353b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b),
('btjl8j81lt690bkpnivlts2b4a6u6mo5', '::1', 1549720257, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732303235373b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b),
('rfthv4fmh7j2klu00ca2prkkrm49i1gv', '::1', 1549720568, 0x5f5f63695f6c6173745f726567656e65726174657c693a313534393732303536383b6964656e746974797c733a32303a227368756268616e6b313240676d61696c2e636f6d223b656d61696c7c733a32303a227368756268616e6b313240676d61696c2e636f6d223b757365725f69647c733a343a2232333332223b6f6c645f6c6173745f6c6f67696e7c733a31303a2231353439363234373632223b6c6173745f636865636b7c693a313534393638393337363b);

-- --------------------------------------------------------

--
-- Table structure for table `company_details`
--

CREATE TABLE `company_details` (
  `id` int(11) NOT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `company_name` varchar(1000) DEFAULT NULL,
  `logo` varchar(1000) DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `address` text,
  `facebook` text,
  `instagram` text,
  `linkedin` text,
  `twitter` text,
  `youtube` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `credits`
--

CREATE TABLE `credits` (
  `id` int(11) NOT NULL,
  `txnid` varchar(255) NOT NULL,
  `amount` float(7,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chips` float DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `free_chips` varchar(5) NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `credits`
--

INSERT INTO `credits` (`id`, `txnid`, `amount`, `user_id`, `chips`, `assigned_by`, `transaction_date`, `expiry_date`, `description`, `free_chips`) VALUES
(1, '62570057575f2a14d16b49959d7e943b', 2000.00, 2331, 2000, 8, '2019-01-24 00:00:00', '2019-03-30 00:00:00', NULL, 'no'),
(2, '0e09d6cb1947dd0e9beda21859874fb8', 5000.00, 2331, 6000, 8, '2019-01-23 00:00:00', '2019-04-30 00:00:00', NULL, 'no'),
(4, '9a7e89ba41e54b4389292dae1f551aaf', 1000.00, 2332, 2500, 8, '2019-02-08 00:00:00', '2019-02-08 00:00:00', NULL, 'no'),
(5, '74644c3fc95a8bdd9c984eada3f03de3', 100.00, 2332, 1000, 8, '2019-02-09 00:00:00', '2019-02-09 00:00:00', 'chips to shubhank', 'no'),
(6, '5c3126bca7208fe79d09227cf5c161b5', 0.00, 2332, 1000, 8, '2019-02-09 00:00:00', '2019-02-09 00:00:00', 'Free Chips', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'superadmin', 'Super Administrator'),
(2, 'admin', 'Admin'),
(3, 'manager', 'Manager'),
(4, 'user', 'General User');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `login`, `time`) VALUES
(4, '::1', 'shahnawaz90.alam@gmail.com', 1549802114);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(9) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci NOT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `contact` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `facebook` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `google_plus` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `twitter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `instagram` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `linkedin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `youtube` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `image`, `address`, `contact`, `email`, `timezone`, `smtp_host`, `smtp_user`, `smtp_pass`, `smtp_port`, `facebook`, `google_plus`, `twitter`, `instagram`, `linkedin`, `youtube`) VALUES
(1, 'Suraaj', 'bug-bounty.jpg', 'Vijay Nagar, Jabalpur', '1234567890', 'admin@suraaj.com', 'Asia/Kolkata', 'Gmail', 'ztrela2k12@gmail.com', 'Sh@V@!35870', '587', 'http://facebook.com', 'https://plus.google.com', 'https://twitter.com', 'https://instagram.in', 'https://linkedin.in', 'https://youtube.in');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `firebase_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activation_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forgotten_password_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `forgotten_password_time` datetime DEFAULT NULL,
  `remember_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `full_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci NOT NULL,
  `zipcode` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `firebase_id`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `full_name`, `dob`, `gender`, `phone`, `bio`, `zipcode`) VALUES
(8, '', 'administrator', '$2y$08$AjP20BOFw6wtSN2mSmVZw.XWgx6Z6aPlz5/ISeGAnQ8puwJxg0qmS', NULL, 'admin@admin.com', '', NULL, NULL, NULL, NULL, 0, 1549724320, 1, 'Ztrela', NULL, 'male', '8770463313', '', NULL),
(2331, '::1', 'shahnawaz90.alam@gmail.com', '$2y$08$d4Yp8gtc8OWiEldt9vU82u1Lwuepgg8c6BahlK33rHD48uQLIdlyG', NULL, 'shahnawaz90.alam@gmail.com', '', NULL, NULL, NULL, NULL, 1548304089, NULL, 1, 'Shahnawaz Alam', NULL, 'male', '9806638656', '', NULL),
(2332, '::1', 'shubhank12@gmail.com', '$2y$08$wIRm0CeisY4oqjeYMrv9OeI9cXG2pEqxsnDiiiKdPJv7JG4MC9Dse', NULL, 'shubhank12@gmail.com', '', NULL, NULL, NULL, NULL, 1549607389, 1549874256, 1, 'Shubhank', NULL, 'male', '9876543210', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_authentication`
--

CREATE TABLE `users_authentication` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expired_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(2346, 8, 1),
(2344, 2331, 2),
(2347, 2332, 4);

-- --------------------------------------------------------

--
-- Stand-in structure for view `users_with_groups`
-- (See below for the actual view)
--
CREATE TABLE `users_with_groups` (
`id` int(11) unsigned
,`ip_address` varchar(45)
,`username` varchar(100)
,`password` varchar(255)
,`salt` varchar(255)
,`email` varchar(254)
,`firebase_id` varchar(255)
,`activation_code` varchar(40)
,`forgotten_password_code` varchar(40)
,`forgotten_password_time` datetime
,`remember_code` varchar(40)
,`created_on` int(11) unsigned
,`last_login` int(11) unsigned
,`active` tinyint(1) unsigned
,`full_name` varchar(1000)
,`dob` varchar(100)
,`gender` varchar(50)
,`phone` varchar(255)
,`zipcode` varchar(15)
,`group_id` mediumint(8) unsigned
,`group_name` varchar(20)
,`group_description` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `user_chips`
--

CREATE TABLE `user_chips` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_chips` int(11) NOT NULL,
  `free_chips` int(11) DEFAULT '0',
  `spent_chips` int(11) DEFAULT '0',
  `balanced_chips` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_chips`
--

INSERT INTO `user_chips` (`id`, `user_id`, `total_chips`, `free_chips`, `spent_chips`, `balanced_chips`, `created_at`, `updated_at`) VALUES
(1, 2332, 4500, 1000, 0, 4500, '2019-02-08 12:24:11', '2019-02-09 01:44:07');

-- --------------------------------------------------------

--
-- Structure for view `users_with_groups`
--
DROP TABLE IF EXISTS `users_with_groups`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `users_with_groups`  AS  select `u`.`id` AS `id`,`u`.`ip_address` AS `ip_address`,`u`.`username` AS `username`,`u`.`password` AS `password`,`u`.`salt` AS `salt`,`u`.`email` AS `email`,`u`.`firebase_id` AS `firebase_id`,`u`.`activation_code` AS `activation_code`,`u`.`forgotten_password_code` AS `forgotten_password_code`,`u`.`forgotten_password_time` AS `forgotten_password_time`,`u`.`remember_code` AS `remember_code`,`u`.`created_on` AS `created_on`,`u`.`last_login` AS `last_login`,`u`.`active` AS `active`,`u`.`full_name` AS `full_name`,`u`.`dob` AS `dob`,`u`.`gender` AS `gender`,`u`.`phone` AS `phone`,`u`.`zipcode` AS `zipcode`,`g`.`id` AS `group_id`,`g`.`name` AS `group_name`,`g`.`description` AS `group_description` from ((`users` `u` join `groups` `g`) join `users_groups` `ug`) where ((`u`.`id` = `ug`.`user_id`) and (`g`.`id` = `ug`.`group_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chip_setting`
--
ALTER TABLE `chip_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `company_details`
--
ALTER TABLE `company_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credits`
--
ALTER TABLE `credits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_authentication`
--
ALTER TABLE `users_authentication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  ADD KEY `fk_users_groups_users1_idx` (`user_id`),
  ADD KEY `fk_users_groups_groups1_idx` (`group_id`);

--
-- Indexes for table `user_chips`
--
ALTER TABLE `user_chips`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chip_setting`
--
ALTER TABLE `chip_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `credits`
--
ALTER TABLE `credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2333;

--
-- AUTO_INCREMENT for table `users_authentication`
--
ALTER TABLE `users_authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2348;

--
-- AUTO_INCREMENT for table `user_chips`
--
ALTER TABLE `user_chips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
