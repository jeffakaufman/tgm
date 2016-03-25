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
class Silk_Ew_Block_Adminhtml_Ew extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ew';
    $this->_blockGroup = 'ew';
    $this->_headerText = Mage::helper('ew')->__('Extended Warranty');
    $this->_addButtonLabel = Mage::helper('ew')->__('Add Extended Warranty');

    parent::__construct();
   //$this->_removeButton('add');
  }
}
