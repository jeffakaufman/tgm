<?php

$installer = $this;

$installer->startSetup();

/*$installer->run("
ALTER TABLE {$this->getTable('silk_ew')}  ADD  `order_number` VARCHAR( 30 ) NOT NULL ,
ADD  `ew_sku` VARCHAR( 30 ) NOT NULL
"
);
*/


$installer->getConnection()->addColumn(
    $installer->getTable('silk_ew'),
    'order_number',
    "varchar (30) NOT NULL"
);
$installer->getConnection()->addColumn(
    $installer->getTable('silk_ew'),
    'ew_sku',
    "varchar (30) NOT NULL"
);


$installer->endSetup(); 