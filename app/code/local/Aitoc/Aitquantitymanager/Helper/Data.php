<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitquantitymanager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getHiddenWebsiteId()
    {
        $oModel = Mage::getModel('aitquantitymanager/mysql4_core_website');
        
        $iWebsiteId = $oModel->getIdByCode('aitoccode');
        
        return $iWebsiteId; 
    }
    
    public function getCataloginventoryStockTable()
    {
        $sOriginalAitocTable = 'aitoc_cataloginventory_stock_item'; 
        $sOriginalStockTable = 'cataloginventory_stock'; 
        
        $sCurrentAitocTable = Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_item');
        
        if ($sOriginalAitocTable == $sCurrentAitocTable)
        {
            $sCurrentStockTable = $sOriginalStockTable;
        }
        else 
        {
            $sPrefix = substr($sCurrentAitocTable, 0, strpos($sCurrentAitocTable, $sOriginalAitocTable));
            $sCurrentStockTable = $sPrefix . $sOriginalStockTable;
        }
        
        return $sCurrentStockTable; 
    }
    
    
}

?>