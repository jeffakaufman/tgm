<?php

class Silk_Carrier_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
   public function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
         if(array_key_exists('silk_carrier',$this->_rates)){
         	     foreach ($this->_rates as $code => $_rates) {
	        	if($code!='silk_carrier'){
	        		unset($this->_rates[$code]);
	        	}
	      }
         }	        
        return $this->_rates;
    }
}

?>