<?php
class Silk_PaypalPartner_Model_Config extends Mage_Paypal_Model_Config
{
	const EDITION_COMMUNITY    = 'Community';
    const EDITION_ENTERPRISE   = 'Enterprise';
    const EDITION_CHINESE_LOCALIZATIION	   = 'Chinese_Localization';

	//SilkSoftware_SI_MagentoComm_PPS
	//SilkSoftware_SI_MagentoEnt_PPS
	//Silksoftware_Cart_EC/Silksoftware_Cart_WPS
    public function getBuildNotationCode($countryCode = null)
    {
    	$codeMaps = array(
    		self::EDITION_COMMUNITY => 'SilkSoftware_SI_MagentoComm_PPS',
    		self::EDITION_ENTERPRISE => 'SilkSoftware_SI_MagentoEnt_PPS',
    		self::EDITION_CHINESE_LOCALIZATIION => 'Silksoftware_Cart_EC'
    	);

    	if($this->getIsChineseEdition()) {
    		return $codeMaps[self::EDITION_CHINESE_LOCALIZATIION];
    	}
    	else {
    		return $codeMaps[Mage::getEdition()];	
    	}
    }

    public function getIsChineseEdition()
    {
        return Mage::getStoreConfig('admin/paypalpartner/initiated_project');
    }
}