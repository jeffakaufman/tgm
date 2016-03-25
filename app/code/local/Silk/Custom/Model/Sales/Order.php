<?php
class Silk_Custom_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function hasCustomFields(){
		$var = $this->getPayment()->getPoNumber();
			if($var){
				return true;
			}else{
				return false;
			}
	}
	public function getFieldHtml(){
		$var =$this->getPayment()->getPoNumber();
		$html = '<b>P.O.# : </b>'.$var.'<br/>';
		return $html;
	}
	 public function cancel()
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation();

            Mage::dispatchEvent('order_cancel_after', array('order' => $this));
        }

        return $this;
    }
    public function canCancel()
    {
        if ($this->canUnhold()) {  // $this->isPaymentReview()
            return false;
        }

        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }
        if ($allInvoiced) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }

        /**
         * Use only state for availability detect
         */
        /*foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToCancel()>0) {
                return true;
            }
        }
        return false;*/
        return true;
    }
}
