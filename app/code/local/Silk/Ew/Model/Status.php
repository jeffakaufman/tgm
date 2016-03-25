<?php
class Silk_Ew_Model_Status extends Varien_Object
{
    const STATUS_NEW	= 1;
    const STATUS_REFURBISHED = 2;
    const STATUS_USED = 3;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_NEW    => Mage::helper('ew')->__('New'),
            self::STATUS_REFURBISHED  => Mage::helper('ew')->__('Refurbished')
            self::STATUS_USED   => Mage::helper('ew')->__('Used')
        );
    }
}
