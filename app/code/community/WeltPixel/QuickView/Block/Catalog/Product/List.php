<?php

class WeltPixel_QuickView_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    /*public function getAddToCartUrl($product, $additional = array())
    {
        if (!$product->getTypeInstance(true)->hasRequiredOptions($product)) {
            return $this->helper('weltpixel_quickview/cart')->getAddUrl($product, $additional);
        }

        return Mage::helper('weltpixel_quickview')->getProductUrl($product, $additional);
    }*/

    public function getQuickViewUrl($product, $additional = array()) {
        return Mage::helper('weltpixel_quickview')->getProductUrl($product, $additional);
    }
}
