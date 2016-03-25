<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Mysql4_Core_Website extends Mage_Core_Model_Mysql4_Website
{
// start aitoc code    
    public function getIdByCode($sCode)
    {
        if (!$sCode) return false;
        
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('website_id'))
            ->where('code=?', $sCode);

        return $this->_getReadAdapter()->fetchOne($select);
    }
// finish aitoc code    
    
}