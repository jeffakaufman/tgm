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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Silk_Misc_Model_Paypal_Payflowpro extends  Mage_Paypal_Model_Payflowpro
{


     /**
      * Return request object with information for 'authorization' or 'sale' action
      *
      * @param Mage_Sales_Model_Order_Payment $payment
      * @param float $amount
      * @return Varien_Object
      */
    protected function _buildPlaceRequest(Varien_Object $payment, $amount)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setAmt(round($amount,2));
        $request->setAcct($payment->getCcNumber());
        $request->setExpdate(sprintf('%02d',$payment->getCcExpMonth()) . substr($payment->getCcExpYear(),-2,2));
        $request->setCvv2($payment->getCcCid());

        if ($this->getIsCentinelValidationEnabled()){
            $params = array();
            $params = $this->getCentinelValidator()->exportCmpiData($params);
            $request = Varien_Object_Mapper::accumulateByMap($params, $request, $this->_centinelFieldMap);
        }

        $order = $payment->getOrder();
        if(!empty($order)){
            $request->setCurrency($order->getBaseCurrencyCode());

            $orderIncrementId = $order->getIncrementId();
            $poNumber         = $payment->getPoNumber();
            $request->setCustref($poNumber)
                ->setComment1($orderIncrementId)
                ->setComment2($poNumber);
                
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setFirstname($billing->getFirstname())
                    ->setLastname($billing->getLastname())
                    ->setStreet(implode(' ', $billing->getStreet()))
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegionCode())
                    ->setZip($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setEmail($payment->getOrder()->getCustomerEmail());
            }
            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $this->_applyCountryWorkarounds($shipping);
                $request->setShiptofirstname($shipping->getFirstname())
                    ->setShiptolastname($shipping->getLastname())
                    ->setShiptostreet(implode(' ', $shipping->getStreet()))
                    ->setShiptocity($shipping->getCity())
                    ->setShiptostate($shipping->getRegionCode())
                    ->setShiptozip($shipping->getPostcode())
                    ->setShiptocountry($shipping->getCountry());
            }
        }
        return $request;
    }

}
