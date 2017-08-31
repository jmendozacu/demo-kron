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
 * Simipos User Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_User extends Mage_Core_Model_Abstract
{
    const MIN_PASSWORD_LENGTH   = 7;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('simipos/user');
    }
    
    public function getName($separator=' ')
    {
        return $this->getFirstName() . $separator . $this->getLastName();
    }
    
    /**
     * Prepare data after load (store_ids)
     * 
     * @return Magestore_SimiPOS_Model_User
     */
    protected function _afterLoad()
    {
    	if ($this->getData('role_permission')
    	   && is_string($this->getData('role_permission'))
    	   && $this->getData('role_permission') != 'Array'
    	) {
    		$this->setData('role_permission', Zend_Json::decode($this->getData('role_permission')));
    	} else if (!is_array($this->getData('role_permission'))) {
    		$this->setData('role_permission', array());
    	}
        $this->setData('stores', explode(',', $this->getData('store_ids')));
        return parent::_afterLoad();
    }
    
    protected function _beforeSave()
    {
    	if ($this->getData('role_permission') && is_array($this->getData('role_permission'))) {
    		$this->setData('role_permission', Zend_Json::encode($this->getData('role_permission')));
    	}
    	if ($this->getOrigData('email')
    	   && $this->getOrigData('email') == Mage::getStoreConfig('simipos/account/username')
    	) {
    		$this->setData('email', $this->getOrigData('email'));
    	}
    	$this->setUsername($this->getEmail());
    	if ($this->getCreatedTime() == NULL) {
    		$this->setCreatedTime(now());
    	}
    	if ($this->getPasswordHash()) {
    		$this->setData('password', $this->getPasswordHash())
    		    ->setOrigData('password', $this->getPasswordHash());
    	} elseif ($this->getNewPassword()) { // Change password
            $this->setPassword($this->_getEncodedPassword($this->getNewPassword()));
        } elseif ($this->getPassword() && $this->getPassword() != $this->getOrigData('password')) { // new user password
            $this->setPassword($this->_getEncodedPassword($this->getPassword()));
        }
        return parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        if ($this->getData('role_permission')
           && is_string($this->getData('role_permission'))
           && $this->getData('role_permission') != 'Array'
        ) {
            $this->setData('role_permission', Zend_Json::decode($this->getData('role_permission')));
        } else if (!is_array($this->getData('role_permission'))) {
            $this->setData('role_permission', array());
        }
    	return parent::_afterSave();
    }
    
    protected function _beforeDelete()
    {
    	if ($this->getData('email')
    	   && $this->getData('email') == Mage::getStoreConfig('simipos/account/username')
    	) {
    		throw new Exception(Mage::helper('simipos')->__('Cannot delete this account. Please disconnect from SimiPOS server and try again.'));
    	}
    	return parent::_beforeDelete();
    }
    
    public function getCurrentStoreId()
    {
        if (!$this->hasData('current_store_id')) {
            $stores = explode(',', $this->getData('store_ids'));
            if (in_array(0, $stores)) {
                $stores = array_keys(Mage::app()->getStores());
            }
            $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
            if (in_array($defaultStoreId, $stores)) {
                $this->setData('current_store_id', $defaultStoreId);
            } else {
                $this->setData('current_store_id', 0);
                foreach ($stores as $storeId) {
                    $store = Mage::app()->getStore($storeId);
                    if ($storeId == $store->getId()) {
                        $this->setData('current_store_id', $storeId);
                        break;
                    }
                }
            }
        }
        return $this->getData('current_store_id');
    }
    
    public function validate()
    {
        $errors = array();
//        if (!Zend_Validate::is($this->getUsername(), 'NotEmpty')) {
//            $errors[] = Mage::helper('adminhtml')->__('User Name is required field.');
//        }
        if (!Zend_Validate::is($this->getFirstName(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('First Name is required field.');
        }
        if (!Zend_Validate::is($this->getLastName(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('Last Name is required field.');
        }
        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('adminhtml')->__('Please enter a valid email address.');
        }
        if ($this->hasNewPassword()) {
            if (Mage::helper('core/string')->strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
                $errors[] = Mage::helper('adminhtml')->__('Password must be at least %d characters.', self::MIN_PASSWORD_LENGTH);
            }
            if (!preg_match('/[a-z]/iu', $this->getNewPassword()) || !preg_match('/[0-9]/u', $this->getNewPassword())) {
                $errors[] = Mage::helper('adminhtml')->__('Password must include both numeric and alphabetic characters.');
            }
            if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
                $errors[] = Mage::helper('adminhtml')->__('The password and the confirmation password must match.');
            }
        }
        if ($this->userExists()) {
            $errors[] = Mage::helper('adminhtml')->__('User with the same Email Address aleady exists.');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
    
    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }
    
    protected function _getEncodedPassword($pwd)
    {
        return Mage::helper('core')->getHash($pwd, 2);
    }
    
    /**
     * Authenticate user to login system
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        $this->loadByUsername($username);
        if (!$this->getId()) {
            return false;
        }
        if (Mage::helper('core')->validateHash($password, $this->getPassword())) {
            return true;
        }
        $this->unsetData();
        return false;
    }
    
    public function login($username, $password)
    {
        $sessId = $this->getSessid();
        if ($this->authenticate($username, $password)) {
            $this->setSessid($sessId);
            $this->getResource()->cleanOldSessions($this)
                ->recordSession($this);
        }
        return $this;
    }
    
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        if ($this->getData('role_permission')
           && is_string($this->getData('role_permission'))
           && $this->getData('role_permission') != 'Array'
        ) {
            $this->setData('role_permission', Zend_Json::decode($this->getData('role_permission')));
        } else if (!is_array($this->getData('role_permission'))) {
            $this->setData('role_permission', array());
        }
        return $this;
    }
    
    public function loadBySessId($sessId)
    {
        $this->setData($this->getResource()->loadBySessId($sessId));
        if ($this->getData('role_permission')
           && is_string($this->getData('role_permission'))
           && $this->getData('role_permission') != 'Array'
        ) {
            $this->setData('role_permission', Zend_Json::decode($this->getData('role_permission')));
        } else if (!is_array($this->getData('role_permission'))) {
            $this->setData('role_permission', array());
        }
        return $this;
    }
    
    public function reload()
    {
        $this->load($this->getId());
        return $this;
    }
    
    public function logoutBySessId($sessid)
    {
        $this->getResource()->clearBySessId($sessid);
        return $this;
    }
    
    public function isAllowed($resource)
    {
    	if ($this->getUserRole() == Magestore_SimiPOS_Model_Role::ROLE_ADMIN) {
    		return true;
    	}
    	$acl = $this->getRolePermission();
    	if ($acl && is_string($acl)) {
    		$acl = Zend_Json::decode($acl);
    		$this->setData('role_permission', $acl);
    	}
    	if (is_array($acl) && isset($acl[$resource])) {
    		$permission = $acl[$resource];
    	} else {
    		$permission = Magestore_SimiPOS_Model_Role::PERMISSION_OWNER;
    	}
    	return Mage::getSingleton('simipos/role')->isAllowed($resource, $permission);
    }
    
    public function getPermission($resource)
    {
    	$acl = $this->getRolePermission();
    	if (is_array($acl) && isset($acl[$resource])) {
    		return $acl[$resource];
    	}
    	return Magestore_SimiPOS_Model_Role::PERMISSION_OWNER;
    }
}
