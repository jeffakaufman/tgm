<?php 
class Silk_Misc_Model_Mysql4_Misc extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('misc/misc', 'misc_id');
    }
}

 ?>