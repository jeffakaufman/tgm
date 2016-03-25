<?php
require_once Mage::getModuleDir('controllers', 'Mage_Sales').DS.'OrderController.php';
class Silk_Company_Sales_OrderController extends Mage_Sales_OrderController
{
      protected function _getSession()
        {
            return Mage::getSingleton('catalog/session');
        }
         /**
         * Initialize order model instance
         *
         * @return Mage_Sales_Model_Order || false
         */
        protected function _initOrder()
        {
            $id = $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($id);
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('This order no longer exists.'));
                $this->_redirect('*/*/');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return false;
            }
            Mage::register('sales_order', $order);
            Mage::register('current_order', $order);
            return $order;
        }
           /**
         * Cancel order
         */
        public function cancelAction()
        {
            $store_id = Mage::app()->getStore()->getStoreId();
            if ($order = $this->_initOrder()) {
                try {
                    $order->cancel();
                    $order->save();
                    Mage::app()->setCurrentStore($store_id);
                    $this->_getSession()->addSuccess(
                        $this->__('The order has been cancelled.')
                    );
                }
                catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                    Mage::logException($e);
                }
                $this->_redirect('*/order/history');
            }
        }
        public function approveAction()
        {
            $store_id = Mage::app()->getStore()->getStoreId();
            $notify = false;
            $visible = false;
            if ($order = $this->_initOrder()) {
                try {
                    $order->setStatus('pending_approved');
                    $order->addStatusHistoryComment()
                           ->setIsVisibleOnFront($visible)
                          ->setIsCustomerNotified($notify);
                    $order->save();
                    Mage::app()->setCurrentStore($store_id);
                    $this->_getSession()->addSuccess(
                        $this->__('The order has been approved.')
                    );
                }
                catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($this->__('The order has not been approved.'));
                    Mage::logException($e);
                }
                $this->_redirect('*/order/history');
            }
        }
}
 ?>
