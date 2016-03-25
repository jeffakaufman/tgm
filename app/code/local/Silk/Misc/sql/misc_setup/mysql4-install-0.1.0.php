<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('silk_warrantyorder')};
CREATE TABLE {$this->getTable('silk_warrantyorder')} (
`misc_id` int(10) NOT NULL AUTO_INCREMENT,
`create_at` TIMESTAMP NOT NULL,
  PRIMARY KEY (`misc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
"
);

$installer->endSetup(); 