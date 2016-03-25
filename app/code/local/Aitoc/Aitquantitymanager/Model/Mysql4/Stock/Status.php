<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Mysql4_Stock_Status extends Mage_CatalogInventory_Model_Mysql4_Stock_Status
{
    
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
#        $this->_init('cataloginventory/stock_status', 'product_id');
        $this->_init('aitquantitymanager/stock_status', 'product_id');
        
// tmp        
/*
        $oModel = Mage::getResourceModel('cataloginventory/stock_item');

        $sItemTable = $oModel->getTable('cataloginventory/stock_item');
        
        if (strpos($sItemTable, 'aitquantitymanager') !== false)
        {
            $sItemTable = str_replace('aitquantitymanager', 'cataloginventory', $sItemTable);
        }
        d(get_class_methods($oModel), 1);
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $sItemTable) , '*')
            ->joinInner(array('p' => $this->getTable('catalog/product_website')), 'main_table.product_id=p.product_id', 'website_id')
            ->joinInner(array('ait_item' => $oModel->getTable('cataloginventory/stock_item')), 'ait_item.product_id=p.product_id AND ait_item.website_id = p.website_id', 'product_id')
#            ->where('product_id =?', $iProductId)
#            ->where('stock_id=?', $stockId)
            ->where('ait_item.website_id IS NOT NULL')
;
#            ->where('website_id=?', Mage::helper('aitquantitymanager')->getHiddenWebsiteId());
#        return $this->_getReadAdapter()->fetchRow($select);
        $aItemList = $this->_getReadAdapter()->fetchAll($select);
        
        
        
d($aItemList);
d($select->__toString());

        d($sItemTable);
#        
        #        d(get_class($oModel));
        
#        d($oModel->getMainTable());
#        d($oModel->getTable('cataloginventory/stock_item'));
#d(get_class_methods($oModel));        
        
d(6,6);        
        */
// insert website
/*

INSERT into `catalog_product_website` (`product_id`, `website_id`)

(SELECT 

`main_table`.`entity_id`, 

3 as 'website_id'


FROM `catalog_product_entity` AS `main_table`)


///
INSERT into `aitquantitymanager_stock_item`

(SELECT 

null as 'item_id',

 `p`.`website_id`, 
`main_table`.`product_id`, 
`main_table`.`stock_id`, 

`main_table`.`qty`, 
`main_table`.`min_qty`, 
`main_table`.`use_config_min_qty`, 
`main_table`.`is_qty_decimal`, 
`main_table`.`backorders`, 
`main_table`.`use_config_backorders`, 
`main_table`.`min_sale_qty`, 
`main_table`.`use_config_min_sale_qty`, 
`main_table`.`max_sale_qty`, 
`main_table`.`use_config_max_sale_qty`, 
`main_table`.`is_in_stock`, 
`main_table`.`low_stock_date`, 
`main_table`.`notify_stock_qty`, 
`main_table`.`use_config_notify_stock_qty`, 
`main_table`.`manage_stock`, 
`main_table`.`use_config_manage_stock`, 
`main_table`.`stock_status_changed_automatically`, 

1 as 'use_default_website_stock'


FROM `cataloginventory_stock_item` AS `main_table`
 INNER JOIN `catalog_product_website` AS `p` ON main_table.product_id=p.product_id
LEFT JOIN `aitquantitymanager_stock_item` AS `ait_item` ON ait_item.product_id=p.product_id AND ait_item.website_id = p.website_id WHERE (ait_item.website_id IS NULL))
*/
//////////////////////////////////////////////////////////////


# `product_id`,`stock_id`,`website_id`        
    }

// start aitoc    

    public function getProductStatusHash($productIds, $stockId = 1)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('website_id', 'stock_status'))
            ->where('product_id IN(?)', $productIds)
            ->where('stock_id=?', $stockId);
//            ->where('website_id=?', $websiteId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    public function getProductDefaultStatus($productId, $stockId = 1)
    {

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), '*')
            ->where('product_id =?', $productId)
            ->where('stock_id=?', $stockId)
            ->where('website_id=?', Mage::helper('aitquantitymanager')->getHiddenWebsiteId());
        return $this->_getReadAdapter()->fetchRow($select);
    }

    
//fin aitoc

}
