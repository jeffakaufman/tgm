<?php

class Silk_Categorygridfilter_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_CATEGORY_COLUMN_ENABLED = 'categorygridfilter/settings/category_column_enabled';

    public function _getConfig($path){
        return Mage::getStoreConfig($path);
    }
    public function isCategoryEnabled(){
        return $this->_getConfig(self::XML_PATH_CATEGORY_COLUMN_ENABLED);
    }

}
