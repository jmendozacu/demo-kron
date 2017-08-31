<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Madapter_Model_Cart extends Mage_Core_Model_Abstract {

    protected function _getProduct($productInfo) {
        $product = null;
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productInfo);
        }
        $currentWebsiteId = Mage::app()->getStore()->getWebsiteId();
        if (!$product
                || !$product->getId()
                || !is_array($product->getWebsiteIds())
                || !in_array($currentWebsiteId, $product->getWebsiteIds())
        ) {
            Mage::throwException(Mage::helper('checkout')->__('The product could not be found.'));
        }
        return $product;
    }

    public function addProduct($productInfo, $requestInfo = null) {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        $productId = $product->getId();

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                    && !$this->_getCart()->getQuote()->hasProductId($productId)
            ) {
                $request->setQty($minimumQty);
            }
        }

        if ($productId) {
            try {
                $result = $this->_getCart()->getQuote()->addProduct($product, $request);
            } catch (Mage_Core_Exception $e) {
                $this->_getCheckoutSession()->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                $redirectUrl = ($product->hasOptionsValidationFail()) ? $product->getUrlModel()->getUrl(
                                $product, array('_query' => array('startcustomization' => 1))
                        ) : $product->getProductUrl();
                $this->_getCheckoutSession()->setRedirectUrl($redirectUrl);
                if ($this->_getCheckoutSession()->getUseNotice() === null) {
                    $this->_getCheckoutSession()->setUseNotice(true);
                }
                Mage::throwException($result);
            }
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }

        Mage::dispatchEvent('checkout_cart_product_add_after', array('quote_item' => $result, 'product' => $product));
        $this->_getCart()->getCheckoutSession()->setLastAddedProductId($productId);
        return $result;
    }

    protected function _initProduct($productId, $storeId = null) {
        $storeId = $storeId == null ? Mage::app()->getStore()->getId() : $storeId;
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId($storeId)
                    ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function _getHelper() {
        return Mage::helper('madapter');
    }

    protected function _getProductRequest($requestInfo) {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }

    public function addCart($x = 0) {
        //Zend_debug::dump(Mage::app()->getRequest());die();
        $data = $this->getData('info');
        if ($x == 0) {
            $item = json_decode($data['cart_item']);
            //foreach ($items as $item) {
            if (!$this->addProductToCart($item)) {
                return false;
            }
            // }
        } else {
            $items = json_decode($data['order_items']);
            foreach ($items as $item) {
                if (!$this->addProductNoCart($item)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function addProductNoCart($infoProduct) {
        $product = $this->_initProduct($infoProduct->product_id);
        if (!$product) {
            return false;
        }
        $params = $this->_getHelper()->getParams($infoProduct, $product->getTypeId());
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
                $request = $this->_getProductRequest($params);

                if ($product->getStockItem()) {
                    $minimumQty = $product->getStockItem()->getMinSaleQty();
                    //If product was not found in cart and there is set minimal qty for it
                    if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                            && !$this->getQuote()->hasProductId($product->getId())
                    ) {
                        $request->setQty($minimumQty);
                    }
                }
                $old_quote = null;
                if (Mage::getSingleton('checkout/session')->getQuote()->getId()) {
                    $old_quote = Mage::getSingleton('checkout/session')->getQuote();
                    Mage::getSingleton('checkout/session')->clear();
                }
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $quote->addProduct($product, $request);
                $quote->save();
                Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
                if ($old_quote)
                    Mage::getSingleton('checkout/session')->setData('old_quote', $old_quote);
            }
        } catch (Exception $e) {
            Mage::log($e);
            return false;
        }
        return true;
    }

    public function indexCart() {
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();
        }
        $this->_getCheckoutSession()->setCartWasUpdated(true);
    }

    public function addProductToCart($infoProduct) {
        $cart = $this->_getCart();

        $product = $this->_initProduct($infoProduct->product_id);
        if (!$product) {
            return false;
        }

        $params = $this->_getHelper()->getParams($infoProduct, $product->getTypeId());
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }


            $itemCurrent = $this->addProduct($product, $params);
            $cart->save();
            $this->_getCheckoutSession()->setCartWasUpdated(true);            
          
            if ($itemCurrent->getParentItem()) {
                $itemCurrent = $itemCurrent->getParentItem();
            }
            Mage::getSingleton('core/session')->setItemIdMobile(null);
            $items = $this->_getCart()->getQuote()->getAllItems();
            
            foreach ($items as $item) {                
                if (($item->getProductId() == $product->getId()) && ($item->getId() != $itemCurrent->getId())) {                        
                    if ($item->compare($itemCurrent)) {                        
                        Mage::getSingleton('core/session')->setItemIdMobile($item->getId());                        
                        break;
                    }
                }
            }            
            if (!Mage::getSingleton('core/session')->getItemIdMobile()) {
                Mage::getSingleton('core/session')->setItemIdMobile($itemCurrent->getId());
            }

            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse())
            );
        } catch (Exception $e) {
            Mage::log($e);
            return false;
        }
        return true;
    }

    public function updateCart() {
        $result = false;
        try {
            $data = $this->getData('info');
            $items = json_decode($data['cart_items']);
            $cartData = array();
            foreach ($items as $item) {
                $cartData[$item->cart_item_id] = array('qty' => $item->product_qty);
            }
            if (count($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                        ->save();
                $result = true;
            }
            $this->_getCheckoutSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $result = false;
        } catch (Exception $e) {
            $result = false;
            Mage::logException($e);
        }
        return $result;
    }

    public function setCouponCode($couponCode) {
        $oldCouponCode = $this->_getCart()->getQuote()->getCouponCode();
        $return = array();
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return $return;
        }
        try {
            $this->_getCart()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getCart()->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();
            if (strlen($couponCode)) {
                if ($couponCode == $this->_getCart()->getQuote()->getCouponCode()) {
                    $total = $this->_getCart()->getQuote()->getTotals();
                    $return['discount'] = abs($total['discount']->getValue());
                    $return['grand_total'] = $this->_getCart()->getQuote()->getGrandTotal();
                } 
            } else {
                //$this->_getCheckoutSession()->addSuccess($this->__('Coupon code was canceled.'));
                //canceled;
            }
        } catch (Mage_Core_Exception $e) {
            
        } catch (Exception $e) {
            Mage::logException($e);
        }
        
        return $return;
    }

}
