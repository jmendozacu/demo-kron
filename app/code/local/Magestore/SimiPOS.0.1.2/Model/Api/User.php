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
 * SimiPOS User API Model
 * Use to call api with prefix: user
 * Methods:
 *  list
 *  info
 *  create
 *  update
 *  updateBlock
 *  delete
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_User extends Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * List all sales staff user
     * 
     * @param array $filters
     * @return array
     */
    public function apiList($filters = null)
    {
        $collection = Mage::getResourceModel('simipos/user_collection');
        if (is_array($filters)) {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        }
        $result = array(
            'total' => count($collection)
        );
        foreach ($collection as $user) {
        	$user->afterLoad();
            $result[$user->getId()] = $user->getData();
            $result[$user->getId()]['name'] = $user->getName();
        }
        return $result;
    }
    
    /**
     * Sales staff user info
     * 
     * @param string $userId
     * @return array
     * @throws Exception
     */
    public function apiInfo($userId)
    {
        $user = Mage::getModel('simipos/user')->load($userId);
        if (!$user->getId()) {
            throw new Exception($this->_helper->__('User is not found.'), 111);
        }
        $result = $user->getData();
        $result['name'] = $user->getName();
        return $result;
    }
    
    /**
     * Create new sale staff user
     * 
     * @param array $userData
     * @return array
     * @throws Exception
     */
    public function apiCreate($userData)
    {
        $user = Mage::getModel('simipos/user');
        $user->setData($userData);
        try {
            $user->setId(null);
            $errors = $user->validate();
            if ($errors !== true) {
                throw new Exception(implode("\n", $errors));
            }
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
     * Update User Infomation
     * 
     * @param string $userId
     * @param array $userData
     * @return array
     * @throws Exception
     */
    public function apiUpdate($userId, $userData)
    {
        $user = Mage::getModel('simipos/user')->load($userId);
        if (!$user->getId()) {
            throw new Exception($this->_helper->__('User is not found.'), 111);
        }
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
     * Update a list of users
     * 
     * @param array $userList
     * @return boolean
     */
    public function apiUpdateBlock($userList)
    {
    	if (!is_array($userList)) {
    		return false;
    	}
    	foreach ($userList as $userData) {
    		if (empty($userData['email'])) {
    			continue;
    		}
    		$email = $userData['email'];
    		$user = Mage::getModel('simipos/user')->loadByUsername($email);
    		$userId = $user->getId();
    		$user->addData($userData)->setId($userId);
    		try {
    			$errors = $user->validate();
    			if ($errors !== true) {
    				continue;
    			}
    			$user->save();
    		} catch (Exception $e) {
    			// List error
    		}
    	}
    	return true;
    }
    
    /**
     * Delete user
     * 
     * @param string $userId
     * @return boolean
     * @throws Exception
     */
    public function apiDelete($userId)
    {
        if ($this->getUser()->getId() == $userId) {
            throw new Exception($this->_helper->__('Cannot delete your account.'), 113);
        }
        $user = Mage::getModel('simipos/user')->load($userId);
        if (!$user->getId()) {
            throw new Exception($this->_helper->__('User is not found.'), 111);
        }
        try {
            $user->delete();
            if ($this->_isRequestFromApp()) {
            	Mage::helper('simipos/magestore')->deleteUser($user);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 114);
        }
        return true;
    }
    
    protected function _isRequestFromApp()
    {
    	return (bool)Mage::app()->getRequest()->getPost('app', false);
    }
}
