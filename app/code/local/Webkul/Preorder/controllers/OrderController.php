<?php

	# Controllers are not autoloaded so we will have to do it manually:
	require_once 'Mage/Sales/controllers/OrderController.php';
	class Webkul_Preorder_OrderController extends Mage_Sales_OrderController {
		public function reorderAction() {
			$helper = Mage::helper("preorder");
			if (!$this->_loadValidOrder()) {
				return;
			}
			$order = Mage::registry('current_order');

			$cart = Mage::getSingleton('checkout/cart');

			$flag = 0;
			$quote = Mage::getSingleton("checkout/cart")->getQuote();
			if(Mage::helper("checkout/cart")->getItemsCount()!=0) {
				foreach($quote->getAllItems() as $item) {
					$currentProductId = $item->getProductId();
					$product = Mage::getModel("catalog/product")->load($currentProductId);
					
					if($helper->isPreorder($currentProductId)) {
						$flag=1;
					}
				}
			}

			if($flag==1) {
				Mage::getSingleton('checkout/session')->addError(Mage::helper('preorder')->__('You can not add other Product with Preorder Product.'));
			} else {
				$quoteItemArray = array();
				$quote = Mage::getSingleton("checkout/cart")->getQuote();
				foreach($quote->getAllItems() as $item) {
					$itemId = $item->getItemId();
					$quoteItemArray[] = $itemId;
				}

				$cartTruncated = false;
				/* @var $cart Mage_Checkout_Model_Cart */

				$items = $order->getItemsCollection();
				foreach ($items as $item) {
					try {
						$cart->addOrderItem($item);
					} catch (Mage_Core_Exception $e){
						if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
							Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
						}
						else {
							Mage::getSingleton('checkout/session')->addError($e->getMessage());
						}
						$this->_redirect('*/*/history');
					} catch (Exception $e) {
						Mage::getSingleton('checkout/session')->addException($e,
							Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
						);
						$this->_redirect('checkout/cart');
					}
				}
				$cart->save();

				$flag = 0;
				$quote = Mage::getSingleton("checkout/cart")->getQuote();
				if(Mage::helper("checkout/cart")->getItemsCount()!=0) {
					foreach($quote->getAllItems() as $item) {
						$currentProductId = $item->getProductId();
						$product = Mage::getModel("catalog/product")->load($currentProductId);
						
						if($helper->isPreorder($currentProductId)) {
							$flag=1;
						}
					}
				}
				if($flag==1) {
					$cartHelper = Mage::getSingleton("checkout/cart");
					$quote = Mage::getSingleton("checkout/cart")->getQuote();
					foreach($quote->getAllItems() as $item) {
						$itemId = $item->getItemId();
						if(in_array($itemId, $quoteItemArray)) {
							$cartHelper->removeItem($itemId);
						}
					}
					$cartHelper->save();
				}
			}
			$this->_redirect('checkout/cart');
		}
	}
