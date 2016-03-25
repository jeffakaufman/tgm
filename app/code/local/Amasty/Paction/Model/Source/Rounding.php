<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Source_Rounding
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'fixed',
                'label' => Mage::helper('ampaction')->__('To specific value')
            ),
            array(
                'value' => 'math',
                'label' => Mage::helper('ampaction')->__('By rules of mathematical rounding')
            ),
        );
    }
}