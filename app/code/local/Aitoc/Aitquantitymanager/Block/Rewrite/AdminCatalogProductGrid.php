<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitpermissions% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitpermissions')){
    class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid_Aittmp extends Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductGrid {} 
 }else{
    /* default extends start */
    class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid_Aittmp extends Mage_Adminhtml_Block_Catalog_Product_Grid {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid extends Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid_Aittmp
{
    // override parent
    protected function _prepareCollection()
    {
        $store = $this->_getStore(); // aitoc code
        $iWebsiteId = $store->getWebsiteId(); // aitoc code
        
        if (!$iWebsiteId)
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
        }
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $iWebsiteId, // aitoc code
                'left');
#d($iWebsiteId);
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            if(version_compare(Mage::getVersion(), '1.11.0.0', '<'))
            {
                $collection->addAttributeToSelect('status');
                $collection->addAttributeToSelect('visibility');
            }
            else
            {
                $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
                $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            }
        }

        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection(); // aitoc code
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
}
