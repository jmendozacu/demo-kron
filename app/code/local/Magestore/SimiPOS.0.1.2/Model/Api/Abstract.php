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
 * SimiPOS API Resource Abstract
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
abstract class Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * @var Magestore_SimiPOS_Helper_Data
     */
    protected $_helper;
    
    public function __construct() {
        $this->_helper = Mage::helper('simipos');
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
    
    /**
     * Retrieve current working sales user
     * 
     * @return Magestore_SimiPOS_Model_User
     */
    public function getUser()
    {
        return $this->_getSession()->getUser();
    }
    
    /**
     * Retrieve current working store
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }
    
    /**
     * Retrieve current store id
     * 
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getCurrentStoreId();
    }
    
    /**
     * Check current user is admin or sales staff
     * 
     * @return type
     */
    public function isAdmin()
    {
        return ($this->getUser()->getUserRole() == Magestore_SimiPOS_Model_Role::ROLE_ADMIN);
    }
}
