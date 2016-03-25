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
class Silk_Ew_Block_Adminhtml_Ew_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ew_form', array('legend'=>Mage::helper('ew')->__('Extended Warranty Detail')));

      $fieldset->addField('first_name', 'text', array(
          'label'     => Mage::helper('ew')->__('First Name'),
          'name'      => 'first_name',
           'wysiwyg'   => false,
          'required'  => true,
      ));
      $fieldset->addField('last_name', 'text', array(
          'label'     => Mage::helper('ew')->__('Last Name'),
          'name'      => 'last_name',
          'wysiwyg'   => false,
          'required'  => true,
      ));

     $fieldset->addField('street_address', 'text', array(
          'label'     => Mage::helper('ew')->__('Street Address'),
          'name'      => 'street_address',
          'wysiwyg'   => false,
          'required'  => true,
      ));

           $fieldset->addField('street_address1', 'text', array(
          'label'     => Mage::helper('ew')->__('Street Address 1'),
          'name'      => 'street_address1',
          'wysiwyg'   => false,
          'required'  => false,
      ));

     $fieldset->addField('city', 'text', array(
          'label'     => Mage::helper('ew')->__('City'),
          'name'      => 'city',
          'wysiwyg'   => false,
          'required'  => true,
      ));

        
     $fieldset->addField('state', 'select', array(
          'label'     => Mage::helper('ew')->__('State'),
          'name'      => 'state',
          'required'  => true,
          'values' => $this->getStateList(),
      ));


     $fieldset->addField('zip_code', 'text', array(
          'label'     => Mage::helper('ew')->__('Zip Code'),
          'name'      => 'zip_code',
          'wysiwyg'   => false,
          'required'  => true,
      ));


       $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('ew')->__('Email'),
          'name'      => 'email',
          'wysiwyg'   => false,
          'required'  => true,
      ));


            
      $fieldset->addField('phone', 'text', array(
          'label'     => Mage::helper('ew')->__('Phone No.'),
          'name'      => 'phone',
          'wysiwyg'   => false,
          'required'  => true,
      ));

      
     $fieldset->addField('model_number', 'text', array(
          'label'     => Mage::helper('ew')->__('Model No.'),
          'name'      => 'model_number',
          'wysiwyg'   => false,
          'required'  => true,
      ));


     $fieldset->addField('serial_number', 'text', array(
          'label'     => Mage::helper('ew')->__('Serial No.'),
          'name'      => 'serial_number',
          'wysiwyg'   => false,
          'required'  => false,
      ));

    $fieldset->addField(
        'date_of_purchase', 
        'date', array(
            'name'      => 'date_of_purchase',
            'label'     => 'Date Of Purchase',
            'format'    => 'MM/dd/yyyy',
            'time'      => false,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'required'  => true,
        )
    );


       $fieldset->addField('retail_price', 'text', array(
          'label'     => Mage::helper('ew')->__('Retail Price'),
          'name'      => 'retail_price',
          'wysiwyg'   => false,
          'required'  => true,
      ));

           
      $fieldset->addField('created_time', 'text', array(
          'label'     => Mage::helper('ew')->__('Registration Date'),
          'name'      => 'created_time',
          'wysiwyg'   => false,
          'required'  => true,
      ));


      $fieldset->addField('order_number', 'text', array(
          'label'     => Mage::helper('ew')->__('Order Number'),
          'name'      => 'order_number',
          'wysiwyg'   => false,
          'required'  => true,
      ));


     $fieldset->addField('ew_sku', 'text', array(
          'label'     => Mage::helper('ew')->__('Extended warranty SKU'),
          'name'      => 'ew_sku',
          'wysiwyg'   => false,
          'required'  => true,
      ));


     
      if ( Mage::getSingleton('adminhtml/session')->getEwData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEwData());
          Mage::getSingleton('adminhtml/session')->setEwData(null);
      } elseif ( Mage::registry('ew_data') ) {
          $form->setValues(Mage::registry('ew_data')->getData());
      }
      return parent::_prepareForm();
  }

  function getStateList(){ 
      $regions = Mage::getResourceModel('directory/region_collection')->addCountryFilter('US')->load();
      $state = array();
      foreach ($regions as $r) {
          $state[] = array('value'=>$r['code'], 'label'=>$r['name']);
      }
      return $state;
  }
}
