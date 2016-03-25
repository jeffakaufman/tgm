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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/*
Mage_Adminhtml_Block_Customer_Grid
Mage_Adminhtml_Block_Widget_Grid
*/
class Silk_Company_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('role_id')
            ->addAttributeToSelect('is_b2b')
            ->addAttributeToSelect('company_name')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('active')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('customer_company_id','customer/company_id','entity_id',null,'left')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
            //->joinAttribute('cust', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());

        if($this->getRequest()->getParam('is_b2b'))
        {
            $collection->addAttributeToFilter('is_b2b',array('eq'=>1));
            $select = $collection->getSelect();
            $select->joinLeft(array('cn'=>'silk_company'), 'at_customer_company_id.`value`=cn.company_id',array('company_name'=>'cn.name'));
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));

        /*$this->addColumn('firstname', array(
            'header'    => Mage::helper('customer')->__('First Name'),
            'index'     => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('Last Name'),
            'index'     => 'lastname'
        ));*/

        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        /** @var $sourceYesNo Mage_Adminhtml_Model_System_Config_Source_Yesno */
        $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');
        $this->addColumn('is_b2b', array(
            'header'    => $this->__('Is B2B'),
            'index'     => 'is_b2b',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => $sourceYesNo->toArray(),
            'sortable'  => true,
        ));
        $this->addColumn('active', array(
            'header'    => $this->__('Active'),
            'index'     => 'active',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => $sourceYesNo->toArray(),
            'sortable'  => true,
        ));
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $data = $this->helper('adminhtml')->prepareFilterString($filter);
        $isB2b = $this->getRequest()->getParam('is_b2b');
        if((isset($data['is_b2b']) && $data['is_b2b']) || $isB2b)
        {
            $this->addColumn('company_name', array(
                'header'    => Mage::helper('company')->__('Company Name'),
                'index'     => 'company_name'
            ));

            $roles = Mage::getModel('company/source_role')->getAllOptions();

            $this->addColumn('role_id', array(
                'header'    =>  Mage::helper('customer')->__('Role'),
                'width'     =>  '100',
                'index'     =>  'role_id',
                'type'      =>  'options',
                'options'   =>  $this->_getAttributeOptions('role_id')
            ));
        }

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }


    protected function _getAttributeOptions($attribute_code)
    {
        $attribute = Mage::getModel('eav/config')->getAttribute('customer', $attribute_code);
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
        $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
