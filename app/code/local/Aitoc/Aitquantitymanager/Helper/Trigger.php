<?php

class Aitoc_Aitquantitymanager_Helper_Trigger extends Mage_Core_Helper_Abstract
{
    protected $_connection;


    public function __construct()
    {
        $this->_connection = Mage::getSingleton('core/resource')->getConnection(
           Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);
    }

    public function buildSystemTrigger($eventtime, $triggerevent, $targettable, array $args)
    {
        $trigger = new Magento_Db_Sql_Trigger();
        $trigger->setTime($eventtime)
            ->setEvent($triggerevent)
            ->setTarget($targettable)
            ->setBody($this->_getTriggerBody($args['table_name']));
        return $trigger;
    }

    protected function _addQuotes($value)
    {
        $temp = explode(".", $value);
        if (count($temp) > 1) {
            $temp[1] = $this->_connection->quoteIdentifier($temp[1]);
            $temp = implode('.', $temp);
        } else {
            $temp = $this->_connection->quoteIdentifier($value);
        }
        return $temp;
    }

    protected function _getTriggerBody(array $tables = array())
    {
        $insert = '';
        foreach ($tables as $table => $vars) {
            $fields = $vars['fields'];
            $values = $vars['values'];
            $query = sprintf('INSERT IGNORE INTO %s', $this->_connection->quoteIdentifier($table));
            $columns = array_map(array($this->_connection, 'quoteIdentifier'), $fields);
            $query = sprintf('%s (%s)', $query, join(', ', $columns));
            $quotedValues = array_map(array($this, '_addQuotes'), $values);
            $query = sprintf('%s VALUES(%s);', $query, join(', ', $quotedValues));
            $insert .= $query;

        }
        return $insert;
    }

}