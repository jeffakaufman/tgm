<?php 

class Silk_OrderStatusEmail_Model_Observer { 

    public function emailSendOrderStatusChangeAfter($observer)
    {
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        $order = $observer->getEvent()->getOrder();
        $ordernumber = $order->getIncrementId();
        //$orderlink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'index.php/admin/sales_order/view/order_id/'.$order->getId();
        $orderlink = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view/order_id/'.$order->getId());
        $financeStatusFilters = explode(',', Mage::getStoreConfig('orderstatusemail/finance_dept/orderstatus'));
        $marketStatusFilters = explode(',', Mage::getStoreConfig('orderstatusemail/marketing_dept/orderstatus'));
        
        $sender = Array(
            'name' => 'Admin',
            'email' => 'admin@benq.com'
        );
        $store = Mage::app()->getStore();
        $vars = array('orderstatus'=>$order->getStatusLabel(), 'orderlink'=>$orderlink, 'ordernumber'=>$ordernumber);
        
        $re1 = array();
        $re2 = array();
        $templateId =  Mage::getStoreConfig('orderstatusemail/email_template/template1');
        if(in_array($order->status, $financeStatusFilters)) {
			$receivers1 =  explode(',', Mage::getStoreConfig('orderstatusemail/finance_dept/orderemail'));
			if (count($receivers1)>0) {
				foreach($receivers1 as $r) {
					$re1[trim($r)] = 'Finance Dept';
				}
			}
            Mage::getModel('core/email_template')->sendTransactional($templateId,
                                                         $sender,
                                                         array_keys($re1),
                                                         array_values($re1),
                                                         $vars,
                                                         $store->getId()
                                                        );
			
		}
		
		if(in_array($order->status, $marketStatusFilters)) {
			$receivers2 =  explode(',', Mage::getStoreConfig('orderstatusemail/marketing_dept/orderemail'));
			if (count($receivers2)>0) {
				foreach($receivers2 as $r) {
					$re2[trim($r)] = 'Marketing Dept';
				}
			}
            Mage::getModel('core/email_template')->sendTransactional($templateId,
                                                         $sender,
                                                         array_keys($re2),
                                                         array_values($re2),
                                                         $vars,
                                                         $store->getId()
                                                        );
			
		}
		
		//notify customer when cancel order
		if ($order->status == 'canceled') {
		    $order->sendOrderUpdateEmail(true, '');
	    }


    }
    
    
    function addShippingSortAndSetCustomStatusWhenSaveOrder($observer) {
		$event = $observer->getEvent();
        $order = $event->getOrder();
        $sort = 999;
        //Mage::log($order->getShippingMethod());
        
        $sortmaps = array(
                'fedex_FEDEX_2_DAY'                         => 55,
                'fedex_FEDEX_2_DAY_AM'                      => 44,
                'fedex_FEDEX_EXPRESS_SAVER'                 => 66,
                'fedex_FEDEX_GROUND'                        => 77,
                'fedex_FIRST_OVERNIGHT'                     => 11,
                'fedex_PRIORITY_OVERNIGHT'                  => 22,
                'fedex_STANDARD_OVERNIGHT'                  => 33,
                'tablerate_bestway'                         => 88,
                'flatrate_flatrate'                         => 99
            );
        if (isset($sortmaps[$order->getShippingMethod()])) {
            $sort = $sortmaps[$order->getShippingMethod()];
	    }
        $order->setShippingSort($sort);

        //add custom status
        $groupId = $order->getCustomerGroupId();
        $group = Mage::getModel('customer/group')->load($groupId);
        $groupName = $group->getCustomerGroupCode();
        if ($groupName == 'Wholesale') {
            if ($order->getStatus() == 'edi_transfer_pending') {
              $order->setStatus('wholesale_tax_verify');
            }else if ($order->getStatus() == 'fraud') {
              $order->setStatus('wholesale_review');
            }
            $order->addStatusHistoryComment('', false); 
        }

        
        //clear inventory cache when purchased a product
        //$this->clearInventoryCache();//this will destroy configurable product, so remove.
	}

    
	
	protected function clearInventoryCache() {
		Mage::getSingleton('cataloginventory/stock_status')->rebuild();
	}
    
}
