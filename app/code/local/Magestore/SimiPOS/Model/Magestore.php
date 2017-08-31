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
 * SimiPOS Magestore Server Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Magestore extends Varien_Object
{
    const MAGESTORE_URL = 'https://www.magestore.com/simipos/'; // api/xmlrpc';
    // DEVELOPMENT
//     const MAGESTORE_URL = 'http://dev.magestore.com/simipos/index.php/';
    
    protected $_apiSession;
    
    protected function getAccountConfig($field)
    {
        return Mage::getStoreConfig('simipos/account/' . $field);
    }
    
    public function __destruct()
    {
    	if ($this->_apiSession) {
    		$client = new Zend_XmlRpc_Client($this->getApiURL());
    		$client->call('endSession', array($this->_apiSession));
    	}
    }
    
    public function getApiURL()
    {
    	return self::MAGESTORE_URL . 'api/xmlrpc';
    }
    
    public function getAuthURL()
    {
    	return self::MAGESTORE_URL . 'simiposmanagement/authentication/check';
    }
    
    /**
     * Account URLs
     */
    public function getCreateAccountURL()
    {
    	return self::MAGESTORE_URL . 'customer/account/create';
    }
    
    public function getForgotPasswordURL()
    {
    	return self::MAGESTORE_URL . 'customer/account/forgotpassword';
    }
    
    public function getDetailTermURL()
    {
    	return self::MAGESTORE_URL . 'simiposmanagement/index/edit/id/' . $this->getAccountConfig('term_id');
    }
    
    /**
     * Auth User on Magestore.com
     */
    public function login($username, $password)
    {
    	$client = new Varien_Http_Client($this->getAuthURL());
    	$client->setParameterPost('username', $username);
    	$client->setParameterPost('password', $password);
    	
    	$response = $client->request(Zend_Http_Client::POST);
    	if ($response instanceof Zend_Http_Response && $response->isSuccessful()) {
    		$result = Zend_Json::decode($response->getBody());
    		if (empty($result)) {
    			throw new Exception(Mage::helper('simipos')->__('Invalidate response.'));
    		}
    		$result = new Varien_Object($result);
    		if ($result->getError()) {
    		  if ($result->getMessage()) {
    		      throw new Exception($result->getMessage());
    		  } else {
    		  	  throw new Exception(Mage::helper('simipos')->__('Invalidate response.'));
    		  }
    		}
    		return $result;
    	} else {
    		throw new Exception(Mage::helper('simipos')->__('Invalidate response.'));
    	}
    }
    
    /**
     * API Action Methods
     */
    public function updatePackage()
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
        	$this->_apiSession = $client->call('login', array(
        	    $this->getAccountConfig('username'),
        	    $this->getAccountConfig('api_key')
        	));
        }
        // Update package
        $packageId = $this->getAccountConfig('term_id');
        $domaintype = $this->getAccountConfig('mode') ? 2 : 1;
        $domain = Mage::helper('simipos/magestore')->getStoreUrl();
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.updatepackage',
            array(
                $packageId,
                $domaintype,
                $domain
            )
        ));
        if (!$result) {
            throw new Exception(Mage::helper('simipos')->__('Package is not found.'));
        }
    }
    
    public function packageInfo()
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.getpackageinfo',
            array(
                $this->getAccountConfig('term_id')
            )
        ));
        if (!$result) {
            throw new Exception(Mage::helper('simipos')->__('Package is not found.'));
        }
        return $result;
    }
    
    public function accountList($email = null)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        // Get list package
        $filter = array(
            'term_id'   => $this->getAccountConfig('term_id')
        );
        if ($email) {
        	$filter['email'] = $email;
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.list',
            array($filter)
        ));
        return $result;
    }
    
    public function accountInfo($accountId)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.info',
            array($accountId)
        ));
        return $result;
    }
    
    public function accountCreate($accountData)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $accountData['term_id'] = $this->getAccountConfig('term_id');
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.create',
            array($accountData, $this->_getSiteType())
        ));
        return $result;
    }
    
    public function accountUpdate($accountId, $accountData)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.update',
            array($accountId, $accountData, $this->_getSiteType())
        ));
        return $result;
    }
    
    public function accountUpdateBlock($accountList)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.updateblock',
            array($accountList, $this->_getSiteType())
        ));
        return $result;
    }
    
    public function updateCustomer($email, $password)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.updatecustomer',
            array($email, $password, $this->_getSiteType())
        ));
        return $result;
    }
    
    public function accountDelete($accountId)
    {
        $client = new Zend_XmlRpc_Client($this->getApiURL());
        if (empty($this->_apiSession)) {
            $this->_apiSession = $client->call('login', array(
                $this->getAccountConfig('username'),
                $this->getAccountConfig('api_key')
            ));
        }
        $result = $client->call('call', array(
            $this->_apiSession,
            'subaccount.delete',
            array($accountId, $this->_getSiteType())
        ));
        return $result;
    }
    
    protected function _getSiteType()
    {
    	if ($this->getAccountConfig('mode')) {
    		return 2;
    	}
    	return 1;
    }
}
