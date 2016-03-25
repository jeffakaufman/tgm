<?php
class Silk_TrustpilotReview_Helper_Data extends Mage_Core_Helper_Abstract
{
   const TRUSTPILOT_REVIEW_ENABLE = 'trustpilot/general/enabled';
   public function _getConfig($path){
        return Mage::getStoreConfig($path);
    }
   public function isModelEnabled(){
        return $this->_getConfig(self::TRUSTPILOT_REVIEW_ENABLE);
    }
}
 ?>
