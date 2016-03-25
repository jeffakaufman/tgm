<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('silk_ew')};
CREATE TABLE {$this->getTable('silk_ew')} (
`ew_id` int(10) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `street_address` text NOT NULL,
  `street_address1` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `email` varchar(60) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `model_number` varchar(30) NOT NULL,
  `serial_number` varchar(13) NOT NULL,
  `date_of_purchase` varchar(20) NOT NULL,
  `retail_price` decimal(10,2) NOT NULL,
  `product_quality` varchar(30) NOT NULL,
  `account_id` int(10) NOT NULL DEFAULT '0',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
  PRIMARY KEY (`ew_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;  
"
);

$installer->endSetup(); 