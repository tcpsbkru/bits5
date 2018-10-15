DROP TABLE IF EXISTS `bits`;
CREATE TABLE `bits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `expected_satoshis` bigint(20) DEFAULT NULL,
  `expected_gvb` decimal(10,2) DEFAULT NULL,
  `actual_gvb` decimal(10,2) DEFAULT NULL,
  `owed_gvb` decimal(10,2) DEFAULT NULL,
  `payment` enum('new','pending','confirmed_wrong_amount','confirmed','complete','unconfirmed','canceled') COLLATE latin1_general_ci NOT NULL,
  `email_sent` enum('not_sent','sent','sent_wrong_amount') COLLATE latin1_general_ci NOT NULL,
  `address` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `cus_address` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `paytime` datetime NOT NULL,
  `times` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_c