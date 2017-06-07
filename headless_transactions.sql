
DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `uid` char(23) DEFAULT NULL COMMENT 'Unique ID for internal use',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Updated at',
  `transaction_id` char(20) DEFAULT NULL COMMENT 'ID given by source',
  `amount` decimal(10,4) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `order_reference` char(15) DEFAULT NULL,
  `customer_increment_id` char(15) DEFAULT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `transaction_date` datetime DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `uid_uindex` (`uid`),
  UNIQUE KEY `transaction_id_index` (`transaction_id`),
  KEY `order_reference` (`order_reference`),
  KEY `customer_reference` (`customer_increment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
