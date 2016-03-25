<?php 
require_once "Enterprise/Checkout/controllers/Adminhtml/CheckoutController.php";

class Silk_Custom_Adminhtml_CheckoutController extends Enterprise_Checkout_Adminhtml_CheckoutController
{
	    public function createOrderAction()
	    {
	        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/create')) {
	            Mage::throwException(Mage::helper('enterprise_checkout')->__('Access denied.'));
	        }
	        try {
	            $this->_initData();
	            if ($this->_redirectFlag) {
	                return;
	            }
	            $activeQuote = $this->getCartModel()->getQuote();
	            $quote = $this->getCartModel()->copyQuote($activeQuote);
	            $PoNumber=$activeQuote->getPayment()->getPoNumber();
	            if ($quote->getId()) {
	                $session = Mage::getSingleton('adminhtml/sales_order_create')->getSession();
	                $session->setQuoteId($quote->getId())
	                   ->setStoreId($quote->getStoreId())
	                   ->setCustomerId($quote->getCustomerId());

	            }
	            $this->_redirect('*/sales_order_create', array(
	                'customer_id' => Mage::registry('checkout_current_customer')->getId(),
	                'store_id' => Mage::registry('checkout_current_store')->getId(),
	                "PoNumber"=>$PoNumber,
	            ));
	            return;
	        } catch (Mage_Core_Exception $e) {
	            $this->_getSession()->addError($e->getMessage());
	        } catch (Exception $e) {
	            Mage::logException($e);
	            $this->_getSession()->addError(
	                Mage::helper('enterprise_checkout')->__('An error has occurred. See error log for details.')
	            );
	        }
	        $this->_redirect('*/*/error');
	    }
	    public function getPoNumberAction(){
	    	$params=$this->getRequest()->getParams();
	    	$number=$params['PoNumber'];
	    	$paymentId=$params['paymentId'];
	    	$payment=Mage::getSingleton('sales/quote_payment');
	    	$payment->load($paymentId);
	    	$payment->setPoNumber($number);
	    	$payment->save();	    	
	    }
}

 ?>