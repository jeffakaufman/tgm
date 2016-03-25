<?php 

class Silk_Carrier_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
	 public function getShippingRates()
	 {

	        if (empty($this->_rates)) {
	            $this->getAddress()->collectShippingRates()->save();

	            $groups = $this->getAddress()->getGroupedAllShippingRates();
	                 if(array_key_exists('silk_carrier',$groups)){
		         	     foreach ($groups as $code => $_rates) {
			        	if($code!='silk_carrier'){
			        		unset($groups[$code]);
			        	}
			      }
		       }	        
	            return $this->_rates = $groups;
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