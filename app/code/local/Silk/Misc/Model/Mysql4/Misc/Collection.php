<?php 

class Silk_Misc_Model_Mysql4_Misc_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('misc/misc');
    }
}



 ?>