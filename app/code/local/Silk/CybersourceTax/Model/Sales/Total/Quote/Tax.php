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
 * Tax totals calculation model
 */ 
class Silk_CybersourceTax_Model_Sales_Total_Quote_Tax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    
    /**
     * Tax caclulation for shipping price
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
     
      /**
     * Tax module helper
     *
     * @var Mage_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * Tax calculation model
     *
     * @var Mage_Tax_Model_Calculation
     */
    protected $_calculator;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config;

    /**
     * Flag which is initialized when collect method is start.
     * Is used for checking if store tax and customer tax requests are similar
     *
     * @var bool
     */
    protected $_areTaxRequestsSimilar = false;


    protected $_roundingDeltas = array();
    protected $_baseRoundingDeltas = array();

    protected $_store;

    /**
     * Hidden taxes array
     *
     * @var array
     */
    protected $_hiddenTaxes = array();

    /**
     * Class constructor
     */
     
     
     
    public function __construct()
    {
        $this->setCode('tax');
        $this->_helper      = Mage::helper('tax');
        $this->_calculator  = Mage::getSingleton('tax/calculation');
        $this->_config      = Mage::getSingleton('tax/config');
    }
     
     
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $store = $address->getQuote()->getStore();

        $address->setTaxAmount(0);
        $address->setBaseTaxAmount(0);
        //$address->setShippingTaxAmount(0);
        //$address->setBaseShippingTaxAmount(0);
        $address->setAppliedTaxes(array());
        
        
                 
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        
        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();
        $itemtaxes = array();
        

        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        /* @var $taxCalculationModel Mage_Tax_Model_Calculation */
        $request = $taxCalculationModel->getRateRequest(
            $address,
            $address->getQuote()->getBillingAddress(),
            $custTaxClassId,
            $store
        );

       $this->_calculateCyberTax($address, $request);
       if(Mage::app()->getStore()->isAdmin()){
            $itemtaxes = Mage::getSingleton('adminhtml/session')->getItemTax();
        }else{
            $itemtaxes = Mage::getSingleton('silk_cybersourcetax/session')->getItemTax();
        }

        foreach ($items as $item) {
            /**
             * Child item's tax we calculate for parent
             */

            
            if ($item->getParentItemId()) {
                continue;
            }
            /**
             * We calculate parent tax amount as sum of children's tax amounts
             */

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                
                foreach ($item->getChildren() as $child) {
                    $discountBefore = $item->getDiscountAmount();
                    $baseDiscountBefore = $item->getBaseDiscountAmount();

                    //$rate = $taxCalculationModel->getRate(
                        //$request->setProductClassId($child->getProduct()->getTaxClassId())
                    //);

                //$child->setTaxPercent($rate);
                //print_r($itemtaxes);
                 $rate = 0;
                
                 if (isset($itemtaxes[$child->getId()]['rate']) && isset($itemtaxes[$child->getId()]['amt']) ) {
                     $child->setTaxPercent($itemtaxes[$child->getId()]['rate']);
                     $rate = $itemtaxes[$child->getId()]['rate'];
                     $child->setTaxAmount($itemtaxes[$child->getId()]['amt']);
                     $child->setBaseTaxPercent($itemtaxes[$child->getId()]['rate']);
                     $child->setBaseTaxAmount($itemtaxes[$child->getId()]['amt']);
                 }else{
                    $child->setTaxPercent(0);
                    $child->setTaxAmount(0);
                    $child->setBaseTaxPercent(0);
                    $child->setBaseTaxAmount(0);
                  }

                    //$child->calcTaxAmount();

                    if ($discountBefore != $item->getDiscountAmount()) {
                        $address->setDiscountAmount(
                            $address->getDiscountAmount() + ($item->getDiscountAmount() - $discountBefore)
                        );
                        $address->setBaseDiscountAmount(
                            $address->getBaseDiscountAmount() + ($item->getBaseDiscountAmount() - $baseDiscountBefore)
                        );

                        $address->setGrandTotal(
                            $address->getGrandTotal() - ($item->getDiscountAmount() - $discountBefore)
                        );
                        $address->setBaseGrandTotal(
                            $address->getBaseGrandTotal() - ($item->getBaseDiscountAmount() - $baseDiscountBefore)
                        );
                    }

                    $this->_saveAppliedTaxes(
                       $address,
                       $taxCalculationModel->getAppliedRates($request),
                       $child->getTaxAmount(),
                       $child->getBaseTaxAmount(),
                       $rate
                    );
                }
                $itemTaxAmount = $item->getTaxAmount() + $item->getDiscountTaxCompensation();
                $address->setTaxAmount($address->getTaxAmount() + $itemTaxAmount);
                $itemBaseTaxAmount = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation();
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $itemBaseTaxAmount);
            } else {
                $discountBefore = $item->getDiscountAmount();
                $baseDiscountBefore = $item->getBaseDiscountAmount();

                $rate = $taxCalculationModel->getRate(
                    $request->setProductClassId($item->getProduct()->getTaxClassId())
                );
                
                //print_r($itemtaxes);
                
                 if (isset($itemtaxes[$item->getId()]['rate']) && isset($itemtaxes[$item->getId()]['amt']) ) {
                     $item->setTaxPercent($itemtaxes[$item->getId()]['rate']);
                     $item->setTaxAmount($itemtaxes[$item->getId()]['amt']);
                     $item->setBaseTaxPercent($itemtaxes[$item->getId()]['rate']);
                     $item->setBaseTaxAmount($itemtaxes[$item->getId()]['amt']);
                 }else{
                    $item->setTaxPercent(0);
                    $item->setTaxAmount(0);
                    $item->setBaseTaxPercent(0);
                    $item->setBaseTaxAmount(0);
                  }
                 

                if ($discountBefore != $item->getDiscountAmount()) {
                    $address->setDiscountAmount(
                        $address->getDiscountAmount() + ($item->getDiscountAmount() - $discountBefore)
                    );
                    $address->setBaseDiscountAmount(
                        $address->getBaseDiscountAmount() + ($item->getBaseDiscountAmount() - $baseDiscountBefore)
                    );

                    $address->setGrandTotal(
                        $address->getGrandTotal() - ($item->getDiscountAmount() - $discountBefore)
                    );
                    $address->setBaseGrandTotal(
                        $address->getBaseGrandTotal() - ($item->getBaseDiscountAmount() - $baseDiscountBefore)
                    );
                }

                $itemTaxAmount = $item->getTaxAmount() + $item->getDiscountTaxCompensation();
                //$address->setTaxAmount($address->getTaxAmount()+$itemTaxAmount);
                $itemBaseTaxAmount = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation();
                //$address->setBaseTaxAmount($address->getBaseTaxAmount() + $itemBaseTaxAmount);

                $applied = $taxCalculationModel->getAppliedRates($request);
                $this->_saveAppliedTaxes(
                   $address,
                   $applied,
                   $item->getTaxAmount(),
                   $item->getBaseTaxAmount(),
                   $rate
                );
            }
        }

               
        if (!Mage::helper('tax')->shippingPriceIncludesTax() && $itemtaxes) {
            $address->setBaseShippingTaxAmount($itemtaxes['shipping']['amt']);
            $address->setShippingTaxAmount($itemtaxes['shipping']['amt']);
        }

        //$address->setGrandTotal($address->getGrandTotal() + $address->getTaxAmount());
        //$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseTaxAmount());
        // $flag = Mage::getSingleton('core/session')->getCaflag();
         //if (!isset($flag) || !$flag) {
          // Mage::getSingleton('core/session')->setCaflag(1);
        // } else {
         //  Mage::getSingleton('core/session')->setCaflag(0);
        // }
         //if(in_array($customerTaxClass, $customerClassFilters)) {
 
        // }

                 
        
        return $this;
    }



    protected function _calculateCyberTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        $rate           = $this->_calculator->getRate($taxRateRequest);
        $inclTax        = $address->getIsShippingInclTax();
        $shipping       = $address->getShippingTaxable();
        $baseShipping   = $address->getBaseShippingTaxable();
        $rateKey        = (string)$rate;
        
        $totals =  Mage::getSingleton('checkout/session')->getQuote()->getTotals();
        $subtotal = 0;
        $discount = 0;
        $taxes = array();
        
        if (!empty($totals["subtotal"]))  $subtotal = $totals["subtotal"]->getValue(); 
        if (!empty($totals['discount']))  $discount = $totals['discount']->getValue();

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $taxes        = $this->_calculator->calcCybersourceTaxAmount($address);
                $tax = $taxes['total_tax'];
                $baseTax    = $taxes['total_tax'];
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $address->getShippingDiscountAmount();
                $baseDiscountAmount = $address->getBaseShippingDiscountAmount();
                $taxes = $this->_calculator->calcCybersourceTaxAmount($address);
                $tax = $taxes['total_tax'];
                $baseTax = $taxes['total_tax'];
                break;
        }

        if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
            $tax        = $this->_deltaRound($tax, $rate, $inclTax);
            $baseTax    = $this->_deltaRound($baseTax, $rate, $inclTax, 'base');
        } else {
            $tax        = $this->_calculator->round($tax);
            $baseTax    = $this->_calculator->round($baseTax);
        }
        
        $this->_addAmount(max(0, $tax));
        $address->setTaxesAmount('state_tax', $taxes['state_tax']['amt']);
        $address->setTaxesAmount('city_tax', $taxes['city_tax']['amt']);
        $address->setTaxesAmount('county_tax',  $taxes['county_tax']['amt']);
        $address->setTaxesAmount('district_tax',  $taxes['district_tax']['amt']);
        
        $this->_addBaseAmount(max(0, $baseTax));
        $address->setShippingTaxAmount(max(0, $tax));
        $address->setBaseShippingTaxAmount(max(0, $baseTax));
        $applied = $this->_calculator->getAppliedRates($taxRateRequest);
        $this->_saveAppliedTaxes($address, $applied, $tax, $baseTax, $rate);
        return $this;
    }


 public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $applied    = $address->getAppliedTaxes();
        $store      = $address->getQuote()->getStore();
        $amount     = $address->getTaxAmount();

        $items = $this->_getAddressItems($address);
        $discountTaxCompensation = 0;
        foreach ($items as $item) {
            $discountTaxCompensation += $item->getDiscountTaxCompensation();
        }
        $taxAmount = $amount + $discountTaxCompensation;
            
            
            
        $fullInfo = array();
        
        $summary = Mage::getSingleton('silk_cybersourcetax/session')->getSummary();

        if (count($summary) > 0) {
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
        }

      if (($amount != 0) || (Mage::helper('tax')->displayZeroTax($store))) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('tax')->__('Tax'),
                'full_info' => $fullInfo,
                'value' => $amount,
                'area' => null
            ));
        }

        return $this;
    }





 /**
     * Process hidden taxes for items and shippings (in accordance with hidden tax type)
     *
     * @return void
     */
    protected function _processHiddenTaxes()
    {
        $this->_getAddress()->setTotalAmount('hidden_tax', 0);
        $this->_getAddress()->setBaseTotalAmount('hidden_tax', 0);
        $this->_getAddress()->setTotalAmount('shipping_hidden_tax', 0);
        $this->_getAddress()->setBaseTotalAmount('shipping_hidden_tax', 0);
        foreach ($this->_hiddenTaxes as $taxInfoItem) {
            if (isset($taxInfoItem['item'])) {
                // Item hidden taxes
                $item           = $taxInfoItem['item'];
                $rateKey        = $taxInfoItem['rate_key'];
                $hiddenTax      = $taxInfoItem['value'];
                $baseHiddenTax  = $taxInfoItem['base_value'];
                $inclTax        = $taxInfoItem['incl_tax'];
                $qty            = $taxInfoItem['qty'];

                if ($this->_config->getAlgorithm($this->_store) == Mage_Tax_Model_Calculation::CALC_TOTAL_BASE) {
                    $hiddenTax      = $this->_deltaRound($hiddenTax, $rateKey, $inclTax);
                    $baseHiddenTax  = $this->_deltaRound($baseHiddenTax, $rateKey, $inclTax, 'base');
                } else {
                    $hiddenTax      = $this->_calculator->round($hiddenTax);
                    $baseHiddenTax  = $this->_calculator->round($baseHiddenTax);
                }

                $item->setHiddenTaxAmount(max(0, $qty * $hiddenTax));
                $item->setBaseHiddenTaxAmount(max(0, $qty * $baseHiddenTax));
                $this->_getAddress()->addTotalAmount('hidden_tax', $item->getHiddenTaxAmount());
                $this->_getAddress()->addBaseTotalAmount('hidden_tax', $item->getBaseHiddenTaxAmount());
            } else {
                // Shipping hidden taxes
                $rateKey        = $taxInfoItem['rate_key'];
                $hiddenTax      = $taxInfoItem['value'];
                $baseHiddenTax  = $taxInfoItem['base_value'];
                $inclTax        = $taxInfoItem['incl_tax'];

                $hiddenTax      = $this->_deltaRound($hiddenTax, $rateKey, $inclTax);
                $baseHiddenTax  = $this->_deltaRound($baseHiddenTax, $rateKey, $inclTax, 'base');

                $this->_getAddress()->setShippingHiddenTaxAmount(max(0, $hiddenTax));
                $this->_getAddress()->setBaseShippingHiddenTaxAmount(max(0, $baseHiddenTax));
                $this->_getAddress()->addTotalAmount('shipping_hidden_tax', $hiddenTax);
                $this->_getAddress()->addBaseTotalAmount('shipping_hidden_tax', $baseHiddenTax);
            }
        }
    }

    /**
     * Check if price include tax should be used for calculations.
     * We are using price include tax just in case when catalog prices are including tax
     * and customer tax request is same as store tax request
     *
     * @param $store
     * @return bool
     */
    protected function _usePriceIncludeTax($store)
    {
        if ($this->_config->priceIncludesTax($store) || $this->_config->getNeedUsePriceExcludeTax()) {
            return $this->_areTaxRequestsSimilar;
        }
        return false;
    }

   
    /**
     * Calculate address tax amount based on one unit price and tax amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _unitBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items = $this->_getAddressItems($address);
        $itemTaxGroups  = array();
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcUnitTaxAmount($child, $rate);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes(
                        $address,
                        $applied,
                        $child->getTaxAmount(),
                        $child->getBaseTaxAmount(),
                        $rate
                    );
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            }
            else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcUnitTaxAmount($item, $rate);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes(
                    $address,
                    $applied,
                    $item->getTaxAmount(),
                    $item->getBaseTaxAmount(),
                    $rate
                );
                $item->setTaxRates($applied);
            }
        }
        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     * Calculate unit tax anount based on unit price
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate)
    {
        $qty        = $item->getTotalQty();
        $inclTax    = $item->getIsPriceInclTax();
        $price      = $item->getTaxableAmount() + $item->getExtraTaxableAmount();
        $basePrice  = $item->getBaseTaxableAmount() + $item->getBaseExtraTaxableAmount();
        $rateKey    = (string)$rate;
        $item->setTaxPercent($rate);

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_config->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $unitTax        = $this->_calculator->calcTaxAmount($price, $rate, $inclTax);
                $baseUnitTax    = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $item->getDiscountAmount() / $qty;
                $baseDiscountAmount = $item->getBaseDiscountAmount() / $qty;

                $unitTax = $this->_calculator->calcTaxAmount($price, $rate, $inclTax);
                $discountRate = ($unitTax/$price) * 100;
                $unitTaxDiscount = $this->_calculator->calcTaxAmount($discountAmount, $discountRate, $inclTax, false);
                $unitTax = max($unitTax - $unitTaxDiscount, 0);
                $baseUnitTax = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax);
                $baseDiscountRate = ($baseUnitTax/$basePrice) * 100;
                $baseUnitTaxDiscount = $this->_calculator
                    ->calcTaxAmount($baseDiscountAmount, $baseDiscountRate, $inclTax, false);
                $baseUnitTax = max($baseUnitTax - $baseUnitTaxDiscount, 0);

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax      = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
                    $baseHiddenTax  = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => $qty,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                } elseif ($discountAmount > $price) { // case with 100% discount on price incl. tax
                    $hiddenTax      = $discountAmount - $price;
                    $baseHiddenTax  = $baseDiscountAmount - $basePrice;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => $qty,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                }
                break;
        }
        $item->setTaxAmount($this->_store->roundPrice(max(0, $qty*$unitTax)));
        $item->setBaseTaxAmount($this->_store->roundPrice(max(0, $qty*$baseUnitTax)));

        return $this;
    }

    /**
     * Calculate address total tax based on row total
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _rowBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items = $this->_getAddressItems($address);
        $itemTaxGroups  = array();
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $this->_calcRowTaxAmount($child, $rate);
                    $this->_addAmount($child->getTaxAmount());
                    $this->_addBaseAmount($child->getBaseTaxAmount());
                    $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied;
                    }
                    $this->_saveAppliedTaxes(
                        $address,
                        $applied,
                        $child->getTaxAmount(),
                        $child->getBaseTaxAmount(),
                        $rate
                    );
                    $child->setTaxRates($applied);
                }
                $this->_recalculateParent($item);
            }
            else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $this->_calcRowTaxAmount($item, $rate);
                $this->_addAmount($item->getTaxAmount());
                $this->_addBaseAmount($item->getBaseTaxAmount());
                $applied = $this->_calculator->getAppliedRates($taxRateRequest);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied;
                }
                $this->_saveAppliedTaxes(
                    $address,
                    $applied,
                    $item->getTaxAmount(),
                    $item->getBaseTaxAmount(),
                    $rate
                );
                $item->setTaxRates($applied);
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);
        return $this;
    }

    /**
     * Calculate item tax amount based on row total
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _calcRowTaxAmount($item, $rate)
    {
        $inclTax        = $item->getIsPriceInclTax();
        $subtotal       = $item->getTaxableAmount() + $item->getExtraRowTaxableAmount();
        $baseSubtotal   = $item->getBaseTaxableAmount() + $item->getBaseExtraRowTaxableAmount();
        $rateKey        = (string)$rate;
        $item->setTaxPercent($rate);

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTax     = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                $discountAmount     = $item->getDiscountAmount();
                $baseDiscountAmount = $item->getBaseDiscountAmount();
                $rowTax = $this->_calculator->calcTaxAmount(
                    max($subtotal - $discountAmount, 0),
                    $rate,
                    $inclTax
                );
                $baseRowTax = $this->_calculator->calcTaxAmount(
                    max($baseSubtotal - $baseDiscountAmount, 0),
                    $rate,
                    $inclTax
                );
                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax      = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
                    $baseHiddenTax  = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => 1,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                } elseif ($discountAmount > $subtotal) { // case with 100% discount on price incl. tax
                    $hiddenTax      = $discountAmount - $subtotal;
                    $baseHiddenTax  = $baseDiscountAmount - $baseSubtotal;
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => 1,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                }
                break;
        }
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));
        return $this;
    }

    /**
     * Calculate address total tax based on address subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Varien_Object $taxRateRequest
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _totalBaseCalculation(Mage_Sales_Model_Quote_Address $address, $taxRateRequest)
    {
        $items          = $this->_getAddressItems($address);
        $store          = $address->getQuote()->getStore();
        $taxGroups      = array();
        $itemTaxGroups  = array();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                    $rate = $this->_calculator->getRate($taxRateRequest);
                    $applied_rates = $this->_calculator->getAppliedRates($taxRateRequest);
                    $taxGroups[(string)$rate]['applied_rates'] = $applied_rates;
                    $taxGroups[(string)$rate]['incl_tax'] = $child->getIsPriceInclTax();
                    $this->_aggregateTaxPerRate($child, $rate, $taxGroups);
                    if ($rate > 0) {
                        $itemTaxGroups[$child->getId()] = $applied_rates;
                    }
                }
                $this->_recalculateParent($item);
            } else {
                $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                $rate = $this->_calculator->getRate($taxRateRequest);
                $applied_rates = $this->_calculator->getAppliedRates($taxRateRequest);
                $taxGroups[(string)$rate]['applied_rates'] = $applied_rates;
                $taxGroups[(string)$rate]['incl_tax'] = $item->getIsPriceInclTax();
                $this->_aggregateTaxPerRate($item, $rate, $taxGroups);
                if ($rate > 0) {
                    $itemTaxGroups[$item->getId()] = $applied_rates;
                }
            }
        }

        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);

        foreach ($taxGroups as $rateKey => $data) {
            $rate = (float) $rateKey;
            $inclTax = $data['incl_tax'];
            $totalTax = $this->_calculator->calcTaxAmount(array_sum($data['totals']), $rate, $inclTax);
            $baseTotalTax = $this->_calculator->calcTaxAmount(array_sum($data['base_totals']), $rate, $inclTax);
            $this->_addAmount($totalTax);
            $this->_addBaseAmount($baseTotalTax);
            $this->_saveAppliedTaxes($address, $data['applied_rates'], $totalTax, $baseTotalTax, $rate);
        }
        return $this;
    }

    /**
     * Aggregate row totals per tax rate in array
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @param   float $rate
     * @param   array $taxGroups
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups)
    {
        $inclTax        = $item->getIsPriceInclTax();
        $rateKey        = (string) $rate;
        $taxSubtotal    = $subtotal     = $item->getTaxableAmount() + $item->getExtraRowTaxableAmount();
        $baseTaxSubtotal= $baseSubtotal = $item->getBaseTaxableAmount() + $item->getBaseExtraRowTaxableAmount();
        $item->setTaxPercent($rate);

        if (!isset($taxGroups[$rateKey]['totals'])) {
            $taxGroups[$rateKey]['totals'] = array();
            $taxGroups[$rateKey]['base_totals'] = array();
        }

        $hiddenTax      = null;
        $baseHiddenTax  = null;
        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTax             = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTax         = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                if ($this->_helper->applyTaxOnOriginalPrice($this->_store)) {
                    $discount           = $item->getOriginalDiscountAmount();
                    $baseDiscount       = $item->getBaseOriginalDiscountAmount();
                } else {
                    $discount           = $item->getDiscountAmount();
                    $baseDiscount       = $item->getBaseDiscountAmount();
                }

                $taxSubtotal        = max($subtotal - $discount, 0);
                $baseTaxSubtotal    = max($baseSubtotal - $baseDiscount, 0);
                $rowTax             = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, $inclTax, false);
                $baseRowTax         = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, $inclTax, false);
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                        $subtotal,
                        $rate,
                        $inclTax,
                        false
                    );
                    $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                        $baseSubtotal,
                        $rate,
                        $inclTax,
                        false
                    );
                }

                if ($inclTax && $discount > 0) {
                    $hiddenTax      = $this->_calculator->calcTaxAmount($discount, $rate, $inclTax, false);
                    $baseHiddenTax  = $this->_calculator->calcTaxAmount($baseDiscount, $rate, $inclTax, false);
                    $this->_hiddenTaxes[] = array(
                        'rate_key'   => $rateKey,
                        'qty'        => 1,
                        'item'       => $item,
                        'value'      => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax'   => $inclTax,
                    );
                }
                break;
        }

        $rowTax     = $this->_deltaRound($rowTax, $rateKey, $inclTax);
        $baseRowTax = $this->_deltaRound($baseRowTax, $rateKey, $inclTax, 'base');
        $item->setTaxAmount(max(0, $rowTax));
        $item->setBaseTaxAmount(max(0, $baseRowTax));

        if (isset($rowTaxBeforeDiscount) && isset($baseRowTaxBeforeDiscount)) {
            $taxBeforeDiscount = max(
                0,
                $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax)
            );
            $baseTaxBeforeDiscount = max(
                0,
                $this->_deltaRound($baseRowTaxBeforeDiscount, $rateKey, $inclTax, 'base')
            );

            $item->setDiscountTaxCompensation($taxBeforeDiscount - max(0, $rowTax));
            $item->setBaseDiscountTaxCompensation($baseTaxBeforeDiscount - max(0, $baseRowTax));
        }

        $taxGroups[$rateKey]['totals'][]        = max(0, $taxSubtotal);
        $taxGroups[$rateKey]['base_totals'][]   = max(0, $baseTaxSubtotal);
        return $this;
    }

    /**
     * Round price based on previous rounding operation delta
     *
     * @param float $price
     * @param string $rate
     * @param bool $direction price including or excluding tax
     * @param string $type
     * @return float
     */
    protected function _deltaRound($price, $rate, $direction, $type='regular')
    {
        if ($price) {
            $rate  = (string) $rate;
            $type  = $type . $direction;
            $delta = isset($this->_roundingDeltas[$type][$rate]) ? $this->_roundingDeltas[$type][$rate] : 0;
            $price += $delta;
            $this->_roundingDeltas[$type][$rate] = $price - $this->_calculator->round($price);
            $price = $this->_calculator->round($price);
        }
        return $price;
    }

    /**
     * Recalculate parent item amounts base on children data
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $rowTaxAmount       = 0;
        $baseRowTaxAmount   = 0;
        foreach ($item->getChildren() as $child) {
            $rowTaxAmount       += $child->getTaxAmount();
            $baseRowTaxAmount   += $child->getBaseTaxAmount();
        }
        $item->setTaxAmount($rowTaxAmount);
        $item->setBaseTaxAmount($baseRowTaxAmount);
        return $this;
    }

    /**
     * Collect applied tax rates information on address level
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   array $applied
     * @param   float $amount
     * @param   float $baseAmount
     * @param   float $rate
     */
    protected function _saveAppliedTaxes(Mage_Sales_Model_Quote_Address $address, $applied, $amount, $baseAmount, $rate)
    {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $process = count($previouslyAppliedTaxes);

        foreach ($applied as $row) {
            if ($row['percent'] == 0) {
                continue;
            }
            if (!isset($previouslyAppliedTaxes[$row['id']])) {
                $row['process']     = $process;
                $row['amount']      = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if (!is_null($row['percent'])) {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount      = $amount/$rate*$row['percent'];
                $baseAppliedAmount  = $baseAmount/$rate*$row['percent'];
            } else {
                $appliedAmount      = 0;
                $baseAppliedAmount  = 0;
                foreach ($row['rates'] as $rate) {
                    $appliedAmount      += $rate['amount'];
                    $baseAppliedAmount  += $rate['base_amount'];
                }
            }


            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) {
                $previouslyAppliedTaxes[$row['id']]['amount']       += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount']  += $baseAppliedAmount;
            } else {
                unset($previouslyAppliedTaxes[$row['id']]);
            }
        }
        $address->setAppliedTaxes($previouslyAppliedTaxes);
    }

    

    /**
     * Process model configuration array.
     * This method can be used for changing totals collect sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        $calculationSequence = $this->_helper->getCalculationSequence($store);
         switch ($calculationSequence) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $config['before'][] = 'discount';
                break;
            default:
                $config['after'][] = 'discount';
                break;
        }
        return $config;
    }

    /**
     * Get Tax label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('tax')->__('Tax');
    }
   
}

