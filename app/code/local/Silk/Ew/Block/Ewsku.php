<?php

class Silk_Ew_Block_Ewsku extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ew/loadewsku.phtml');
        $data = $this->getRequest()->getParams();
        $this->setEws($this->getWarranties($data['order_number']));

    }

     public function getWarranties($oid) {
        $wdata = array();
         $_order = Mage::getModel('sales/order')->loadByIncrementId($oid);
         $_items = $_order->getItemsCollection();
          foreach ($_items as $it) {
              $product = Mage::getModel('catalog/product')->load($it->getProductId());
              if ($product->getAsWarrantyOption() && $product->getModelNo() && $this->__getAppliedQty($oid, $product->getSku()) < $it->getQtyOrdered()) {
                 $wdata[] = $product->getSku();
              }

          }
        return $wdata;
    }

    protected function __getAppliedQty($oid, $sku) {
        $ews = Mage::getResourceModel('ew/ew_collection')
            ->addFieldToSelect('ew_id')
            ->addFieldToFilter('order_number',$oid) 
            ->addFieldToFilter('ew_sku',$sku);
        ;
        return $ews->getSize();
    }


  }
