<?php 
 
error_reporting(7);

//date_default_timezone_set("America/Los_Angeles");

$basePath = dirname(dirname(__FILE__));
require_once($basePath . DIRECTORY_SEPARATOR . 'shell' . DIRECTORY_SEPARATOR . 'abstract.php');

class Shell_Ew_Register extends Mage_Shell_Abstract{
		public function run(){
		 $ob=Mage::getModel('misc/misc')->load(2);
	             $checkbegin=$ob->getCreateAt();
	             $nowTime= time()-60*60*24*30;//Mage::helper('core')->formatDate(now(), 'medium', true);
	             $checkend=date('Y-m-d H:i:s',$nowTime);
	             $ob->create_at=$checkend;
	             $ob->save();
	             $sales = Mage::getModel('sales/order')->getCollection();
	             $sales=$sales->addFieldToFilter('created_at',array('gt'=>$checkbegin))
	             			  ->addFieldToFilter('created_at',array('lt'=>$checkend));
	             $model = Mage::getModel('ew/ew');
	             foreach($sales as $order){
			if(strtolower($order->getStatus())=="complete"){
				$ordernumber = $order->getRealOrderId();
		             	$cid = $order->getCustomerId();
		             	$items = $order->getAllItems();
		             	
	             	 	if($order->getShippingAddress()){
				       		$address = $order->getShippingAddress();
				       		$streetaddress=$address->getStreet();
				       	}elseif($order->getBillingAddress()){
				       		$address = $order->getBillingAddress();
				       		$streetaddress=$address->getStreet();
				       	}
				       	$firstName=$address->getFirstname();
				       	$lastName = $address->getLastname();
						$city = $address->getCity();
						$state = $address->getCountryId();
						$zipcode=$address->getPostcode();
						$email=($address->getEmail()!='')?$address->getEmail():$order->getCustomerEmail();
						$phone=$address->getTelephone();
						foreach ($items as $item) {
							$sku[]=$item->getSku();
						}
		             	foreach ($items as $item) {
		             		$data=array();
		             		$additional=$item->getProductOptions();
							if($additional['additional_options']){
								foreach ($additional['additional_options'] as $value) {
									if($value['label']=='model_no:'){
										 $value['label']='model_number';
									}elseif($value['label']=='purchase_date:'){
										 $value['label']='date_of_purchase';
									}elseif($value['label']=='retail_price:'){
										$value['label']='retail_price';
									}
										$data[$value['label']]=$value['value'];
									}
								$data['date_of_purchase']=date('Y-m-d',strtotime($data['date_of_purchase']));
								$data['retail_price']=(str_replace(',', '', substr($data['retail_price'], 1)));
								if(in_array($data['model_number'], $sku)){
										$productSku=substr($data['model_number'], -1);
										if($productSku==1){
											$data['product_quality']='N';
										}elseif($productSku==2){
											$data['product_quality']='R';
										}
								}else{
									$data['product_quality']='U';
								}
								$data['first_name']=$firstName;
								$data['last_name']=$lastName;
								$data['street_address']=$streetaddress[0];
								$data['street_address1']=$streetaddress[1];
								$data['city']=$city;
								$data['state']=$state;
								$data['zip_code']=$zipcode;
								$data['email']=$email;
								$data['phone']=$phone;
								$data['order_number']=$ordernumber;
								$data['ew_sku']=$item->getSku();
								$data['created_time']=date('Y-m-d H:i:s',time());//register time
								if($item->getQtyOrdered()>1){
									for ($i=0; $i <$item->getQtyOrdered() ; $i++) { 
										$model->setData($data);
						                			$model->setAccountId($cid);
										$model->save();
									}
								}else{
									$model->setData($data);
					                			$model->setAccountId($cid);
									$model->save();
								}
								
							}
		             	}
		            }
	             }      
		}
}


$shell = new Shell_Ew_Register();
$shell->run();

 ?>
