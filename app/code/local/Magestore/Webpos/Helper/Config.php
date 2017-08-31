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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpos Helper Config
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Daniel Magestore Developer
 */
class Magestore_Webpos_Helper_Config extends Mage_Core_Helper_Abstract {
	
	const ENABLE_DELIVERY_DATE_PATH = "webpos/general/enable_delivery_date";
	
	/*
	* return (int)Store Id
	*/
	public function getCurrentStoreId(){
		return Mage::app()->getStore()->getId();
	}	
	
	/*
	* Input: configuration path
	* return store configuration
	*/
	public function getStoreConfig($path){
		$storeId = $this->getCurrentStoreId();
		return Mage::getStoreConfig($path,$storeId);
	}	
	
	/*
	* function to get config enable/disable delivery date
	* return boolean
	*/
	public function isEnableDeliveryDate(){
		return $this->getStoreConfig(self::ENABLE_DELIVERY_DATE_PATH);
	}
	
}