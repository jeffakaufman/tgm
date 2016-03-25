<?php

$this->startSetup();
 
$this->addAttribute('catalog_category', 'featured_home', array(
    'group'         => 'General Information',
    'input'         => 'select',
    'type'          => 'int',
    'label'         => 'Featured in Home Page',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$this->addAttribute('catalog_category', 'featured_category', array(
    'group'         => 'General Information',
    'input'         => 'select',
    'type'          => 'int',
    'label'         => 'Featured in Category Page',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

 
$this->endSetup();

?>
