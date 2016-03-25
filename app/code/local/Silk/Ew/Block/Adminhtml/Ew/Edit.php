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
class Silk_Ew_Block_Adminhtml_Ew_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ew';
        $this->_controller = 'adminhtml_ew';
        
        $this->_updateButton('save', 'label', Mage::helper('ew')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('ew')->__('Delete'));
        $this->_removeButton('reset');
        //$this->_addButton('saveandcontinue', array(
           //'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
           //'onclick'   => 'saveAndContinueEdit()',
           //'class'     => 'save',
       //), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ew_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ew_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ew_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ew_data') && Mage::registry('ew_data')->getId() ) {
            return Mage::helper('ew')->__("edit  extended warranty from '%s'", $this->htmlEscape(Mage::registry('ew_data')->getFirstName().' '.Mage::registry('ew_data')->getLastName()));
        } else {
            return Mage::helper('ew')->__('Add Extended Warranty');
        }
    }
}
