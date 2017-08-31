<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Contacts
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Contacts index controller
 *
 * @category   Mage
 * @package    Mage_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */


class Sp_Pay4leter_Pay4leterController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {

    }
    public function pay4laterSessionAction(){
    
    	 $session = Mage::getSingleton('checkout/session');
         $session->setPay4laterData($_POST);

    	echo 'sucess';
    }
     public function redirectAction() {

        $session = Mage::getSingleton('checkout/session');
        $session->setPay4laterStandardQuoteId($session->getQuoteId());
        $order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        //order confirmation mail  before payment 
        $order_confirmation_mail = Mage::getStoreConfig('payment/pay4leter/order_confirmation_mail_before_payment');
        if ($order_confirmation_mail == 1) {
            $order->sendNewOrderEmail();
        }
        $order->save();
		$this->getResponse()->setBody($this->getLayout()->createBlock('pay4leter/form_redirect')->toHtml());
        $session->unsQuoteId();
    }

/**
     * When a customer cancel payment from Pay4later.
     */
    public function cancelAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPay4laterStandardQuoteId(true));


        $order_history_comment = '';
        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {

                $order_history_comments = $this->getCheckout()->getPay4laterErrorMessage();
                foreach ($order_history_comments as $order_history_comment) {
                    if ($order_history_comment != '')
                        $order->addStatusHistoryComment($order_history_comment, true);
                }
                $order->cancel()->save();
            }
        }

        /* we are calling getPay4laterStandardQuoteId with true parameter, the session object will reset the session if parameter is true.
          so we don't need to manually unset the session */
        Mage::getSingleton('checkout/session')->addError("Pay4later Payment has been cancelled and the transaction has been declined.");
        if ($order_history_comment != '')
            Mage::getSingleton('checkout/session')->addError($order_history_comment);
        $this->_redirect('checkout/cart');
    }

    /**
     * when Pay4later returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function successAction() {
        echo "here comes from pay4later after success";exit;
        if (!$this->getRequest()->isPost()) {
            $this->cancelAction();
            return false;
        }

        $status = true;

        $response = $this->getRequest()->getPost();
        if (empty($response)) {
            $status = false;
        }

        $encResponse = '';

        $pay4later = Mage::getModel('pay4later/method_pay4later');


        $encryptionkey = Mage::getStoreConfig('payment/pay4later/encryptionkey');
        if (isset($response["encResp"])) {
            $encResponse = $response["encResp"];
        }

        $rcvdString = $pay4later->decrypt($encResponse, $encryptionkey);
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);



        $Order_Id = '';
        $tracking_id = '';
        $order_status = '';
        $response_array = array();

        for ($i = 0; $i < count($decryptValues); $i++) {
            $information = explode('=', $decryptValues[$i]);
            if (count($information) == 2) {
                $response_array[$information[0]] = $information[1];
            }
        }


        if (isset($response_array['order_id']))
            $Order_Id = $response_array['order_id'];
        if (isset($response_array['tracking_id']))
            $tracking_id = $response_array['tracking_id'];
        if (isset($response_array['order_status']))
            $order_status = $response_array['order_status'];
        if (isset($response_array['currency']))
            $currency = $response_array['currency'];
        if (isset($response_array['Amount']))
            $payment_mode = $response_array['Amount'];

        $order_history_comments = '';
        $order_history_keys = array('tracking_id', 'failure_message', 'payment_mode', 'card_name', 'status_code', 'status_message', 'bank_ref_no');
        foreach ($order_history_keys as $order_history_key) {

            if ((isset($response_array[$order_history_key])) && trim($response_array[$order_history_key]) != '') {
                if (trim($response_array[$order_history_key]) == 'null')
                    continue;
                $order_history_comments .= $order_history_key . " : " . $response_array[$order_history_key];
            }
        }

        $order_history_comments_array = array();
        $order_history_comments_array[] = $order_history_comments;

        if ($order_status == "Success") {

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($Order_Id);

            $f_passed_status = Mage::getStoreConfig('payment/pay4later/payment_success_status');
            $message = Mage::helper('Pay4later')->__('Your payment is authorized.');
            $order->setState($f_passed_status, true, $message);


            if ($order_history_comments != '')
                $order->addStatusHistoryComment($order_history_comments, true);
            ///////////////////////////////////
            $payment_confirmation_mail = Mage::getStoreConfig('payment/pay4later/payment_confirmation_mail');
            $order_confirmation_mail = Mage::getStoreConfig('payment/pay4later/order_confirmation_mail_before_payment');
            if ($order_confirmation_mail == 0) {
                $order->sendNewOrderEmail();
            }
            if ($payment_confirmation_mail == "1") {
                $order->sendOrderUpdateEmail(true, 'Your payment is authorized.');
            }
            ////////////////////////////
            $order->save();

            $session = Mage::getSingleton('checkout/session');
            $session->setQuoteId($session->getPay4laterStandardQuoteId(true));
            /**
             * set the quote as inactive after back from Pay4later
             */
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

            $this->_redirect('checkout/onepage/success', array('_secure' => true));
        } else {
            $error_message = " Order Cancel due to order status " . $order_status;
            $order_history_comments_array[] = $error_message;
            $this->getCheckout()->setPay4laterErrorMessage($order_history_comments_array);
            $this->cancelAction();
            return false;
        }
    }

    public function errorAction() {
        $this->_redirect('checkout/onepage/');
    }
}
