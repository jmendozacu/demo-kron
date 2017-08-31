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
 * SimiPOS Role Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Role
{
    const ROLE_ADMIN    = 1;
    const ROLE_SALES    = 2;
    
    protected $_adminActions = array(
        'user.list',
        'user.info',
        'user.create',
        'user.update',
        'user.updateBlock',
        'user.delete'
    );
    
    /**
     * Role ACL
     * @var array
     */
    protected $_roleAcl;
    const PERMISSION_OWNER      = 0;
    const PERMISSION_OTHER      = 1;
    const PERMISSION_ADMIN      = 2;
    const PERMISSION_DENIED     = 4;
    
    public function getAdminActions()
    {
        return $this->_adminActions;
    }
    
    public function __construct()
    {
    	$this->_roleAcl = array(
	    // Customer
	        // 'customer.create'  => Mage::helper('simipos')->__('Create Customer'),
	        'customer'         => array(
    	           'type'      => 'label',
    	           'label'     => Mage::helper('simipos')->__('Manage customers'),
    	       ),
            'customer.update'  => array(
                   'type'      => 'group',
                   'label'     => Mage::helper('simipos')->__('Update Customer'),
               ),
	        'customer.delete'  => array(
    	           'type'      => 'group',
    	           'label'     => Mage::helper('simipos')->__('Delete Customer'),
    	       ),
	    // Order
	        'order.list'       => array(
                   'type'      => 'advanced',
                   'label'     => Mage::helper('simipos')->__('View Order List'),
               ),
	        'order.invoice'    => array(
                   'type'      => 'advanced',
                   'label'     => Mage::helper('simipos')->__('Invoice Orders'),
               ),
	        'order.refund'     => array(
                   'type'      => 'advanced',
                   'label'     => Mage::helper('simipos')->__('Refund Orders'),
               ),
            'order.cancel'     => array(
                   'type'      => 'advanced',
                   'label'     => Mage::helper('simipos')->__('Cancel Orders')
               ),
    	// Staff
    	    'user.list'        => Mage::helper('simipos')->__('View staff user list'),
    	    'user.create'      => array(
    	           'depend'    => 'user.list',
    	           'label'     => Mage::helper('simipos')->__('Create staff users')
    	       ),
    	    'user.delete'      => array(
                   'depend'    => 'user.list',
                   'label'     => Mage::helper('simipos')->__('Delete staff users')
               ),
    	    'user.update'      => array(
                   'depend'    => 'user.list',
                   'label'     => Mage::helper('simipos')->__('Update staff users'),
               ),
	    );
    }
    
    public function getRoleAcl()
    {
    	return $this->_roleAcl;
    }
    
    public function getPermissionOption()
    {
    	return array(
    	   self::PERMISSION_DENIED     => Mage::helper('simipos')->__('No permission'),
    	   self::PERMISSION_OWNER      => Mage::helper('simipos')->__('Created by this user'),
    	   self::PERMISSION_OTHER      => Mage::helper('simipos')->__('Created by other staff'),
    	   self::PERMISSION_ADMIN      => Mage::helper('simipos')->__('All orders')
    	);
    }
    
    public function isAllowed($resource, $permission)
    {
    	if (isset($this->_roleAcl[$resource])
    	   && is_array($this->_roleAcl[$resource])
    	   && isset($this->_roleAcl[$resource]['type'])
    	   && $this->_roleAcl[$resource]['type'] == 'advanced'
    	) {
    		if ($permission == self::PERMISSION_DENIED) {
    			return false;
    		}
    		return true;
    	}
    	if (!$permission && (isset($this->_roleAcl[$resource])
    	   || in_array($resource, $this->_adminActions))
    	) {
    		return false;
    	}
    	return true;
    }
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::ROLE_ADMIN    => Mage::helper('simipos')->__('Admin'),
            self::ROLE_SALES    => Mage::helper('simipos')->__('Sales staff'),
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}
