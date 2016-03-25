<?php
/**
 * Sync product qty and price from ERP system's data 
 *
 * @author claudio
 */

ini_set("max_execution_time","18000");
ini_set("memeory_limit","8192");

require_once '../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$resource = Mage::getSingleton('core/resource');
$w = $resource->getConnection('core_write');
$incrementId = 0;
if(isset($argc) && $argc==2){
    $incrementId = $argv[1];
}
if (!$incrementId) {
    die('error increment Id');
}

$order = Mage::getModel('sales/order')->load($incrementId, 'increment_id');
$order->setStatus('closed');
$order->addStatusHistoryComment('', false);
$order->save();

 
