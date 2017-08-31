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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Model_Customer extends Mage_Core_Model_Abstract {

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    public function _construct() {
        parent::_construct();
    }

    public function getCustomerByEmail($email) {
        return Mage::getModel('customer/customer')->getCollection()
                        ->addFieldToFilter('email', $email)
                        ->getFirstItem();
    }

    public function registerCustomer($data) {
        $checkCustomer = $this->getCustomerByEmail($data['user_email']);
        if ($checkCustomer->getId()) {
            $result['status'] = 'FAIL';
            $result['message'] = 'Account is already exist';
            return Mage::helper('core')->jsonEncode($result);
        }

        $name = Mage::helper('madapter')->soptName($data['user_name']);
        $result = array();
        $customer = Mage::getModel('customer/customer')
                ->setFirstname($name['first_name'])
                ->setLastname($name['last_name'])
                ->setEmail($data['user_email']);

        //$newPassword = $customer->generatePassword();
        $customer->setPassword($data['user_password']);
        try {
            $customer->save();
            $result['user_id'] = $customer->getId();
            $result['status'] = 'SUCCESS';
//             $this->_getSession()->setCustomerAsLoggedIn($customer);
        } catch (Exception $e) {
            $result['status'] = 'FAIL';
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    public function login($data) {
        $result = array();
        if ($this->_getSession()->isLoggedIn()) {
            $result['user_id'] = $this->_getSession()->getCustomer()->getId();
            $result['user_name'] = $this->_getSession()->getCustomer()->getName();
            $result['token'] = $this->_getSession()->getCustomer()->getPasswordHash();
            $result['status'] = 'SUCCESS';
            return Mage::helper('core')->jsonEncode($result);
        } else {
            $session = $this->_getSession();
            try {
                $session->login($data['user_email'], $data['user_password']);
                $result['user_id'] = $this->_getSession()->getCustomer()->getId();
                $result['user_name'] = $this->_getSession()->getCustomer()->getName();
                $result['token'] = $this->_getSession()->getCustomer()->getPasswordHash();
                $result['status'] = 'SUCCESS';
            } catch (Mage_Core_Exception $e) {
                $result['status'] = 'FAIL';
            }
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    public function logout() {
        $result = array();
        try {
            $this->_getSession()->logout()
                    ->setBeforeAuthUrl(Mage::getUrl());
            $result['status'] = 'SUCCESS';
        } catch (Exception $e) {
            $result['status'] = 'FAIL';
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    public function changePassword($data) {
        $result = array();
        if (!$this->_getSession()->isLoggedIn()) {
            return Mage::helper('core')->jsonEncode($result['status'] = 'FAIL');
        }
        $customer = $this->_getSession()->getCustomer();
        $customer->setChangePassword(1);
        $currPass = $data['old_password'];
        $newPass = $data['new_password'];
        $confPass = $data['com_password'];

        $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
        if (Mage::helper('core/string')->strpos($oldPass, ':')) {
            list($_salt, $salt) = explode(':', $oldPass);
        } else {
            $salt = false;
        }
        if ($customer->hashPassword($currPass, $salt) == $oldPass) {
            if (strlen($newPass)) {
                $customer->setPassword($newPass);
                $customer->setConfirmation($confPass);
                try {
                    $customer->save();
                    $this->_getSession()->setCustomer($customer);
                    $result['status'] = 'SUCCESS';
                } catch (Exception $e) {
                    $result['status'] = 'FAIL';
                }
            } else {
                $result['status'] = 'FAIL';
            }
        } else {
            $result['status'] = 'FAIL';
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    public function getProfile() {
        $result = array();
        if ($this->_getSession()->isLoggedIn()) {
            $customer = $this->_getSession()->getCustomer();
            $result['user_id'] = $customer->getId();
            $result['user_name'] = $customer->getName();
            $result['user_email'] = $customer->getEmail();
            //$result['addressBook'] = Mage::helper('madapter')->getAdressBook($customer);            
        }
        return Mage::helper('madapter')->encodeJson('userInfo', $result);
    }

    public function getAddressBook() {
        $list = array();
        if ($this->_getSession()->isLoggedIn()) {
            $customer = $this->_getSession()->getCustomer();
            $data = Mage::helper('madapter')->getAdressBook($customer);
            if ($data) {
                $list[] = Mage::helper('madapter')->getAdressBook($customer);
            }
        }
        return $list;
    }

    public function getAddress() {
        $data = array();
        $list = array();
        if ($this->_getSession()->isLoggedIn()) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $customer = $this->_getSession()->getCustomer();
            // check address billing and shipping
            $billing = $customer->getPrimaryBillingAddress();
            $address_billing_id = $quote->getBillingAddress()->getId();
            $address_shipping_id = $quote->getShippingAddress()->getId();
            if ($billing) {
                $list[] = Mage::helper('madapter')->getAddress($billing, $customer);
                $data[] = Mage::helper('madapter')->getAddressToOrder($billing, $customer, $address_billing_id, $address_shipping_id);
            }
            $shipping = $customer->getPrimaryShippingAddress();
            if ($shipping) {
                $item = Mage::helper('madapter')->getAddress($shipping, $customer);
                if (!in_array($item, $list)) {
                    $data[] = Mage::helper('madapter')->getAddressToOrder($shipping, $customer, $address_billing_id, $address_shipping_id);
                    $list[] = $item;
                }
            }

            //check adress additional
            $adrress_addition = $customer->getAdditionalAddresses();
            foreach ($adrress_addition as $adrr) {
                $item = Mage::helper('madapter')->getAddress($adrr, $customer);
                if (!in_array($item, $list)) {
                    $data[] = Mage::helper('madapter')->getAddressToOrder($adrr, $customer, $address_billing_id, $address_shipping_id);
                    $list[] = $item;
                }
            }
            //check addrress in orders
            if (isset($data['is_get_order_address']) && $data['is_get_order_address'] == "YES") {
                $orders = Mage::getModel('sales/order')->getCollection()
                        ->addFieldToFilter('customer_id', $this->_getSession()->getCustomer()->getId());
                foreach ($orders as $order) {
                    $shipping = $order->getShippingAddress();
                    $item_shipping = Mage::helper('madapter')->getAddress($shipping, $customer);
                    if (!in_array($item_shipping, $list)) {
                        $data[] = Mage::helper('madapter')->getAddressToOrder($shipping, $customer, $address_billing_id, $address_shipping_id);
                        $list[] = $item_shipping;
                    }
                    $billing = $order->getBillingAddress();
                    $item_billing = Mage::helper('madapter')->getAddress($billing, $customer);
                    if (!in_array($item_billing, $list)) {
                        $data[] = Mage::helper('madapter')->getAddressToOrder($billing, $customer, $address_billing_id, $address_shipping_id);
                        $list[] = $item_billing;
                    }
                }
            }
        }
        //Zend_debug::dump();die();
        return Mage::helper('madapter')->encodeJson('addressList', $data);
    }

    /* if  x = 1 -> save for biiling or shipping    
     * else save address addition
     */

    public function saveAddress(&$data, $x = 0) {
        $stateModel = Mage::getModel('madapter/state');
        $result = true;
        $check_state = false;
        if ($this->_getSession()->isLoggedIn()) {
            if ($x == 1) {
                $countryB = $data['b_country_code'];
                $listState = $stateModel->getStates($countryB);
                if (count($listState) == 0)
                    $check_state = true;
                foreach ($listState as $state) {
                    if (in_array($data['b_state_code'], $state)
                            || in_array($data['b_state_name'], $state)) {
                        $data['b_state_id'] = $state['state_id'];
                        $check_state = true;
                        break;
                    }
                }
                if (!$check_state)
                    return false;
                $addressB = array();
                $addressB = Mage::helper('madapter')->convertDataBilling($data);
                $addressB['id'] = $data['b_id'];
                $addressB['vat_id'] = false;
                $result = $this->saveAddressCustomer($addressB, 1);
                if (!$result)
                    return false;

                $countryS = $data['s_country_code'];
                $listState = $stateModel->getStates($countryS);
                if (count($listState) == 0)
                    $check_state = true;
                foreach ($listState as $state) {
                    if (in_array($data['s_state_code'], $state)
                            || in_array($data['s_state_name'], $state)) {
                        $data['s_state_id'] = $state['state_id'];
                        $check_state = true;
                        break;
                    }
                }
                if (!$check_state)
                    return false;
                $addressS = array();
                $addressS = Mage::helper('madapter')->convertDataShipping($data);
                $addressS['id'] = $data['s_id'];
                $addressS['vat_id'] = false;
                $result = $this->saveAddressCustomer($addressS, 2);
                if (!$result)
                    return false;
            }else {
                $country = $data['country_code'];
                $listState = $stateModel->getStates($country);
                if (count($listState) == 0)
                    $check_state = true;
                foreach ($listState as $state) {
                    if (in_array($data['state_code'], $state)
                            || in_array($data['state_name'], $state)) {
                        $data['state_id'] = $state['state_id'];
                        $check_state = true;
                        break;
                    }
                }
                if (!$check_state) {
                    return Mage::helper('core')->jsonEncode(array('status' => 'FAIL', 'message' => 'State invalid'));
                }
                $address = array();
                $address = Mage::helper('madapter')->convertDataAddress($data);
                $address['id'] = isset($data['address_id']) == true ? $data['address_id'] : null;
                $result = $this->saveAddressCustomer($address, 3);
                if (!$result) {
                    return Mage::helper('core')->jsonEncode(array('status' => 'FAIL', 'message' => 'Can not save address customer'));
                } else {
                    return Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS', 'state_id' => $data['state_id'], 'state_code' => $data['state_code'], 'state_name' => $data['state_name']));
                }
            }
            return true;
        } else {
            if ($x == 0) {
                $country = $data['country_code'];
                $listState = $stateModel->getStates($country);
                if (count($listState) == 0)
                    $check_state = true;
                foreach ($listState as $state) {
                    if (in_array($data['state_code'], $state)
                            || in_array($data['state_name'], $state)) {
                        $data['state_id'] = $state['state_id'];
                        $check_state = true;
                        break;
                    }
                }
                if (!$check_state) {
                    return Mage::helper('core')->jsonEncode(array('status' => 'FAIL', 'message' => 'State invalid'));
                } else {
                    return Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS', 'state_id' => $data['state_id'], 'state_code' => $data['state_code'], 'state_name' => $data['state_name']));
                }
            }
            return false;
        }
    }

    /**
     * 
     * @param type $data
     * @param type $type (1 - billing, 2- shipping, 3- addition)
     * @return boolean
     */
    public function saveAddressCustomer($data, $type) {
        $result = true;
        $errors = false;
        $customer = $this->_getSession()->getCustomer();
        $address = Mage::getModel('customer/address');
        $addressId = $data['id'];
        if ($addressId) {
            $existsAddress = $customer->getAddressById($addressId);
            if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                $address->setId($existsAddress->getId());
            }
        }
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
        try {
            $addressForm->compactData($data);

            if ($type == 1) {
                $address->setCustomerId($customer->getId())
                        ->setIsDefaultBilling(1);
            } elseif ($type == 2) {
                $address->setCustomerId($customer->getId())
                        ->setIsDefaultShipping(1);
            } else {
                $address->setCustomerId($customer->getId());
            }
            $addressErrors = $address->validate();
            if ($addressErrors !== true) {
                $errors = true;
            }
            if (!$errors)
                $address->save();
            else
                $result = false;
        } catch (Mage_Core_Exception $e) {
            $result = false;
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function getOptions($type, $item) {
        $options = null;
        $productMadapter = Mage::getModel('madapter/product');
        if ($type == 'bundle') {
            $options = $productMadapter->getBunldedOptions($item);
        } else {
            $options = $productMadapter->getUsedProductOption($item);
        }
        return $options;
    }

    /**
     * return item in cart
     */
    public function getCart() {
        $list = array();
//        if ($this->_getSession()->isLoggedIn()) {
            //die('111111111');
            $quote = $this->_getCheckoutSession()->getQuote();
            $allItems = $quote->getAllVisibleItems();
            foreach ($allItems as $item) {
                //Zend_debug::dump($item->getData());die();
                $product = $item->getProduct();
                $options = $this->getOptions($product->getTypeId(), $item);
                $list[] = array(
                    'cart_item_id' => $item->getId(),
                    'product_id' => $product->getId(),
                    'product_name' => $product->getName(),
                    'product_price' => $item->getPrice(),
                    'product_image' => Mage::getSingleton('madapter/product')->getImageProduct($product),
                    'product_qty' => $item->getQty(),
                    'options' => $options,
                );
            }
//        }
        return $list;
    }

}