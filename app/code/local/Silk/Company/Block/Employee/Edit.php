<?php

class Silk_Company_Block_Employee_Edit extends Mage_Core_Block_Template
{
	protected function _getSession()
	{

	}

	public function getCustomer()
	{
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
			return Mage::getModel('customer/customer')->load($id);
		}
		else
		{
			return Mage::getModel('customer/customer');
		}
	}
}