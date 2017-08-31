<?php
class MW_FreeGift_CheckoutController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
	protected function _canProcessRule($rule, $address)
	{
		//multi coupon
		if(!$rule->getData('is_active')){
			return false;
		}
		if($rule->getData('discount_qty') && ($rule->getData('discount_qty') <= $rule->getData('times_used'))){
			return false;
		}
		if (!$rule->hasIsValid()) {
			$rule->afterLoad();
			if (!$rule->validate($address)) {
				$rule->setIsValid(false);
				return false;
			}
			$rule->setIsValid(true);
		}
		return $rule->getIsValid();
		
	}
	
	protected function _initProduct($productId)
	{
		if ($productId) {
			$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
			if ($product->getId()) {
				return $product;
			}
		}
		return false;
	}
	
	protected function _flagRule($ruleId)
		{	
		$flagRule = Mage::getSingleton('core/session')->getFlagRule();
		$ruleValue = "";
		
		if($flagRule != ""){
			$ruleValue = $flagRule.",".$ruleId;			
		}else{
			$ruleValue = $ruleId;
		}	
			
		return Mage::getSingleton('core/session')->setFlagRule($ruleValue);
	}
	
	protected function _flagCoupon($rule,$coupon)
	{			
		$flagCoupon = Mage::getSingleton('core/session')->getFlagCoupon();
				
		if($flagCoupon != null && is_array($flagCoupon)){
			$flagCoupon[$rule] = $coupon;
		}else{
			$flagCoupon = array();	
			$flagCoupon[$rule] = $coupon;
		}	
		return Mage::getSingleton('core/session')->setFlagCoupon($flagCoupon);
	}
	
	// integrate free gift code with discount code
	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }
            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
            
        } elseif(!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) 
        {	
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
	
	public function freegiftdiscountPostAction()
    {		 
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

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
					$this->couponPostFreeGift($couponCode);
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
	protected function couponPostFreeGift($couponCode)
    {        
		if ($couponCode) {
			//multi coupons
			$coupon = Mage::getModel('freegift/coupon')->load($couponCode, 'code');
			if ($coupon->getRuleId()) {
					$ruleId = $coupon->getRuleId();
					$rule   = Mage::getModel('freegift/salesrule')->load($ruleId, 'rule_id');
				}else {
					$rule 	= Mage::getModel('freegift/salesrule')->load($couponCode, 'coupon_code');
			}							
			if ($rule->getId()) {
				$now = Mage::getModel('core/date')->date('Y-m-d');
				if ((!$rule->getFromDate() || $now >= $rule->getFromDate()) && (!$rule->getToDate() || $now <= $rule->getToDate())) {				
					$quote   = $this->_getSession()->getQuote();
					$address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();					
					if ($this->_canProcessRule($rule, $address)) {										
						$appliedCode = unserialize($quote->getFreegiftCouponCode());
						if (!is_array($appliedCode) || !in_array($couponCode, $appliedCode)) {					
							$appliedCode[] = $couponCode;
							$quote->setFreegiftCouponCode(serialize($appliedCode))->setTotalsCollectedFlag(false)->collectTotals()->save();
							$valid_code = true;					
							/*Mage::getSingleton('core/session')->unsFlagRule();
							Mage::getSingleton('core/session')->unsFlagCoupon();*/
							/* Automatically add free product*/
							$productIds = explode(",", $rule->getData('gift_product_ids'));
							$cart       = $this->_getCart();
							if ($rule->getNumberOfFreeGift() == count($productIds)) {									
								foreach ($productIds as $productId) {
									$product = $this->_initProduct($productId);										
									$product->addCustomOption('freegift_with_code', 1);
									$product->setPrice(0);
									$request = array(
										'product' => $product->getId(),
										'qty' => 1,
										'freegift_with_code' => 1,
										'freegift_coupon_code' => $couponCode,										
										'rule_id' => $rule->getId()
									);
									$isRequire = false;		
									foreach ($product->getOptions() as $o) {
										if($o->getIsRequire()) $isRequire = true;			
									}
									if(($product->getTypeId()=='simple') && !$product->getTypeInstance(true)->hasRequiredOptions($product) && !$isRequire){																
										$cart->addProduct($product, $request);
										$cart->save();										
										Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
										
										$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart', $product->getName()));
										$id = $rule->getId();                                   
										$this->_flagRule($id);
										$this->_flagCoupon($id,$couponCode);
									}else{
										$id = $rule->getId();                                   
										$this->_flagRule($id);
										$this->_flagCoupon($id,$couponCode);
									}
								}
							}else {									
									$id = $rule->getId();                                   
									$this->_flagRule($id);
									$this->_flagCoupon($id,$couponCode);																
								}													
						} else {
							$valid_code = false;
						}
					} else {
						$valid_code = false;
					}
				} else {
					$valid_code = false;
				}
			} else {
				$valid_code = false;
			}
		} else {
			$valid_code = false;
		}
		if ($valid_code) {
				$this->_getSession()->addSuccess($this->__('Free gift code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode)));
			} else {			
				$this->_getSession()->addError($this->__('Free gift code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
		}
		
		$this->_redirect('checkout/cart/index');
    }
	
	
	public function couponPostAction()
	{
		$couponCode = (string) $this->getRequest()->getParam('freegift_coupon_code');
		if ($couponCode) {
			//multi coupons
			$coupon = Mage::getModel('freegift/coupon')->load($couponCode, 'code');
			if ($coupon->getRuleId()) {
				$ruleId = $coupon->getRuleId();
				$rule   = Mage::getModel('freegift/salesrule')->load($ruleId, 'rule_id');
			} else {
				$rule 	= Mage::getModel('freegift/salesrule')->load($couponCode, 'coupon_code');
			}

			if ($rule->getId()) {
				$now = Mage::getModel('core/date')->date('Y-m-d');
				if ((!$rule->getFromDate() || $now >= $rule->getFromDate()) && (!$rule->getToDate() || $now <= $rule->getToDate())) {
					$quote   = $this->_getSession()->getQuote();
					$address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
					if ($this->_canProcessRule($rule, $address)) {
						$appliedCode = unserialize($quote->getFreegiftCouponCode());

						if (!is_array($appliedCode) || !in_array($couponCode, $appliedCode)) {
                            /* This mean: customer using old coupon has entered before */
							$appliedCode[] = $couponCode;
							$quote->setFreegiftCouponCode(serialize($appliedCode))->setTotalsCollectedFlag(false)->collectTotals()->save();
							$valid_code = true;									
							/*Mage::getSingleton('core/session')->unsFlagRule();
							Mage::getSingleton('core/session')->unsFlagCoupon();*/
							/* Automatically add free product*/
							$productIds = explode(",", $rule->getData('gift_product_ids'));
							$cart       = $this->_getCart();
							if ($rule->getNumberOfFreeGift() == count($productIds)) {
								foreach ($productIds as $productId) {
									$product = $this->_initProduct($productId);										
									$product->addCustomOption('freegift_with_code', 1);
									$product->setPrice(0);
									$request = array(
										'product' => $product->getId(),
										'qty' => 1,
										'freegift_with_code' => 1,
										'freegift_coupon_code' => $couponCode,										
										'rule_id' => $rule->getId(),
                                        'text_gift' => array(
                                            'label' => Mage::helper('freegift')->__('Free Gift with coupon'),
                                            'value' => $rule->getName()
                                        )
									);
									$isRequire = false;		
									foreach ($product->getOptions() as $o) {
										if($o->getIsRequire()) $isRequire = true;			
									}
									if(($product->getTypeId()=='simple') && !$product->getTypeInstance(true)->hasRequiredOptions($product) && !$isRequire){																
										$cart->addProduct($product, $request);
										$cart->save();										
										Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
										
										$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart', $product->getName()));
										$id = $rule->getId();                                   
										$this->_flagRule($id);
										$this->_flagCoupon($id,$couponCode);
									}else{
										$id = $rule->getId();                                   
										$this->_flagRule($id);
										$this->_flagCoupon($id,$couponCode);
									}
								}
							} else {
								$id = $rule->getId();
								$this->_flagRule($id);
								$this->_flagCoupon($id,$couponCode);																
							}													
						} else {
							$valid_code = false;
						}
					} else {
						$valid_code = false;
					}
				} else {
					$valid_code = false;
				}

			} else {
				$valid_code = false;
			}
		} else {
			$valid_code = false;
		}
		if ($valid_code) {
			$this->_getSession()->addSuccess($this->__('Free gift code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode)));
			} else {			
			$this->_getSession()->addError($this->__('Free gift code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
		}
		
		$this->_redirect('checkout/cart/index');
	}
}