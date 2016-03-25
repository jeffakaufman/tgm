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
 * Adminhtml newsletter queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Silk_Company_Block_Adminhtml_Customer_Edit_Tab_Employee_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('employeeGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(Mage::helper('customer')->__('No Newsletter Found'));

    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/newsletter', array('_current'=>true));
    }

    protected function _prepareCollection()
    {
        $customer = Mage::registry('current_customer');
        echo $customer->getCompanyId();
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('role_id')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->addAttributeToSelect('company_id',$customer->getCompanyId())
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
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

        $roles = Mage::getModel('company/source_role')->getOptionArray();

        $this->addColumn('role_id', array(
            'header'    =>  Mage::helper('customer')->__('Role'),
            'width'     =>  '100',
            'index'     =>  'role_id',
            'type'      =>  'options',
            'options'   =>  $roles,
        ));

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        // $this->addColumn('billing_postcode', array(
        //     'header'    => Mage::helper('customer')->__('ZIP'),
        //     'width'     => '90',
        //     'index'     => 'billing_postcode',
        // ));

        // $this->addColumn('billing_country_id', array(
        //     'header'    => Mage::helper('customer')->__('Country'),
        //     'width'     => '100',
        //     'type'      => 'country',
        //     'index'     => 'billing_country_id',
        // ));

        // $this->addColumn('billing_region', array(
        //     'header'    => Mage::helper('customer')->__('State/Province'),
        //     'width'     => '100',
        //     'index'     => 'billing_region',
        // ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> 'adminhtml/customer/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

}
