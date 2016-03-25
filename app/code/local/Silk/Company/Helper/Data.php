<?php
/**
 * Silk_Company extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Silk
 * @package        Silk_Company
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Company default helper
 *
 * @category    Silk
 * @package     Silk_Company
 * @author      jacob.shi@silksoftware.com
 */
class Silk_Company_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
    public function getOrderUrl()
    {
        return $this->_getUrl('sales/order/history');
    }
    public function getRole(){
        $role = array();
        $role[]= Silk_Company_Model_Role::getRole('ADMIN');
        $role[]= Silk_Company_Model_Role::getRole('MANAGER');
        return $role;
    }
    public function getCustomerId($company_id)
    {
        $customers = Mage::getResourceModel('customer/customer_collection')
                     ->addAttributeToSelect('entity_id')
                     ->addAttributeToFilter('company_id',$company_id)
                     ;
        $customer_id = array();
        foreach ($customers as $customer) {
           $customer_id[] = $customer->getId();
        }
        return $customer_id;
    }

    public function canProcessOrder(Mage_Sales_Model_Order $order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $session_customer = Mage::getSingleton('customer/session')->getCustomer();
        // add role validate
        if(!in_array($session_customer->getRoleId(),$this->getRole())) return false;
        // order status
        $status = $order->getStatus();
        if(preg_match('#approved#i', $status)|| preg_match('#cancel#i', $status)) return false; //approved || cancel
        //add b2b vilidate
        $order_customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if($session_customer->getIsB2b() && ($session_customer->getCompanyId() == $order_customer->getCompanyId())) return true;
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            return true;
        }
        return false;
    }
    public function getBuyer($order)
    {
        $order_customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        return $order_customer->getName();
    }
}
