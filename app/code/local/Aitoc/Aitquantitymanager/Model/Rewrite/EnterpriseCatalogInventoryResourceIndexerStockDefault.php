<?php


class Aitoc_Aitquantitymanager_Model_Rewrite_EnterpriseCatalogInventoryResourceIndexerStockDefault
    extends Enterprise_CatalogInventory_Model_Resource_Indexer_Stock_Default
{
    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct()
    {
        $this->_init('aitquantitymanager/stock_status', 'product_id');
    }

    /**
     * Get the select object for get stock status by product ids
     *
     * @param int|array $entityIds
     * @param bool $usePrimaryTable use primary or temporary index table
     * @return Varien_Db_Select
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
// start aitoc code        

        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'));
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('cw.website_id')
            ->join(
#                array('cis' => $this->getTable('cataloginventory/stock')),
                array('cis' => Mage::helper('aitquantitymanager')->getCataloginventoryStockTable()), // aitoc code
                '',
                array('stock_id'))
            ->joinLeft(
#                array('cisi' => $this->getTable('cataloginventory/stock_item')),
                array('cisi' => $this->getTable('aitquantitymanager/stock_item')),
//                'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
                'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id AND cisi.product_id = e.entity_id AND cisi.website_id = pw.website_id', // aitoc code
#                'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id AND cisi.website_id= ' . $iWebsiteId, // aitoc code
                array())
            ->columns(array('qty' => new Zend_Db_Expr('IF(cisi.qty > 0, cisi.qty, 0)')))
            ->where('cw.website_id != 0')
#            ->where('cw.website_id != ' . Mage::helper('aitquantitymanager')->getHiddenWebsiteId()) // aitoc code
#            ->where('cw.website_id = ' . $iWebsiteId) // aitoc code
            ->where('e.type_id = ?', $this->getTypeId());

        // add limitation of status
        $condition = $adapter->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $condition);

        if ($this->_isManageStock()) {
            $statusExpr = new Zend_Db_Expr('IF(cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 0,'
                . ' 1, cisi.is_in_stock)');
        } else {
            $statusExpr = new Zend_Db_Expr('IF(cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 1,'
                . 'cisi.is_in_stock, 1)');
        }

        $select->columns(array('status' => $statusExpr));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
#d(get_ait_debug_print_backtrace(20), 1,1);        
        
# d($select->__tostring(),0);        
        return $select;
    }

}