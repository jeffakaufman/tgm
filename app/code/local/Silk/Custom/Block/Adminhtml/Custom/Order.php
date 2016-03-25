<?php
class Silk_Custom_Block_Adminhtml_Custom_Order extends Mage_Adminhtml_Block_Sales_Order_Abstract{
	public function getCustomVars(){
		$model = $this->getOrder()->getPayment();
		$vars['P.O.#']=$model->getPoNumber();
		return $vars;
	}
}