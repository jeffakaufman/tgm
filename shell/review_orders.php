<?php

/**
 * @author Martin Zhang <martin.zhang@silksoftware.com>
 * @copyright Copyright (c) SILK Software Inc. (http://www.silksoftware.com)
 */

error_reporting(7);

//date_default_timezone_set("America/Los_Angeles");

$basePath = dirname(dirname(__FILE__));
require_once($basePath . DIRECTORY_SEPARATOR . 'shell' . DIRECTORY_SEPARATOR . 'abstract.php');

class Silk_Review_Orders extends Mage_Shell_Abstract
{
    protected $_order;
    
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

    public function getOrderList()
    {
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $filterStatus = '"payment_review"';
		
        $collection->getSelect()->where('main_table.state in ('.$filterStatus.')');
        $orders = $collection->load();
        return $orders;
    }

    public function run()
    {
        $orders = $this->getOrderList();
        foreach ($orders as $order) {
            $method = $order->getPayment()->getMethod();
            if ($method == 'verisign') {
		try{
		    $order->getPayment()
			  ->registerPaymentReviewAction(Mage_Sales_Model_Order_Payment::REVIEW_ACTION_UPDATE, true);
            if ($this->checkUpdate($order)) {
                $order->save();
            }
		} catch (Mage_Core_Exception $e) {
		   Mage::log($e->getMessage());
		} catch (Exception $e) {
		   Mage::logException($e);
		}   
            }
        }
    }


    public function checkUpdate($order, $isOnline=true) {
        $payment = $order->getPayment();
        $transactionId = $isOnline ? $payment->getLastTransId() : $payment->getTransactionId();

        if ($isOnline) {
            $payment->getMethodInstance()
                ->setStore($order->getStoreId())
                ->fetchTransactionInfo($payment, $transactionId);
        } else {
            // notification mechanism is responsible to update the payment object first
        }
        if ($payment->getIsTransactionApproved()) {
            $result = 1;
        } elseif ($payment->getIsTransactionDenied()) {
            $result = 2;
        } else {
            $result = 0;
        }

        return $result;

    }
}

$shell = new Silk_Review_Orders();
$shell->run();

