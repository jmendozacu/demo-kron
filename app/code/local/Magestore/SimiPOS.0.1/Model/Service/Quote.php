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
 * Simipos Quote Service Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
	/**
	 * Rewrite Validate Quote before create Order
	 * 
	 * @return Magestore_SimiPOS_Model_Service_Quote
	 */
	protected function _validate()
	{
        $helper = Mage::helper('sales');
	    if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
	    	$method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException($helper->__('Please specify a shipping method.'));
            }
        }
        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($helper->__('Please select a valid payment method.'));
        }
		return $this;
	}
}
