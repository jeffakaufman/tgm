<?php
class Silk_Company_Block_Order_Buttons extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('company/order/buttons.phtml');
    }
    public function getOrder()
    {
        return Mage::registry('current_order');
    }
    public function getApproveUrl($order)
    {
        return $this->getUrl('company/sales_order/approve', array('order_id' => $order->getId()));
    }
     public function getCancelUrl($order)
    {
        return $this->getUrl('company/sales_order/cancel', array('order_id' => $order->getId()));
    }
    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('*/*/history');
        }
        return Mage::getUrl('*/*/form');
    }
    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::helper('sales')->__('Back to My Orders');
        }
        return Mage::helper('sales')->__('View Another Order');
    }
}
?>
