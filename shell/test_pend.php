<?php

/**
 * @author Martin Zhang <martin.zhang@silksoftware.com>
 * @copyright Copyright (c) SILK Software Inc. (http://www.silksoftware.com)
 */

error_reporting(7);

//date_default_timezone_set("America/Los_Angeles");

$basePath = dirname(dirname(__FILE__));
require_once($basePath . DIRECTORY_SEPARATOR . 'shell' . DIRECTORY_SEPARATOR . 'abstract.php');

class Silk_Sales_Export_Orders extends Mage_Shell_Abstract
{
    protected $_order;
    protected $_line;
    protected $_file;
    protected $_day = 1;
    protected $_model;
    protected $_fromdate;
    protected $_todate;
    
    protected $_store_code;
    protected $_store_id=-1;
    

    protected function _getCollectionClass()
    {
        return 'sales/order_collection';
    }

    protected function display($var)
    {
        if (is_array($var) || is_object($var)) {
            var_dump($var);
        } else {
            echo $var;
        }
        echo "\n";
    }

    protected function header()
    {
        //order id, created, store code, coupon code, 
        //billing name, billing address1, billing address2, billing zip, billing region, billing city, billing phone, billing fax, billing email, 
        //shipping name, shipping address1, shipping address2, shipping zip, shipping region, shipping city, shipping phone, shipping fax, 
        //product name, color, size, inv id, style id, sku, qty, price, subtotal, shipping, tax, discount, gift wrapping, total
        $thstyle = 'border:none;background:#4B2176;padding:.75pt .75pt .75pt .75pt;color:yellow;';
        $html  = '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes">';
        $html .= '<td style="'.$thstyle.'">#</td>';
        //$html .= '<td style="'.$thstyle.'">Ship Method</td>';
        $html .= '<td style="'.$thstyle.'">Pending Days</td>';
        $html .= '<td style="'.$thstyle.'">Order#</td>';
        $html .= '<td style="'.$thstyle.'">Model #</td>';
        $html .= '<td style="'.$thstyle.'">Product</td>';
        $html .= '<td style="'.$thstyle.'">Con</td>';  
        $html .= '<td style="'.$thstyle.'">L#</td>';         
        $html .= '<td style="'.$thstyle.'">Order Status</td>';
        $html .= '<td style="'.$thstyle.'">Inventory Status</td>';
        $html .= '<td style="'.$thstyle.'">Ordered</td>';
        $html .= '<td style="'.$thstyle.'">Shipped</td>';
        $html .= '<td style="'.$thstyle.'">Balance</td>';
        $html .= '<td style="'.$thstyle.'">Bal Amt</td>';
        $html .= '<td style="'.$thstyle.'">WH</td>';
        $html .= '<td style="'.$thstyle.'">ERP SO #</td>';
        $html .= '<td style="'.$thstyle.'">ERP DO #</td>';
        $html .= '<td style="'.$thstyle.'">Purchased On</td>';
        $html .= '<td style="'.$thstyle.'">Customer Name</td>';
       
        $html .= '</tr>';
        
        return $html;
    }
    
    
     protected function footer($total_balance=0, $total_balamount='$0')
    {
        //order id, created, store code, coupon code, 
        //billing name, billing address1, billing address2, billing zip, billing region, billing city, billing phone, billing fax, billing email, 
        //shipping name, shipping address1, shipping address2, shipping zip, shipping region, shipping city, shipping phone, shipping fax, 
        //product name, color, size, inv id, style id, sku, qty, price, subtotal, shipping, tax, discount, gift wrapping, total
        $thstyle = 'border:none;background:#4B2176;padding:.75pt .75pt .75pt .75pt;color:yellow;font-weight:bold;';
        $html  = '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes">';
        $html .= '<td style="'.$thstyle.'"></td>';
        //$html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';  
        $html .= '<td style="'.$thstyle.'"></td>';         
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'">Total</td>';
        $html .= '<td style="'.$thstyle.'">'.$total_balance.'</td>';
        $html .= '<td style="'.$thstyle.'">'.$total_balamount.'</td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
        $html .= '<td style="'.$thstyle.'"></td>';
       
        $html .= '</tr>';
        
        return $html;
    }


    protected function groupShipping($methodtitle, $i)
    {
        $thstyle = 'border:none;background:#ffffff;padding:.75pt .75pt .75pt .75pt;padding-top: 20px; color:#000000;font-weight:bold;';
        if ($i == 0) {
            $thstyle = 'border:none;background:#ffffff;padding:.75pt .75pt .75pt .75pt;padding-top:5px; color:#000000;font-weight:bold;';
        }
        $html  = '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes">';
        $html .= '<td colspan="18" style="'.$thstyle.'">   Shipping Method: '.$methodtitle.'</td>';
        $html .= '</tr>';
        
        return $html;
    }

    
    

    protected function getAddress($streets, $i)
    {
        if ($streets) {
            if (isset($streets[$i])) {
                return $streets[$i];
            }
        }
         
        return '';
    }

    protected function getStoreCode($sid)
    {
        $code = '';
        $store = Mage::getModel('core/store')->load($sid);
        
        if ($store) {
            $code = $store->getCode();
        }

        return $code;
    }
    
    protected function setStoreCode($storeCode)
    {
    	$stores = array_keys(Mage::app()->getStores());
    	foreach($stores as $id){
    		$store = Mage::app()->getStore($id);
    		if($store->getCode()==$storeCode) {
    			$this->_store_id=$store->getId();
    			return true;
    		}
    	}
    	
    	
    	$this->display( 'Store "' . $storeCode. '" not found.');
    	
    	return false;// if not found
    }
    
    protected function get_timezone_offset($remote_tz, $origin_tz = null) {
	   	if($origin_tz === null) {
    		if(!is_string($origin_tz = date_default_timezone_get())) {
    			return false; // A UTC timestamp was returned — bail out!
    		}
    	}
    	$origin_dtz = new DateTimeZone($origin_tz);
    	$remote_dtz = new DateTimeZone($remote_tz);
    	$origin_dt = new DateTime("now", $origin_dtz);
    	$remote_dt = new DateTime("now", $remote_dtz);
    	$offset = $origin_dtz->getOffset($origin_dt)-$remote_dtz->getOffset($remote_dt);
    	return $offset;
    }

    public function getOrderList()
    {
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        //$filterStatus = '"pending", "processing"';
        $filterStatus = '';
        $configFilterStatus = explode(',', Mage::getStoreConfig('orderstatusemail/send_orders/orderstatus'));
        foreach ($configFilterStatus as $f) {
			$filterStatus .= '"'.$f.'",';
		}
		$filterStatus .='"end"';
		

        $collection->getSelect()->where('main_table.status in ('.$filterStatus.')');
        //$collection->getSelect()->where('DATE(main_table.created_at)>date_add(?, interval -'.$this->_day.' day)', date("Y-m-d"));
        //$collection->getSelect()->where('main_table.created_at>date_add(?, interval -'.$this->_day.' day)', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
        $collection->getSelect()->order('shipping_sort', 'asc');
        $collection->getSelect()->order('created_at', 'asc');
        
        $orders = $collection->load();
         
        //$this->display($collection->getSelect()->__toString());
        
        if (count($orders)<1){
            $this->display("No orders can be exported.");
            return;
        }
        
        $html = '<table style="mso-cellspacing:1.5pt;border:solid purple 1.0pt;mso-border-alt:solid purple .75pt;
   mso-yfti-tbllook:1184;font-size:9.0pt;">';
        $pre_shipping = '';
        $total_balance = 0;
        $total_balamount = 0;
        $i = 0;
          //$html .= $this->header();
        foreach ($orders as $order) {
            $ord = Mage::getModel('sales/order')->load($order->getId());
            $oid = $order->getIncrementId();
            $cat = Mage::getModel('core/date')->date(null, $order->getCreatedAt());
            $scd = $this->getStoreCode($order->getStoreId());
            $ccd = $order->getCouponCode();

            $bnm = $order->getBillingAddress()->getName();
            $ba1 = $this->getAddress($order->getBillingAddress()->getStreet(),0);
            $ba2 = $this->getAddress($order->getBillingAddress()->getStreet(),1);
            $bzi = $order->getBillingAddress()->getZip();         //  'billing zip';
            $bst = $order->getBillingAddress()->getRegion();
            $bct = $order->getBillingAddress()->getCity();
            $bph = $order->getBillingAddress()->getTelephone();   //  'billing phone';
            $bfa = $order->getBillingAddress()->getFax();         //  'billing fax';
            $bem = $order->getBillingAddress()->getEmail();       //  'billing email';
            
            if ($order->getShippingAddress()) {
                $snm = $order->getShippingAddress()->getName();
                $sa1 = $this->getAddress($order->getShippingAddress()->getStreet(),0);
                $sa2 = $this->getAddress($order->getShippingAddress()->getStreet(),1);
                $szi = $order->getShippingAddress()->getZip();;
                $sre = $order->getShippingAddress()->getRegion();
                $sct = $order->getShippingAddress()->getCity();
                $sph = $order->getShippingAddress()->getTelephone();
                $sfa = $order->getShippingAddress()->getFax();
            }
            $orderlink = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view/order_id/'.$order->getId());
            $orderlink = str_replace('send_orders.php/', '', $orderlink);
            //$orderlink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'index.php/admin/sales_order/view/order_id/'.$order->getId();
           
            
            $items = $order->getItemsCollection();

            $pendingdays = floor((strtotime(date("Y-m-d H:i:s"))-strtotime($order->getCreatedAt()))/(24*60*60));
            //$pendingdays = floor((strtotime(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))-strtotime($order->getCreatedAt()))/(24*60*60));
            $created = Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true);

            if ($pre_shipping != $order->getShippingDescription()) {
                 $html .= $this->groupShipping($order->getShippingDescription(), $i);
				 $html .= $this->header();
			}
            $pre_shipping = $order->getShippingDescription();
            $line_number = 0;
            foreach ($items as $item) {
                if (!$item->getParentItemId()) {
					$line_number += 1;
                    $prd = Mage::getModel('catalog/product')->load($item->getProductId());
                    //$shipments = Mage::getResourceModel('mymodule/my_custom_collection')
                    //->addAttributeToSelect('*')
                   //->addCommentsToFilter(array('Possible comment', 'Another possible comment'));
                   
                   $shipconn = Mage::getResourceModel('sales/order_shipment_item_collection');
                   //echo 'xxxxxxxxxx'.$item->getProductId();
                    $shipconn->getSelect()->joinLeft(array('shipment'=>'sales_flat_shipment'), 'main_table.parent_id=shipment.entity_id', 'shipment.benq_erp_do');
                    $shipconn->getSelect()->where('shipment.order_id=?',$order->getId());
                    $shipconn->getSelect()->where('main_table.product_id=?',$item->getProductId())->group(array('main_table.product_id'));
                   //$e = $shipconn->getSelectSql(true);
                   //echo $e;
                   $shipdata = $shipconn->load();
                   $packages = '';
                   //print_r($shipdata);
                   if ($shipconn->getSize()) {
					    $data = $shipdata->getData();
					    $packages = $data[0]['benq_erp_do'];
				   }
				    $balance = (int)$item->getQtyOrdered() - (int)$item->getQtyShipped();
                    if ($balance == 0) continue;
                    $total_balance += $balance;
                    $total_balamount += $item->getRowTotal();
                    $i += 1;
                    $tdstyle = 'border:none;padding:.75pt .75pt .75pt .75pt';
                    if ($i%2 == 0) {
						$tdstyle .= ';background:#FFFFD9';
				    }
                    $stock = ($prd->getStockItem()->getIsInStock() && $prd->getStockItem()->getQty()>0)?'In stock':'Out of stock';
                    if ($stock == 'In stock' && $pendingdays > 3) {
                        $tdstyle .=';color:red';
                    }
					$html .= '<tr style="mso-yfti-irow:1">';
					$html .= '<td style="'.$tdstyle.'">'.$i.'</td>';
					//$html .= '<td style="'.$tdstyle.'">'.$order->getShippingDescription().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$pendingdays.'</td>';
					$html .= '<td style="'.$tdstyle.'"><a href="'.$orderlink.'">'.$oid.'</a></td>';
					$html .= '<td style="'.$tdstyle.'">'.$prd->getSku().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$prd->getName().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$prd->getAttributeText('condition').'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$line_number.'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$order->getStatusLabel().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$stock.'</td>';
					$html .= '<td style="'.$tdstyle.'">'.(int)$item->getQtyOrdered().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.(int)$item->getQtyShipped().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$balance.'</td>';
					$html .= '<td style="'.$tdstyle.'">'.Mage::helper('core')->currency($item->getRowTotal(),true,false).'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$prd->getAttributeText('benq_warehouse').'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$order->getExtOrderId().'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$packages.'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$created.'</td>';
					$html .= '<td style="'.$tdstyle.'">'.$snm.'</td>';
					$html .= '</tr>';
            
            
                }
            }
            
            
        }
        $total_balamount = Mage::helper('core')->currency($total_balamount,true,false);
        $html .= $this->footer($total_balance, $total_balamount);
        $html .=  '</table>';
        return $html;
      
    }

    public function run()
    {
        $body = $this->getOrderList();
        $date = Mage::helper('core')->formatDate(now(), 'medium', true);
        //$receivers =  explode(',', Mage::getStoreConfig('orderstatusemail/send_orders/orderemail'));
        $receivers =  explode(',', 'claudio.xu@silksoftware.com,heartj@126.com');
		$mailer = Mage::getModel('core/email_template_mailer');
		$templateId =  Mage::getStoreConfig('orderstatusemail/email_template/template3');
		if (count($receivers)>0) {
			foreach($receivers as $r) {
				$emailInfo = Mage::getModel('core/email_info');
				$emailInfo->addTo(trim($r));
				$mailer->addEmailInfo($emailInfo);
			}
		}
		$sender = Array(
            'name' => 'Admin',
            'email' => 'admin@benq.com'
        );
		$mailer->setSender($sender);
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'body' => $body,
                'date'=> $date
            )
        );
		$mailer->send();
        
    }
}

$shell = new Silk_Sales_Export_Orders();
$shell->run();

