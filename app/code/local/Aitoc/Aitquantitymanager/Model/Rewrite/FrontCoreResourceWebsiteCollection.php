<?php
class Aitoc_Aitquantitymanager_Model_Rewrite_FrontCoreResourceWebsiteCollection extends Mage_Core_Model_Resource_Website_Collection
{
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        
        $idsSelect->where('main_table.code != "aitoccode"');
		
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        return $this->getConnection()->fetchCol($idsSelect);
    }
}