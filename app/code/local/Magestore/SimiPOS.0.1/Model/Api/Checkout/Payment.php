<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Checkout Product API Model
 * Use to call api with prefix: checkout_payment
 * Methods:
 *  list
 *  setMethod
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Payment
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    /**
     * Retrieve list of payment methods
     * 
     * @return array
     */
    public function apiList()
    {
        $quote = $this->_getQuote();
        
        $total = $quote->getBaseSubtotal();
        $methodsResult = array();
        $methods = Mage::helper('payment')->getStoreMethods($quote->getStoreId(), $quote);
        foreach ($methods as $key => $method) {
            if ($this->_canUsePaymentMethod($method, $quote)
                && ($total != 0
                    || $method->getCode() == 'free'
                    || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles())
            )) {
                $methodsResult[$method->getCode()] = array(
                    'code'  => $method->getCode(),
                    'title' => $method->getTitle(),
                    'ccTypes'   => $this->_getPaymentMethodAvailableCcTypes($method),
                );
            }
        }
        $methodsResult['total'] = count($methodsResult);
        return $methodsResult;;
    }
    
    /**
     * set payment mothod for shopping cart
     * 
     * @param array $paymentData
     * @return boolean
     * @throws Exception
     */
    public function apiSetMethod($paymentData)
    {
        $quote = $this->_getQuote();
        
        $paymentMethod = isset($paymentData['method']) ? $paymentData['method'] : null;
        if ($quote->isVirtual()) {
            if (!$quote->getBillingAddress()->getId()) {
                throw new Exception($this->_helper->__('Billing address has not been set'), 71);
            }
            $quote->getBillingAddress()->setPaymentMethod($paymentMethod);
        } else {
            if (!$quote->getShippingAddress()->getId()) {
                throw new Exception($this->_helper->__('Shipping address has not been set'), 72);
            }
            $quote->getShippingAddress()->setPaymentMethod($paymentMethod);
        }
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        $total = $quote->getBaseSubtotal();
        $methods = Mage::helper('payment')->getStoreMethods($quote->getStoreId(), $quote);
        
        foreach ($methods as $key => $method) {
            if ($method->getCode() == $paymentMethod) {
                if (!$this->_canUsePaymentMethod($method, $quote)
                    || ($total == 0
                        && $method->getCode() != 'free'
                        && (!$quote->hasRecurringItems() || !$method->canManageRecurringProfiles())
                )) {
                    throw new Exception($this->_helper->__('Method not allowed'), 73);
                }
            }
        }
        try {
            $payment = $quote->getPayment();
            $payment->importData($paymentData);
            
            $quote->setTotalsCollectedFlag(false)
                ->collectTotals()
                ->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 74); // payment method is not set
        }
        return true;
    }
    
    /**
     * Check payment method can used for quote or not
     * 
     * @param type $method
     * @param type $quote
     * @return boolean
     */
    protected function _canUsePaymentMethod($method, $quote)
    {
        if (!($method->isGateway() || $method->canUseInternal()) ) {
            return false;
        }
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }
        if (!$method->canUseForCurrency(Mage::app()->getStore($quote->getStoreId())->getBaseCurrencyCode())) {
            return false;
        }
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');
        if ((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
    
    /**
     * Retrieve all cctypes for current method
     * 
     * @param type $method
     * @return array
     */
    protected function _getPaymentMethodAvailableCcTypes($method)
    {
        $ccTypes = Mage::getSingleton('payment/config')->getCcTypes();
        $methodCcTypes = explode(',', $method->getConfigData('cctypes'));
        foreach ($ccTypes as $code => $title) {
            if (!in_array($code, $methodCcTypes)) {
                unset($ccTypes[$code]);
            }
        }
        if (empty($ccTypes)) {
            return null;
        }
        return $ccTypes;
    }
}
