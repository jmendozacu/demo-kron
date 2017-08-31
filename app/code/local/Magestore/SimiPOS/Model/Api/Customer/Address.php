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
 * SimiPOS Customer API Model
 * Use to call api with prefix: customer_address
 * Methods:
 *  customer_address.list
 *  customer_address.address
 *  customer_address.info
 *  customer_address.create
 *  customer_address.update
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Customer_Address extends Magestore_SimiPOS_Model_Api_Abstract
{
    protected $_filtersMap = array(
        'customer_address_id'   => 'entity_id',
    );
    
    protected $_ignoredAttributeCodes = array(
        'entity_id',
        'parent_id',
        'attribute_set_id',
        'entity_type_id'
    );
    
    /**
     * Retrieve customer addresses list by customer id
     * 
     * @param int $customerId
     * @return array
     */
    public function apiList($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        $result = array();
        foreach ($customer->getAddresses() as $address) {
            $data = $this->_prepareData($address->toArray());
            $data['is_default_billing'] = $customer->getDefaultBilling() == $address->getId();
            $data['is_default_shipping']= $customer->getDefaultShipping() == $address->getId();
            $result[$address->getId()] = $data;
        }
        return $result;
    }
    
    /**
     * Billing address for customer
     * 
     * @param int $customerId
     * @return array
     * @throws Exception
     */
    public function apiAddress($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        $result = array();
        foreach ($customer->getAddresses() as $address) {
            if ($customer->getDefaultBilling() == $address->getId()) {
                $result = $this->_prepareData($address->toArray());
                $result['id'] = $address->getId();
                return $result;
            }
        }
        foreach ($customer->getAddresses() as $address) {
            $result = $this->_prepareData($address->toArray());
            $result['id'] = $address->getId();
            return $result;
        }
        return $result;
    }
    
    /**
     * Retrieve customer address info by id
     * 
     * @param string $addressId
     * @return array
     */
    public function apiInfo($addressId)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if (!$address->getId()) {
            throw new Exception($this->_helper->__('Customer address is not found.'), 35);
        }
        $result = $this->_prepareData($address->toArray());
        if ($customer = $address->getCustomer()) {
            $result['is_default_billing'] = $customer->getDefaultBilling() == $address->getId();
            $result['is_default_shipping']= $customer->getDefaultShipping() == $address->getId();
        }
        $result['id'] = $address->getId();
        return $result;
    }
    
    /**
     * Create new customer address
     * 
     * @param int $customerId
     * @param array $addressData
     * @return int
     */
    public function apiCreate($customerId, $addressData)
    {
        $customer = Mage::getModel('simipos/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        $addressData = $this->_helper->prepareData($addressData);
        $address = Mage::getModel('customer/address')
            ->setData($this->_prepareData($addressData));
        $address->setCustomerId($customerId);
        
        // $valid = $address->validate();
        // if (is_array($valid)) {
            // throw new Exception(implode("\n", $valid), 36);
        // }
        try {
            $address->save();
            if (!$customer->getData('default_billing')) {
            	$customer->setData('default_billing', $address->getId());
            	$customer->save();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 37); // Data invalid
        }
        return $address->getId();
    }
    
    /**
     * Update customer information
     * 
     * @param int $addressId
     * @param array $addressData
     * @return boolean
     */
    public function apiUpdate($addressId, $addressData)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if (!$address->getId()) {
            throw new Exception($this->_helper->__('Customer address is not found.'), 35);
        }
        $addressData = $this->_helper->prepareData($addressData);
        $address->addData($this->_prepareData($addressData));
        
        // $valid = $address->validate();
        // if (is_array($valid)) {
            // throw new Exception(implode("\n", $valid), 36);
        // }
        try {
            $address->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 37); // Data invalid
        }
        return true;
    }
    
    protected function _prepareData($data)
    {
        foreach ($this->_ignoredAttributeCodes as $ignoreAttribute) {
            if (isset($data[$ignoreAttribute])) {
                unset($data[$ignoreAttribute]);
            }
        }
        return $data;
    }
}
