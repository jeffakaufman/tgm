<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

class Amasty_Paction_Model_Source_Append
{
    public function toOptionArray()
    {
        $helper = Mage::helper('ampaction');
        return array(
            array(
                'value' => $helper->getAppendTextBefore(),
                'label' => 'Before Attribute Text',
            ),
            array(
                'value' => $helper->getAppendTextAfter(),
                'label' => 'After Attribute Text',
            ),
        );
    }
}