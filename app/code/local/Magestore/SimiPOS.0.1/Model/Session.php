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
 * SimiPOS API Session Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Session extends Mage_Core_Model_Session_Abstract
{
	const MAX_INT_VALUE = 4294967295;
    protected $_currentSessId = null;
    public function getSessionId() {
        return $this->_currentSessId;
    }
    public function setSessionId($sessId = null) {
        if (!is_null($sessId)) {
            $this->_currentSessId = $sessId;
        }
        return $this;
    }
    
    public function start($sessionName=null)
    {
        $this->_currentSessId = md5(time() . $sessionName . rand());
        return $this;
    }
    
    public function init($namespace, $sessionName=null)
    {
        if (is_null($this->_currentSessId)) {
            $this->start($sessionName);
        }
        return $this;
    }
    
    public function revalidateCookie()
    {
        // Don't use cookie
    }
    
    public function clear()
    {
        if ($sessId = $this->getSessionId()) {
            try {
                Mage::getModel('simipos/user')->logoutBySessId($sessId);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }
    
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            return ;
        }
        if ($username == Mage::getStoreConfig('simipos/account/username')
            && $password == Mage::getStoreConfig('simipos/account/api_key')
        ) {
        	$user = Mage::getModel('simipos/user')
        	   ->loadByUsername($username)
        	   ->setSessid($this->getSessionId());
        	if ($user->getId()) {
        		$this->setUser($user);
        		$user->getResource()->cleanOldSessions($user)
        		    ->recordSession($user);
        		// Record session
        		$this->setQuoteId(self::MAX_INT_VALUE);
        	}
        } else {
            $user = Mage::getModel('simipos/user')
                ->setSessid($this->getSessionId())
                ->login($username, $password);
        }
        if ($user->getId() && $user->getStatus() != Magestore_SimiPOS_Model_Status::STATUS_ACTIVE) {
            throw new Exception(Mage::helper('simipos')->__('Your account has been deactivated.'), 14);
        } else if ($user->getId()) {
            $this->setUser($user);
        } else {
            throw new Exception(Mage::helper('simipos')->__('Unable to login.'), 15);
        }
        return $user;
    }
    
    public function isAllowed($resource)
    {
        if ($user = $this->getUser()) {
            if ($user->getUserRole() == Magestore_SimiPOS_Model_Role::ROLE_ADMIN) {
            	if ($this->getQuoteId() == self::MAX_INT_VALUE) {
            		$adminActions = Mage::getSingleton('simipos/role')->getAdminActions();
            		if (!in_array($resource, $adminActions)) {
            			return false;
            		}
            	}
                return true;
            }
            return $user->isAllowed($resource);
//            $adminActions = Mage::getSingleton('simipos/role')->getAdminActions();
//            if (in_array($resource, $adminActions)) {
//                return false;
//            }
//            return true;
        }
        return false;
    }
    
    public function isSessionExpired($user)
    {
        if (!$user->getId()) {
            return true;
        }
        $timeout = time() - strtotime($user->getLogdate());
        return $timeout > Mage::getStoreConfig('simipos/general/session_timeout');
    }
    
    public function isLoggedIn($sessId = false)
    {
        $userExists = $this->getUser() && $this->getUser()->getId();
        if (!$userExists && $sessId !== false) {
            return $this->_renewBySessId($sessId);
        }
        // Only use when you want to delete order, product... from simipos Api
        // if ($userExists) {
            // Mage::register('isSecureArea', true, true);
        // }
        return $userExists;
    }
    
    protected function _renewBySessId($sessId)
    {
        $user = Mage::getModel('simipos/user')->loadBySessId($sessId);
        if (!$user->getId() || !$user->getSessid()) {
            return false;
        }
        if ($user->getSessid() == $sessId && !$this->isSessionExpired($user)) {
            $this->setUser($user);
            $user->getResource()->recordSession($user);
            return true;
        }
        return false;
    }
    
    public function getCurrentStoreId()
    {
        if ($this->hasData('current_store_id')) {
            return $this->getData('current_store_id');
        }
        $user = $this->getUser();
        if ($user && $user->getId() && $user->getCurrentStoreId()) {
            $this->setData('current_store_id', $user->getCurrentStoreId());
        }
        return $this->getData('current_store_id');
    }
    
    public function setQuoteId($quoteId)
    {
        $this->setData('quote_id', $quoteId);
        $this->getUser()->getResource()->recordQuoteId($this);
        return $this;
    }
    
    public function getQuoteId()
    {
        if (!$this->hasData('quote_id')) {
            $this->setData('quote_id',
                $this->getUser()->getResource()->getQuoteId($this)
            );
        }
        return $this->getData('quote_id');
    }
}
