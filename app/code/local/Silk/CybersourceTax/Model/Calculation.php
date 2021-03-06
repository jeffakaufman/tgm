<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cybersource Tax Calculation Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Silk_CybersourceTax_Model_Calculation extends Mage_Tax_Model_Calculation
{


    /**
     * Calculate rated tax abount based on price and tax rate.
     * If you are using price including tax $priceIncludeTax should be true.
     *
     * @param   float $price
     * @param   float $taxRate
     * @param   boolean $priceIncludeTax
     * @return  float
     */

    protected $_item = array();
    

    public function calcCybersourceTaxAmount($address = NULL)
    {
        
        $scope = 'core/session';       

        if (!Mage::getStoreConfig('tax/cybersourcetax_soap/active')) return 0;

        /**
         * These are sent from some GUI and assembled into the applicable arrays.
         */

        $primaryAddress = Mage::getSingleton('customer/session')->getCustomer()
            ->getPrimaryShippingAddress();

        $primaryBillAddress = Mage::getSingleton('customer/session')->getCustomer()
            ->getPrimaryBillingAddress();

        $street1 = '';
        $city = '';
        $state = '';
        $stateCode = '';
        $country = 'US';
        $postcode = '';
        $firstname = '';
        $lastname = '';
        $email = '';
        $stateId = 0;

        if ($primaryBillAddress) {
            $firstname = $primaryBillAddress->getFirstName();
            $lastname = $primaryBillAddress->getLastName();
            $email = $primaryBillAddress->getEmail();
            $street1 = $primaryBillAddress->getStreet();
            $city = $primaryBillAddress->getCity();
            $state = $primaryBillAddress->getRegion();
            $stateCode = $primaryBillAddress->getRegionCode();
            $stateId = $primaryBillAddress->getRegionId();
            $country = $primaryBillAddress->getCountryId();
            $postcode = $primaryBillAddress->getPostcode();            
        }

        if ($primaryAddress) {
            $firstname = $primaryAddress->getFirstName();
            $lastname = $primaryAddress->getLastName();
            $email = $primaryAddress->getEmail();
            $street1 = $primaryAddress->getStreet();
            $city = $primaryAddress->getCity();
            $state = $primaryAddress->getRegion();
            $stateCode = $primaryAddress->getRegionCode();
            $stateId = $primaryAddress->getRegionId();
            $country = $primaryAddress->getCountryId();
            $postcode = $primaryAddress->getPostcode();            
        }
        if ($address) {           
            $city = $address->getCity();
            $state = $address->getRegion();
            $stateCode = $address->getRegionCode();
            $stateId = $address->getRegionId() ? $address->getRegionId() : 0;
            $country = $address->getCountryId();
            $postcode = $address->getPostcode();
        }



        $bill_array = array('firstName' => $firstname, 'lastName' => $lastname, 'street1' => $street1,
            'city' => $city, 'state' => $state, 'postalCode' => $postcode, 'country' => $country,
            'email' => $email, 'ipaddress' => Mage::helper('core/http')->getRemoteAddr());

        $item_array = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (Mage::app()->getStore()->isAdmin()) {
            $scope = 'adminhtml/session';
            $taxscope =  'adminhtml/session';
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
            $customer   = $quote->getCustomer(); 

            $gid = isset($_POST['order']['account']['group_id'])?$_POST['order']['account']['group_id']:Mage::getSingleton($scope)->getCustomerGroup();
            if($gid){
                $groupModel = Mage::getModel('customer/group')->load($gid);
                $customerTaxClass = $groupModel->getTaxClassId();
            }else{
                $customerTaxClass = $customer->getTaxClassId();   
            }
        } else {
            $scope = 'core/session';
            $taxscope =  'silk_cybersourcetax/session';
            $customerTaxClass = Mage::getSingleton('customer/session')->getCustomer()->getTaxClassId();
        }
        

        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, Mage::app()->getStore());
        $cartItems = $quote->getAllItems();

        $totalprice = 0;
        $last_item = 0;
        $total_sub = $quote->getTotals();

        $prev_shipfee = (Mage::getSingleton($scope)->getCyberShipfee()===''?'null':Mage::getSingleton($scope)->getCyberShipfee());
        
        $shipfee = 'null';
        if(isset($total_sub['shipping'])){
            $shipfee = $total_sub['shipping']->getValue();
        }
        Mage::getSingleton($scope)->setCyberShipfee($shipfee);

    	$productClassFilters = explode(',', Mage::getStoreConfig('tax/cybersourcetax_soap/product_tax_class'));
        foreach ($cartItems as $it) {
          if (in_array($it->getTaxClassId(), $productClassFilters)) {
            $this->_item[] = $it->getId();
            $pr = $it->getPrice(); 
            $orgprice = $pr * $it->getQty();
            $fiprice = $orgprice - $it->getDiscountAmount();
	        $totalprice += $fiprice;
            $item_array[$it->getId()] = array('unitPrice' => $fiprice);
            $last_item = $it->getId();
          }
        }

        
        $last_changed = false;
        if($last_item != Mage::getSingleton($scope)->getLastItem() && $last_item) {       
            Mage::getSingleton($scope)->setLastItem($last_item);       
            $last_changed = true;
        }

	    $customerClassFilters = explode(',', Mage::getStoreConfig('tax/cybersourcetax_soap/customer_tax_class'));
        $stateFilters = explode(',', Mage::getStoreConfig('tax/cybersourcetax_soap/region_id'));

        $debug = Mage::getStoreConfig('tax/cybersourcetax_soap/debug');
        
         
        $totaltaxs = array('total_tax'=>0, 'district_tax'=>0, 'state_tax'=>0, 'county_tax'=>0, 'city_tax'=>0);
        
        $prev_total = Mage::getSingleton($scope)->getCyberTotal();
        $prev_addr = Mage::getSingleton($scope)->getCyberAddr();
        $prev_group = Mage::getSingleton($scope)->getCustomerGroup();       
        
        $cart_changed = ($totalprice!==$prev_total || $last_changed);       

        $addr_changed = ($state.$city.$postcode!=$prev_addr);
        $group_changed = (isset($_POST['order']['account']['group_id']) && $_POST['order']['account']['group_id']!=$prev_group);

        $shipmethod = ($address->getShippingMethod() && $shipfee!=='null');
        $ship_changed = ($shipfee!==$prev_shipfee);       
        if ($shippingTaxClass){
            //add shipping fee to cal tax
            $item_array['shipping'] = array('unitPrice' => $shipfee);
        }else{
            $item_array['shipping'] = array('unitPrice' => 0);
            $ship_changed = false;
            $shipmethod = true;
        }

        if($addr_changed) {
            Mage::getSingleton($scope)->setCyberAddr($state.$city.$postcode);
        }

        if($group_changed && ($_POST['order']['account']['group_id']!='')){
            Mage::getSingleton($scope)->setCustomerGroup($_POST['order']['account']['group_id']);
        }

        if ($ship_changed){
            Mage::getSingleton($scope)->setCyberShipFee($shipfee);
        }


        Mage::getSingleton($scope)->setCyberTotal($totalprice);

        $prev_caltax = Mage::getSingleton($taxscope)->getCyberTotalTax();
       
        //add log 10.16,2014 
        Mage::log('Addr:'.$state.'|'.$city.'|'.$postcode.', last item:'.$last_item.', customerTaxClass:'.$customerTaxClass.', state Id:'.$stateId, null, 'cybersource_tax.log'); 

        if (in_array($customerTaxClass, $customerClassFilters) && in_array($stateId, $stateFilters)){

          if (($cart_changed || $addr_changed || $group_changed || $ship_changed) && $last_item && ($shipmethod || $quote->getIsVirtual())){

        /**
         * Authorize a transaction.
         */
        try {
            $soap = new Silk_CybersourceTax_Model_Api_Cybertax();
            $soap->setReferenceCode(array('ST', 'YYYY', 'J', '-', 'RNDM'));
            $soap->setTaxRequest($bill_array, $item_array);
            //echo '<pre>';
            //print_r($soap->getSoapRequest());
            //echo '</pre>';
            if ($debug) {
                Mage::log('Request: '.print_r($soap->getSoapRequest(),true),null,'cybersource_tax.log');
            }
        } catch (SoapFault $e) {
            exit($e->getMessage());
        }
        try {
            $result = $soap->runTax();

            if ($debug) {
                Mage::log('Response: '.print_r($result,true),null,'cybersource_tax.log');
            }

            $summary = array();
            $itemTax = array();

            if ($result->reasonCode == 100) {
                if(Mage::app()->getStore()->isAdmin()){
                   $totals = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getTotals();
                }else{
                   $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals();
                }
                $discount = 0;
                $taxes = new stdClass();

                $subtotal = $totalprice;
                $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, Mage::app()->getStore());
                if ($shippingTaxClass){
                    $subtotal = $subtotal+$shipfee;
                }

                $itemobj = array();
                if (!is_array($result->item)) {
                    $itemobj[] = $result->item;
                } else {
                    $itemobj = $result->item;
                }

                $stateTax = 0;
                $cityTax = 0;
                $countyTax = 0;
                $districtTax = 0;
                $totalTax = 0;

                foreach ($itemobj as $ctl) {
                    if (isset($this->_item[$ctl->id])) {
                        $id = $this->_item[$ctl->id];
                    }else{
                        $id = 'shipping';
                    }
                    $rate = 0;
                    $amt = 0;
                    $rate = ($ctl->totalTaxAmount && $item_array[$id]['unitPrice']? $ctl->totalTaxAmount / $item_array[$id]['unitPrice'] : 0) * 100;
                    $amt = $ctl->totalTaxAmount;
                    $stateTax = $stateTax + $ctl->stateTaxAmount;
                    $cityTax = $cityTax + $ctl->cityTaxAmount;
                    $countyTax = $countyTax + $ctl->countyTaxAmount;
                    $districtTax = $districtTax + $ctl->districtTaxAmount;
                    $totalTax = $totalTax + $ctl->totalTaxAmount;

                    $itemTax[$id] = array(
                        'rate' => $rate,
                        'amt' => $amt
                    );

                }

                    $summary[] = $totaltaxs['state_tax'] = array(
                        'name' => 'State Tax',
                        'rate' => number_format($stateTax / $subtotal, 4) * 100,
                        'amt' => $stateTax
                    );
                    $summary[] = $totaltaxs['city_tax'] = array(
                        'name' => 'City Tax',
                        'rate' => number_format($cityTax / $subtotal, 4) * 100,
                        'amt' => $cityTax
                    );
                    $summary[] = $totaltaxs['county_tax'] = array(
                        'name' => 'County Tax',
                        'rate' => number_format($countyTax / $subtotal, 4) * 100,
                        'amt' => $countyTax
                    );
                    $summary[] = $totaltaxs['district_tax'] = array(
                        'name' => 'District Tax',
                        'rate' => number_format($districtTax / $subtotal, 4) * 100,
                        'amt' => $districtTax
                    );

                    $totaltaxs['total_tax'] = $totalTax;
                    Mage::getSingleton($taxscope)->setSummary($summary);
                    Mage::getSingleton($taxscope)->setItemTax($itemTax);
            }

        } catch (Exception $e) {
            exit($e->getMessage());
        }
      }elseif(count($prev_caltax)>0){
            $totaltaxs = $prev_caltax;
      }
     }else{
         Mage::getSingleton($taxscope)->setSummary(array());
         Mage::getSingleton($taxscope)->setItemTax(array());
     }

        Mage::getSingleton($taxscope)->setCyberTotalTax($totaltaxs);

        return $totaltaxs;
    }

   
}
