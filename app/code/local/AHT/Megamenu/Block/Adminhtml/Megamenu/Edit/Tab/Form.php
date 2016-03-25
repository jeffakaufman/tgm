<?php

class AHT_Megamenu_Block_Adminhtml_Megamenu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true); 
        }
    }

  protected function _prepareForm()
  {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('megamenu_form', array('legend'=>Mage::helper('megamenu')->__('Item information')));

		$fieldset->addField('title', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Label'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'title',
		));
		
		$fieldset->addField('menu_type', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Menu Type'),
		  'name'      => 'menu_type',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('Catalog Category'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('Static Content'),
			  ),
		  ),
		));
		
		$fieldset->addField('max_level', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Max Level'),
		  'name'      => 'max_level',
		  'after_element_html' => '<small><i>'.Mage::helper('megamenu')->__('Blank to use <a href="%s" target="_blank" title="Open config page">general config</a>', Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/megamenu')).'</i></small>',
		  'class'	  => 'validate-number',
		));
		
		$fieldset->addField('use_thumbnail', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Add Category Thumbnail'),
		  'name'      => 'use_thumbnail',
		  'values'    => array(
			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('No'),
			  ),
			  
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('Yes'),
			  ),
		  ),
		));
		
		$fieldset->addField('url', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Link'),
		  'name'      => 'url',
		  'after_element_html' => '<div id="url_note"><small><i>'.Mage::helper('megamenu')->__('Blank to use category link').'</i></small></div>',
		));
		
		$fieldset->addField('position', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Position'),
		  'name'      => 'position',
		  'class'	  => 'validate-number',
		  'required'  => true,
		));
		
		$columns = Mage::getStoreConfig('megamenu/general/max_column');
		if($columns==0 || $columns=='' || $columns==NULL){
			$columns = 5;
		}
		$arrColumns = array();
		for($i=1; $i<=$columns; $i++){
			$arrColumns[] = array(
				'value'	=> $i,
				'label'	=> $i,
			);
		}
		
		$fieldset->addField('columns', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Columns'),
		  'name'      => 'columns',
		  'values'    => $arrColumns,
		));
		
		$fieldset->addField('align_menu', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Align Menu Item'),
		  'name'      => 'align_menu',
		  'values'    => array(
			  array('value'     => 'left', 'label'     => 'Align Left'),
			  array('value'     => 'right', 'label'     => 'Align Right'),
		  ),
		));
		
		$fieldset->addField('align_dropdown', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Align Dropdown'),
		  'name'      => 'align_dropdown',
		  'values'    => array(
			  array('value'     => 'left', 'label'     => 'Align Left'),
			  array('value'     => 'right', 'label'     => 'Align Right'),
		  ),
		));
		
		
		
		$fieldset->addField('dropdown_position', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Dropdown Position'),
		  'name'      => 'dropdown_position',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('Depend Menu Item'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('Depend Menu Container'),
			  ),
		  ),
		));
		
		$fieldset->addField('special_class', 'text', array(
		  'label'     => Mage::helper('megamenu')->__('Special Class'),
		  'name'      => 'special_class',
		));

		$fieldset->addField('status', 'select', array(
		  'label'     => Mage::helper('megamenu')->__('Status'),
		  'name'      => 'status',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('megamenu')->__('Enabled'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('megamenu')->__('Disabled'),
			  ),
		  ),
		));
		
		
		if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('megamenu')->__('Store View'),
                'title' => Mage::helper('megamenu')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'style' => 'height:150px',
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }
		
     
		$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $fieldset->addField('from_date', 'date', array(
            'name' => 'from_date',
            'time' => true,
            'label' => Mage::helper('megamenu')->__('From Date'),
            'title' => Mage::helper('megamenu')->__('From Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
        ));

        $fieldset->addField('to_date', 'date', array(
            'name' => 'to_date',
            'time' => true,
            'label' => Mage::helper('megamenu')->__('To Date'),
            'title' => Mage::helper('megamenu')->__('To Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
        ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMegamenuData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMegamenuData());
          Mage::getSingleton('adminhtml/session')->setMegamenuData(null);
      } elseif ( Mage::registry('megamenu_data') ) {
          $form->setValues(Mage::registry('megamenu_data')->getData());
      }
      return parent::_prepareForm();
  }
}