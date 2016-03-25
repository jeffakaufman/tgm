<?php

class Silk_Tablerate_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Tablerate
{
    /**
     * Enter description here...
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeQty += $item->getQty() * ($child->getQty() - (is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0));
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeQty += ($item->getQty() - (is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0));
                }
            }
        }

        if (!$request->getConditionName()) {
            $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);
        }

         // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        $result = Mage::getModel('shipping/rate_result');
        $rate = $this->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        if (!empty($rate) && $rate['price'] >= 0) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('tablerate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('bestway');
            $method->setMethodTitle($this->getConfigData('name'));
            if($this->getConfigFlag('free_shipping_enable')){
                if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
                    $shippingPrice = 0;
                }
            }else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            }
            $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            $method->setPrice($shippingPrice);
            $method->setCost($rate['cost']);

            $result->append($method);
        }

        return $result;
    }
}
