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
 * SimiPOS Invoice Cash Total Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Method_Paypalhere extends Mage_Payment_Block_Info
{
    /**
     * Prepare information specific to current payment method
     *
     * @param Varien_Object|array $transport
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
    	$transport   = parent::_prepareSpecificInformation($transport);
    	
    	$info        = array();
    	$paymentInfo = $this->getPaymentInfo();
    	
    	// Public Values
    	if (isset($paymentInfo['Type'])) $info[$this->__('Type')] = $paymentInfo['Type'];
    	
    	if (!$this->getIsSecureMode()
    	   && isset($paymentInfo['Type'])
    	   && $paymentInfo['Type'] != 'Cash'
    	   && isset($paymentInfo['TxId']) && $paymentInfo['TxId']
    	) { // Only showed on backend, loaded info from Paypal server
    		$info[$this->__('Unique Transaction ID')] = $paymentInfo['TxId'];
    		
    		$serverInfo = $this->getMethod()->getTransaction($paymentInfo['TxId']);
    		if (isset($serverInfo['RECEIVEREMAIL'])) $info[$this->__('Receiver Email')] = $serverInfo['RECEIVEREMAIL'];
    		
    		if (isset($serverInfo['PAYERID'])) $info[$this->__('Payer ID')] = $serverInfo['PAYERID'];
    	    if (isset($serverInfo['EMAIL'])) {
                $info[$this->__('Payer Email')] = $serverInfo['EMAIL'];
            } elseif (isset($paymentInfo['Email'])) {
            	$info[$this->__('Payer Email')] = $paymentInfo['Email'];
            }
    		if (isset($serverInfo['PAYERSTATUS'])) $info[$this->__('Payer Status')] = $serverInfo['PAYERSTATUS'];
    		
    		if (isset($serverInfo['PROTECTIONELIGIBILITY'])) $info[$this->__('Merchant Protection Eligibility')] = $serverInfo['PROTECTIONELIGIBILITY'];
    		
    		if (isset($serverInfo['RECEIPTID'])) $info[$this->__('Receipt ID')] = $serverInfo['RECEIPTID'];
    		if (isset($serverInfo['INVNUM'])) {
    			$info[$this->__('Invoice Number')] = $serverInfo['INVNUM'];
    		} elseif (isset($paymentInfo['InvoiceId'])) {
    			$info[$this->__('Invoice Number')] = $paymentInfo['InvoiceId'];
    		}
            if (isset($serverInfo['PAYMENTSTATUS'])) $info[$this->__('Payment Status')] = $serverInfo['PAYMENTSTATUS'];
    		
    		if (isset($serverInfo['CORRELATIONID'])) $info[$this->__('Last Correlation ID')] = $serverInfo['CORRELATIONID'];
    		if (isset($serverInfo['TRANSACTIONID'])) $info[$this->__('Last Transaction ID')] = $serverInfo['TRANSACTIONID'];
    	} else { // Public
    		if (isset($paymentInfo['Email'])) $info[$this->__('Email')] = $paymentInfo['Email'];
    	}
    	return $transport->addData($info);
    }
    
    /**
     * get basic payment info
     * 
     * @return array
     */
    protected function getPaymentInfo()
    {
        $infoArray = array();
    	if ($this->getInfo()->getPoNumber()) {
    	   foreach (explode('&', $this->getInfo()->getPoNumber()) as $part) {
              list($key, $value) = explode('=', $part);
    		  $infoArray[$key] = $value;
    	   }
    	}
    	return $infoArray;
    }
}
