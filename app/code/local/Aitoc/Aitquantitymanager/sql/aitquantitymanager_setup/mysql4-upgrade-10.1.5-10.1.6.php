<?php

if(version_compare(Mage::getVersion(), '1.13.0.0', '>'))
{
    $subscriptions = array(
        array(
            'target_table' => $this->getTable('cataloginventory/stock_item'),
            'trigger_event' => Magento_Db_Sql_Trigger::SQL_EVENT_INSERT,
            'event_time' => Magento_Db_Sql_Trigger::SQL_TIME_AFTER,
            'dest_tables' => array(
                'table_name' => array(
                    $this->getTable('cataloginventory_stock_status_cl') => array(
                        'fields' => array('product_id'),
                        'values' => array('NEW.product_id')),
                    $this->getTable('catalog_product_index_price_cl') => array(
                        'fields' => array('entity_id'),
                        'values' => array('NEW.product_id'))
                ),
            )
        ),
        array(
            'target_table' => $this->getTable('cataloginventory/stock_item'),
            'trigger_event' => Magento_Db_Sql_Trigger::SQL_EVENT_UPDATE,
            'event_time' => Magento_Db_Sql_Trigger::SQL_TIME_AFTER,
            'dest_tables' => array(
                'table_name' => array(
                    $this->getTable('cataloginventory_stock_status_cl') => array(
                        'fields' => array('product_id'),
                        'values' => array('NEW.product_id')),
                    $this->getTable('catalog_product_index_price_cl') => array(
                        'fields' => array('entity_id'),
                        'values' => array('NEW.product_id'))
                ),
            )
        ),
        array(
            'target_table' => $this->getTable('cataloginventory/stock_item'),
            'trigger_event' => Magento_Db_Sql_Trigger::SQL_EVENT_DELETE,
            'event_time' => Magento_Db_Sql_Trigger::SQL_TIME_AFTER,
            'dest_tables' => array(
                'table_name' => array(
                    $this->getTable('cataloginventory_stock_status_cl') => array(
                        'fields' => array('product_id'),
                        'values' => array('OLD.product_id')),
                    $this->getTable('catalog_product_index_price_cl') => array(
                        'fields' => array('entity_id'),
                        'values' => array('OLD.product_id'))
                ),
            )
        ),
    );

    $triggerHelper = Mage::helper('aitquantitymanager/trigger');
    foreach ($subscriptions as $arguments) {

        $triggerCreateQuery = $triggerHelper->buildSystemTrigger(
            $arguments['event_time'],
            $arguments['trigger_event'],
            $arguments['target_table'],
            $arguments['dest_tables']
        )->assemble();
        $this->getConnection()->query($triggerCreateQuery);

    }
}

