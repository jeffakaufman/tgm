<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitquantitymanager_Model_Mysql4_Indexer_Stock
    extends Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
{

    protected $_defaultIndexer  = 'aitquantitymanager/indexer_stock_default';

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('aitquantitymanager/stock_status', 'product_id');
    }

    protected function _getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = Mage::getSingleton('catalog/product_type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['stock_indexer'])) {
                    $modelName = $typeInfo['stock_indexer'];
                } else {
                    $modelName = $this->_defaultIndexer;
                }
                $isComposite = !empty($typeInfo['composite']);
                
                
// start aitoc coded
                if ($modelName == 'bundle/indexer_stock')
                {
                    $indexer = Mage::getModel('aitquantitymanager/rewrite_frontBundleMysql4IndexerStock')
                        ->setTypeId($typeId)
                        ->setIsComposite($isComposite);
                }
                else 
                {
                    $indexer = Mage::getResourceModel($modelName)
                        ->setTypeId($typeId)
                        ->setIsComposite($isComposite);
                }
// finish aitoc code


                /*$indexer = Mage::getResourceModel($modelName)
                    ->setTypeId($typeId)
                    ->setIsComposite($isComposite);*/

                $this->_indexers[$typeId] = $indexer;
            }
        }
        return $this->_indexers;
    }
}