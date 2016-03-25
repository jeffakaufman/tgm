<?php
class Silk_Ew_Block_Form extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('ew/form.phtml');
        $this->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
        $address = Mage::getSingleton('customer/session')->getCustomer()
                          ->getPrimaryShippingAddress();
        $this->setAddr($address);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('ew')->__('Apply Extended Warranty'));

    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    

     public function getCustomerOrders() {
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('nin' => array('closed','canceled')))
        ;
        $odata = array();
        foreach ($orders as $o) {
         $_order = Mage::getModel('sales/order')->load($o->getId());
         $_items = $_order->getItemsCollection();
          foreach ($_items as $it) {
              $product = Mage::getModel('catalog/product')->load($it->getProductId());
              $orderno='';
              if ($product->getAsWarrantyOption() && $product->getModelNo() && $this->__getAppliedQty($_order->getIncrementId(), $product->getSku()) < $it->getQtyOrdered()) {
                 $odata[] = $_order->getIncrementId();
                 break;
              }
          }
        }
        return $odata;
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
