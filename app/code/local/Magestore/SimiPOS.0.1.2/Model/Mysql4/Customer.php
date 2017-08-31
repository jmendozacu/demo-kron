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
 * Simipos Customer Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Mysql4_Customer
extends Mage_Customer_Model_Entity_Customer
{
	protected function _beforeSave(Varien_Object $customer)
	{
		Mage_Eav_Model_Entity_Abstract::_beforeSave($customer);
		
		if (!$customer->getEmail() && !$customer->getTelephone() && !$customer->getFirstname()) {
            throw Mage::exception('Mage_Customer',
                Mage::helper('simipos')->__('Please enter customers\' name, email or telephone number.')
            );
        }
        if (!$customer->getEmail() && !$customer->getTelephone()) {
        	return $this;
        }
        
        // Check Validate Email, Telephone
        $collection = Mage::getResourceModel('customer/customer_collection');
        if ($customer->getEmail()) {
        	$collection->addFieldToFilter('email', $customer->getEmail());
        }
        if ($customer->getTelephone()) {
        	$collection->addFieldToFilter('telephone', $customer->getTelephone());
        }
        if ($customer->getSharingConfig()->isWebsiteScope()) {
        	$collection->addFieldToFilter('website_id', $customer->getWebsiteId());
        }
        if ($customer->getId()) {
        	$collection->addFieldToFilter('entity_id', array('neq' => $customer->getId()));
        }
        if ($collection->getSize()) {
            throw Mage::exception(
                'Mage_Customer', Mage::helper('simipos')->__('This customer\'s email and telephone number already exist.'),
                Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS
            );
        }
        return $this;
	}
	
	public function updateTelephone($customerId, $telephone)
	{
		$readAdapter    = $this->_getReadAdapter();
		$this->_getWriteAdapter()->update(
		    $this->getTable('customer/entity'),
		    array('telephone' => $telephone),
		    $readAdapter->quoteInto('entity_id = ?', $customerId)
		);
	}
}
