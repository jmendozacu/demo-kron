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
 * SimiPOS Shipping Methods Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Source_Payment
{
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->getPaymentMethods() as $method) {
        	$options[] = array(
        	    'value'    => $method->getCode(),
        	    'label'    => $method->getTitle()
        	);
        }
        return $options;
    }
    
    public function getPaymentMethods()
    {
    	$methods = Mage::helper('payment')->getPaymentMethods();
    	$result  = array();
    	foreach ($methods as $code => $methodConfig) {
    		if ($code == 'cashin' || $code == 'payanywhere') {
    			continue;
    		}
    		$prefix = Mage_Payment_Helper_Data::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
    	    if (!$model = Mage::getStoreConfig($prefix . 'model')) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance) {
                continue;
            }
            if (!$this->_canUsePaymentMethod($methodInstance)) {
            	continue;
            }
            $result[] = $methodInstance;
    	}
    	return $result;
    }
    
    protected function _canUsePaymentMethod($method)
    {
    	if (!($method->isGateway() || $method->canUseInternal())) {
    		return false;
    	}
    	if (!$method->getTitle()) {
    		return false;
    	}
    	return true;
    }
}
