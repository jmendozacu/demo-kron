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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Observer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Madapter_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }
    
    public function paymentMethodIsActive($observer){
        $result = $observer['result'];
        $method = $observer['method_instance'];               
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'zooz' || $method->getCode() == 'paypal_mobile'
                || $method->getCode() == 'transfer_mobile'){
            if (Mage::app()->getRequest()->getControllerModule() != 'Magestore_Madapter'){
                $result->isAvailable = false;
            }            
        }
    }    
}