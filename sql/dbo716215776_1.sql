DROP TABLE IF EXISTS `bits`;
CREATE TABLE IF NOT EXISTS `bits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `expected_satoshis` bigint(20) NOT NULL,
  `actual_satoshis` bigint(20) NOT NULL,
  `expected_gvb` bigint(20) NOT NULL,
  `actual_gvb` bigint(20) NOT NULL,
  `owed_gvb` bigint(20) NOT NULL,
  `payment` enum('new','pending','confirmed_wrong_amount','confirmed','complete','unconfirmed','canceled') COLLATE latin1_general_ci NOT NULL,
  `email_sent` enum('not_sent','sent','sent_wrong_amount') COLLATE latin1_general_ci NOT NULL,
  `address` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `cus_address` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `paytime` datetime NOT NULL,
  `times` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;