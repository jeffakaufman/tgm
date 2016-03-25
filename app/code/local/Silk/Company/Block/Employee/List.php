<?php
/**
 * Silk_Company extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Silk
 * @package        Silk_Company
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Company list block
 *
 * @category    Silk
 * @package     Silk_Company
 * @author Ultimate Module Creator
 */
class Silk_Company_Block_Employee_List extends Mage_Core_Block_Template
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $_customer = $this->_getSession()->getCustomer();
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('company_id', array('eq'=>$_customer->getCompanyId()))
            ->addAttributeToFilter('entity_id', array('neq'=>$_customer->getId()))
            ->setOrder('updated_at','desc');

        $this->setCompanys($companys);
        $this->setCustomers($customers);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Silk_Company_Block_Company_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'company.company.html.pager'
        )
        ->setCollection($this->getCustomers());
        $this->setChild('pager', $pager);
        $this->getCustomers()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
