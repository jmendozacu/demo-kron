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
 * Rewrite Free Shipping
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Carrier_Freeshipping extends Mage_Shipping_Model_Carrier_Freeshipping
{
    /**
     * Retrieve config flag for store by field
     *
     * @param string $field
     * @return bool
     */
    public function getConfigFlag($field)
    {
        if ($field == 'active' && Mage::getStoreConfigFlag('simipos/checkout/freeshipping', $this->getStore())) {
            return true;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfigFlag($path, $this->getStore());
    }
    
    /**
     * FreeShipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $result = Mage::getModel('shipping/rate_result');
        $this->_updateFreeMethodQuote($request);
        
        if (($request->getFreeShipping())
            || ($request->getBaseSubtotalInclTax() >= $this->getConfigData('free_shipping_subtotal'))
            || (Mage::getSingleton('simipos/session')->getSessionId()
                && Mage::getStoreConfig('simipos/checkout/freeshipping', $this->getStore())
            )
        ) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }
}
