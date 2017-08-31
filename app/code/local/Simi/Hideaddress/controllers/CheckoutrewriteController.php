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
 * @category 	Simi
 * @package 	Simi_Connector
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Mconnect Index Controller
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Hideaddress_CheckoutrewriteController extends Simi_Connector_CheckoutController {

    protected $_active = true;

    public function get_order_configAction() {
        if (Mage::getStoreConfig('hideaddress/general/enable') == 1) {
            $data = $this->getData();
            if ($data && $data->store_id) {
                Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, Mage::app()->getStore($data->store_id)->getCode(), TRUE);
                Mage::app()->setCurrentStore(
                        Mage::app()->getStore($data->store_id)->getCode()
                );
                Mage::getSingleton('core/locale')->emulate($data->store_id);
            }
            if ($this->_active) {
                $results = Mage::getModel('hideaddress/hideaddress_show')->checkDataRequired($data);
                if (!isset($results) || $results != null) {
                    $this->_printDataJson($results);
                }
            }
            // Billing Address
            if ($data->billingAddress->name == null) {
                $data->billingAddress->name = "N/A";
            }
            if ($data->billingAddress->prefix == null) {
                $data->billingAddress->prefix = "N/A";
            }
             if ($data->billingAddress->suffix == null) {
                $data->billingAddress->suffix = "N/A";
            }
             if ($data->billingAddress->email == null) {
                $data->billingAddress->email = "N/A";
            }
            if ($data->billingAddress->street == null) {
                $data->billingAddress->street = "N/A";
            }
            if ($data->billingAddress->phone == null) {
                $data->billingAddress->phone = "N/A";
            }
            if ($data->billingAddress->city == null) {
                $data->billingAddress->city = "N/A";
            }
            if ($data->billingAddress->country_code == null) {
                $data->billingAddress->country_code = "US";
            }
            if ($data->billingAddress->zip == null) {
                $data->billingAddress->zip = "N/A";
            }
            if ($data->billingAddress->state_name == null) {
                $data->billingAddress->state_name = "N/A";
            }
            if ($data->billingAddress->state_id == null) {
                $data->billingAddress->state_id = "N/A";
            }

            // Shipping Address
            if ($data->shippingAddress->name == null) {
                $data->shippingAddress->name = "N/A";
            }
            if ($data->shippingAddress->prefix == null) {
                $data->shippingAddress->prefix = "N/A";
            }
             if ($data->shippingAddress->suffix == null) {
                $data->shippingAddress->suffix = "N/A";
            }
             if ($data->shippingAddress->email == null) {
                $data->shippingAddress->email = "N/A";
            }
            if ($data->shippingAddress->street == null) {
                $data->shippingAddress->street = "N/A";
            }
            if ($data->shippingAddress->phone == null) {
                $data->shippingAddress->phone = "N/A";
            }
            if ($data->shippingAddress->city == null) {
                $data->shippingAddress->city = "N/A";
            }
            if ($data->shippingAddress->country_code == null) {
                $data->shippingAddress->country_code = "US";
            }
            if ($data->shippingAddress->zip == null) {
                $data->shippingAddress->zip = "N/A";
            }
            if ($data->shippingAddress->state_name == null) {
                $data->shippingAddress->state_name = "N/A";
            }
            if ($data->shippingAddress->state_id == null) {
                $data->shippingAddress->state_id = "N/A";
            }

            $this->setData($data);
            $information = Mage::getModel('connector/checkout')->getOrderConfig($data);
            $this->_printDataJson($information);
        } else {
            parent::get_order_configAction();
        }
    }

}