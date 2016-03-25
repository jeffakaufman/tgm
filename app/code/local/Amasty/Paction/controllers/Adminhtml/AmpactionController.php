<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Adminhtml_AmpactionController extends Mage_Adminhtml_Controller_Action
{
    public function doAction()
    {
        $productIds  = $this->getRequest()->getParam('product');
        $val         = $this->getRequest()->getParam('ampaction_value');
        $commandType = trim($this->getRequest()->getParam('command'));
        $storeId     = (int)$this->getRequest()->getParam('store', 0);
        $enhanced    = $this->getRequest()->getParam('enhanced', 0);
        
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/products/mass_product_actions/' . $commandType)) {
            if (is_array($val)) {
                $val = implode(',', $val);
            } else {
                $val = trim($val);
            }

            try {
                $command = Amasty_Paction_Model_Command_Abstract::factory($commandType);
                
                $success = $command->execute($productIds, $storeId, $val);
                if ($success){
                     $this->_getSession()->addSuccess($success);
                }
                
                // show non critical erroes to the user
                foreach ($command->getErrors() as $err){
                     $this->_getSession()->addError($err);
                }            
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Error: %s', $e->getMessage()));
            }
        } else {
            $this->_getSession()->addError($this->__('Access denied.'));
        }

        if ($enhanced) {
            $this->_redirect('enhancedgrid/catalog_product/index', array('store'=> $storeId));
        } else {
            $this->_redirect('adminhtml/catalog_product/index', array('store'=> $storeId));
        }

        return $this;        
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products/mass_product_actions');
    }
}