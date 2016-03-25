<?php


class Silk_Ew_Block_List extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ew/list.phtml');

        $ews = Mage::getResourceModel('ew/ew_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('account_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('created_time', 'desc')
        ;
        

        $this->setEws($ews);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('ew')->__('My Extended Warranty'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'ew.pager')
            ->setCollection($this->getEws());
        $this->setChild('pager', $pager);
        $this->getEws()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($ew)
    {
        return $this->getUrl('extended-warranty/*/view', array('ew_id' => $ew->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function getProductQuality($code) {
      $quality = array('N'=>'New','R'=>'Refurbished','U'=>'Used');
      return $quality[$code];
    }
    public function getExpirationDate($_ew){
            $modelNumber=$_ew->getModelNumber();
            $model= Mage::getModel('catalog/product');
            $productid=$model->getIdBySku($modelNumber);
            $product=$model->load($productid);
            $warrantyPeriod=$product->getAttributeText('warranty_period');
            $start=$_ew->getDateOfPurchase();
            $end=explode(".", $_ew->getEwSku());
            $end=$end[1];
            $end=substr($end, -1).' year';
            $now=Mage::helper('core')->formatDate(now(), 'medium', true);
            $currentTimestamp = Mage::getModel('core/date')->timestamp(strtotime($now));
            $endTimestamp= Mage::getModel('core/date')->timestamp(strtotime($end));
            $startTimestamp= Mage::getModel('core/date')->timestamp(strtotime($start));
            $warrantyTimestamp= Mage::getModel('core/date')->timestamp(strtotime($start.'+'. $warrantyPeriod));
            if($warrantyPeriod && $warrantyTimestamp > $currentTimestamp){
                 $timestamp= Mage::getModel('core/date')->timestamp(strtotime($start .'+'. $warrantyPeriod .'+'. $end));
                 $expirationDate=Mage::getModel('core/date')->date('Y-m-d ', $timestamp);
                 return $expirationDate;
            }else{
                $start= $_ew->getCreatedTime();
                $timestamp= Mage::getModel('core/date')->timestamp(strtotime($start .'+'.$end));
                $expirationDate=Mage::getModel('core/date')->date('Y-m-d ',$timestamp);
                return $expirationDate;
            }
                  
    }
}
