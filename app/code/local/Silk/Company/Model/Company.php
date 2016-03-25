<?php

class Silk_Company_Model_Company extends Mage_Core_Model_Abstract
{
	protected function _construct(){
       $this->_init("company/company");
    }

    public function loadByCustomer($customer)
    {
    	
    }
}