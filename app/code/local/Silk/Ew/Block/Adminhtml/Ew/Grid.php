<?php
/**
 * Silk
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Silk
 * @package    Silk_Ew
 * @author     Silk Development Team
 * @copyright  Copyright (c) 2013 Silk. (http://www.magerevol.com)
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Silk_Ew_Block_Adminhtml_Ew_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ewGrid');
      $this->setDefaultSort('ew_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('ew/ew')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('ew_id', array(
          'header'    => Mage::helper('ew')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'ew_id',
      ));

      $this->addColumn('first_name', array(
          'header'    => Mage::helper('ew')->__('First Name'),
          'align'     =>'left',
          'index'     => 'first_name',
      ));
       $this->addColumn('last_name', array(
          'header'    => Mage::helper('ew')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'last_name',
      ));
      
      $this->addColumn('email', array(
          'header'    => Mage::helper('ew')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
      
      $this->addColumn('model_number', array(
          'header'    => Mage::helper('ew')->__('Model No.'),
          'align'     =>'left',
          'index'     => 'model_number',
      ));
      
      $this->addColumn('product_quality', array(
          'header'    => Mage::helper('ew')->__('Product Quality'),
          'align'     =>'left',
          'index'     => 'product_quality',
          'type'=>'options',
          'options'=>array('N'=>'New','R'=>'Refurbished','U'=>'Used'),
      ));
      
      $this->addColumn('created_time', array(
          'header'    => Mage::helper('ew')->__('Registration Date'),
          'align'     =>'left',
          'type'      => 'datetime',
          'index'     => 'created_time',
      ));

     $this->addColumn('order_number', array(
          'header'    => Mage::helper('ew')->__('Order#'),
          'align'     =>'left',
          'index'     => 'order_number',
      ));

     $this->addColumn('ew_sku', array(
          'header'    => Mage::helper('ew')->__('Extended Warranty SKU'),
          'align'     =>'left',
          'index'     => 'ew_sku',
      ));

	  
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('ew')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('ew')->__('View Detail'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('ew')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('ew')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ew_id');
        $this->getMassactionBlock()->setFormFieldName('ew');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('ew')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('ew')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
