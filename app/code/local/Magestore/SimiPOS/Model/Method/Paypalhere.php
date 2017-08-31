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
class Magestore_SimiPOS_Model_Method_Paypalhere extends Mage_Payment_Model_Method_Abstract
{
	protected $_code  = 'paypalhere';
	
	protected $_infoBlockType = 'simipos/method_paypalhere';
	
	protected $_merchantApiNVPUrls = array(
	   'live'    => 'https://api-3t.paypal.com/nvp',
	   'sandbox' => 'https://api-3t.sandbox.paypal.com/nvp',
    );
    
    public function getMerchantApiUrl()
    {
    	$mode = $this->getConfigData('sandbox_flag') ? 'sandbox' : 'live';
    	return $this->_merchantApiNVPUrls[$mode];
    }
    
    /**
     * Retreive transaction data from Paypal server
     * 
     * @param $transactionId
     * @return array
     */
    public function getTransaction($transactionId)
    {
    	$client = new Varien_Http_Client($this->getMerchantApiUrl());
    	$params = array(
    	   'METHOD'    => 'GetTransactionDetails',
    	   'VERSION'   => '62.5',
    	   'USER'      => $this->getConfigData('api_username'),
    	   'PWD'       => $this->getConfigData('api_password'),
    	   'SIGNATURE' => $this->getConfigData('api_signature'),
    	   'TRANSACTIONID' => $transactionId
    	);
    	try {
    		$client->setRawData(http_build_query($params));
    		$response = $client->request(Zend_Http_Client::POST);
    		if ($response instanceof Zend_Http_Response && $response->isSuccessful()) {
    			$result = array();
    			parse_str($response->getBody(), $result);
    			if (isset($result['ACK']) && $result['ACK'] == 'Success') {
    				return $result;
    			}
    		}
    	} catch (Exception $e) {}
    	return array();
    }
	
	/**
	 * Rewrite config data
	 * 
	 * @param string $field
	 * @param mixed $storeId
	 * @return mixed
	 */
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
		  'business_account'  => $this->getConfigData('business_account'),
//		  'api_username'      => $this->getConfigData('api_username'),
//		  'api_password'      => $this->getConfigData('api_password'),
//		  'api_signature'     => $this->getConfigData('api_signature'),
//		  'sandbox_flag'      => $this->getConfigData('sandbox_flag'),
		  'accepted_method'   => $this->getConfigData('accepted_method'),
		  'line_items_enabled'=> $this->getConfigData('line_items_enabled'),
		);
	}
	
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Magestore_SimiPOS_Model_Method_Paypalhere
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $this->getInfoInstance()->setPoNumber($data->getPurchasedOrder());
        return $this;
    }
    
    /**
     * validate payment method info
     */
    public function validate()
    {
    	parent::validate();
    	$transactionInfo = $this->getInfoInstance()->getPoNumber();
    	if (!$transactionInfo) {
    		Mage::throwException(Mage::helper('simipos')->__('Invalid PaypalHere Transaction Info.'));
    	}
    	return $this;
    }
}
