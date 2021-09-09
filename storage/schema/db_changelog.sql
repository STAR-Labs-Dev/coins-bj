--
-- Title for coin payment
--
INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('deposit/store', 'Deposit Fund', 'Deposit', '');
INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('deposit/coinpayment-transaction-info', 'Coinpayment Summery', 'Coinpayment Summery', '');

--
-- Dumping data for table `settings`
--
INSERT INTO `settings` (`name`, `value`, `type`) VALUES
('admin_access_ip_setting', 'Disabled', 'admin_security'),
('admin_access_ips', '::1', 'admin_security');
-- ('admin_2fa', 'Disabled', 'admin_security');

--
-- Dynamic admin routes
--

INSERT INTO `preferences` (`category`, `field`, `value`) VALUES ('preference', 'admin_url_prefix', 'admin');

--
-- Table structure for table `qr_codes`
--
DROP TABLE IF EXISTS `qr_codes`;
CREATE TABLE IF NOT EXISTS `qr_codes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT NULL,
  `object_type` varchar(20) DEFAULT NULL,
  `secret` varchar(191) DEFAULT NULL,
  `status` varchar(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qr_codes_user_id_index` (`object_id`),
  KEY `qr_codes_object_type_index` (`object_type`),
  KEY `qr_codes_secret_index` (`secret`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Metas
--

INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('wallet-list', 'Wallet List', 'Wallet List', '');
INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('deposit/paypal-payment/success/{amount}', 'Deposit With PayPal', 'Deposit With PayPal', '');