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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales Order Total PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Silk_CybersourceTax_Model_Sales_Order_Pdf_Total_Taxdetail extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix().$amount;
        }
        $label = Mage::helper('sales')->__($this->getTitle()) . ':';
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        
        if (preg_match('/_tax/', $this->getSourceField())) {
            $fontSize = 7;
        }
        
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }

    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getFullTaxInfo()
    {
        
        /** @var $source Mage_Sales_Model_Order */
        $source = $this->getOrder();

        $fullInfo = array();
        
        $summary = array();
        if ($source->getStateTax() > 0)
            $summary[] = array('name'=>'State Tax', 'rate'=>number_format($source->getStateTax()/$source->getSubtotal(), 4) * 100, 'amt'=>$source->getStateTax());
        
        if ($source->getCityTax() > 0)
            $summary[] = array('name'=>'City Tax', 'rate'=>number_format($source->getCityTax()/$source->getSubtotal(), 4) * 100, 'amt'=>$source->getCityTax());
       
        if ($source->getCountyTax() > 0)
            $summary[] = array('name'=>'County Tax', 'rate'=>number_format($source->getCountyTax()/$source->getSubtotal(), 4) * 100, 'amt'=>$source->getCountyTax());
        
        if ($source->getDistrictTax() > 0)
            $summary[] = array('name'=>'District Tax', 'rate'=>number_format($source->getDistrictTax()/$source->getSubtotal(), 4) * 100, 'amt'=>$source->getDistrictTax());
        
        foreach ($summary as $key => $row) {
            $id = 'cyber-' . $key;
            $fullInfo[$id] = array(
                'percent' => $row['rate'],
                'id' => $id,
                'process' => 0,
                'label' => $row['name'],
                'amount' => $row['amt'],
                'font_size' => 7,
                'base_amount' => $row['amt']
            );
        }

        return $fullInfo;
        
        
        
    }

    /**
     * Check if we can display total information in PDF
     *
     * @return bool
     */
    public function canDisplay()
    {
        $amount = $this->getAmount();
        return $this->getDisplayZero() || ($amount != 0);
    }

    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod($this->getSourceField());
    }
}
