<?php
/**
 * Anais_Accessories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Anais
 * @package    Anais_Accessories
 * @copyright  Copyright (c) 2011 Anais Software Services
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */ 
 /**
 * @category   Anais
 * @package    Anais_Accessories
 * @author     Marius Strajeru <marius.strajeru@anais-it.com>
 */ 
class Silk_Featured_Block_Featured extends Mage_Core_Block_Template{
	
  public function _prepareLayout()	{
		return parent::_prepareLayout();
	}
	
public function getHomeFeaturedCollection() {
       $store_id = Mage::app()->getStore()->getId();
		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($store_id)
			->addFieldToFilter('featured',1)
            ->addFieldToFilter('status',1);
        
		return $collection;
	}
 
public function getSideFeaturedCollection() {
    $store_id = Mage::app()->getStore()->getId();
    $collection = Mage::getModel('catalog/product')
        ->getCollection()
            ->addStoreFilter($store_id)
			->addFieldToFilter('featured',1)
            ->addFieldToFilter('status',1);
        
		return $collection;
	}
    
       
}
