<?php
$installer = $this;
/* @var $installer Aitoc_Aitdownloadablefiles_Model_Resource_Eav_Mysql4_Setup */
if(version_compare(Mage::getVersion(), '1.11.0.0',"ge"))
{
    $installer->startSetup();

    $conn = $installer->getConnection();

    $conn->dropColumn($installer->getTable('aitquantitymanager/stock_item'),'stock_status_changed_automatically');

    $conn->dropColumn($installer->getTable('aitquantitymanager/stock_item'),'enable_qty_increments');

    $conn->dropColumn($installer->getTable('aitquantitymanager/stock_item'),'use_config_enable_qty_increments');

    $conn->addColumn($installer->getTable('aitquantitymanager/stock_item'),'use_config_enable_qty_inc',
            array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Use Config Enable Qty Increments'
            ));

    $conn->addColumn($installer->getTable('aitquantitymanager/stock_item'),'enable_qty_increments',
            array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    'comment'   => 'Enable Qty Increments'
            ));

    $conn->addColumn($installer->getTable('aitquantitymanager/stock_item'),'stock_status_changed_auto',
            array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            'comment'   => 'Stock Status Changed Automatically'
            ));

    $installer->endSetup();
}      