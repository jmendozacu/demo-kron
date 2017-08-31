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
 * SimiPOS Api Server Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api
{
    /**
     * @var Magestore_SimiPOS_Helper_Data
     */
    protected $_helper;
    
    public function __construct() {
        $this->_helper = Mage::helper('simipos');
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMIN, Mage_Core_Model_App_Area::PART_EVENTS);
    }
    
    /**
     * Retrieve web server session
     * 
     * @return Magestore_SimiPOS_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('simipos/session');
    }
    
    protected function _startSession($sessionId=null)
    {
        $this->_getSession()->setSessionId($sessionId);
        $this->_getSession()->init('simipos', 'api');
        return $this;
    }
    
    protected function _isAllowed($actionMethod)
    {
        return $this->_getSession()->isAllowed($actionMethod);
    }
    
    /**
     * Start session and retrieve session id
     * 
     * @return string
     */
    public function startSession()
    {
        $this->_startSession();
        return $this->_getSession()->getSessionId();
    }
    
    public function endSession($sessionId)
    {
        $this->_startSession($sessionId);
        $this->_getSession()->clear();
        return true;
    }
    
    /**
     * Run API Call
     * 
     * @param array $data
     * @return mixed
     */
    public function run($data)
    {
        if (empty($data['method'])) {
            throw new Exception($this->_helper->__('No method is specified'), 2);
        }
        $method = $data['method'];
        if ($method == 'login') {
            // Login is method that do not need session id
            if (empty($data['username']) || empty($data['password'])) {
                throw new Exception($this->_helper->__('Invalid username or password!'), 11);
            }
            return $this->login($data['username'], $data['password']);
        }
        // Check session id
        if (empty($data['session'])) {
            throw new Exception($this->_helper->__('Access Denied.'), 3);
        }
        $this->_startSession($data['session']);
        if (!$this->_getSession()->isLoggedIn($data['session'])) {
            throw new Exception($this->_helper->__('Session expired.'), 12);
        }
        // Check permission for method
        if (!$this->_isAllowed($method)) {
            throw new Exception($this->_helper->__('Access Denied.'), 3);
        }
        // Check param input
        $params = isset($data['params']) ? Mage::helper('core')->jsonDecode($data['params']) : array();
        if (!is_array($params)) {
            $params = array($params);
        }
        // Is current method of this model
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        // Is an API model
        list($resourceName, $methodName) = explode('.', $method);
        if (empty($resourceName) || empty($methodName)) {
            throw new Exception($this->_helper->__('Invalid method.'), 4);
        }
        $model = Mage::getModel('simipos/api_' . $resourceName);
        $methodName = 'api' . ucfirst($methodName);
        if (is_callable(array(&$model, $methodName))) {
            return call_user_func_array(array(&$model, $methodName), $params);
        }
        throw new Exception($this->_helper->__('API cannot be called.'), 5);
    }
    
/*******************************************************************************
 ******** WORKING WITH CURRENT USER METHODS - CALL DIRECTLY ********************
 ******************************************************************************/
    /**
     * login and retrieve session id
     * 
     * @param string $username
     * @param string $password
     * @return string
     */
    public function login($username, $password)
    {
        $this->_startSession();
        $this->_getSession()->login($username, $password);
        if (!$this->_getSession()->getCurrentStoreId()) {
            throw new Exception($this->_helper->__('Can not find a store for this user'), 13);
        }
        return $this->_getSession()->getSessionId();
    }
    
    public function info()
    {
        $user = $this->_getSession()->getUser();
        $result = $user->getData();
        $result['name'] = $user->getName();
        return $result;
    }
    
    public function update($userData)
    {
    	$userId = $this->_getSession()->getUser()->getId();
    	$user = Mage::getModel('simipos/user')->load($userId);
    	if (isset($userData['user_id'])) {
            unset($userData['user_id']);
        }
        $user->addData($userData);
        try {
            $errors = $user->validate();
            if ($errors !== true) {
                throw new Exception(implode("\n", $errors));
            }
            // setNewPassword
            $user->save();
            if ($this->_isRequestFromApp()) {
                Mage::helper('simipos/magestore')->updateUser($user);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 112);
        }
        $result = $user->getData();
        $result['name'] = $user->getName();
        return $result;
    }
    
    /**
     * Logout current session
     * 
     * @return boolean
     */
    public function logout()
    {
        $this->_getSession()->clear();
        return true;
    }
    
    /**
     * Retrieve all available store for current user
     * 
     * @return array
     */
    public function stores()
    {
        $stores = array();
        $storeIds = explode(',', $this->_getSession()->getUser()->getStoreIds());
        if (in_array(0, $storeIds)) {
            $storeIds = array_keys(Mage::app()->getStores());
        }
        foreach ($storeIds as $storeId) {
            $store = Mage::app()->getStore($storeId);
            if ($store->getId() != $storeId) {
                continue;
            }
            $stores[$store->getWebsiteId()]['name'] = $store->getWebsite()->getName();
            $stores[$store->getWebsiteId()][$store->getGroupId()]['name'] = $store->getGroup()->getName();
            $stores[$store->getWebsiteId()][$store->getGroupId()][$store->getId()] = $store->getName();
        }
        return $stores;
    }
    
    /**
     * Set current store
     * 
     * @param string $storeId
     * @return string
     */
    public function currentStore($storeId)
    {
        if ($storeId == $this->_getSession()->getCurrentStoreId()) {
            return $storeId;
        }
        $store = Mage::app()->getStore($storeId);
        if ($store->getId() != $storeId) {
            return $this->_getSession()->getCurrentStoreId();
        }
        $storeIds = explode(',', $this->_getSession()->getUser()->getStoreIds());
        if (!in_array($storeId, $storeIds)) {
            return $this->_getSession()->getCurrentStoreId();
        }
        $this->_getSession()->setCurrentStoreId($storeId);
        return $storeId;
    }
    
    /**
     * Get Store Information
     * 
     * @return array
     */
    public function storeInfo()
    {
    	$storeId = $this->_getSession()->getCurrentStoreId();
    	$store = Mage::app()->getStore($storeId);
    	$result = array(
    	   'name'  => $store->getFrontendName(),
    	   'phone' => Mage::getStoreConfig('general/store_information/phone', $store),
    	   'address'   => Mage::getStoreConfig('general/store_information/address', $store)
    	);
    	$logoSrc = Mage::getDesign()->setStore($store)->getSkinUrl(
    	   Mage::getStoreConfig('design/header/logo_src', $store)
    	   , array(
    	       '_area'     => 'frontend'
    	   )
    	);
    	$result['logo'] = $logoSrc;
    	
    	$printLogo = Mage::getStoreConfig('sales/identity/logo', $store);
    	if (!empty($printLogo)) {
    		$printLogo = Mage::getStoreConfig('web/unsecure/base_media_url', $store)
    		  . 'sales/store/logo/' . $printLogo;
    	} else {
    		$printLogo = Mage::getDesign()->getSkinUrl('images/logo_print.gif', array(
    		    '_area'     => 'frontend'
    		));
    	}
    	$result['print_logo'] = $printLogo;
    	
    	return $result;
    }
    
    protected function _isRequestFromApp()
    {
        return (bool)Mage::app()->getRequest()->getPost('app', false);
    }
}
