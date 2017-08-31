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
 * Simipos Order Print Data Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Pdf_Order extends Mage_Sales_Model_Order_Pdf_Abstract
{
	protected function _getHeaderData()
	{
		return array(
		    Mage::helper('sales')->__('Products'),
		    Mage::helper('sales')->__('SKU'),
		    Mage::helper('sales')->__('Qty'),
		    Mage::helper('sales')->__('Price'),
		    Mage::helper('sales')->__('Tax'),
		    Mage::helper('sales')->__('Subtotal')
		);
	}
	
	public function getPdf()
	{
		// Overide PDF abstract method
	}
	
	protected function _getPrintLogo($store)
	{
		$image = Mage::getStoreConfig('sales/identity/logo', $store);
		if ($image) {
			$imageFile = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
			if (is_file($imageFile)) {
				return Mage::getBaseUrl('media') . 'sales/store/logo/' . $image;
			}
		}
		return '';
	}
	
	protected function _getAddress($store)
	{
		$address = array();
		foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value) {
			if (!empty($value)) {
				$value  = preg_replace('/<br[^>]*>/i', "\n", $value);
				$address[] = $value;
			}
		}
		return $address;
	}
	
	protected function _getOrderInfo($order)
	{
	    $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
	    
	    $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
	        ->setArea('adminhtml')
            ->setIsSecureMode(true)
            ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);
        $paymentInfo = array();
        foreach ($payment as $value) {
        	if (trim($value) != '') {
        		$value = preg_replace('/<br[^>]*>/i', "\n", $value);
        		$paymentInfo[] = $value;
        	}
        }
	    
        if ($order->getIsVirtual()) {
            $orderInfo = array(
                array(
                    'header' => Mage::helper('sales')->__('Sold to:'),
                    'text'   => $billingAddress
                ),
                array(
                    'header' => Mage::helper('sales')->__('Payment Method:'),
                    'text'   => $paymentInfo
                )
            );
        } else {
        	$shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
        	
        	$shippingMethod  = $order->getShippingDescription();
        	$shippingMethodData = array();
        	$shippingMethodData[] = $shippingMethod;
        	$shippingMethodData[] = ' ';
        	$shippingMethodData[] = '(' . Mage::helper('sales')->__('Total Shipping Charges') . ' '
                . $order->formatPriceTxt($order->getShippingAmount()) . ')';
        	
            $orderInfo = array(
                array(
                    'header' => Mage::helper('sales')->__('Sold to:'),
                    'text'   => $billingAddress
                ),
                array(
                    'header' => Mage::helper('sales')->__('Ship to:'),
                    'text'   => $shippingAddress
                ),
                array(
                    'header' => Mage::helper('sales')->__('Payment Method:'),
                    'text'   => $paymentInfo
                ),
                array(
                    'header' => Mage::helper('sales')->__('Shipping Method:'),
                    'text'   => $shippingMethodData
                )
            );
        }
        return $orderInfo;
	}
	
	public function _getTotalsData($source, $order)
	{
		$totalsData = array();
		$totals = $this->_getTotalsList($source);
		foreach ($totals as $total) {
			$total->setOrder($order)
                ->setSource($source);
            if ($total->canDisplay()) {
            	foreach ($total->getTotalsForDisplay() as $totalData) {
            		$totalsData[] = array(
            		    'label'   => $totalData['label'],
            		    'amount'  => $totalData['amount']
            		);
            	}
            }
		}
		return $totalsData;
	}
	
	public function getPdfData($order)
	{
		$this->_beforeGetPdf();
		
        $pdf = array();
        $store = Mage::app()->getStore($order->getStoreId());
		Mage::app()->setCurrentStore($order->getStoreId());
		Mage::app()->getLocale()->emulate($order->getStoreId());
		
		$pdf['print_logo'] = $this->_getPrintLogo($store);
		$pdf['print_address'] = $this->_getAddress($store);
		$pdf['items_header'] = $this->_getHeaderData();
		
		$pdf['invoices'] = array();
		$invoices = $order->getInvoiceCollection();
		$itemRender = Mage::getSingleton('simipos/pdf_order_item')
		      ->setOrder($order);
		if ($invoices->count()) {
			// Invoices
			foreach ($invoices as $invoice) {
				$invoiceData = array();
				// Header
				$invoiceData['header'] = array(
				    Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId(),
				    Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(),
				    Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
                        $order->getCreatedAtStoreDate(), 'medium', false
                    )
				);
				// Invoice Items
                $itemRender->setSource($invoice);
                $invoiceData['items'] = array();
				foreach ($invoice->getAllItems() as $item) {
					if ($item->getOrderItem()->getParentItem()) {
						continue;
					}
					$itemRender->setItem($item);
					if ($item->getOrderItem()->getProductType() == 'bundle') {
						foreach ($itemRender->draw() as $value) {
							$invoiceData['items'][] = $value;
						}
					} else {
					    $invoiceData['items'][] = $itemRender->draw();
					}
				}
				// Totals
				$invoiceData['totals'] = $this->_getTotalsData($invoice, $order);
				$pdf['invoices'][] = $invoiceData;
			}
		} else {
			// Order as invoice
            $invoiceData = array();
            // Header
            $invoiceData['header'] = array(
                Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(),
                Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
                    $order->getCreatedAtStoreDate(), 'medium', false
                )
            );
            // Order Items
            $itemRender->setSource($order);
            $invoiceData['items'] = array();
            foreach ($order->getAllItems() as $item) {
            	if ($item->getParentItem()) {
            		continue;
            	}
            	$itemRender->setItem($item);
                if ($item->getProductType() == 'bundle') {
                    foreach ($itemRender->draw() as $value) {
                        $invoiceData['items'][] = $value;
                    }
                } else {
                    $invoiceData['items'][] = $itemRender->draw();
                }
            }
            // Totals
            $invoiceData['totals'] = $this->_getTotalsData($order, $order);
            $pdf['invoices'][] = $invoiceData;
		}
		
		$pdf['order_info'] = $this->_getOrderInfo($order);
		
		Mage::app()->getLocale()->revert();
		$this->_afterGetPdf();
		return $pdf;
	}
	
    public function getOrderData($order)
    {
        $this->_beforeGetPdf();
        
        $pdf = array();
        $store = Mage::app()->getStore($order->getStoreId());
        Mage::app()->setCurrentStore($order->getStoreId());
        Mage::app()->getLocale()->emulate($order->getStoreId());
        
        $pdf['items_header'] = $this->_getHeaderData();
        
        // Order Items
        $itemRender = Mage::getSingleton('simipos/pdf_refund_item')
              ->setOrder($order)
              ->setSource($order);
        $pdf['items'] = array();
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $itemRender->setItem($item);
            if ($item->getProductType() == 'bundle') {
                foreach ($itemRender->draw() as $value) {
                    $pdf['items'][] = $value;
                }
            } else {
                $pdf['items'][] = $itemRender->draw();
            }
        }
        // Totals
        // $pdf['totals'] = $this->_getTotalsData($order, $order);
        
        $pdf['order_info'] = $this->_getOrderInfo($order);
        
        Mage::app()->getLocale()->revert();
        $this->_afterGetPdf();
        return $pdf;
    }
}
