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
class Silk_Ew_Block_Adminhtml_Ew_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ew_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ew')->__('Extended Warranty Detail'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ew')->__('Extended Warranty Detail'),
          'title'     => Mage::helper('ew')->__('Extended Warranty Detail'),
          'content'   => $this->getLayout()->createBlock('ew/adminhtml_ew_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
