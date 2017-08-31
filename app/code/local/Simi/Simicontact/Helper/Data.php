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
 * @package 	Magestore_Simicontact
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simicontact Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @author  	Magestore Developer
 */
class Simi_Simicontact_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getContacts(){	
		return array(
			'email' => $this->getConfig("email"),
			'phone' => $this->getConfig("phone"),
			'website' => $this->getConfig("website"),			
		);		
	}
	
	public function getConfig($value){
		return Mage::getStoreConfig("simicontact/general".$value);
	}
}