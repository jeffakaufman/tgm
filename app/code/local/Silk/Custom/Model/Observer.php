<?php
class Silk_Custom_Model_Observer{
	public function saveQuoteBefore($evt){
		$quote = $evt->getQuote();
		$post = Mage::app()->getFrontController()->getRequest()->getPost();
		$payment=$quote->getPayment();
		if(isset($post['custom']['comment'])){
			$var = $post['custom']['comment'];
			$payment->setPoNumber($var);

		}
	}
	public function saveQuoteAfter($evt){
	        $payment = $evt->getQuote()->getPayment();
	        $var=$payment->getPoNumber();
	        if(!empty($var)){
	                $payment->save();
            	}

	    }
	public function saveOrderAfter($evt){
		$qpayment = $evt->getQuote()->getPayment();
		$opayment=$evt->getOrder()->getPayment();
		$var = $qpayment->getPoNumber();
		if(!empty($var)){
			$opayment->setPoNumber($var);
			$opayment->save();
		}
	}
	public function createOrderAfter($evt){;
		$order  =  $evt->getOrder();
		$post = Mage::app()->getFrontController()->getRequest()->getPost();
		if(isset($post['custom']['PoNumber'])){
			$var = $post['custom']['PoNumber'];
			$opayment=$order->getPayment();
			$opayment->setPoNumber($var);
			$opayment->save();
		}
	}
}