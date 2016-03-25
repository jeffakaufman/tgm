<?php
/**
 * @copyright  Copyright (c) 2013 AITOC, Inc. 
 */
class Aitoc_Aitquantitymanager_Model_Rewrite_EnterpriseCatalogIndexActionProductPriceRefresh
extends Enterprise_Catalog_Model_Index_Action_Product_Price_Refresh
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

		//aitoc code
		$aitTempWebsite = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
		
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
            ->where('cw.website_id != 0')
            ->where('cw.website_id != ?', $aitTempWebsite); // aitoc code

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
}