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
 * Madapter Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
//    public function preDispatch() {
//        parent::preDispatch();
//        if (!Mage::getSingleton('customer/session')->isLoggedIn()
//                && !Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout()
//        ) {
//            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
//            $this->_message($this->__('Customer not logged in.'), self::MESSAGE_STATUS_ERROR, array(
//                'logged_in' => '0'
//            ));
//            return;
//        }
//    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function apiAction() {
        $url_action = $this->getRequest()->getParam('action');
//        if (!Mage::helper('madapter')->checkApiKey($url_action))
//            return;

        switch ($url_action) {
            case 'get_spot_products':
                $limit = $this->getRequest()->getParam('limit');
                if (!isset($limit) || $limit <= 0) {
                    $limit = 10;
                }
                $productList = Mage::getModel('madapter/product')->getProductsBestSeller($limit);
                $productListJson = Mage::helper('madapter')->encodeSpotProductJson('productList', $productList);
                echo $productListJson;
                break;
            case 'get_product_detail':
                $product_id = $this->getRequest()->getParam('product_id');
                $product = Mage::getModel('madapter/product')->getDetailProduct($product_id);
                $productJson = Mage::helper('madapter')->encodeJson('productDetail', $product);
                echo $productJson;
                break;
            case 'get_related_products':
                $limit = $this->getRequest()->getParam('limit');
                if (!isset($limit) || $limit <= 0) {
                    $limit = 5;
                }
                $product_id = $this->getRequest()->getParam('product_id');
                $productList = Mage::getModel('madapter/product')->getRelateProducts($product_id, $limit);
                $productJson = Mage::helper('madapter')->encodeJson('relatedProductList', $productList);
                echo $productJson;
                break;
            case 'get_category_products':
                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];
                $category_id = $this->getRequest()->getParam('category_id');
                $sort_option = $this->getRequest()->getParam('sort_option');
                $productList = Mage::getModel('madapter/product')->getCategoryProducts($category_id, $limit, $offset,null ,$sort_option);
                array_shift($productList);
                $productJson = Mage::helper('madapter')->encodeJson('productList', $productList);
                echo $productJson;
                break;
            case 'get_cart_products':
                $product_ids = $this->getRequest()->getParam('product_id_string');
                $productList = Mage::getModel('madapter/product')->getCartProducts($product_ids);
                $productJson = Mage::helper('madapter')->encodeJson('productList', $productList);
                echo $productJson;
                break;
            case 'search_products':
                $keyword = $this->getRequest()->getParam('key_word');
                $category_id = null;
                if ($this->getRequest()->getParam('category_id')) {
                    $category_id = $this->getRequest()->getParam('category_id');
                }
                $sort_option = $this->getRequest()->getParam('sort_option');

                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];

                $productList = Mage::getModel('madapter/product')->getSearchProducts($keyword, $category_id, $sort_option, $offset, $limit);
                if (!$offset) {
                    $productJson = Mage::helper('madapter')->encodeJsonSearch('productList', $productList, $productList[0]);
                } else {
                    $productJson = Mage::helper('madapter')->encodeJsonSearch('productList', $productList, 0);
                }
                echo $productJson;
                break;
            case 'get_categories':
                $category_id = null;
                if ($this->getRequest()->getParam('category_id')) {
                    $category_id = $this->getRequest()->getParam('category_id');
                }

                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];
                $categoryList = Mage::getModel('madapter/category')->getCategories($category_id, $limit, $offset);
                $categoryJson = Mage::helper('madapter')->encodeJson('categoryList', $categoryList);
                echo $categoryJson;
                break;
            case 'place_order':
                $data = $this->getRequest()->getParams();
//                 $data['customer_id'] = '';
//                 $data['b_address_id'] = '';
//                 $data['b_name'] = 'asdsadas';
//                 $data['b_street'] = 'dasdasdasd';
//                 $data['b_city'] = 'sdfghjk';
//                 $data['b_state_id'] = '12';
//                 $data['b_state_name'] = 'erty';
//                 $data['b_zip'] = '95050';
//                 $data['b_country_code'] = 'US';
//                 $data['b_phone'] = '841699145958';
//                 $data['b_email'] = 'ta@gmail.com';
//                 $data['s_address_id'] = '';
//                 $data['s_name'] = 'asdsadas';
//                 $data['s_street'] = 'dasdasdasd';
//                 $data['s_city'] = 'sdfghjk';
//                 $data['s_state_id'] = '12';
//                 $data['s_state_name'] = 'erty';
//                 $data['s_zip'] = '95050';
//                 $data['s_country_code'] = 'US';
//                 $data['s_phone'] = '841699145958';
//                 $data['s_email'] = 'ta@gmail.com';
//                 $data['s_method_code'] = 'freeshipping_freeshipping';
//                 $data['payment_method'] = 'ON_DELIVERY';
//                 $data['order_items'] = '[{"product_id": 166, "product_qty": 1}]';
//                 $data['is_buy_now'] = 'YES';
                $cart = Mage::getModel('madapter/cart');
                Mage::getSingleton('core/session')->setData('order_info', $data);
                Mage::getSingleton('core/session')->setData('check_method', 0);
                $cart->setData('info', $data);
                if ($data['is_buy_now'] == 'YES') {
                    $result = $cart->addCart(1);
                    if (!$result) {
                        echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
                        break;
                    }
                }

                $this->_redirect('madapter/checkout/index');
                break;
            case 'update_order_coupon':
                $data = $this->getRequest()->getParams();
                $cart = Mage::getSingleton('madapter/cart');
                $return = array();
                if (isset($data['coupon_code']) && $data['coupon_code'])
                    $return = $cart->setCouponCode($data['coupon_code']);
                echo Mage::helper('madapter')->encodeJson('couponInfo', $return);
                break;
            case 'register':
                $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->registerCustomer($data);
                break;
            case 'sign_in':
                $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->login($data);
                break;
            case 'sign_out':
                //$data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->logout();
                break;
            case 'change_password':
                $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->changePassword($data);
                break;
            case 'get_profile':
                //$data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->getProfile();
                break;
            case 'get_user_addresses':
                // $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->getAddress();
                break;
            case 'get_order_config':
                $data = $this->getRequest()->getParams();
                $order = Mage::getModel('madapter/order');

                echo $order->getShippingMethods($data);
                break;
            case 'get_order_history':
                $order = Mage::getModel('madapter/order');
                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];
                $list = $order->getOrdershistory($limit, $offset);
                echo Mage::helper('madapter')->encodeJson('orderList', $list);
                break;
            case 'get_order_detail':
                $id = $this->getRequest()->getParam('order_id');
                $order = Mage::getModel('madapter/order');
                $detail = $order->getOrderDetail($id);
                echo Mage::helper('madapter')->encodeJson('orderDetail', $detail);
                break;
            case 'get_product_reviews':
                $productId = $this->getRequest()->getParam('product_id');
                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];
                $star = $this->getRequest()->getParam('star');
                $review = Mage::getModel('madapter/product')->getProductReview($productId, $limit, $offset, null, $star);
                echo $review;
                break;
            case 'update_payment_status':
                $data = $this->getRequest()->getParams();
                // $data['payment_status'] = 'PRO';
                // $data['transaction_id'] = '234567890987654wert';
                // $data['invoice_number'] = '100000045';
                // $data['last_four_digits'] = 'wddadad';
                // $data['fund_source_type'] = 'VISA';
                Mage::getSingleton('core/session')->setPaymentData($data);
                $this->_redirect('madapter/checkout/updatePayment');
                break;
            case 'save_address_book':
                $data = $this->getRequest()->getParams();
                if (Mage::getModel('madapter/customer')->saveAddress($data, 1)) {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS'));
                } else {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
                }
                break;
            case 'get_address_book':
                $list = Mage::getModel('madapter/customer')->getAddressBook();
                echo Mage::helper('madapter')->encodeJson('addressBook', $list);
                break;
            case 'save_address':
                $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->saveAddress($data);
                break;
            case 'save_order_address':
                $data = $this->getRequest()->getParams();
                echo Mage::getModel('madapter/customer')->saveAddress($data);
                break;
            case 'get_states':
                $data = $this->getRequest()->getParam('country_code');
                //$data = strtolower($data);
                $_state = Mage::getModel('madapter/state');
                $states = $_state->getStates($data);
                echo Mage::helper('madapter')->encodeJson('stateList', $states);
                break;
            case 'get_allowed_countries':
                $_country = Mage::getModel('madapter/country');
                $countries = $_country->getAllowedCountries();
                echo Mage::helper('madapter')->encodeJson('countryList', $countries);
                break;
            case 'get_country_config':
                $_country = Mage::getModel('madapter/country');
                $default_country = $_country->getDefaultCountry();
                if ($default_country) {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS', 'defaultCountry' => $default_country));
                } else {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
                }
                break;
            case 'get_currency_symbol':
                $_country = Mage::getModel('madapter/country');
                $currency_symbol = $_country->getCurrencySymbol();
                if ($currency_symbol) {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS', 'currency_symbol' => $currency_symbol));
                } else {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
                }
                break;
            case 'add_to_cart':
                $data = $this->getRequest()->getParams();
                $cart = Mage::getModel('madapter/cart');
                $cart->setData('info', $data);
                $result = $cart->addCart();
                if ($result == true) {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS', 'cart_item_id' => Mage::getSingleton('core/session')->getItemIdMobile()));
                } else {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL', 'message' => $result));
                }
                break;
            case 'edit_cart':
                $data = $this->getRequest()->getParams();
                $cart = Mage::getModel('madapter/cart');
                $cart->setData('info', $data);
                $result = $cart->updateCart();
                if ($result) {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS'));
                } else {
                    echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
                }
                break;
            case 'get_customer_cart':
                $result = Mage::getModel('madapter/customer')->getCart();
                echo Mage::helper('madapter')->encodeJson('cartItemList', $result);
                break;
            case 'check_options':
                $data = $this->getRequest()->getParams();
                $result = Mage::getModel('madapter/product')->checkOption($data);
                echo Mage::helper('madapter')->encodeJson('optionIdList', $result);
                break;
            case 'get_merchant_info':
                $list = Mage::helper('madapter')->getMechantInfo();
                echo json_encode($list);
                break;
            case 'get_all_products':
                $arry_off_limt = Mage::helper('madapter')->checkOffLimit($this->getRequest()->getParam('limit'), $this->getRequest()->getParam('offset'));
                $limit = (int) $arry_off_limt['limit'];
                $offset = (int) $arry_off_limt['offset'];

                $productsCollection = Mage::getModel('madapter/product')->getProductsCollection();
                $sort_option = $this->getRequest()->getParam('sort_option');
                $productList = Mage::getModel('madapter/product')->getListProduct($productsCollection, $offset, $limit, $sort_option);
                if (!$offset) {
                    $productJson = Mage::helper('madapter')->encodeJsonSearch('productList', $productList, $productList[0]);
                } else {
                    $productJson = Mage::helper('madapter')->encodeJsonSearch('productList', $productList, 0);
                }
                echo $productJson;
                break;
            case 'get_banner':
                $list = Mage::getModel('madapter/banner')->getBannerList();
                echo Mage::helper('madapter')->encodeJson('bannerList', $list);
                break;
            case 'register_device':
                $data = $this->getRequest()->getParams();
                $device = Mage::getModel('madapter/device')->setDataDevice($data);
                $result = array('status' => $device);
                echo json_encode($result);
                break;
            case 'check_login_status':
                // Zend_debug::dump(get_class_methods(Mage::getSingleton('customer/session')));
                $data = $this->getRequest()->getParams();
                $pass = $data['token'];
                $user_id = $data['user_id'];
                if (!$user_id || !$pass) {
                    $result = array('status' => 'FAIL');
                    echo json_encode($result);
                    break;
                }
                $customer = Mage::getModel('customer/customer')->load($user_id);
                if ($pass == $customer->getPasswordHash()) {
                    $customer_session = Mage::getSingleton('customer/session');
                    $t = $customer_session->loginById($user_id);
                    $result = array('status' => 'SUCCESS');
                } else {
                    $result = array('status' => 'FAIL');
                }
                echo json_encode($result);
                break;
            case 'update_paypal_payment':
                $data = $this->getRequest()->getParams();
                Mage::getSingleton('core/session')->setPaymentData($data);
                $this->_redirect('madapter/checkout/updatePayPal');
                break;
            case 'get_payment_gateway_config':
                $data = $this->getRequest()->getParams();
                echo Mage::helper('madapter')->getPaymentAway();
                break;
            case 'save_shipping_method':
                $data = $this->getRequest()->getParams();
                try {
                    $result = Mage::getSingleton('checkout/type_onepage')->saveShippingMethod($data['s_method_code']);
                    if (!$result) {
                        Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(),
                            'quote' => Mage::getSingleton('checkout/type_onepage')->getQuote()));
                    }
                    Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
                    $total = Mage::getSingleton('checkout/session')->getQuote()->getTotals();
                    $tax = 0;
                    if (isset($total['tax']) && $total['tax']->getValue()) {
                        $tax = $total['tax']->getValue(); //Tax value if present
                    } else {
                        $tax = 0;
                    }
                    $result_r = array('status' => 'SUCCESS', 'grand_total' => Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal(), 'tax' => $tax);
                    echo json_encode($result_r);
                } catch (Exception $e) {
                    $result_r = array('status' => 'FAIL');
                    echo json_encode($result_r);
                }
                break;
            case 'test':
                $grandTotal = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                Zend_debug::dump($grandTotal->getRealOrderId());
                Zend_debug::dump(get_class_methods($grandTotal));
                //Zend_debug::dump(get_class_methods(Mage::getSingleton('checkout/session')));
                break;
        }
    }

}