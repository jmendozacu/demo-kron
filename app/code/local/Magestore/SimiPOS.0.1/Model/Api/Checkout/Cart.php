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
 * SimiPOS Checkout Cart API Model
 * Use to call api with prefix: checkout_cart
 * Methods:
 *  id
 *  info
 *  totals
 *  address
 *  items
 *  formatPrice
 *  createOrder
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Cart
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
	/**
	 * Reset quote id
	 * 
	 * @param string $quoteId
	 * @return boolean
	 */
	public function apiId($quoteId)
	{
		$quote = Mage::getModel('sales/quote');
		$quote->setStoreId($this->getStoreId())
		    ->load($quoteId);
		if (!$quote->getId() || $quote->getReservedOrderId()) {
			throw new Exception($this->_helper->__('Quote is not found.'), 41);
		}
		try {
			$this->_getSession()->setQuoteId($quote->getId());
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 42);
		}
		return true;
	}
	
    /**
     * retrieve full quote info
     * 
     * @return array
     */
    public function apiInfo()
    {
        $quote = $this->_getQuote();
        
        $allowedAttributes = array(
            'store_id', 'orig_order_id', 'grand_total',
            'coupon_code', 'subtotal', 'subtotal_with_discount',
            'checkout_method', 'customer_id', 'customer_group_id', 'customer_email',
            'simi_discount_amount', 'simi_discount_percent', 'simi_discount_desc'
        );
        $result = array();
        foreach ($quote->getData() as $attribute => $value) {
            if (in_array($attribute, $allowedAttributes)) {
                $result[$attribute] = $value;
            }
        }
        $result['customer_name'] = $quote->getCustomer()->getName();
        $result['customer_telephone'] = $quote->getCustomer()->getTelephone();
        $result['id'] = $quote->getId();
        
        $result['shipping_address'] = $quote->getShippingAddress()->getId();
        $result['billing_address'] = $quote->getBillingAddress()->getId();
        
        $result['is_virtual'] = $quote->isVirtual();
        /*
        $result = $this->_getAttributes($quote);
        $result['shipping_address'] = $this->_getAttributes($quote->getShippingAddress());
        $result['billing_address'] = $this->_getAttributes($quote->getBillingAddress());
        $result['items'] = array();
        foreach ($quote->getAllItems() as $item) {
            $result['items'][$item->getId()] = $this->_getAttributes($item);
        }
        $result['payment'] = $this->_getAttributes($quote->getPayment());
        $result['totals'] = $this->_totals($quote);
        */
        return $result;
    }
    
    /**
     * Totals
     * 
     * @param type $quote
     * @return type
     */
    protected function _totals($quote)
    {
        $totals = $quote->getTotals();
        
        $result = array();
        $index = 1;
        foreach ($totals as $total) {
        	if (abs($total->getValue()) < 0.0001) {
        		continue;
        	}
            $result[$index++] = array(
                'code'  => $total->getCode(),
                'title' => $total->getTitle(),
                'amount'=> $total->getValue(),
            );
        }
        return $result;
    }
    
    /**
     * Retrieve totals info
     * 
     * @return array
     */
    public function apiTotals()
    {
        $quote = $this->_getQuote();
        return $this->_totals($quote);
    }
    
    /**
     * get address of quote
     * 
     * @param string $type
     * @return array
     */
    public function apiAddress($type)
    {
        $quote = $this->_getQuote();
        if ($type == 'billing') {
            return $this->_getAddressData($quote->getBillingAddress());
        }
        return $this->_getAddressData($quote->getShippingAddress());
    }
    
    /**
     * Response address data
     * 
     * @param Varien_Object $address
     * @return array
     */
    protected function _getAddressData($address)
    {
        $result = array();
        $allowedAttribute = array(
            'quote_id', 'customer_id', 'save_in_address_book', 'customer_address_id', 'address_type',
            'email', 'prefix', 'firstname', 'middlename', 'lastname', 'suffix',
            'company', 'street', 'city', 'region', 'region_id', 'postcode',
            'country_id', 'telephone', 'fax', 'same_as_billing',
            'shipping_method', 'customer_notes',
        );
        foreach ($allowedAttribute as $attribute) {
            if ($address->hasData($attribute)) {
                $result[$attribute] = $address->getData($attribute);
            }
        }
        if ($address->getId()) {
            $result['id'] = $address->getId();
        }
        return $result;
    }
    
    /**
     * Retrieve all visible items
     * 
     * @return array
     */
    public function apiItems()
    {
        $quote = $this->_getQuote();
        $result = array();
        foreach ($quote->getAllVisibleItems() as $item) {
            $result[$item->getId()] = $this->_getItemData($item);
            // Product Data
            $product = Mage::getModel('catalog/product')->setStoreId($this->getStoreId());
            $product->load($item->getData('product_id'));
            $result[$item->getId()]['product_data'] = Mage::getSingleton('simipos/api_product')
                ->apiInfo($product);
            $result[$item->getId()]['image'] = Mage::helper('catalog/image')
                ->init($product, 'small_image')->resize(280, 220)->__toString();
            // Selected Option Data
            $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            $result[$item->getId()]['selected_options'] = $this->_prepareOptions($infoRequest);
        }
        return $result;
    }
    
    /**
     * prepare selected options for item
     * 
     * @param array $options
     * @return array
     */
    protected function _prepareOptions($options)
    {
        $allowOptions = array(
            'options', 'bundle_option', 'super_attribute', 'super_group',
            'send_friend', 'recipient_name', 'recipient_email', 'recipient_ship', 'message', 'day_to_send',
            'giftcard_amount', 'custom_giftcard_amount', 'giftcard_sender_name', 'giftcard_sender_email',
            'giftcard_recipient_name', 'giftcard_recipient_email', 'giftcard_message',
        );
        $selectedOptions = array();
        foreach ($options as $code => $value) {
            if (!in_array($code, $allowOptions)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $selectedOptions[$code . '[' . $subKey . ']'] = $subValue;
                }
            } else {
                $selectedOptions[$code] = $value;
            }
        }
        return $selectedOptions;
    }
    
    /**
     * Format price for shopping cart
     * 
     * @return array
     */
    public function apiFormatPrice()
    {
        return Mage::app()->getLocale()->getJsPriceFormat();
    }
    
    /**
     * create order from shopping cart
     * 
     * @param array $configData     order create configuration data
     * @return array
     */
    public function apiCreateOrder($configData = array())
    {
        /**
         * Config Data:
         *  is_shipped
         *  is_invoice
         *  cash_in
         */
        $quote = $this->_getQuote();
        $result = array();
        
        if (!empty($configData['cash_in'])) {
            $cashIn = floatval($configData['cash_in']);
            $baseCashIn = $cashIn / $this->getStore()->convertPrice(1);
            $quote->setSimiposCash($cashIn)
                ->setSimiposBaseCash($baseCashIn);
        }
        
        if (isset($configData['payment'])) {
            $quote->getPayment()->importData($configData['payment']);
        }
        
        $customerApi = Mage::getModel('simipos/api_checkout_customer');
        $isNewCustomer = $customerApi->prepareCustomerForQuote($quote);
        try {
            $quote->collectTotals();
            
            // Attach simipos user for order
            $quote->setSimiposUser($this->getUser()->getId());
            $quote->setSimiposEmail($this->getUser()->getEmail());
            
            // Prepare customer information
            if ($quote->getCustomerId()) {
            	$simiCustomer = Mage::getModel('simipos/customer')
            	   ->setData($quote->getCustomer()->getData());
            	$quote->setCustomer($simiCustomer);
            }
            
            $service = Mage::getModel('simipos/service_quote', $quote);
            $service->submitAll();
            
            if ($isNewCustomer) {
                try {
                    $customerApi->involveNewCustomer($quote);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            
            $order = $service->getOrder();
            if ($order) {
                Mage::dispatchEvent('checkout_type_onepage_save_order_after', array(
                    'order' => $order,
                    'quote' => $quote
                ));
                if ($order->getCustomerEmail()) {
                    try {
                        $order->sendNewOrderEmail();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
            Mage::dispatchEvent('checkout_submit_all_after', array(
                'order' => $order,
                'quote' => $quote
            ));
            // Clear current quote id
            $this->_getSession()->setQuoteId(null);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 45); // cannot create order
        }
        // Process Shipping and Payment - No throw exeption here
        $transaction = Mage::getModel('core/resource_transaction')
            ->addObject($order);
        $needSave = false;
        if (in_array($quote->getPayment()->getMethod(), array('checkmo', 'cashondelivery', 'purchaseorder'))) {
        	$configData['is_invoice'] = true;
        }
        if (($order->getSimiposCash() >= $order->getGrandTotal()
                || !empty($configData['is_invoice'])
            ) && $order->canInvoice()
        ) {
            $invoice = $order->prepareInvoice();
            $invoice->register();
            $transaction->addObject($invoice);
            $needSave = true;
        }
        if (!empty($configData['is_shipped']) && $order->canShip()) {
            $shipment = $order->prepareShipment();
            $shipment->register();
            $transaction->addObject($shipment);
            $needSave = true;
        }
        try {
            if ($needSave) {
                $transaction->save();
            }
            if (isset($invoice)) {
                $result['invoice'] = $invoice->getIncrementId();
            } else {
                $invoice = $order->getInvoiceCollection()->getFirstItem();
                if ($invoice && $invoice->getId()) {
                    $result['invoice'] = $invoice->getIncrementId();
                }
            }
            if (isset($shipment)) {
                $result['shipment'] = $shipment->getIncrementId();
                Mage::dispatchEvent('simipos_api_checkout_cart_order_shipment', array(
                    'model'     => $this,
                    'order'     => $order,
                    'shipment'  => $shipment
                ));
            }
        } catch (Exception $e) {
            // no throw here
        }
        $result['order'] = $order->getIncrementId();
        $result['status'] = $order->getStatus();
        $result['status_label'] = $order->getStatusLabel();
        return $result;
    }
}
