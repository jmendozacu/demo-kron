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
 * Use to call api with prefix: checkout_shipping
 * Methods:
 *  list
 *  setMethod
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Shipping
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    public function __construct() {
        $this->_ignoredAttributeCodes[] = 'address_id';
        $this->_ignoredAttributeCodes[] = 'created_at';
        $this->_ignoredAttributeCodes[] = 'updated_at';
        $this->_ignoredAttributeCodes[] = 'rate_id';
        $this->_ignoredAttributeCodes[] = 'carrier_sort_order';
    }
    
    /**
     * Retrieve list of shipping methods
     * 
     * @return array
     * @throws Exception
     */
    public function apiList()
    {
        $quote = $this->_getQuote();
        $quoteShippingAddress = $quote->getShippingAddress();
        if (!$quoteShippingAddress->getId()) {
            throw new Exception($this->_helper->__('Shipping address has not been set'), 61);
        }
        try {
            $quoteShippingAddress->collectShippingRates()->save();
            $groupedRates = $quoteShippingAddress->getGroupedAllShippingRates();
            
            $ratesResult = array();
            $shippingMethod = $quoteShippingAddress->getShippingMethod();
            $defaultMethod = Mage::getStoreConfig('simipos/checkout/default_shipping', $this->getStore());
            foreach ($groupedRates as $carrierCode => $rates ) {
                $carrierName = $carrierCode;
                if (!is_null(Mage::getStoreConfig('carriers/'.$carrierCode.'/title'))) {
                    $carrierName = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
                }
                foreach ($rates as $rate) {
                    if (!$shippingMethod && $defaultMethod == $carrierCode) {
                        $this->apiSetMethod($rate->getCode());
                        $shippingMethod = $rate->getCode();
                    }
                    $rateItem = $this->_getAttributes($rate);
                    $rateItem['carrierName'] = $carrierName;
                    $ratesResult[$rate->getId()] = $rateItem;
                }
            }
            if (!$shippingMethod) {
                foreach ($ratesResult as $rate) {
                    $this->apiSetMethod($rate['code']);
                    break;
                }
            }
            $ratesResult['total'] = count($ratesResult);
        } catch (Mage_Core_Exception $e) {
            throw new Exception($e->getMessage(), 64); // Cannot retrieve list of shipping method
        }
        return $ratesResult;
    }
    
    /**
     * Set shipping method
     * 
     * @param string $shippingMethod
     * @return boolean
     */
    public function apiSetMethod($shippingMethod)
    {
        $quote = $this->_getQuote();
        $quoteShippingAddress = $quote->getShippingAddress();
        if (!$quoteShippingAddress->getId()) {
            throw new Exception($this->_helper->__('Shipping address has not been set'), 61);
        }
        $rate = $quoteShippingAddress->collectShippingRates()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            throw new Exception($this->_helper->__('Shipping method is not available'), 62);
        }
        try {
            $quoteShippingAddress->setShippingMethod($shippingMethod);
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 63); // Shipping method is not set
        }
        return true;
    }
}
