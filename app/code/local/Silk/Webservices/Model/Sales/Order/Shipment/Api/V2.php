<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales order shippment API V2
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// class Mage_Sales_Model_Order_Shipment_Api_V2 extends Mage_Sales_Model_Order_Shipment_Api
class Silk_Webservices_Model_Sales_Order_Shipment_Api_V2 extends Mage_Sales_Model_Order_Shipment_Api_V2
{
    /**
     * Create new shipment for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @return string
     */
    public function create($orderIncrementId, $itemsQty = array(), $comment = null, $email = false,
        $includeComment = false
    ) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $itemsQty = $this->_prepareItemQtyData($itemsQty);
        /**
          * Check order existing
          */
        if (!$order->getId()) {
             $this->_fault('order_not_exists');
        }

        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
             $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do shipment for order.'));
        }

         /* @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            if ($email) {
                $shipment->setEmailSent(true);
            }
            $shipment->getOrder()->setIsInProcess(true);
            try {
/*
* Add tracking data to this shipment
* by: Silk Software 2011-12-12
*/
		list($carrier, $title, $trackNumber) = explode("|", $comment, 3);
		$track = Mage::getModel('sales/order_shipment_track')
			->setNumber($trackNumber)
			->setCarrierCode($carrier)
			->setTitle($title);

		$shipment->addTrack($track);
/*
* Add tracking data to this shipment
* by: Silk Software 2011-12-12
*/
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (Mage_Core_Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }
            return $shipment->getIncrementId();
        }
        return null;
    }
}
