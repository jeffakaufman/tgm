<?php

class WeltPixel_Selector_Model_Adminhtml_System_Config_Showprice
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'yes',
                'label' => 'Yes',
            ),
            array(
                'value' => 'no',
                'label' => 'No',
            ),
        );
    }
}