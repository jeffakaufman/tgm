<?php
class Silk_GoogleAnalytics_Helper_Data extends Mage_GoogleAnalytics_Helper_Data
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE        = 'google/analytics/active';
    const XML_PATH_TYPE          = 'google/analytics/type';
    const XML_PATH_ACCOUNT       = 'google/analytics/account';
    const XML_PATH_ANONYMIZATION = 'google/analytics/anonymization';

    /**
     * @var classic google analytics tracking code
     */
    const TYPE_ANALYTICS = 'analytics';

    /**
     * @var google analytics universal tracking code
     */
    const TYPE_UNIVERSAL = 'universal';

    /**
     * Whether GA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }

    /**
     * Whether GA IP Anonymization is enabled
     *
     * @param null $store
     * @return bool
     */
    public function isIpAnonymizationEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ANONYMIZATION, $store);
    }

    /**
     * Get GA account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
    }

    /**
     * Returns true if should use Google Universal Analytics
     *
     * @param string $store
     * @return string
     */
    public function isUseUniversalAnalytics($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TYPE, $store) == self::TYPE_UNIVERSAL;
    }
}
