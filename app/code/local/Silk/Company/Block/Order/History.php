<?php
class Silk_Company_Block_Order_History extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('company/order/history.phtml');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $role_id =$customer->getRoleId();

        if($role_id && in_array($role_id,Mage::helper('company')->getRole())){
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', array('in' => Mage::helper('company')->getCustomerId($customer->getCompanyId())))
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->setOrder('created_at', 'desc')
            ;
        }else{

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->setOrder('created_at', 'desc')
            ;
        }
        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('Company Orders'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'company.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
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
