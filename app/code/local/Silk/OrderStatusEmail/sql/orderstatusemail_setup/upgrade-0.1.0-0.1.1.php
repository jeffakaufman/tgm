<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'shipping_sort', array(
    'label' => 'Shipping Sort',
    'type'  => 'int'
));
$installer->endSetup();
