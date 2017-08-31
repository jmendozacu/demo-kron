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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpos Helper
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Helper_Payment extends Mage_Core_Helper_Abstract {
    /*
      These are some functions to get payment method information
     */

    public function getCashMethodTitle() {
        $title = Mage::getStoreConfig('payment/cashforpos/title');
        if ($title == '')
            $title = "Cash ( For Web POS only)";
        return $title;
    }

    public function isCashPaymentEnabled() {
        return (Mage::getStoreConfig('payment/cashforpos/active') && $this->isAllowOnWebPOS('cashforpos'));
    }

    public function getCcMethodTitle() {
        $title = Mage::getStoreConfig('payment/ccforpos/title');
        if ($title == '')
            $title = "Cash ( For Web POS only)";
        return $title;
    }

    public function isCcPaymentEnabled() {
        return (Mage::getStoreConfig('payment/ccforpos/active') && $this->isAllowOnWebPOS('ccforpos'));
    }

    public function isWebposShippingEnabled() {
        return Mage::getStoreConfig('carriers/webpos_shipping/active');
    }

    public function getCp1MethodTitle() {
        $title = Mage::getStoreConfig('payment/cp1forpos/title');
        if ($title == '')
            $title = "Web POS - Custom Payment 1";
        return $title;
    }

    public function isCp1PaymentEnabled() {
        return (Mage::getStoreConfig('payment/cp1forpos/active') && $this->isAllowOnWebPOS('cp1forpos'));
    }

    public function getCp2MethodTitle() {
        $title = Mage::getStoreConfig('payment/cp2forpos/title');
        if ($title == '')
            $title = "Web POS - Custom Payment 2";
        return $title;
    }

    public function isCp2PaymentEnabled() {
        return (Mage::getStoreConfig('payment/cp2forpos/active') && $this->isAllowOnWebPOS('cp2forpos'));
    }

    public function getCodMethodTitle() {
        $title = Mage::getStoreConfig('payment/codforpos/title');
        if ($title == '')
            $title = "Web POS - Cash On Delivery";
        return $title;
    }

    public function isCodPaymentEnabled() {
        return (Mage::getStoreConfig('payment/codforpos/active') && $this->isAllowOnWebPOS('codforpos'));
    }

    public function getMultipaymentMethodTitle() {
        $title = Mage::getStoreConfig('payment/multipaymentforpos/title');
        if ($title == '')
            $title = "Web POS - Split Payments";
        return $title;
    }

    public function getMultipaymentActiveMethodTitle() {
        $payments = Mage::getStoreConfig('payment/multipaymentforpos/payments');
        if ($payments == '')
            $payments = explode(',', 'cp1forpos,cp2forpos,cashforpos,ccforpos,codforpos');
        return explode(',', $payments);
    }

    public function isMultiPaymentEnabled() {
        return (Mage::getStoreConfig('payment/multipaymentforpos/active') && $this->isAllowOnWebPOS('multipaymentforpos'));
    }

    public function isAllowOnWebPOS($code) {
        $allowPayments = Mage::getModel('webpos/source_adminhtml_payment')->getAllowPaymentMethods();
        if (Mage::getStoreConfig('webpos/payment/allowspecific_payment', Mage::app()->getStore()->getId()) == '1') {
            $specificpayment = Mage::getStoreConfig('webpos/payment/specificpayment', Mage::app()->getStore()->getId());
            $specificpayment = explode(',', $specificpayment);
            if (in_array($code, $specificpayment)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
	
	public function getDefaultPaymentMethod(){
		return Mage::getStoreConfig('webpos/payment/defaultpayment');
	}
	
	public function updateCashTransactionFromOrder($order,$newCash){
		try{
			$enable_till = Mage::getStoreConfig('webpos/general/enable_tills');
			if ($order->getIncrementId() && $enable_till) {
				$payment_method = $order->getPayment()->getMethodInstance()->getCode();
				$cashIn = (float)$order->getData('webpos_cash');
				if($newCash >= $cashIn){
					$cashIn = (float)($newCash - $cashIn);
					$cashOut = 0;
				}else{
					$cashOut = (float)($cashIn - $newCash);
					$cashIn = 0;
				}
				$data_transaction = array(
					'payment_method' => $payment_method,
					'cash_in' => $cashIn,
					'cash_out' => $cashOut,
					'amount' => $cash,
					'store_id' => $order->getStoreId(),
					'user_id' => $order->getWebposAdminId(),
					'order_id' => $order->getIncrementId(),
					'till_id' => $order->getTillId(),
					'location_id' => $order->getLocationId()
				);

				if ($order->getData('webpos_cash') > 0) {
					Mage::getModel('webpos/transaction')->saveTransactionData($data_transaction);
				}
			}
		}catch(Exception $e){
			
		}
	}
}
