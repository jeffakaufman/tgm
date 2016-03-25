<?php
class Silk_Custom_Block_Custom_Order extends Mage_Core_Block_Template{
	public function getCustomVars(){
		$model = $this->getOrder()->getPayment();
		$vars['P.O.#']=$model->getPoNumber();
		return $vars;
	}
	public function getOrder()
	{
		return Mage::registry('current_order');
	}
}