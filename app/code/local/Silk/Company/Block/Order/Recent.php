<?php
class Silk_Company_Block_Order_Recent extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        //TODO: add full name logic
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $role_id =$customer->getRoleId();

        if($role_id && in_array($role_id,Mage::helper('company')->getRole())){
             $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->addAttributeToFilter('customer_id', array('in' => Mage::helper('company')->getCustomerId($customer->getCompanyId())))
                ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addAttributeToSort('created_at', 'desc')
                ->setPageSize('5')
                ->load()
            ;
        }else{
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addAttributeToSort('created_at', 'desc')
                ->setPageSize('5')
                ->load()
            ;
        }
        $this->setOrders($orders);
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('company/order/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('company/order/track', array('order_id' => $order->getId()));
    }

    protected function _toHtml()
    {
        if ($this->getOrders()->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('company/order/reorder', array('order_id' => $order->getId()));
    }
     public function getApproveUrl($order)
    {
        return $this->getUrl('company/sales_order/approve', array('order_id' => $order->getId()));
    }
     public function getCancelUrl($order)
    {
        return $this->getUrl('company/sales_order/cancel', array('order_id' => $order->getId()));
    }
}
