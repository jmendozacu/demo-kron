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
 * SimiPOS Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Observer
{
    /**
     * Process save oder / order_grid for each sale
     */
    public function quoteSubmitBefore($observer)
    {
        $quote = $observer['quote'];
        if (!$quote->getSimiposUser()) {
            return ;
        }
        $order = $observer['order'];
        $order->setSimiposUser($quote->getSimiposUser());
        $order->setSimiposEmail($quote->getSimiposEmail());
    }
    
    public function updateInvoiceView($observer)
    {
        $block = $observer['block'];
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Payment)) {
            return ;
        }
        if ($block->getAction()->getFullActionName() != 'adminhtml_sales_order_invoice_view') {
            return ;
        }
        $invoiceId = $block->getParentBlock()->getInvoice()->getIncrementId() . '.png';
        if (!file_exists(Mage::getBaseDir('media') . DS . 'simipos' . DS . $invoiceId)) {
            return ;
        }
        $signature = Mage::getBaseUrl('media') . 'simipos/' . $invoiceId;
        $transport = $observer['transport'];
        $html  = $transport->getHtml();
        $html .= '<table><tbody><tr>
    <td>' . Mage::helper('simipos')->__('Customer Signature') . '</td>
    <td><img style="max-width: 200px;" src="' . $signature . '" /></td>
</tr></tbody></table>';
        $transport->setHtml($html);
    }
    
    public function paypalPrepareLineItems($observer)
    {
        if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
            if ($paypalCart = $observer->getPaypalCart()) {
                $salesEntity = $paypalCart->getSalesEntity();
                
                $baseDiscount = $salesEntity->getSimiposBaseCash();
                if ($baseDiscount > 0.0001) {
                    $paypalCart->updateTotal(
                        Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,
                        (float)$baseDiscount,
                        Mage::helper('simipos')->__('Cash In')
                    );
                }
            }
            return ;
        }
        $salesEntity = $observer->getSalesEntity();
        $additional = $observer->getAdditional();
        if ($salesEntity && $additional) {
            $baseDiscount = $salesEntity->getSimiposBaseCash();
            if ($baseDiscount > 0.0001) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                    'name'  => Mage::helper('simipos')->__('Cash In'),
                    'qty'   => 1,
                    'amount'=> -(float)$baseDiscount,
                ));
                $additional->setItems($items);
            }
        }
    }
    
    public function quoteItemSetProduct($observer)
    {
        $product = $observer['product'];
        if ($product->getSku() != 'simipos-customsale') {
            return ;
        }
        $name = $product->getCustomOption('name');
        if ($name && $name->getValue()) {
            $item = $observer['quote_item'];
            $item->setName($name->getValue());
        }
    }
    
    public function saveCustomerAddress($observer)
    {
    	$address = $observer['customer_address'];
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    	if ($customer && $customer->getId()
    	   && $customer->getDefaultBilling() == $address->getId()
    	) {
    		try {
    			Mage::getResourceModel('simipos/customer')->updateTelephone($customer->getId(), $address->getTelephone());
    		} catch (Exception $e) {}
    	}
    }
    
    public function prepareSaveCustomer($observer)
    {
    	$request = $observer['request'];
    	
    	$accountData = $request->getParam('account');
    	if (empty($accountData['default_billing'])) {
    		return ;
    	}
    	
    	$addresses = $request->getParam('address');
    	if (empty($addresses) || !is_array($addresses)
    	   || empty($addresses[$accountData['default_billing']])
    	   || empty($addresses[$accountData['default_billing']]['telephone'])
    	) {
    		return ;
    	}
    	
        $customer = $observer['customer'];
        Mage::getResourceModel('simipos/customer')->updateTelephone(
            $customer->getId(),
            $addresses[$accountData['default_billing']]['telephone']
        );
    }
    
    public function orderPaymentPlaceStart($observer)
    {
    	$payment = $observer['payment'];
    	$order = $payment->getOrder();
    	if ($order->getSimiposBaseCash() > 0.0001) {
    		// Update paid info
    		$was = $payment->getDataUsingMethod('base_amount_paid');
    		$payment->setDataUsingMethod('base_amount_paid', $was + $order->getSimiposBaseCash());
    		
    		$was = $payment->getDataUsingMethod('amount_paid');
    		$payment->setDataUsingMethod('amount_paid', $was + $order->getSimiposCash());
    	}
    }
}
