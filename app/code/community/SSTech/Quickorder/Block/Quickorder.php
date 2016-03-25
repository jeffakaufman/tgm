<?php   
class SSTech_Quickorder_Block_Quickorder extends Mage_Core_Block_Template{
	
	public function getQuickattriute()
	{
		$quickorder = Mage::getStoreConfig('quickorder/quickorder/quickorder'); 
		$product = Mage::getModel('catalog/product');
        $store_id = Mage::app()->getStore()->getId();
        $_productCollection = $product->getCollection()
            ->addStoreFilter($store_id)
            ->addAttributeToFilter('type_id', array('eq' => 'simple'))->addAttributeToSelect('*');
		return $_productCollection;
		
    }

}
