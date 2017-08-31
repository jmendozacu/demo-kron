<?php
require_once("Mage/Checkout/controllers/CartController.php");
class Velanapps_Shopy_CartController extends Mage_Checkout_CartController
{
	public function couponPostAction()
    {
       
	   /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }
		
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
		
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
		
        $oldCouponCode = $this->_getQuote()->getCouponCode();
		
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }
		
        try {
			
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();
			
			//SHOPY CODE STARTS HERE
			$this->_addMyProductShoppingCart($couponCode, $oldCouponCode);
			//SHOPY CODE ENDS HERE
            
			if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
                    $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }
        $this->_goBack();
    }
	
	protected function _updateShoppingCart()
    {
		try {
            $cartData = $this->getRequest()->getParam('cart');
            
			if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
					if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
				
                $cart->updateItems($cartData)
                    ->save();
				
				//SHOPY CODE STARTS HERE
				if($cart->getQuote()->getSubtotal() < 100) {
					$this->_updateMyProductShoppingCart();
				}
				//SHOPY CODE STARTS HERE
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }
	
	public function _addMyProductShoppingCart($couponCode, $oldCouponCode) 
	{	
		$cart = $this->_getCart();
		$cart->init();
		foreach($cart->getQuote()->getAllVisibleItems() as $item) {
			if($item->getProduct()->getId() == 2312) {
				$id = $item->getProduct()->getId();
				$name = $item->getProduct()->getName();
				$item_id = $item->getId();
				$qty = $item->getQty();
				break;
			}
		}
		if($couponCode == "FREEMUG" && $oldCouponCode != "FREEMUG") { 
			if($cart->getQuote()->getSubtotal() > 100) {
				
				$product = Mage::getModel('catalog/product')
						->setStoreId(Mage::app()->getStore()->getId())
						->load(2312);
				if($product->getId()) {
					$params['qty'] = 1;
					$cart->addProduct($product, $params);
					$cart->save();
					/* if (!$this->_getSession()->getNoCartRedirect(true)) {
						if (!$cart->getQuote()->getHasError()) {
							$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
							$this->_getSession()->addSuccess($message);
						}
					} */
				}
				if($qty) { Mage::getSingleton("customer/session")->setData("OLD_FREEMUG_QTY", $qty); } else {
					Mage::getSingleton("customer/session")->setData("NEW_FREEMUG_QTY", 1);
				}
				Mage::getSingleton("customer/session")->setData("FREEMUG_UPDATE", "FREEMUG");
				Mage::getSingleton("customer/session")->setData("REMOVE_FREEMUG", FALSE);
				return true;
			} 
		} else {
			if($oldCouponCode == "FREEMUG" && $couponCode != "FREEMUG") {
				$new_qty = $qty - 1;
				if($id && !strlen($couponCode) && $item_id && $qty || $new_qty) {					
					$cartData = array($item_id => array('qty' => $new_qty));
					$cartData = $cart->suggestItemsQty($cartData);
					$cart->updateItems($cartData)
						->save();
					/* if (!$this->_getSession()->getNoCartRedirect(true)) {
						if (!$cart->getQuote()->getHasError()) {
							if($new_qty){
								$message = $this->__('A Qty was reduced form the %s in your shopping cart.', Mage::helper('core')->escapeHtml($name));
								$this->_getSession()->addSuccess($message);
							} else {
								$message = $this->__('%s was removed in your shopping cart.', Mage::helper('core')->escapeHtml($name));
								$this->_getSession()->addSuccess($message);
							}
						}
					} */
				}
			}
		}
	}
	
	protected function _updateMyProductShoppingCart($before_cart)
    {
		$cart = $this->_getCart(); $cart->init();
		if(Mage::getSingleton("customer/session")->getData("FREEMUG_UPDATE") ) {
			if(Mage::getSingleton("customer/session")->getData("OLD_FREEMUG_QTY") || Mage::getSingleton("customer/session")->getData("NEW_FREEMUG_QTY")) {
				if(!Mage::getSingleton("customer/session")->getData("REMOVE_FREEMUG")) {
					foreach($cart->getQuote()->getAllVisibleItems() as $item) {
						if($item->getProduct()->getId() == 2312) {
							$id = $item->getProduct()->getId();
							$name = $item->getProduct()->getName();
							$item_id = $item->getId();
							$qty = $item->getQty();
							break;
						}
					}
					$new_qty = $qty - 1;
					$cartData = array($item_id => array('qty' => $new_qty));
					
					$cartData = $cart->suggestItemsQty($cartData);
					$cart->updateItems($cartData)
						->save();
					/* if (!$this->_getSession()->getNoCartRedirect(true)) {
						if (!$cart->getQuote()->getHasError()) {
							if($new_qty){
								$message = $this->__('A Qty was reduced form the %s in your shopping cart.', Mage::helper('core')->escapeHtml($name));
								$this->_getSession()->addSuccess($message);
							} else {
								$message = $this->__('%s was removed in your shopping cart.', Mage::helper('core')->escapeHtml($name));
								$this->_getSession()->addSuccess($message);
							}
						}
					} */ 
					Mage::getSingleton("customer/session")->setData("REMOVE_FREEMUG", TRUE);
				}
			}
		}
	}
}

?>