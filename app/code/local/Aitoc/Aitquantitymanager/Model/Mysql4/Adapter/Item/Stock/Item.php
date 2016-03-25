<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Mysql4_Adapter_Item_Stock_Item extends Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
{
    
    /**
     * Indexed fields (PK) in tables of the module
     * @var array
     */
    protected $indexedFields = array('item_id', 'website_id', 'product_id', 'stock_id', 'low_stock_date');


    /**
     * Insert New data on merge
     *
     * @param array     $mappedWebsites
     * @param object    $connection
     * @param string    $entityName
     * @param array     $fields
     *
     * @return Aitoc_Aitquantitymanager_Model_Mysql4_Adapter_Item_StockItem
     */
    protected function _mergeTableDataInWebsiteScopeInsert($mappedWebsites, $connection, $entityName, $fields)
    {
        $srcTable    = $this->getTable($entityName);
        $targetTable = $this->_getStagingTableName($srcTable);
        
        $updateField = end($fields);

        foreach ($mappedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (empty($stagingWebsiteId) || empty($masterWebsiteIds)) {
                continue;
            }
            $stagingWebsiteId     = intval($stagingWebsiteId);
            $_websiteFieldNameSql = 'website_id';

            foreach ($masterWebsiteIds as $masterWebsiteId) {
                if (empty($masterWebsiteId)) {
                    continue;
                }
                $masterWebsiteId = intval($masterWebsiteId);
                
                $updateFields = array_filter($fields, array($this, 'filterFields'));                                
                
                $updateFieldsSql = array();
                
                foreach ($updateFields as $updateField)
                {
                    $updateFieldsSql[] = '`' . $updateField . '` = VALUES(`' . $updateField . '`)';
                }
                
                $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE " . implode(', ', $updateFieldsSql);
                
                $_fields = $fields;
                foreach ($_fields as $id => $field) {
                    if ($field == 'website_id') {
                        //$_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "{$field} = {$stagingWebsiteId}";
                        unset($_fields[$id]);                        
                    } elseif ($field == 'scope_id') {
                        //$_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "`scope` = 'websites' AND `{$field}` = {$stagingWebsiteId}";
                    }
                }
                
                $_fields=array_merge(array('website_id' => new Zend_Db_Expr($masterWebsiteId)),$_fields);
                
                $srcSelectSql = $this->_getSimpleSelect($srcTable, $_fields, $_websiteFieldNameSql);
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

				if (is_null($connection))
                {
                    $connection = $this->_getWriteAdapter();
                }
				
                $connection->query($destInsertSql);
                
            }
        }
        return $this;
    }
    
    /**
     * Detects indexed fields
     * @param string $field
     * @return boolean
     */
    public function filterFields($field)
    {
        return !in_array($field, $this->indexedFields);
    }
}