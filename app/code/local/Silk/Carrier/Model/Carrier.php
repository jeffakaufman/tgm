<?php
class Silk_Carrier_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
        protected $_code = 'silk_carrier';

        public function collectRates(Mage_Shipping_Model_Rate_Request $request)
        {
                 if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
                    return false;
                }
                $region=explode(',', Mage::getStoreConfig('carriers/'.$this->_code.'/region'));
                $ship_price=Mage::getStoreConfig('carriers/'.$this->_code.'/price');
                //county
                $regionCode = $request->getDestRegionCode();
                if(in_array($regionCode, $region)){
                    return false;
                }
                $result = Mage::getModel('shipping/rate_result');  
                $price='';  
                if ($request->getAllItems()) {
                        if( count($request->getAllItems())>1){
                                $status=false;
                                foreach ($request->getAllItems() as $item) {
                                    if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                                        continue;
                                    }
                                    $product = $item->getProduct();
                                    if($product->getHasInstallationFee()){
                                         $status=true;
                                    }
                                }
                                if($status){
                                        foreach ($request->getAllItems() as $item) {
                                            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                                                continue;
                                            }
                                            $qty=$item->getQty();
                                            $price += (float)$ship_price*(int)$qty;
                                        }
                                        $result->append($this->_getExpressShippingRate($price));
                                }                        
                        }else{
                                foreach ($request->getAllItems() as $item) {
                                    if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                                        continue;
                                    } 
                                    $product = $item->getProduct();
                                    if ($product->getHasInstallationFee()) {
                                        $qty=$item->getQty();
                                        $price = (float)$ship_price*(int)$qty;
                                        $result->append($this->_getExpressShippingRate($price)); 
                                    }
                                }                          
                        }           
                }   
            return $result;
        }
        protected function _getExpressShippingRate($price)
        {
            $rate = Mage::getModel('shipping/rate_result_method');
            /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('wgs');
            $rate->setMethodTitle('White Glove Service');
            $rate->setPrice($price);
            $rate->setCost($price);
            return $rate;
        }
        public function getAllowedMethods()
        {

               return array(
                    'wgs'=>'White Glove Service'
                );
        }
}