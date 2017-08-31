<?php

class Magestore_Madapter_CartController extends Mage_Core_Controller_Front_Action {

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    protected function _initProduct($productId, $storeId = 1) {
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

    public function _getHelper() {
        return Mage::helper('madapter');
    }

    public function addAction($infoProduct) {
        $cart = $this->_getCart();
        $params = $this->_getHelper()->getParams($infoProduct);
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct($infoProduct->product_id);

            if (!$product) {
                return false;
            }

            $cart->addProduct($product, $params);
            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);
            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
        } catch (Exception $e) {
            Mage::log($e);
        }
        return true;
    }

}
?>
