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
 * Tax totals modification block. Can be used just as subblock of Mage_Sales_Block_Order_Totals
 */
class Silk_CybersourceTax_Block_Sales_Order_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    

    /**
     * Initialize all order totals relates with tax
     *
     * @return Mage_Tax_Block_Sales_Order_Tax
     */
    public function initTotals()
    {
        /** @var $parent Mage_Adminhtml_Block_Sales_Order_Invoice_Totals */
        $parent = $this->getParentBlock();
        $this->_order   = $parent->getOrder();
        $this->_source  = $parent->getSource();
        
        //$this->_initBreakdownTax('city_tax', 'City Tax', $this->_source->getCityTax());
        //$this->_initBreakdownTax('county_tax', 'County Tax', $this->_source->getCountyTax());
        //$this->_initBreakdownTax('district_tax', 'District Tax', $this->_source->getDistrictTax());
       // $this->_initBreakdownTax('state_tax', 'State Tax', $this->_source->getStateTax());

        $store = $this->getStore();
        $allowTax = ($this->_source->getTaxAmount() > 0) || ($this->_config->displaySalesZeroTax($store));
        $grandTotal = (float) $this->_source->getGrandTotal();
        if (!$grandTotal || ($allowTax && !$this->_config->displaySalesTaxWithGrandTotal($store))) {
            $this->_addTax();
        }
        
        $this->_initSubtotal();
        $this->_initShipping();
        $this->_initDiscount();
        $this->_initGrandTotal();
        return $this;
    }


 protected function _initBreakdownTax($code, $title, $val)
    {
        if ($val>0) {   
            $taxTotal = new Varien_Object(array(
                'code'      => $code,
                'value'     => $val,
                'label'=> $title
            ));
            $this->getParentBlock()->addTotal($taxTotal);
        } 
        return $this;
    }
    
    
    
    public function getFullTaxInfo()
    {
        $fullInfo = array();
        $summary = array();
        $total = 0;
        foreach ($this->_order->getAllItems() as $it){
            if ($it->getTaxAmount()>0) {
                $pr = $it->getPrice(); //+$it->getWeeeTaxAppliedAmount();
                $orgprice = $pr * $it->getQtyOrdered();
                $fiprice = $orgprice - $it->getDiscountAmount();
                $total += $fiprice;
            }
        }

        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, Mage::app()->getStore());
        if ($shippingTaxClass){
            $total = $total+$this->_order->getShippingAmount();
        }

        if ($this->_order->getStateTax() > 0){
            $r1 = number_format($this->_order->getStateTax()/$total, 8);
            $ra1 = number_format($this->_order->getStateTax()/$total, 4) * 100;
            $summary[] = array('name'=>'State Tax', 'rate'=>$ra1, 'amt'=>$r1 * $total);
        }        

        if ($this->_order->getCityTax() > 0){
            $r2 = number_format($this->_order->getCityTax()/$total, 8);
            $ra2 = number_format($this->_order->getCityTax()/$total, 4) * 100;
            $summary[] = array('name'=>'City Tax', 'rate'=>$ra2, 'amt'=>$r2 * $total);
        }
       
        if ($this->_order->getCountyTax() > 0){
            $r3 = number_format($this->_order->getCountyTax()/$total, 8);
            $ra3 = number_format($this->_order->getCountyTax()/$total, 4) * 100;
            $summary[] = array('name'=>'County Tax', 'rate'=>$ra3, 'amt'=>$r3 * $total);
        }
        
        if ($this->_order->getDistrictTax() > 0) {
            $r4 = number_format($this->_order->getDistrictTax()/$total, 8);
            $ra4 = number_format($this->_order->getDistrictTax()/$total, 4) * 100;
            $summary[] = array('name'=>'District Tax', 'rate'=>$ra4, 'amt'=>$r4 * $total);
        }
        
        foreach ($summary as $key => $row) {
            $id = 'cyber-' . $key;
            $fullInfo[$id] = array(
                'rates' => array(array(
                        'code' => $row['name'],
                        'title' => $row['name'],
                        'percent' => $row['rate'],
                        'position' => $key,
                        'priority' => $key,
                        'rule_id' => 0
                )),
                'percent' => $row['rate'],
                'id' => $id,
                'process' => 0,
                'amount' => $row['amt'],
                'base_amount' => $row['amt']
            );
        }

        return $fullInfo;
     }


    
    
}
