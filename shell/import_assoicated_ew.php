<?php
/**
 * batch update product filed
 *
 * @author claudio
 */

ini_set("max_execution_time","18000");
ini_set("memeory_limit","8192");


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
while (($data = fgetcsv($handle, 500, ',')) !== false) {
    $sku = explode(',',$data[1]);
    $ew = explode(',',$data[0]);
    foreach($sku as $s) { 
        if(!empty($data[0])){
            setRelated($s,$ew);
        }
    }
    echo $row."---------\n";
    $row++;
}

function setRelated($sku,$related_array) {
    require_once '../app/Mage.php';
    umask(0);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    $resource = Mage::getSingleton('core/resource');
    $w = $resource->getConnection('core_write');
    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    if(!$product || $product->getVisibility() == 1){
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if(!$product){
            echo "SKU ".$sku."  no exist \n";
            return false;
        }
        
    }
    $links = array();
    foreach($related_array as $rsku){
        $related = Mage::getModel('catalog/product')->loadByAttribute('sku', $rsku);
        if($related){
            $links[$related->getId()] = array('position'=>null);
        }
    }
    if(empty($links)){
        echo 'Sku :'.$sku.' all related url key not exist'.$br;
        return false;
    }
    //$product->setRelatedLinkData($links);
    $product->setAccessoriesLinkData($links);
    //exit();
    try{
        $product->save();
        echo "Sku : ".$sku." success \n";
    }catch(Exception $e){
        echo "Sku: ".$sku." ".$e->getMessage()."\n";
    }
    

}


?>
