<?php

class Silk_Ew_IndexController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

   public function listAction()
    {
        $this->loadLayout();
        $this->renderLayout();
 
    }

   public function viewAction()
    {

        $this->loadLayout();
        $this->renderLayout();
 
    }

    public function successAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    
    public function submitAction() {
        if ($data = $this->getRequest()->getPost()) {
            $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $model = Mage::getModel('ew/ew');

            $data['date_of_purchase'] = date('Y-m-d',strtotime($data['date_of_purchase']));
            $data['product_quality'] = $this->__getQuality($data['model_number']);

            $model->setData($data);
            $model->setAccountId($cid);

            $msg = Mage::helper('ew')->__('Unable to request.');
            try{
               $error = false;
               foreach ($data as $d=>$v) {
                   if (!Zend_Validate::is(trim($v), 'NotEmpty') && $d != 'serial_number' && $d != 'street_address1') {
                       $error = true;
                       $msg = Mage::helper('ew')->__("Some fields are required. ");
                   }
               }
               if ($data['serial_number'] && !preg_match("/[A-Z]{2}[A-Z0-9]{3}[0-9]{5}[A-Z0-9]{3}/", trim($data['serial_number']))) {
                   $error = true;
                   $msg = Mage::helper('ew')->__("Serial number is invalid. ");
               }
               if ($this->__wasApplied($data)) {
                   $error = true;
                   $msg = Mage::helper('ew')->__("The extended warranty application was existed or can't find matched warranty order.");
               }

               if ($error) {
                   throw new Exception();
               }
               $model->save();
               Mage::getSingleton('core/session')->addSuccess('You have applied successfully!');
               $this->_redirect('extended-warranty/index/success');
             } catch (Exception $e) {
               Mage::getSingleton('core/session')->addError($msg);
               $this->_redirectReferer();
               return;
             }
        }
        
    }


     protected function __wasApplied($data) {
        $ews = Mage::getResourceModel('ew/ew_collection')
            ->addFieldToSelect('ew_id')
            ->addFieldToFilter('order_number',$data['order_number']) 
            ->addFieldToFilter('ew_sku',$data['ew_sku']);
        ;
        $order_qty = 0;
        $_order = Mage::getModel('sales/order')->loadByIncrementId($data['order_number']);
        $_items = $_order->getItemsCollection();
        foreach ($_items as $it) {
              $product = Mage::getModel('catalog/product')->load($it->getProductId());
              if ($product->getAsWarrantyOption() && $product->getModelNo() &&  $product->getSku()== $data['ew_sku']) {
                 if (in_array($data['model_number'],explode(',',$product->getModelNo()))) {
                     $order_qty = $it->getQtyOrdered();
                     break;
                 }
              }
         }

        if ($ews->getSize() < $order_qty) {
            return false;
        }

        return true;


    }



    public function loadmodelAction() {
        $this->loadLayout('empty')->renderLayout();
    }

    public function loadewskuAction() {
        $this->loadLayout('empty')->renderLayout();
    }

    public function __getQuality($sku) {
        $suffix = substr($sku,-3);
        $val = '';
        if ($suffix == '001') {
            $val = 'N';
        }elseif ($suffix == '002') {
            $val = 'R';
        }else{
            $val = 'U';
        }
        return $val;
    }
}
