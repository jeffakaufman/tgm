<?php
class Silk_Ordersku_Model_Grid extends Mage_Core_Model_Abstract
{

    protected $_listColumns = array();
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Set transaction isolation level for SESSION as READ COMMITTED
     * in order to avoid deadlocks
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    protected function setTransactionIsolationLevel(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        try {
            $connection = $collection->getConnection();
            $connection->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Modify select of orders grid collection
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param array $listColumns
     * @return void
     */
    public function modifyOrdersGridCollection(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection
    )
    {

        $this->setTransactionIsolationLevel($collection);

        $this->setOrderItemTbl($collection);

        $collection->getSelect()->group('main_table.entity_id');

        $collection->addFieldToSelect('*');

        Varien_Profiler::start('mw_addCustomColumnsSelect_updateCollection');
        $this->updateCollection($collection);
        Varien_Profiler::stop('mw_addCustomColumnsSelect_updateCollection');

        return;
    }



    /**
     * @return $this
     */
    public function setOrderItemTbl(Mage_Sales_Model_Resource_Order_Grid_Collection $collection) {

        Varien_Profiler::start('setOrderItemTbl');
        if ($collection->getSelect()!==null && !isset($collection->_setFields['setOrderItemTbl'])) {

            $expressions = array (
                'product_names' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`name` SEPARATOR \'\n\')'),
                'skus' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`sku` SEPARATOR \'\n\')'),
                'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_id` SEPARATOR \'\n\')'),
                'product_options' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_options` SEPARATOR \'^\')')
            );


            $collection->getSelect()->joinLeft(array('order_item_tbl'=>$collection->getTable('sales/order_item')),
                '`order_item_tbl`.`order_id` = `main_table`.`entity_id` AND `order_item_tbl`.`parent_item_id` IS NULL',
                $expressions
            );

            $collection->getSelect()->joinLeft(array('order_tbl'=>$collection->getTable('sales/order')),
                'order_tbl.entity_id = main_table.entity_id',
                array(
                    'ip'=>'order_tbl.remote_ip',
                    'ship_method'=>'order_tbl.shipping_description',
                    'coupon'=>'order_tbl.coupon_code'
                ));
            $collection->_setFields['setOrderItemTbl'] = true;
        }
        Varien_Profiler::stop('setOrderItemTbl');
        return $collection;
    }

    /**
     * Join select as sub select (fix for total records bug)
     * Reset where from sub select to select
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @throws Zend_Db_Select_Exception
     */
    protected function updateCollection(Mage_Sales_Model_Resource_Order_Grid_Collection $collection)
    {
        if ((string)Mage::getConfig()->getModuleConfig('AW_Ordertags')->active=='true' ||
            (string)Mage::getConfig()->getModuleConfig('Amasty_Orderattr')->active=='true') {
            return;
        }
        $select = $collection->getSelect();
        $oldSelect = clone $collection->getSelect();
        $oldWherePart = $oldSelect->getPart('where');
        $oldSelect->reset('where');
//        $select->setPart('where', str_ireplace('AND', '', array_pop($oldWherePart)));
//        $select->setPart('where', $oldWherePart);
        $select->reset();
        $select->from(array('main_table'=>$oldSelect));
        $select->setPart('where', $oldWherePart);
    }
}
