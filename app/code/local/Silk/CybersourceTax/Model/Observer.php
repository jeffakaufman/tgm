<?php 

class Silk_CybersourceTax_Model_Observer { 

    public function unsetSessionAfterLoadAdminhtml($observer)
    {
       //Mage::log('controller name:'.Mage::app()->getRequest()->getControllerName());
       if (Mage::app()->getRequest()->getControllerName() != 'sales_order_create' && Mage::app()->getStore()->isAdmin()) {
	  //unset group session
          Mage::getSingleton('adminhtml/session')->unsetData('customer_group');
       }

    }
    
    
}
