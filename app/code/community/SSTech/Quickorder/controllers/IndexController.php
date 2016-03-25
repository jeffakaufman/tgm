<?php
class SSTech_Quickorder_IndexController extends Mage_Core_Controller_Front_Action {

	protected function _getSession() {
		return Mage::getSingleton('customer/session');
	}
	public function indexAction() {
		if (Mage::getStoreConfigFlag("quickorder/quickorder/quickorder")) {
			$this->loadLayout();
			$this->getLayout()->getBlock("head")->setTitle($this->__("Quickorder"));
			$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
			$breadcrumbs->addCrumb("home", array(
					"label" => $this->__("Home Page"),
					"title" => $this->__("Home Page"),
					"link"  => Mage::getBaseUrl()
				));

			$breadcrumbs->addCrumb("quickorder", array(
					"label" => $this->__("Quickorder"),
					"title" => $this->__("Quickorder")
				));

			$this->renderLayout();
			if (!$this->_getSession()->isLoggedIn()) {

			}
			$params     = $this->getRequest()->getParams();
			$collection = $params['all'];
			if (count($params)) {
				$add1 = true;
				foreach ($collection as $products) {
					if (array_key_exists('checkbox', $products)) {
						$add = true;
						if ($products['qty'] > 0) {
							break;
						} else {
							$add  = false;
							$add1 = false;
						}
					} else {

						$add = false;
					}
				}

				if ($add && $add1) {
					$cart = Mage::getModel('checkout/cart');
					$cart->init();
					foreach ($collection as $products) {
						if ($products['checkbox'] == 1 && $products['qty'] > 0) {
							$pModel = Mage::getModel('catalog/product');
							$pModel->load($products['product_id']);
							if ($pModel->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
								try {
									$cart->addProduct($pModel, array('qty' => $products['qty']));
								} catch (Exception $e) {
									continue;
								}
							}
						}
					}
					$cart->save();
					if ($this->getRequest()->isXmlHttpRequest()) {
						exit('1');
					}
					$message = $this->__('Products were added to your shopping cart.');
					Mage::getSingleton('checkout/session')->addSuccess($message);
					$this->_redirect('checkout/cart');
				} else {
					if (!$add1) {
						Mage::getSingleton('core/session')->addError('Quantity should be greater than Zero!');
					} else {
						Mage::getSingleton('core/session')->addError('Please select products');
					}

					$this->_redirect('quickorder');
				}
			}
		} else {
			$this->norouteAction();
			return;
		}

	}

	public function accountAction() {
		if (Mage::getStoreConfigFlag("quickorder/quickorder/quickorder")) {
			$this->loadLayout();
			$this->getLayout()->getBlock("head")->setTitle($this->__("Quickorder"));
			$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
			$breadcrumbs->addCrumb("home", array(
					"label" => $this->__("Home Page"),
					"title" => $this->__("Home Page"),
					"link"  => Mage::getBaseUrl()
				));

			$breadcrumbs->addCrumb("quickorder", array(
					"label" => $this->__("Quickorder"),
					"title" => $this->__("Quickorder")
				));

			$this->renderLayout();
		} else {
			$this->norouteAction();
			return;
		}
	}

	// 	public function ajaxAction() {
	// 		$params     = $this->getRequest()->getParams();
	// 		$collection = $params['all'];
	// 		if (count($params)) {
	// 			$add1 = true;
	// 			foreach ($collection as $products) {
	// 				if (array_key_exists('checkbox', $products)) {
	// 					$add = true;
	// 					if ($products['qty'] > 0) {
	// 						break;
	// 					} else {
	// 						$add  = false;
	// 						$add1 = false;
	// 					}
	// 				} else {

	// 					$add = false;
	// 				}
	// 			}

	// 			if ($add && $add1) {
	// 				$cart = Mage::getModel('checkout/cart');
	// 				$cart->init();
	// 				foreach ($collection as $products) {
	// 					if ($products['checkbox'] == 1 && $products['qty'] > 0) {
	// 						$pModel = Mage::getModel('catalog/product');
	// 						$pModel->load($products['product_id']);
	// 						if ($pModel->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
	// 							try {
	// 								$cart->addProduct($pModel, array('qty' => $products['qty']));
	// 							} catch (Exception $e) {
	// 								continue;
	// 							}
	// 						}
	// 					}
	// 				}
	// 				$cart->save();
	// 			}
	// 			echo $this->_getCartDataToHtml();
	// 		}
	// 	}

	// 	public function _getCartDataToHtml() {
	// 		$totalPrice   = 0;
	// 		$cartDataJson = '<div style="" class="inner-wrapper"><p class="block-subtitle"> Recently Added Item(s)</p><ol id="mini-cart" class="mini-products-list">';
	// 		$items        = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
	// 		foreach ($items as $item) {
	// 			$totalPrice += $item->getPrice();
	// 			$cartDataJson .= <<<EOF
	// <li class="item odd">
	// 	<a href="http://local.benb.com/{$item->getName()}.html" title="{$item->getName()}" class="product-image"><img src="http://local.benb.com/media/catalog/product/cache/5/thumbnail/50x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/thumbnail.jpg" alt="{$item->getName()}"></a>
	// 	<div class="product-details">
	// 		<p class="product-name">
	// 			<a href="http://local.benb.com/{$item->getName()}.html">{$item->getName()}</a>
	// 		</p>
	// 		<table cellpadding="0">
	// 			<tbody><tr>
	// 				<th>Price</th>
	// 					<td><span class="price">${$item->getPrice()}</span></td>
	// 				</tr>
	// 				<tr>
	// 					<th>Qty</th>
	// 					<td>{$item->getQty()}</td>
	// 				</tr>
	// 			</tbody>
	// 		</table>
	// 		<a href="http://local.benb.com/checkout/cart/delete/id/68424/form_key/j8eWLcspELI5vBCY/uenc/aHR0cDovL2xvY2FsLmJlbmIuY29tL3F1aWNrb3JkZXIv/" onclick="return confirm('Are you sure you would like to remove this item from the shopping cart?');" title="remove item" class="btn-remove">remove item</a>
	// 	</div>
	// </li>
	// EOF

	// 		;

	// 	}
	// 	$cartDataJson .= <<<TDD
	// </ol>
	//             <script type="text/javascript">decorateList('mini-cart', 'none-recursive')</script>
	//                                                         <p class="subtotal">
	//                                             <span class="label">Cart Subtotal:</span> <span class="price">${$totalPrice}</span>                                    </p>
	//                         <div class="actions">
	//                                     <button class="button" type="button" onclick="setLocation('http://local.benb.com/checkout/onepage/')">Checkout</button>
	//                     <p class="paypal-logo">
	//     <span class="paypal-or">-OR-</span>
	// <a id="ec_shortcut_5ec8962f875a7b1b041af515be3225dc" href="http://local.benb.com/paypaluk/express/start/"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="Checkout with PayPal" title="Checkout with PayPal"></a>
	//     <!-- <span class="paypal-or">-OR-</span> -->
	// </p>
	//                                <a href="http://local.benb.com/checkout/cart/"><span>&lt; Go to Shopping Cart</span></a>
	//             </div>
	//                 </div>
	// TDD

	// ;

	// return $cartDataJson;
	// }
}
