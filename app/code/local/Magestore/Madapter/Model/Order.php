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
class Magestore_Madapter_Model_Order extends Mage_Core_Model_Abstract {

    protected function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function _construct() {
        parent::_construct();
    }

    public function customerLogin() {
        if ($this->_getSession()->isLoggedIn())
            return true;
        return false;
    }

    public function getShippingMethods($data) {
        if (!$this->customerLogin()) {
            $this->_getOnepage()->saveCheckoutMethod('guest');
        }

        $biiling_address = Mage::helper('madapter')->convertDataBilling($data);
        $shipping_address = Mage::helper('madapter')->convertDataShipping($data);
        $result = null;
        try {
            $result_billing = $this->_getOnepage()->saveBilling($biiling_address, $biiling_address['customer_address_id']);
            $result = $result_billing['message'];
            $result_shipping = $this->_getOnepage()->saveShipping($shipping_address, $shipping_address['customer_address_id']);
            $result = $result_shipping['message'];
            $this->_getCheckoutSession()->getQuote()->getShippingAddress()->collectShippingRates()->save();
        } catch (Exception $e) {
            
        }
        $result = array_unique($result);

        $shipping = $this->_getCheckoutSession()->getQuote()->getShippingAddress();

        $tax = 0;
        $total = $this->_getCheckoutSession()->getQuote()->getTotals();
        $methods = $shipping->getGroupedAllShippingRates();
        $list = array();
        foreach ($methods as $_ccode => $_carrier) {
            foreach ($_carrier as $_rate) {
                $list[] = array(
                    's_method_id' => $_rate->getId(),
                    's_method_code' => $_rate->getCode(),
                    's_method_title' => $_rate->getCarrierTitle(),
                    's_method_fee' => floatval($_rate->getPrice()),
                    's_method_name' => $_rate->getMethodTitle(),
                );
            }
        }

        $result_s = $this->_getOnepage()->saveShippingMethod($list[0]['s_method_code']);
        if (!$result_s) {
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => Mage::app()->getRequest(),
                'quote' => $this->_getOnepage()->getQuote()));
        }
        $this->_getOnepage()->getQuote()->collectTotals()->save();

        $grandTotal = $total['grand_total']->getValue();
        $subTotal = $total['subtotal']->getValue();
        $discount = 0;

        if (isset($total['discount']) && $total['discount']) {
            $discount = abs($total['discount']->getValue());
        }


        if (isset($total['tax']) && $total['tax']->getValue()) {
            $tax = $total['tax']->getValue(); //Tax value if present
        } else {
            $tax = 0;
        }

        $coupon = $this->_getCheckoutSession()->getQuote()->getCouponCode();
        return Mage::helper('madapter')->encodeOrderJson('shipmentMethodList', $list, (float) $tax, $subTotal, $grandTotal, $discount, $coupon, $result);
    }

    public function getOrdershistory($limit, $offset) {
        $list = array();
        if (!$this->customerLogin())
            return $list;
        $orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_id', $this->_getSession()->getCustomer()->getId());

        if ($offset > count($orders))
            return null;
        $check_limit = 0;
        $check_offset = 0;
        foreach ($orders as $order) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $list[] = array(
                'order_id' => $order->getIncrementId(),
                'order_status' => $order->getStatus(),
                'order_date' => $order->getUpdatedAt(),
                'recipient' => $order->getShippingAddress()->getName(),
                'order_items' => $this->getProductFromOrderList($order->getAllVisibleItems())
            );
        }
        return $list;
    }

    public function getProductNameFromOrder($itemCollection) {
        $productInfo = array();
        foreach ($itemCollection as $item) {
            $productInfo[] = array('product_name' => $item->getName());
        }
        return $productInfo;
    }

    public function getProductFromOrderList($itemCollection) {
        $productInfo = array();
        foreach ($itemCollection as $item) {
            //$product_id = $item->getProductId();
            //$product = Mage::getModel('catalog/product')->load($product_id);
            // $image = Mage::helper('madapter')->getImageProduct($product);
            $productInfo[] = array(
                //  'product_id' => $product_id,
                'product_name' => $item->getName(),
                    //   'product_price' => $item->getPrice(),
                    // 'product_image' => $image,
                    //'product_qty' => $item->getQtyToShip(),
            );
        }
        return $productInfo;
    }

    public function getProductFromOrderDetail($order) {
        $productInfo = array();
        $itemCollection = $order->getAllVisibleItems();
        foreach ($itemCollection as $item) {
            $options = array();
            // $product = $item->getProduct();
            //$options = Mage::getModel('madapter/customer')->getOptions($product->getTypeId(), $item);
            if ($item->getProductOptions()) {
                $options = $this->getOptions($item->getProductType(), $item->getProductOptions());
            }
            //Zend_debug::dump($options);die();
            $product_id = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($product_id);
            // Zend_debug::dump($item->getData());die();
            $image = Mage::helper('madapter')->getImageProduct($product);
            $productInfo[] = array(
                'product_id' => $product_id,
                'product_name' => $item->getName(),
                'product_price' => $item->getPrice(),
                'product_image' => $image,
                'product_qty' => $item->getQtyToShip(),
                'options' => $options,
            );
        }

        return $productInfo;
    }

    public function getOrderDetail($id) {
        $detail = array();
        if (!$this->customerLogin())
            return $detail;
        $order = Mage::getModel('sales/order')->loadByIncrementId($id);
      //  Zend_debug::dump($order->getData());die();
        $shipping = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $shipping_street = $shipping->getStreetFull();
        $billing_street = $billing->getStreetFull();
        $email = $this->_getSession()->getCustomer()->getEmail();
        $detail[] = array(
            'order_id' => $id,
            'order_date' => $order->getUpdatedAt(),
            'order_code' => $order->getIncrementId(),
            'order_total' => $order->getGrandTotal(),
            'order_subtotal' => $order->getSubtotal(),
            'tax' => $order->getTaxAmount(),
            's_fee' => $order->getShippingAmount(),
            'order_gift_code' => $order->getCouponCode(),
            'discount' => abs($order->getDiscountAmount()),
            'order_note' => $order->getCustomerNote(),
            'order_items' => $this->getProductFromOrderDetail($order),
            'payment_method' => $order->getPayment()->getMethod(),
            'card_4digits' => '',
            's_method_name' => $order->getShippingDescription(),
            's_name' => $shipping->getName(),
            's_street' => $shipping_street,
            's_city' => $shipping->getCity(),
            's_state_name' => $shipping->getRegion(),
            's_state_code' => $shipping->getRegionCode(),
            's_zip' => $shipping->getPostcode(),
            's_country_name' => $shipping->getCountryModel()->loadByCode($billing->getCountry())->getName(),
            's_country_code' => $shipping->getCountry(),
            's_phone' => $shipping->getTelephone(),
            's_email' => $email,
            'b_name' => $billing->getName(),
            'b_street' => $billing_street,
            'b_city' => $billing->getCity(),
            'b_state_name' => $billing->getRegion(),
            'b_state_code' => $billing->getRegionCode(),
            'b_zip' => $billing->getPostcode(),
            'b_country_name' => $billing->getCountryModel()->loadByCode($billing->getCountry())->getName(),
            'b_country_code' => $billing->getCountry(),
            'b_phone' => $billing->getTelephone(),
            'b_email' => $email,
        );
        return $detail;
    }

    public function getOptions($type, $options) {
        $list = array();
        if ($type == 'bundle') {
            foreach ($options['bundle_options'] as $option) {
                foreach ($option['value'] as $value) {
                    $list[] = array(
                        'option_title' => $option['label'],
                        'option_value' => $value['title'],
                        'option_price' => $value['price'],
                    );
                }
            }
        } elseif ($type == 'configurable') {
            foreach ($options['attributes_info'] as $option) {
                $list[] = array(
                    'option_title' => $option['label'],
                    'option_value' => $option['value'],
                    'option_price' => isset($option['price']) == true ? $option['price'] : 0,
                );
            }
        } elseif ($type == 'simple') {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $list[] = array(
                        'option_title' => $option['label'],
                        'option_value' => $option['value'],
                        'option_price' => isset($option['price']) == true ? $option['price'] : 0,
                    );
                }
            }
        }
        return $list;
    }

}