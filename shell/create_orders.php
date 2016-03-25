<?php
require_once '../app/Mage.php';
umask(0);

if(isset($argc) && $argc==3){
    $storeid = $argv[1];
    $quoteid = $argv[2];
}else{
    die("error parameters!\n");
}


Mage::app()->setCurrentStore($storeid);
createOrder($quoteid);


function createOrder($quoteId){
   
   $quoteObj = Mage::getModel('sales/quote')->load($quoteId); 

   $items = $quoteObj->getAllItems();        
   $quoteObj->reserveOrderId();

   $quotePaymentObj = $quoteObj->getPayment(); 
   $quotePaymentObj->setMethod('checkmo');
   $quoteObj->setPayment($quotePaymentObj); 


    $convertQuoteObj = Mage::getSingleton('sales/convert_quote');
    $orderObj = $convertQuoteObj->addressToOrder($quoteObj->getShippingAddress());

    $orderPaymentObj = $convertQuoteObj->paymentToOrderPayment($quotePaymentObj);

    $orderObj->setBillingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getBillingAddress()));
    $orderObj->setShippingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getShippingAddress()));


    $orderObj->setPayment($convertQuoteObj->paymentToOrderPayment($quoteObj->getPayment()));

    foreach ($items as $item) 
    {

        $orderItem = $convertQuoteObj->itemToOrderItem($item);        
        $options = array();
       if ($productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct())) 
       {
         $options = $productOptions;
       }
       if ($addOptions = $item->getOptionByCode('additional_options')) 
       {
        $options['additional_options'] = unserialize($addOptions->getValue());
       }
       if ($options) 
       {
          $orderItem->setProductOptions($options);
       }
       if ($item->getParentItem())
       {
            $orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
       }
       $orderObj->addItem($orderItem);
    }


    $orderObj->setCanShipPartiallyItem(false);
    $totalDue = $orderObj->getTotalDue();

    $orderObj->place(); //calls _placePayment
    $orderObj->save();

    $orderObj->load(Mage::getSingleton('sales/order')->getLastOrderId());
    $lastOrderId = $orderObj->getIncrementId();

    echo "Recent Order Id :".$lastOrderId."\n\n";  

}


