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
 * SimiPOS Cash Total Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Total_Quote_Cash extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('simipos_cash');
    }
    
    /**
     * Prepare Data to Storage for Order
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_SimiPOS_Model_Total_Quote_Cash
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->getSimiposCash() < 0.0001) {
            return $this;
        }
        // Order Cash
        $address->setSimiposCash($quote->getSimiposCash())
            ->setSimiposBaseCash($quote->getSimiposBaseCash());
        // Update total Paid and Refund
        $address->setTotalPaid($quote->getSimiposCash())
            ->setBaseTotalPaid($quote->getSimiposBaseCash());
        if ($quote->getSimiposCash() >= $address->getGrandTotal()) {
            $address->setTotalRefunded($quote->getSimiposCash() - $address->getGrandTotal())
                ->setBaseTotalRefunded($quote->getSimiposBaseCash() - $address->getBaseGrandTotal());
            if ($payment = $quote->getPayment()) {
            	if ($method  = $payment->getMethodInstance()) {
            		if ($method->getCode() == 'cashin') {
            			// prepair for cashin invoice
            			$address->setTotalPaid($address->getTotalRefunded())
            			    ->setBaseTotalPaid($address->getBaseTotalRefunded());
            		}
            	}
            }
        }
        return $this;
    }
    
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
