<?php
class Silk_Paypal_Model_Config extends Mage_Paypal_Model_Config
{
    /**
    * BN code getter
    * override method
    *
    * @param string $countryCode ISO 3166-1
    */
    public function getBuildNotationCode($countryCode = null)
    {
        $newBnCode = 'SilkSoftware_SI_MagentoEnt_PPS';
        //if you would like to retain the product and country code
        //E.g., Company_Test_EC_US
        //$bnCode = parent::getBuildNotationCode($countryCode);
        //$newBnCode = str_replace('Varien_Cart','Prjoect_Test',$bnCode);
        return $newBnCode;
    }
}
