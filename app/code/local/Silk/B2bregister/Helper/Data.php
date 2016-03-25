<?php
class Silk_B2bregister_Helper_Data extends Mage_Core_Helper_Abstract
{
   const ROUTE_ACCOUNT_LOGIN = 'customer/account/login';
   const REFERER_QUERY_PARAM_NAME = 'referer';
   const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';


    public function getCreateAccountUrl(){
        return $this->_getUrl('b2bregister/account/create');
    }
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('b2bregister/account/createpost');
    }
    public function getPostActionUrl(){
        return $this->_getUrl('b2bregister/account/loginpost');
    }
    public function getRegisterUrl()
    {
        return $this->_getUrl('b2bregister/account/create');
    }
    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }
    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (!$referer && !Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('customer/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }
}
?>
