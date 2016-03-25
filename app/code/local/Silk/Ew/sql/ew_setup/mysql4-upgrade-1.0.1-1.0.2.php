<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('silk_ew')}  CHANGE `date_of_purchase` `date_of_purchase` DATE NOT NULL;
"
);


$installer->endSetup(); 