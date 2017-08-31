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
 * SimiPOS Status Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Method_Payanywhere extends Mage_Payment_Model_Method_Abstract
{
	protected $_code  = 'payanywhere';
	
	protected $_canCapture = true;
	
	public function getConfigData($field, $storeId = null)
	{
		if ($field == 'active') {
			if (!Mage::getSingleton('simipos/session')->getSessionId()) {
				return false;
			}
		}
		return parent::getConfigData($field, $storeId);
	}
	
	public function getAppMerchantInfo()
	{
		return array(
		    'merchant_id' => ($decrypted = Mage::helper('core')->decrypt($this->getConfigData('merchant_id')))
		                   ? $decrypted : (string)$this->getConfigData('merchant_id'),
		    'login_id'    => ($decrypted = Mage::helper('core')->decrypt($this->getConfigData('login_id')))
                           ? $decrypted : (string)$this->getConfigData('login_id'),
            'user_name'   => (string)$this->getConfigData('user_name'),
            'password'    => ($decrypted = Mage::helper('core')->decrypt($this->getConfigData('password')))
                           ? $decrypted : (string)$this->getConfigData('password'),
		);
	}
}
