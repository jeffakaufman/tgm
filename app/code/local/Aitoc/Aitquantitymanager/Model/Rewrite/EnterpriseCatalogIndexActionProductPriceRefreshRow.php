<?php
/**
 * @copyright  Copyright (c) 2013 AITOC, Inc. 
 */
class Aitoc_Aitquantitymanager_Model_Rewrite_EnterpriseCatalogIndexActionProductPriceRefreshRow
extends Enterprise_Catalog_Model_Index_Action_Product_Price_Refresh_Row
{
    /**
     * Prepare website current dates table
     *
     * @return Enterprise_Catalog_Model_Index_Action_Product_Price_Abstract
     */
    protected function _prepareWebsiteDateTable()
    {
        $write = $this->_connection;
        $baseCurrency = Mage::app()->getBaseCurrencyCode();

        $select = $write->select()
            ->from(
                array('cw' => $this->_getTable('core/website')),
                array('website_id')
            )
            ->join(
                array('csg' => $this->_getTable('core/store_group')),
                'cw.default_group_id = csg.group_id',
                array('store_id' => 'default_store_id')
            )
            ->where('cw.website_id NOT IN (0, '. Mage::helper('aitquantitymanager')->getHiddenWebsiteId().')'); // aitoc changes there


        $data = array();
        foreach ($write->fetchAll($select) as $item) {
            /** @var $website Mage_Core_Model_Website */
            $website = Mage::app()->getWebsite($item['website_id']);

            if ($website->getBaseCurrencyCode() != $baseCurrency) {
                $rate = Mage::getModel('directory/currency')
                    ->load($baseCurrency)
                    ->getRate($website->getBaseCurrencyCode());
                if (!$rate) {
                    $rate = 1;
                }
            } else {
                $rate = 1;
            }

            /** @var $store Mage_Core_Model_Store */
            $store = Mage::app()->getStore($item['store_id']);
            if ($store) {
                $timestamp = Mage::app()->getLocale()->storeTimeStamp($store);
                $data[] = array(
                    'website_id'   => $website->getId(),
                    'website_date' => Varien_Date::formatDate($timestamp, false),
                    'rate'         => $rate
                );
            }
        }

        $table = $this->_getTable('catalog/product_index_website');
        $this->_emptyTable($table);
        if ($data) {
            $write->insertMultiple($table, $data);
        }

        return $this;
    }
    
    /**
     * Prepare tier price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return Enterprise_Catalog_Model_Index_Action_Product_Price_Abstract
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $write = $this->_connection;
        $table = $this->_getTable('catalog/product_index_tier_price');
        $this->_emptyTable($table);

        $websiteExpression = $write->getCheckSql('tp.website_id = 0', 'ROUND(tp.value * cwd.rate, 4)', 'tp.value');
        $select = $write->select()
            ->from(
                array('tp' => $this->_getTable(array('catalog/product', 'tier_price'))),
                array('entity_id')
            )
            ->join(
                array('cg' => $this->_getTable('customer/customer_group')),
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id')
            )
            ->join(
                array('cw' => $this->_getTable('core/website')),
                'tp.website_id = 0 OR tp.website_id = cw.website_id',
                array('website_id')
            )
            ->join(
                array('cwd' => $this->_getTable('catalog/product_index_website')),
                'cw.website_id = cwd.website_id',
                array()
            )
            ->where('cw.website_id NOT IN (0, '. Mage::helper('aitquantitymanager')->getHiddenWebsiteId().')') // aitoc changes there
            ->columns(new Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }
    
    /**
     * Prepare group price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return Enterprise_Catalog_Model_Index_Action_Product_Price_Abstract
     */
    protected function _prepareGroupPriceIndex($entityIds = null)
    {
        $write = $this->_connection;
        $table = $this->_getTable('catalog/product_index_group_price');
        $this->_emptyTable($table);

        $websiteExpression = $write->getCheckSql('gp.website_id = 0', 'ROUND(gp.value * cwd.rate, 4)', 'gp.value');
        $select = $write->select()
            ->from(
                array('gp' => $this->_getTable(array('catalog/product', 'group_price'))),
                array('entity_id')
            )
            ->join(
                array('cg' => $this->_getTable('customer/customer_group')),
                'gp.all_groups = 1 OR (gp.all_groups = 0 AND gp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id')
            )
            ->join(
                array('cw' => $this->_getTable('core/website')),
                'gp.website_id = 0 OR gp.website_id = cw.website_id',
                array('website_id')
            )
            ->join(
                array('cwd' => $this->_getTable('catalog/product_index_website')),
                'cw.website_id = cwd.website_id',
                array()
            )
            ->where('cw.website_id NOT IN (0, '. Mage::helper('aitquantitymanager')->getHiddenWebsiteId().')') // aitoc changes there
            ->columns(new Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('gp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('gp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }
}