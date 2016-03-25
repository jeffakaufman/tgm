<?php
 
error_reporting(7);

//date_default_timezone_set("America/Los_Angeles");

$basePath = dirname(dirname(__FILE__));
require_once($basePath . DIRECTORY_SEPARATOR . 'shell' . DIRECTORY_SEPARATOR . 'abstract.php');
 
class Shell_Retrieve_Orders extends Mage_Shell_Abstract
{

   /**
    * sendEmail
    * @param  [string] $orderId  [description]
    * @param  [obj] $warranty [description]
    * @return [type]           [description]
    */
    public function sendEmail($orderId,$warranty)
  {     
            // This is the template name from your etc/config.xml 
            $template_id =Mage::getStoreConfig('orderstatusemail/email_template/template4');
            // Who were sending to...
            $email_to = explode(',', Mage::getStoreConfig('orderstatusemail/warranty_sender/warrantyemail'));
            $customer_name   = array();
            // getmodel core_model_email_template
            $email_template  = Mage::getModel('core/email_template');
            //Getting the Store E-Mail Sender Name.
            $senderName = Mage::getStoreConfig('trans_email/ident_general/name');
            //Getting the Store General E-Mail.
            $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
            //Variables for Confirmation Mail.
            $vars = array(
                'orderId'=>$orderId,
                'warranty'=>$warranty
                );
            $sender=array(
                'name'=>$senderName,
                'email'=>$senderEmail
                );
            //Send the email!
            $email_template->sendTransactional($template_id, $sender, $email_to, $customer_name, $vars);     

   }

    /**
     * Run script
     *
     */
    public function run()
    {
             $ob=Mage::getModel('misc/misc')->load(1);
             $LastSendEmailTime=$ob->getCreateAt();
             $nowTime= Mage::helper('core')->formatDate(now(), 'medium', true);
             $ob->create_at=$nowTime;
             $ob->save();
             $sales = Mage::getModel('sales/order')->getCollection();
             $sales=$sales->AddFieldToFilter('created_at',array('gt'=>$LastSendEmailTime));
             foreach($sales as $order)
            {       
                    if($order->getAllItems() && count($order->getAllItems())>1){
                         $warranty=array();
                         $model_no=array();
                         $product=array();
                         $hasProduct=array();
                         $oid = $order->getRealOrderId();
                          foreach ($order->getAllItems() as  $item) {
                             $product[]=$item->getSku();
                         }
                         foreach ($order->getAllItems() as  $item) {
                                    if(array_key_exists("model_no", $item->getBuyRequest()->getData())){
                                                 $warranty=trim($item->getSku());
                                                 $buyRequest=$item->getBuyRequest()->getData();
                                                 $model_no[$warranty]=trim($buyRequest['model_no']);
                                            }
                                       if(array_key_exists("warranty-options", $item->getBuyRequest()->getData())){
                                                $hasProduct[]=$item->getSku();
                                       }
                         }
                         if(count($model_no)>0 && count($hasProduct)>0){
                                $warranties="";
                                foreach ($model_no as $key=>$value) {
                                    if( ! in_array($value, $product)){
                                        $warranties.=$key." ";
                                    }
                                }
                                if($warranties){
                                   $this->sendEmail($oid,trim($warranties));
                                }
                         }                              
                    }
            }
    }
}
$shell = new Shell_Retrieve_Orders();
$shell->run();
?>