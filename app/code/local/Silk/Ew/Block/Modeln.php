<?php

class Silk_Ew_Block_Modeln extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ew/loadmodel.phtml');
        $data = $this->getRequest()->getParams();
        $this->setMns($this->getMnumber($data['ew_sku']));

    }

     public function getMnumber($sku) {
         $mdata = array();
         $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
         if ($product->getAsWarrantyOption() && $product->getModelNo()) {
             $mdata = array_merge($mdata,explode(',', $product->getModelNo()));
         }
        $mdata = array_unique($mdata);
        return $mdata;
    }


  }
