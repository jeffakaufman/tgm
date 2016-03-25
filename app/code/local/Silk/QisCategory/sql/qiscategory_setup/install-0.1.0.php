<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_category', 'active', array(
		'type'    => 'varchar',
		'input'   => 'image',
		'backend' => 'catalog/category_attribute_backend_image',
		'group' => 'General',
		'label'         => 'Active category backgroud image',
		'visible'       => 1,
		'required'      => 0,
		'user_defined' => 1,
		'frontend_input' =>'',
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible_on_front'  => 1,
));

$setup->addAttribute('catalog_category', 'inactive', array(
		'type'    => 'varchar',
		'input'   => 'image',
		'backend' => 'catalog/category_attribute_backend_image',
		'group' => 'General',
		'label'         => 'Inactive  category backgroud image',
		'visible'       => 1,
		'required'      => 0,
		'user_defined' => 1,
		'frontend_input' =>'',
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible_on_front'  => 1,
));

$setup->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'active',
		'999'  //sort_order
);

$setup->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'inactive',
		'999'  //sort_order
);

$installer->endSetup();

echo 'macro setup';