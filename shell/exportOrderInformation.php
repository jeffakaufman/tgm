<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
require_once 'abstract.php';
date_default_timezone_set('GMT');
class Mage_Shell_Export_Order_Informantion extends Mage_Shell_Abstract
{
  public function run(){
       $resource = Mage::getSingleton('core/resource');
       $r = $resource->getConnection('core_read');
       $w = $resource->getConnection('core_write');
       $orderTitle = array('Order #', 'Order Date','Order Amount', 'Order Status','shipping method','Payment Method');
       $filename = 'order_infor';
       $file = $filename.'.csv';
       if(!file_exists('order_export')) {
          mkdir('order_export');
       }
       $fp = fopen('order_export/'.$file, 'a');
       // today saturday night
       $new = strtotime(date("y-m-d",time())); //y-m-d
       $to = $new;
       $from = $new-3600*24*365*4;
       $from_date = date('Y-m-d' . ' 00:00:00', $from);
       $to_date = date('Y-m-d' . ' 23:59:59', $to);
      /**
      Order No
      Order Date
      Order Amount
      Order Status ( cancel, close, ship,... )
      shipping method
      payment method ( credit card, amazon payment, paypal...)
       */
       $status = 'complete';
       $sql_order = "SELECT main_tbl.`increment_id`,main_tbl.`created_at`,main_tbl.`base_grand_total`,main_tbl.`status`,order_tbl.`shipping_description`,payment_tbl.`method` FROM `sales_flat_order_grid` AS main_tbl
       LEFT JOIN `sales_flat_order` AS order_tbl ON order_tbl.`entity_id`= main_tbl.`entity_id`
       LEFT JOIN `sales_flat_order_payment` AS payment_tbl ON payment_tbl.`parent_id`= main_tbl.`entity_id`
       WHERE (main_tbl.`created_at` >= '{$from_date}' AND main_tbl.`created_at` <= '{$to_date}') ORDER BY main_tbl.`created_at`;";
       //echo $sql_order;
       fputcsv($fp,$orderTitle,"\t");
       foreach ($r->fetchAll($sql_order) as $order) {
          $order['method'] =  Mage::getStoreConfig('payment/' . $order['method'] . '/title');
          fputcsv($fp,$order,"\t");
       }
       fclose($fp);
  }

}
$shell = new Mage_Shell_Export_Order_Informantion();
$shell->run();
 ?>
