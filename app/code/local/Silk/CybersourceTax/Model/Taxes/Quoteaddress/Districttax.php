<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Zerkella
 * @package     Zerkella_Wrapping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Silk_CybersourceTax_Model_Taxes_Quoteaddress_Districttax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('district_tax');
    }

    /**
     * Collect totals information about wrapping
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Zerkella_Wrapping_Model_Total_Wrapping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
          parent::collect($address);
        
        $total =  $address->getTaxesAmount('district_tax');

        $this->_addAmount($total); // store view currency amount

        // Also store in address for later reference in fetch()
        $address->setDistrictTax($total);


        return $this;
    }

    /**
     * Add shipping totals information to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Zerkella_Wrapping_Model_Total_Wrapping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDistrictTax();
        if ($amount != 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => 'District Tax',
                'value' => $amount
            ));
        }
        return $this;
    }
}
