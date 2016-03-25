<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Mysql4_FrontCatalogResourceEavMysql4ProductIndexerPrice extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
{
    // overide parent
    protected function _prepareWebsiteDateTable()
    {
        $write = $this->_getWriteAdapter();

        $baseCurrency = Mage::app()->getBaseCurrencyCode();

        $select = $write->select()
            ->from(
                array('cw' => $this->getTable('core/website')),
                array('website_id'))
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'cw.default_group_id = csg.group_id',
                array('store_id' => 'default_store_id'))
                
            ->where('cw.code != "aitoccode"') // aitoc code
            
            ->where('cw.website_id != 0');
        $is11Version = version_compare(Mage::getVersion(),'1.11.0.0','>=');
        $data = array();
        foreach ($write->fetchAll($select) as $item) {
            /* @var $website Mage_Core_Model_Website */
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

            /* @var $store Mage_Core_Model_Store */
            $store = Mage::app()->getStore($item['store_id']);
            if ($store) {
                $timestamp = Mage::app()->getLocale()->storeTimeStamp($store);
                $date = $is11Version ? 'website_date' : 'date';
                $data[] = array(
                    'website_id' => $website->getId(),
                    $date       => $this->formatDate($timestamp, false),
                    'rate'       => $rate
                );
            }
        }
        
        $write->beginTransaction();
        $table = $this->_getWebsiteDateTable();
        $write->delete($table);
        
        if ($data) {
            $write->insertMultiple($table, $data);
        }
        if($is11Version)
        {
            $write->commit();            
        }
        
        return $this;
    }    
    
}
