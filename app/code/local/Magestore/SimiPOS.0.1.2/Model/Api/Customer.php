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
 * Use to call api with prefix: customer
 * Methods:
 *  customer.list
 *  customer.search
 *  customer.info
 *  customer.create
 *  customer.update
 *  customer.delete
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Customer extends Magestore_SimiPOS_Model_Api_Abstract
{
    protected $_filtersMap = array(
        'customer_id'   => 'entity_id',
    );
    
    protected $_ignoredAttributeCodes = array(
        'entity_id',
        'attribute_set_id',
        'entity_type_id'
    );
    
    /**
     * Retrieve customer list by filter
     * 
     * @param array|null $filters
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function apiList($filters = null, $page = null, $limit = null)
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect();
        if (is_array($filters)) {
            foreach ($filters as $field => $value) {
                if (isset($this->_filtersMap[$field])) {
                    $field = $this->_filtersMap[$field];
                }
                $collection->addFieldToFilter($field, $value);
            }
        }
        if ($page > 0) {
            $collection->setCurPage($page);
        }
        if ($limit > 0) {
            $collection->setPageSize($limit);
        }
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $customer) {
            $result[$customer->getId()] = array(
                'name'  => $customer->getData('name') ? $customer->getData('name') : $customer->getName(),
                'email' => $customer->getData('email'),
                'group_id'  => $customer->getData('group_id'),
                'telephone' => $customer->getData('telephone'),
            );
        }
        return $result;
    }
    
    /**
     * Search for list customer by Name/Email
     * 
     * @param string $searchTerm
     * @param int|nul $page
     * @param int|null $limit
     * @return array
     */
    public function apiSearch($searchTerm, $page = null, $limit = null)
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect();
        if ($searchTerm)
        $collection->addFieldToFilter(array(
            array(
                'attribute' => 'name',
                'like'      => "%$searchTerm%"
            ),
            array(
                'attribute' => 'email',
                'like'      => "%$searchTerm%"
            ),
            array(
                'attribute' => 'telephone',
                'like'      => "%$searchTerm%"
            ),
        ));
        if ($page > 0) {
            $collection->setCurPage($page);
        }
        if ($limit > 0) {
            $collection->setPageSize($limit);
        }
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $customer) {
            $result[$customer->getId()] = array(
                'name'  => $customer->getData('name') ? $customer->getData('name') : $customer->getName(),
                'email' => $customer->getData('email'),
                'group_id'  => $customer->getData('group_id'),
                'telephone' => $customer->getData('telephone'),
            );
        }
        return $result;
    }
    
    /**
     * Retrieve customer info by customer id or email
     * 
     * @param string $customerId
     * @return array
     */
    public function apiInfo($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        return $this->_responseData($customer);
    }
    
    /**
     * Create new customer
     * 
     * @param array $customerData
     * @return array
     */
    public function apiCreate($customerData)
    {
        $customerData = $this->_prepareData($customerData);
        try {
            $customer = Mage::getModel('simipos/customer')
                ->setData($customerData)
                ->setStore($this->getStore());
            if ($customer->getEmail() && !Zend_Validate::is($customer->getEmail(), 'EmailAddress')) {
            	throw new Exception(Mage::helper('adminhtml')->__('Please enter a valid email address.'));
            }
            $customer->save();
            // Create password and send email
            $newPassword = $customer->generatePassword();
            $customer->changePassword($newPassword);
            $customer->sendNewAccountEmail('registered', '', $this->getStoreId());
            $customer->setPassword(null);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 32); // Data Invalid
        }
        return $this->_responseData($customer);
    }
    
    /**
     * Update customer information
     * 
     * @param int $customerId
     * @param array $customerData
     * @return array
     */
    public function apiUpdate($customerId, $customerData)
    {
        $customer = Mage::getModel('simipos/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        $customer->addData($this->_prepareData($customerData));
        if ($customer->getEmail() && !Zend_Validate::is($customer->getEmail(), 'EmailAddress')) {
            throw new Exception(Mage::helper('adminhtml')->__('Please enter a valid email address.'), 32);
        }
        $customer->save();
        return $this->_responseData($customer);
    }
    
    protected function _responseData($customer)
    {
        $result = $this->_prepareData($customer->getData());
        $result['name'] = $customer->getName();
        $result['id'] = $customer->getId();
        return $result;
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
    
    public function apiDelete($customerId)
    {
    	$customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        try {
        	$customer->delete();
        } catch (Exception $e) {
        	throw new Exception($e->getMessage(), 32); // Data Invalid
        }
        return true;
    }
}
