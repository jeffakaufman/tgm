<?php
class Silk_Ordersku_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return array
     */
    public function getAllGridColumns()
    {
        $options = Mage::getModel('ordersku/system_config_source_orders_grid')->toArray();
        return $options;
    }
    /**
     * @return array
     */
    public function getGridColumns()
    {
        $listColumns = Mage::getModel('ordersku/system_config_source_orders_grid')->toListColumnsArray();;
        return $listColumns;
    }
    /**
     * @return array
     */
    public function getAllCustomerGridColumns()
    {
        $options = Mage::getModel('ordersku/system_config_source_orders_customer_grid')->toArray();
        return $options;
    }
    public function getCustomerGridColumns()
    {
        $listColumns = Mage::getModel('ordersku/system_config_source_orders_customer_grid')->toListColumnsArray();;
        return $listColumns;
    }
}
 ?>
