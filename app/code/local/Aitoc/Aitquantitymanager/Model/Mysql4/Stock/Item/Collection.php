<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitquantitymanager_Model_Mysql4_Stock_Item_Collection extends Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
{
    protected function _construct()
    {
///ait/        $this->_init('cataloginventory/stock_item');
        $this->_init('aitquantitymanager/stock_item');   
    }

}