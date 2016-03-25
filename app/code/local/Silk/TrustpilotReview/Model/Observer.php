<?php
class Silk_TrustpilotReview_Model_Observer {
    //this checks if the tab can be added. You don't want to add the tab when selecting the product type and attribute set or when selecting the configurable attributes.
    protected function _canAddTab($product){
        if ($product->getId()){
            return true;
        }
        if (!$product->getAttributeSetId()){
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable'){
            if ($request->getParam('attributes')){
                return true;
            }
        }
        return false;
    }
    //the method that actually remove the tab
    public function removeProductTabBlock($observer){
       $helper = $this->getTrHelper();
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        //if on product tabs block and the tab can be added...
        if ($block->getType()== 'adminhtml/catalog_product_edit_tabs' && $this->_canAddTab($product)){
            //in case there is an ajax tab
            if($helper->isModelEnabled()){
              $block->removeTab('reviews');
            }

        }
        return $this;
    }
    protected function getTrHelper()
    {
        return Mage::helper('trustpilot');
    }
}


 ?>
