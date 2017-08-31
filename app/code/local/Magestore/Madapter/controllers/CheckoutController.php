<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Madapter_CheckoutController extends Mage_Core_Controller_Front_Action {

    protected function _ajaxRedirectResponse() {
        $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Session Expired')
                ->setHeader('Login-Required', 'true')
                ->sendResponse();
        return $this;
    }

    protected $_oldQuote;

    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function _getHelper() {
        return Mage::helper('madapter');
    }

    public function indexAction() {
        $data = Mage::getSingleton('core/session')->getData('order_info');

        if (!$this->indexPlace()) {
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
            $this->cleanSession();
            return;
        }
        if (isset($data['customer_id']) && $data['customer_id']) {
            $this->saveMethod('customer');
        } elseif (isset($data['customer_password']) && $data['customer_password']) {
            $this->saveMethod('register');
        } else {
            $this->saveMethod('guest');
        }
        $this->saveBilling($data);
        $this->saveShipping($data);
        if (!$this->saveShippingMethod($data['s_method_code'])) {
            $this->saveShipping($data);
            $this->saveShippingMethod($data['s_method_code']);
        }
        $this->savePayment($data['payment_method']);
        $reuslt = $this->saveOrder($data['payment_method']);		
        if ($reuslt['success'] == true) {
            //Zend_Debug::dump($this->_getCheckoutSession()->getLastRealOrderId());die();
            if (!$this->_getCheckoutSession()->getLastRealOrderId()) {				
                $arr = array(
                    'status' => 'FAIL',
                );
            } else {
                $arr = array(
                    'invoice_number' => $this->_getCheckoutSession()->getLastRealOrderId(),
                    'status' => 'SUCCESS',
                );
            }
            $this->cleanSession();
            echo Mage::helper('core')->jsonEncode($arr);
        } else {
            $this->cleanSession();
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
        }

        if ($data['is_buy_now'] == 'YES' && $this->_oldQuote != null) {
            $this->_getCheckoutSession()->replaceQuote($this->_oldQuote);
        }
        return;
    }

    public function indexPlace() {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            return false;
        }
        $quote = $this->_getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
        	//Error at this
            return false;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            return false;
        }
        $this->_getCheckoutSession()->setCartWasUpdated(false);
        $this->_getOnepage()->initCheckout();
        return true;
    }

    public function cleanSession() {
        $session = $this->_getOnepage()->getCheckout();
        $lastOrderId = $session->getLastOrderId();
        $this->_oldQuote = $session->getData('old_quote');
        $session->clear();
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
    }

    public function saveMethod($method) {
        $this->_getOnepage()->saveCheckoutMethod($method);
    }

    public function saveBilling($data) {
        $billing = $this->_getHelper()->convertDataBilling($data);
        //Zend_debug::dump($billing);die();
        $this->_getOnepage()->saveBilling($billing, $billing['customer_address_id']);
    }

    public function saveShipping($data) {
        $shipping = $this->_getHelper()->convertDataShipping($data);
        $this->_getOnepage()->saveShipping($shipping, $shipping['customer_address_id']);
        //$this->_getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates($data['shipment_method_id']);
        $this->_getCheckoutSession()->getQuote()->getShippingAddress()->collectShippingRates()->save();
        //$this->_getOnepage()->getQuote()->collectTotals()->save();
    }

    public function saveShippingMethod($method) {
        $result = $this->_getOnepage()->saveShippingMethod($method);
        if (!$result) {
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(),
                'quote' => $this->_getOnepage()->getQuote()));
        }
        $this->_getOnepage()->getQuote()->collectTotals()->save();
        return true;
    }

    public function savePayment($method) {
        if ($method == 'COD') {
            $method = array('method' => 'cashondelivery');
        } elseif ($method == 'ZOOZ_MOBILE') {
            $method = array('method' => 'zooz');
        } elseif ($method == 'PAYPAL_MOBILE') {
            $method = array('method' => 'paypal_mobile');
        } elseif ($method == 'BANK_MOBILE') {
            $method = array('method' => 'transfer_mobile');
        }else{
            $method = array('method' => 'checkmo');
        }
        //Zend_debug::dump($method);die();
        $this->_getOnepage()->savePayment($method);
        // $this->_getOnepage()->getQuote()->getPayment()->importData($method);
    }

    public function saveOrder($payment_method) {
        $result = array();
        $result['success'] = true;
        try {
            // if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                // $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                // if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    // return $result['success'] = false;
                // }
            // }
            if (Mage::getSingleton('core/session')->getData('check_method') == 0) {
                Mage::getSingleton('core/session')->setData('check_method', 1);
                $redirect = Mage::getUrl('madapter/checkout/index/');
                Header('Location: ' . $redirect);
                exit();
            }
            // if ($payment_method == 'ON_MOBILE') {
            // return $result;
            // }
            $this->_getOnepage()->saveOrder();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_getOnepage()->getCheckout()->setUpdateSection(null);
			$result['success'] = false;
        } catch (Exception $e) {
            Mage::logException($e);
			$result['success'] = false;
        }
        $this->_getOnepage()->getQuote()->save();
        return $result;
    }

    protected $_order;

    protected function _getOrder($orderId) {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
                return;
            }
        }
        if (!$this->_order->canInvoice())
            return FALSE;
        return $this->_order;
    }

    protected function _initInvoice($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order)
            return false;
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }

        //Zend_debug::dump(get_class_methods($order));die();
        Mage::getModel('madapter/madapter')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', $data['fund_source_type'])
                ->setData('transaction_dis', $data['last_four_digits'])
                ->setData('transaction_email', $data['transaction_email'])
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                ->setData('status', $data['payment_status'])
                ->setData('order_id', $order->getId())
                ->save();
        Mage::getSingleton('core/session')->setOrderIdForEmail($order->getId());
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($items);
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->setEmailSent(true)->register();
        //$invoice->setTransactionId();
        Mage::register('current_invoice', $invoice);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
        $transactionSave->save();
        //if ($data)
        //$order->sendOrderUpdateEmail();
        $order->sendNewOrderEmail();
        Mage::getSingleton('core/session')->setOrderIdForEmail(null);
        return true;
    }

    public function updatePaymentAction() {
        $data = Mage::getSingleton('core/session')->getPaymentData();
        //Zend_debug::dump($data);die();
        Mage::log($data);
        if ($data['payment_status'] == 'PENDING' || !$data['transaction_id']
                || !$data['invoice_number']) {
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
            Mage::getSingleton('core/session')->setPaymentData(null);
            return;
        }
        try {
            if ($this->_initInvoice($data['invoice_number'], $data))
                echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS'));
            else
                echo Mage::helper('core')->jsonEncode(array('status' => 'PENDING'));
        } catch (Exception $e) {
            Mage::logException($e);
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
        }
        Mage::getSingleton('core/session')->setPaymentData(null);
        return;
    }

    // for paypal mobile
    public function updatePayPalAction() {
        $dataComfrim = Mage::getSingleton('core/session')->getPaymentData();
        $confirm = $dataComfrim['paypal_confirm'];
        $confirm_db = json_decode($confirm);
        $data = array();
        if (is_object($confirm_db->proof_of_payment->adaptive_payment)) {
            $data = Mage::helper('madapter/url')->getResponseBody($confirm_db->proof_of_payment->adaptive_payment, 1);
            $data['invoice_number'] = $dataComfrim['invoice_number'];
        } else {
            $data = Mage::helper('madapter/url')->getResponseBody($confirm_db->proof_of_payment->rest_api, 0);
            $data['invoice_number'] = $dataComfrim['invoice_number'];
        }
        Mage::log($data);
        if ($data['payment_status'] == 'PENDING' || !$data['transaction_id']
                || !$data['invoice_number']) {
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
            Mage::getSingleton('core/session')->setPaymentData(null);
            return;
        }
        try {
            if ($this->_initInvoice($data['invoice_number'], $data))
                echo Mage::helper('core')->jsonEncode(array('status' => 'SUCCESS'));
            else
                echo Mage::helper('core')->jsonEncode(array('status' => 'PENDING'));
        } catch (Exception $e) {
            Mage::logException($e);
            echo Mage::helper('core')->jsonEncode(array('status' => 'FAIL'));
        }
        Mage::getSingleton('core/session')->setPaymentData(null);
        return;
    }

}

?>
