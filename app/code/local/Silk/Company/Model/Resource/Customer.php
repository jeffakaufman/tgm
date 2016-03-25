<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer entity resource model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Silk_Company_Model_Resource_Customer extends Mage_Customer_Model_Resource_Customer
{

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param Varien_Object $customer
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        if(!$customer->isB2b() || $customer->isAdmin())
        {
            $this->_saveAddresses($customer);
        }
        return Mage_Eav_Model_Entity_Abstract::_afterSave($customer);
    }


}
