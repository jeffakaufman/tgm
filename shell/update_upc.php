<?php
ini_set("max_execution_time","18000");
ini_set("memeory_limit","8192");


require_once('../app/Mage.php');

umask(0);
Mage::app('default');
Mage::app ()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


if(isset($argc) && $argc==2){
    $filename = $argv[1];
    $br = "\n";
}else{
    $filename = $_GET['file'];
    $br = '<br />';
}

$handle = fopen($filename.'.csv', 'r');
$row=1;
$parent_sku = '';
$sku_array = array();
$resource = Mage::getSingleton('core/resource');
$w = $resource->getConnection('core_write');
$w->query('SET FOREIGN_KEY_CHECKS=0;');
$row = 0;
while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    $products = Mage::getModel('catalog/product')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('benq_item_num',$data[0]);
    $row = $row+1;
    echo "line $row".", products:";
    foreach ($products as $product) {
        $product->setData('upc_code', $data[1]);
        $product->save();
        echo $product->getName()."|";
    }
    echo $data[0]."|".$data[1]."\n";
}
$w->query('SET FOREIGN_KEY_CHECKS=1;');

