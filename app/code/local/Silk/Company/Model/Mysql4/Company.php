<?php
class Silk_Company_Model_Mysql4_Company extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("company/company", "id");
    }
}