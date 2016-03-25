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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cybersource
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Cybersource_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 

$installer->addAttribute('order', 'state_tax', array(
    'label' => 'State Tax',
    'type'  => 'decimal'
));
$installer->addAttribute('invoice', 'state_tax', array(
    'label' => 'State Tax',
    'type'  => 'decimal'
));


$installer->addAttribute('order', 'city_tax', array(
    'label' => 'City Tax',
    'type'  => 'decimal'
));
$installer->addAttribute('invoice', 'city_tax', array(
    'label' => 'City Tax',
    'type'  => 'decimal'
));


$installer->addAttribute('order', 'district_tax', array(
    'label' => 'District Tax',
    'type'  => 'decimal'
));
$installer->addAttribute('invoice', 'district_tax', array(
    'label' => 'District Tax',
    'type'  => 'decimal'
));


$installer->addAttribute('order', 'county_tax', array(
    'label' => 'County Tax',
    'type'  => 'decimal'
));
$installer->addAttribute('invoice', 'county_tax', array(
    'label' => 'County Tax',
    'type'  => 'decimal'
));

 
$installer->endSetup();

