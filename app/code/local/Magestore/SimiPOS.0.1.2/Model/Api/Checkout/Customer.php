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
 * SimiPOS Checkout Cart Customer API Model
 * Use to call api with prefix: checkout_customer
 * Methods:
 *  set
 *  setAddress
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Customer
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    /**
     * Set customer for shopping cart
     * 
     * @param array $customerData
     * @return boolean
     */
    public function apiSet($customerData)
    {
        $quote = $this->_getQuote(true);
        
        $customerData = $this->_prepareData($customerData);
        if (!isset($customerData['mode'])) {
            throw new Exception($this->_helper->__('Customer mode is unknown'), 33);
        }
        if (isset($customerData['id'])) {
            $customerData['entity_id'] = $customerData['id'];
        }
        
        switch($customerData['mode']) {
        case Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER:
            $customer = $this->_getCustomer($customerData['entity_id']);
            $customer->setMode(Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER);
            break;
        
        case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
        case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
            $customer = Mage::getModel('customer/customer')->setData($customerData);
            // $customer->setFirstname(Mage::getStoreConfig('simipos/checkout/firstname', $this->getStore()))
                // ->setLastname(Mage::getStoreConfig('simipos/checkout/lastname', $this->getStore()))
                // ->setEmail(Mage::getStoreConfig('simipos/checkout/email', $this->getStore()));
            // $customer->addData($customerData);
            
            if ($customer->getMode() == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
                $password = $customer->generatePassword();
                $customer->setPassword($password)
                    ->setConfirmation($password);
            }
            
            // $isCustomerValid = $customer->validate();
            // if ($isCustomerValid !== true && is_array($isCustomerValid)) {
                // throw new Exception(implode("\n", $isCustomerValid), 34); // Customer data invalid
            // }
            break;
        }
        try {
            $quote->setCustomer($customer)
                ->setCheckoutMethod($customer->getMode())
                ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
                ->save();
            $quote->collectTotals()->save();
            if ($customer->getDefaultBillingAddress() && $customer->getDefaultBillingAddress()->getId()) {
                // Assign address for quote when change customer
                $billingData = $customer->getDefaultBillingAddress()->getData();
                $billingData['mode'] = Mage_Sales_Model_Quote_Address::TYPE_BILLING;
                $billingData['use_for_shipping'] = true;
            } else {
                $billingData = array(
                    'mode'      => Mage_Sales_Model_Quote_Address::TYPE_BILLING,
                    'use_for_shipping'  => true
                );
            }
            $this->apiSetAddress($billingData);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 43); // cannot add customer for shopping cart
        }
        return true;
    }
    
    /**
     * Set customer address for shopping cart
     * 
     * @param array $addressData
     * @return boolean
     * @throws Exception
     */
    public function apiSetAddress($addressData)
    {
        $quote = $this->_getQuote();
        
        $addressData = $this->_prepareData($addressData);
        if (isset($addressData['id'])) {
            $addressData['entity_id'] = $addressData['id'];
        }
        
        $addressMode = $addressData['mode'];
        unset($addressData['mode']);
        
        $address = Mage::getModel('sales/quote_address');
        if (!empty($addressData['entity_id'])) {
            $customerAddress = $this->_getCustomerAddress($addressData['entity_id']);
            if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                throw new Exception($this->_helper->__('Address not belong to customer'), 44);
            }
            $address->importCustomerAddress($customerAddress);
        } else {
            $address->setData(Mage::getStoreConfig('simipos/checkout', $this->getStore()));
            Mage::dispatchEvent('simipos_api_checkout_customer_address', array(
                'user'      => $this->getUser(),
                'address'   => $address
            ));
            if ($quote->getCustomerEmail()) {
                $address->setEmail($quote->getCustomerEmail());
            }
            $address->addData($addressData);
        }
        $address->implodeStreetAddress();
        // if (($validateRes = $address->validate())!==true) {
            // throw new Exception(implode("\n", $validateRes), 45); // Address error
        // }
        
        switch($addressMode) {
        case Mage_Sales_Model_Quote_Address::TYPE_BILLING:
            $address->setEmail($quote->getCustomer()->getEmail());
            if (!$quote->isVirtual()) {
                if (empty($addressData['use_for_shipping'])) {
                    $shippingAddress = $quote->getShippingAddress();
                    $shippingAddress->setSameAsBilling(0);
                } else {
                    $billingAddress = clone $address;
                    $billingAddress->unsAddressId()->unsAddressType();
                    
                    $shippingAddress = $quote->getShippingAddress();
                    $shippingMethod = $shippingAddress->getShippingMethod();
                    $shippingAddress->addData($billingAddress->getData())
                        ->setSameAsBilling(1)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                }
            }
            $quote->setBillingAddress($address);
            break;
        case Mage_Sales_Model_Quote_Address::TYPE_SHIPPING:
            $address->setCollectShippingRates(true)
                ->setSameAsBilling(0);
            $quote->setShippingAddress($address);
            break;
        }
        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 46); // address is not set
        }
        return true;
    }
    
    /**
     * get customer by id
     * 
     * @param int $customerId
     * @return Mage_Customer_Model_Customer
     * @throws Exception
     */
    protected function _getCustomer($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception($this->_helper->__('Customer is not found.'), 31);
        }
        return $customer;
    }
    
    /**
     * get customer address by id
     * 
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     * @throws Exception
     */
    protected function _getCustomerAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if (!$address->getId()) {
            throw new Exception($this->_helper->__('Customer address is not found.'), 35);
        }
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }
    
    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function prepareCustomerForQuote(Mage_Sales_Model_Quote $quote)
    {
        $isNewCustomer = false;
        switch ($quote->getCheckoutMethod()) {
        case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
            $this->_prepareGuestQuote($quote);
            break;
        case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
            $this->_prepareNewCustomerQuote($quote);
            $isNewCustomer = true;
            break;
        default:
            $this->_prepareCustomerQuote($quote);
            break;
        }
        return $isNewCustomer;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_SimiPOS_Model_Api_Checkout_Customer
     */
    protected function _prepareGuestQuote(Mage_Sales_Model_Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_SimiPOS_Model_Api_Checkout_Customer
     */
    protected function _prepareNewCustomerQuote(Mage_Sales_Model_Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();
        
        $customer = $quote->getCustomer();
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $quote->setCustomer($customer)
            ->setCustomerId(true);

        return $this;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_SimiPOS_Model_Api_Checkout_Customer
     */
    protected function _prepareCustomerQuote(Mage_Sales_Model_Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $quote->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
            || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);

        return $this;
    }

    /**
     * Involve new customer to system
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_SimiPOS_Model_Api_Checkout_Customer
     */
    public function involveNewCustomer(Mage_Sales_Model_Quote $quote)
    {
        $customer = $quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation');
        } else {
            $customer->sendNewAccountEmail();
        }
        return $this;
    }
}
