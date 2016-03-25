<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_CountOfCarouselItems extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => '1', 'label' => $hlp->__('One')),
			array('value' => '2', 'label' => $hlp->__('Two')),
            array('value' => '3', 'label' => $hlp->__('Three')),
			array('value' => '4', 'label' => $hlp->__('Four')),
			array('value' => '5', 'label' => $hlp->__('Five')),
			array('value' => '6', 'label' => $hlp->__('Six')),
			array('value' => '7', 'label' => $hlp->__('Seven')),
		);
	}
	
}