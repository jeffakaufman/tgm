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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml order tax totals block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Silk_CybersourceTax_Block_Adminhtml_Sales_Order_Totals_Tax extends Silk_CybersourceTax_Block_Sales_Order_Tax
{
    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var $source Mage_Sales_Model_Order */
        $cuorder = $this->getOrder();

        $fullInfo = array();
        $summary = array();

        $finalTotal = 0;
        foreach ($cuorder->getAllItems() as $it){
            if ($it->getTaxAmount()>0) {
                $pr = $it->getPrice(); //+$it->getWeeeTaxAppliedAmount();
                $orgprice = $pr * $it->getQtyOrdered();
                $fiprice = $orgprice - $it->getDiscountAmount();
                $finalTotal += $fiprice;
            }
        }

        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, Mage::app()->getStore());
        if ($shippingTaxClass && $cuorder->getShippingTaxAmount()>0){
            $finalTotal = $finalTotal + $cuorder->getShippingAmount();
        }
        if ($cuorder->getStateTax() > 0){
            $r1 = number_format($cuorder->getStateTax()/$finalTotal, 8);
            $ra1 = number_format($cuorder->getStateTax()/$finalTotal, 4) * 100;
            $summary[] = array('name'=>'State Tax', 'rate'=>$ra1, 'amt'=>$r1 * $finalTotal);
        }
        
        if ($cuorder->getCityTax() > 0){
            $r2 = number_format($cuorder->getCityTax()/$finalTotal, 8);
            $ra2 = number_format($cuorder->getCityTax()/$finalTotal, 4) * 100;
            $summary[] = array('name'=>'City Tax', 'rate'=>$ra2, 'amt'=>$r2 * $finalTotal);
        } 
        if ($cuorder->getCountyTax() > 0){
            $r3 = number_format($cuorder->getCountyTax()/$finalTotal, 8);
            $ra3 = number_format($cuorder->getCountyTax()/$finalTotal, 4) * 100;
            $summary[] = array('name'=>'County Tax', 'rate'=>$ra3, 'amt'=>$r3 * $finalTotal);
        }

        if ($cuorder->getDistrictTax() > 0){
            $r4 = number_format($cuorder->getDistrictTax()/$finalTotal, 8);
            $ra4 = number_format($cuorder->getDistrictTax()/$finalTotal, 4) * 100;
            $summary[] = array('name'=>'District Tax', 'rate'=>$ra4, 'amt'=>$r4 * $finalTotal);
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
                'title' => $row['name'],
                'tax_amount' => $row['amt'],
                'base_tax_amount' => $row['amt']
            );
        }

        return $fullInfo;
    }

    /**
     * Display tax amount
     *
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return Mage::helper('adminhtml/sales')->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }


     public function getCurrentObj($source){
        if (Mage::registry('current_invoice')) {
            $current = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $current = Mage::registry('current_creditmemo');
        } else {
            $current = $source;
        }

        return $current;

    }

}
