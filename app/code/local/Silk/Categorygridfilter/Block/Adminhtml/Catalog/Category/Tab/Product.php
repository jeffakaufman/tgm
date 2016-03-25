<?php
class Silk_Categorygridfilter_Block_Adminhtml_Catalog_Category_Tab_Product extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product
{
    protected function _getWebsite()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::getModel('core/store')->load($storeId)->getWebsiteId();
    }
    public function setCollection($collection){
        if(Mage::helper('categorygridfilter')->isCategoryEnabled()){
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                if(Mage::helper('catalog')->isModuleEnabled('Aitoc_Aitquantitymanager')){
                    $website_id = $this->_getWebsite();
                    $collection->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    "{{table}}.stock_id=1 AND {{table}}.website_id='{$website_id}'",
                    'left');
                }else{
                    $collection->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left');
                }

            }
            $collection->addAttributeToSelect('special_price');
            // var_dump($collection->getSelect()->__toString());
        }
        parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        if(Mage::helper('categorygridfilter')->isCategoryEnabled()){
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addColumnAfter('qty',
                    array(
                        'header'=> Mage::helper('catalog')->__('Inventory'),
                        'width' => '100px',
                        'type'  => 'number',
                        'index' => 'qty',
                ),'sku');
            }
            $this->addColumnAfter('special_price', array(
                'header'    => Mage::helper('catalog')->__('Special Price'),
                'type'  => 'currency',
                'width'     => '1',
                'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index'     => 'special_price'
            ), 'price');
        }
       parent::_prepareColumns();
      return $this;
    }
}

?>
