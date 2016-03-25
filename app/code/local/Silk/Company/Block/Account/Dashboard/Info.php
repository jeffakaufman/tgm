<?php

class Silk_Company_Block_Account_Dashboard_Info extends Mage_Core_Block_Template
{
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	public function getCompany()
	{
		$session = $this->_getSession();
		$_customer = $session->getCustomer();
		return $_customer->getCompany();
	}
}