<?php
class Silk_Ordersku_Model_Observer
{
      /**
     * Update columns of sales order grid
     *
     * Event: core_layout_block_create_after
     * Observer Name: ordersku_add_custom_columns
     *
     * @param $observer
     */
    public function addColumnsToSalesOrdersGrid($observer)
    {
        $helper = $this->getOsHelper();
        if (Mage::app()->getRequest()->getControllerName() != 'sales_order') {
            return;
        }
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/widget_grid_massaction_item') {
            /** @var Mage_Adminhtml_Block_Sales_Order_Grid $block */
            $block = $block->getLayout()->getBlock('sales_order.grid');

            if (!$block) {
                return;
            }
            $allColumns = $helper->getAllGridColumns();
            $listColumns = $helper->getGridColumns();
            foreach ($listColumns as $column) {
                switch ($column) {
                      case 'skus':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('skus', array(
                            'header' => $helper->__('SKU(s)'),
                            'index' => 'skus',
                            'renderer' => 'ordersku/adminhtml_sales_order_renderer_products'
                        ));
                     }
                     break;
                    case 'ip':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('ip', array(
                            'header' => $helper->__('IP'),
                            'index' => 'ip',
                        ));
                     }
                     break;
                    case 'coupon':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('coupon', array(
                            'header' => $helper->__('Coupon'),
                            'index' => 'coupon',
                        ));
                     }
                     break;
                    case 'ship_method':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('ship_method', array(
                            'header' => $helper->__('Ship Method'),
                            'index' => 'ship_method',
                        ));
                     }
                     break;
                }
            }
            foreach ($allColumns as $position => $column) {
                if (!in_array($column, $listColumns)) {
                      $block->removeColumn($column);
                  } else {
                      $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                  }
             }
        }

        return;
    }
    public function addColumnsToCustomerOrdersGrid($observer)
    {
       $helper = $this->getOsHelper();
       if (Mage::app()->getRequest()->getControllerName() != 'customer') {
            return;
        }
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/widget_grid_massaction') {
            /** @var Mage_Adminhtml_Block_Sales_Order_Grid $block */
            $block = $block->getLayout()->getBlock('adminhtml.customer.edit.tab.orders');
            if (!$block) {
                return;
            }
            $allColumns = $helper->getAllCustomerGridColumns();
            $listColumns = $helper->getCustomerGridColumns();
            foreach ($listColumns as $column) {
                switch ($column) {
                    case 'skus':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('skus', array(
                            'header' => $helper->__('SKU(s)'),
                            'index' => 'skus',
                            'renderer' => 'ordersku/adminhtml_sales_order_renderer_products'
                        ));
                     }
                     break;
                    case 'ip':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('ip', array(
                            'header' => $helper->__('IP'),
                            'index' => 'ip',
                        ));
                     }
                     break;
                    case 'ship_method':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('ship_method', array(
                            'header' => $helper->__('Ship Method'),
                            'index' => 'ship_method',
                        ));
                     }
                     break;
                    case 'coupon':
                    if (in_array($column, $listColumns)) {
                       $block->addColumn('coupon', array(
                            'header' => $helper->__('Coupon'),
                            'index' => 'coupon',
                        ));
                     }
                     break;
                }
            }
            foreach ($allColumns as $position => $column) {
                if (!in_array($column, $listColumns)) {
                      $block->removeColumn($column);
                  } else {
                      $this->addColumnBySortPosition($position, $allColumns, $block, $column);
                  }
             }
        }

        return;
    }
     /**
     * Add custom columns in sales order grid collection
     *
     * Event: sales_order_grid_collection_load_before
     * Observer Name: ordersku_add_custom_columns_select
     *
     * @param $observer
     * @return void
     */
    public function addCustomColumnsSelect($observer)
    {
      $helper = $this->getOsHelper();
      if (Mage::app()->getRequest()->getControllerName() == 'sales_order' || Mage::app()->getRequest()->getControllerName() == 'customer') {
          Varien_Profiler::start('ordersku_addCustomColumnsSelect');
          /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $orderCollection */
          $orderGridCollection = $observer->getOrderGridCollection();
          $model = Mage::getModel('ordersku/grid');
          $model->modifyOrdersGridCollection($orderGridCollection);
          Varien_Profiler::stop('ordersku_addCustomColumnsSelect');
      }

        return;
    }
    protected function getOsHelper()
    {
        return Mage::helper('ordersku');
    }
        /**
     * @param int $position
     * @param array $allColumns
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $block
     * @param string $column
     *
     * @return void
     */
    protected function addColumnBySortPosition($position, $allColumns, $block, $column)
    {
        if ($position > 0 && isset($allColumns[$position-1])) {
            if (!is_object($block->getColumn($column))) {
                return;
            }
            $block->getColumn($column)->setData('filter_index','main_table.'.$column);
            $thatColumn = $block->getColumn($column)->getData();
            $block->removeColumn($column);
            $columnBeforeThat = $allColumns[$position-1];
            $block->addColumnAfter($column, $thatColumn, $columnBeforeThat);
        }
    }
}
 ?>
