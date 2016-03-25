<?php

class Silk_CustomPaymentMethod_Block_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods {

    protected function _canUseMethod($method) {
        /**
         * Check for User Groups
         */

        if ($method->getCode() == 'checkmo') {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('customer/session');
                $data = $customer->getCustomer();

                $group_id = $data->getGroupId();
                $specificgroups = explode(",",$method->getConfigData('specificgroups'));
                $testgroup = trim($method->getConfigData('specificgroups'));
                if(!in_array($group_id,$specificgroups) || $testgroup===""){
                    return false;
                }
            } else return false;
        }

        return parent::_canUseMethod($method);
    }
}

?>
