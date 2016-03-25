    <?php 
    class Silk_Ew_Model_Observer { 

        public function warranty($observer){
             $params = Mage::app()->getRequest()->getParams();
             $_item = $observer->getEvent()->getItems();
             $product=$_item[0]->getProduct();
             $item = $_item[0];
             $isWarranty=$product->getAsWarrantyOption();
             if($isWarranty){
                    if(isset($params['model_no']) && isset($params['purchase_date']) && isset($params['retail_price'])){
                                     $additionalOptions = array();
                                     $v1 = trim($params['model_no']);
                                     $v2 = $params['purchase_date'];
                                     $v3 = Mage::helper('core')->currency($params['retail_price'], true, false);
                                     $options = array(
                                            "model_no:"=>$v1, 
                                            "purchase_date:"=>$v2, 
                                            "retail_price:"=>$v3
                                            );
                                     if(!empty($params['serial_number'])){
                                        $options['serial_number']=$params['serial_number'];
                                     }
                                         foreach ($options as $key => $value)
                                         {
                                               $additionalOptions[] = array(
                                                                        'label' => $key,
                                                                         'value' => $value,
                                                                       );
                                         }
                                         $item->addOption(array(
                                                'code' => 'additional_options',
                                                'value' => serialize($additionalOptions)
                                               ));
                    }elseif(isset($params['related_product'])){
                                $product=Mage::getModel("catalog/product")->load($params['product']);
                                $sku=trim($product->getSku());
                                $price=Mage::helper('core')->currency($product->getPrice(), true, false);
                                $time=date('m/d/y',time());
                                $additionalOptions = array();
                                $options = array(
                                    "model_no:"=>$sku, 
                                    "purchase_date:"=>$time, 
                                    "retail_price:"=>$price
                                  );
                                 foreach ($options as $key => $value)
                                 {
                                       $additionalOptions[] = array(
                                                                'label' => $key,
                                                                 'value' => $value,
                                        );
                                 }
                                $item->addOption(array(
                                                'code' => 'additional_options',
                                                'value' => serialize($additionalOptions)
                                               ));
                    }
               }
        }
        public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
       {
    	    $quoteItem = $observer->getItem();
    	    if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
    	        $orderItem = $observer->getOrderItem();
    	        $options = $orderItem->getProductOptions();
    	        $options['additional_options'] = unserialize($additionalOptions->getValue());
    	        $orderItem->setProductOptions($options);
    	    }
        }


    }
