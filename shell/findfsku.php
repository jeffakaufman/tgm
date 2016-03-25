<?php
/**
 * Sync product qty and price from ERP system's data 
 *
 * @author claudio
 */

ini_set("max_execution_time","18000");
ini_set("memeory_limit","8192");

$handle = fopen('ewmodels.csv', 'r');

if(!$handle) die('have no any update!');
$row=1;
while (($data = fgetcsv($handle, 200, ',')) !== false) {
    lksku($data[0], $row);       
    $row++;
}

function lksku($sku, $line) {
    require_once '../app/Mage.php';
    umask(0);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    $resource = Mage::getSingleton('core/resource');
    $w = $resource->getConnection('core_write');
    $f2 = fopen('newbundlemodels.csv', 'a');
    $have = 0;
    for ($i=1;$i<3;$i++){
        $fullsku = $sku.'-00'.$i;
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $fullsku);
        if ($product) {
           fwrite($f2,$fullsku.',');
           fclose($f2);
           $have = 1;
        } 
    }

   for ($j=1;$j<3;$j++){
        $fullsku = $sku.'.00'.$j;
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $fullsku);
        if ($product) {
           echo $fullsku."\n";
           fwrite($f2,$fullsku.',');
           fclose($f2);
           $have = 1;
        } 
    }


    if ($sku == ''||empty($sku)) {
        fwrite($f2,"\n");
        fclose($f2);
    }else if (!$have) {
        fwrite($f2,$sku.'-001,');
        fclose($f2);
    }
    
}

 
